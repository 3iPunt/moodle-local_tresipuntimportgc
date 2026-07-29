<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * @package     local_tresipuntimportgc
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright   3iPunt <https://www.tresipunt.com/>
 */

namespace local_tresipuntimportgc\external;

use calendar_event;
use coding_exception;
use context_course;
use core_external\external_api;
use core_external\external_function_parameters;
use core_external\external_single_structure;
use core_external\external_value;
use html_writer;
use invalid_parameter_exception;
use local_tresipuntimportgc\local\drive_files;
use local_tresipuntimportgc\local\trace_router;
use local_tresipuntimportgc\providers\google;
use moodle_exception;
use stdClass;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/calendar/lib.php');

class importcalendar_external extends external_api {

    /**
     * Import Calendard Parameters.
     *
     * @return external_function_parameters
     */
    public static function importcalendar_parameters(): external_function_parameters {
        return new external_function_parameters(
            array(
                'providerid' => new external_value(PARAM_TEXT, 'Course ID Provider', VALUE_REQUIRED),
                'courseid' => new external_value(PARAM_INT, 'Course id for import events', VALUE_REQUIRED)
            )
        );
    }

    /**
     * Imports the course calendar events into the Moodle course calendar.
     *
     * Only events not linked to Classroom content are imported: content
     * events are already covered when their module is created.
     *
     * @param  string $providerid Classroom course id.
     * @param  int    $courseid   Moodle course id.
     * @return array
     * @throws coding_exception
     * @throws invalid_parameter_exception
     * @throws moodle_exception
     */
    public static function importcalendar(string $providerid, int $courseid): array {
        global $USER, $DB;

        $syscontext = \context_system::instance();
        self::validate_context($syscontext);
        require_capability('local/tresipuntimportgc:import', $syscontext);
        self::validate_parameters(
            self::importcalendar_parameters(), [
                'providerid' => $providerid,
                'courseid' => $courseid
            ]
        );
        $provider = new google();
        $resevents = $provider->get_calendar_events($providerid);
        if (!$resevents->success) {
            trace_router::trace('importfileerror', 'danger',
                ['name' => $providerid, 'error' => $resevents->error->to_string()]);
            return ['success' => false, 'errors' => $resevents->error->to_string(), 'id' => $courseid];
        }

        // Discard the events tied to course contents.
        $events = array_filter($resevents->data, static function ($event) {
            return !$event->isclassroom;
        });

        if (empty($events)) {
            trace_router::trace('noteventsfound', 'warning');
        } else {
            trace_router::trace('eventsfound', 'warning', count($events));
            foreach ($events as $googleevent) {
                // TODO template.
                // La descripción de Google es texto plano: mismo tratamiento que
                // en los módulos y el resumen del curso (escapa, respeta saltos
                // y enlaza URLs).
                $summary = text_to_html((string) $googleevent->description, false, false, true);
                // TODO replace link to Meet Conference for a Zoom, BigBlue, etc resource.
                foreach ($googleevent->conferencelinks as $link) {
                    $summary .= '<hr>';
                    $summary .= html_writer::link($link,
                        get_string('conference', 'local_tresipuntimportgc'), ['target' => '_blank']);
                }
                if ($googleevent->location !== '') {
                    $summary .= '<hr>';
                    $summary .= html_writer::tag('h5', get_string('location', 'moodle'));
                    $summary .= html_writer::tag('p', s($googleevent->location));
                    $summary .= '<br>';
                }

                // calendar_event::create() se llama con su $checkcapability por
                // defecto (true), así que la capacidad de crear eventos SÍ se
                // comprueba: no hay ninguna propiedad que lo desactive.
                $event = new stdClass();
                $event->eventtype = 'course';
                $event->type = CALENDAR_EVENT_COURSE;
                $event->name = $googleevent->title;
                $event->description = $summary;
                $event->format = FORMAT_HTML;
                $event->courseid = $courseid;
                $event->groupid = 0;
                $event->userid = $USER->id;
                $event->modulename = '0';
                $event->instance = 0;
                $event->timestart = $googleevent->timestart;
                $event->visible = 1;
                $event->timeduration = $googleevent->timeduration;
                $ev = calendar_event::create($event);

                // Adjuntos → ficheros de Moodle en el área del evento (E10.10):
                // los de Drive se descargan y se referencian con @@PLUGINFILE@@;
                // los que no son de Drive quedan como enlace externo.
                if ($ev && !empty($googleevent->attachments)) {
                    $coursecontext = context_course::instance($courseid);
                    $links = [];
                    foreach ($googleevent->attachments as $attachment) {
                        if ($attachment->fileid !== '') {
                            $meta = $provider->get_drive_file($attachment->fileid);
                            if ($meta->success) {
                                drive_files::store($provider, $meta->data, $coursecontext->id,
                                    (int) $USER->id, 'calendar', 'event_description', '/', (int) $ev->id);
                                $links[] = html_writer::link(
                                    '@@PLUGINFILE@@/' . rawurlencode($meta->data->name), $meta->data->name);
                                continue;
                            }
                        }
                        $links[] = html_writer::link($attachment->url, $attachment->title, ['target' => '_blank']);
                    }
                    $newdesc = $summary . '<hr>' . html_writer::tag('h5', get_string('files'))
                        . implode('<br>', $links);
                    $DB->set_field('event', 'description', $newdesc, ['id' => $ev->id]);
                }
            }
        }

        return [
            'success' => true,
            'errors' => '',
            'id' => $courseid
        ];
    }

    /**
     * Import Calendar Returns.
     *
     * @return external_single_structure
     */
    public static function importcalendar_returns(): external_single_structure {
        return new external_single_structure(
            array(
                'success' => new external_value(PARAM_BOOL, 'Was it a success?'),
                'errors' => new external_value(PARAM_TEXT, 'Error message'),
                'id' => new external_value(PARAM_INT, 'Course ID', false)
            )
        );
    }
}
