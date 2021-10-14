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
use external_api;
use external_function_parameters;
use external_single_structure;
use external_value;
use Google_Exception;
use Google_Service_Calendar;
use Google_Service_Calendar_Event;
use html_writer;
use invalid_parameter_exception;
use local_tresipuntimportgc\providers\google;
use moodle_exception;
use ReflectionException;
use stdClass;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->libdir . '/externallib.php');
require_once($CFG->dirroot . '/webservice/lib.php');
require_once($CFG->dirroot . '/local/tresipuntimportgc/lib.php');
require_once($CFG->dirroot.'/calendar/lib.php');

class importcalendar_external extends external_api {

    /**
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
     * @param string $providerid
     * @param int $courseid
     * @return array
     * @throws Google_Exception
     * @throws ReflectionException
     * @throws coding_exception
     * @throws invalid_parameter_exception
     * @throws moodle_exception
     */
    public static function importcalendar(string $providerid, int $courseid): array {
        global $CFG, $USER;
        require_once($CFG->dirroot . '/course/lib.php');
        require_once($CFG->dirroot . '/user/externallib.php');
        self::validate_parameters(
            self::importcalendar_parameters(), [
                'providerid' => $providerid,
                'courseid' => $courseid
            ]
        );
        $provider = new google();
        $courseclassroom = $provider->get_course($providerid);
        // Needs the same client as for the first login, if a new client or provider with other scopes is created, it skips it because it is already logged in.
        $gdrvieclient = $provider->get_client();
        $tokenjson = json_decode($gdrvieclient->getAccessToken(), true);
        $service = new Google_Service_Calendar($gdrvieclient);
        $calendarid = accessProtected($courseclassroom->data->providerdata, 'modelData')['calendarId'];
        $optParams = array(
            'maxResults' => 100,
            'orderBy' => 'startTime',
            'singleEvents' => true,
            'timeMin' => date('c'),
        );
        $results = $service->events->listEvents($calendarid, $optParams);
        /** @var Google_Service_Calendar_Event[] $googleevents */
        $googleevents = $results->getItems();
        if (empty($googleevents)) {
            print_trace('noteventsfound', 'warning');
        } else {
            // First we discard here the events related to course contents, as their calendar event is already created when creating the mod.
            foreach ($googleevents as $key => $googleevent) {
                // There is no other way to distinguish classroom calendar events from those created manually in the same calendar.
                if (strripos($googleevent->getDescription(), 'https://classroom.google.com') !== false) {
                    unset($googleevents[$key]);
                }
            }
            print_trace('eventsfound', 'warning', count($googleevents));
            foreach ($googleevents as $googleevent) {
                $start = $googleevent->getStart()->dateTime;
                if (empty($start)) {
                    $start = $googleevent->getStart()->date;
                }
                $end = $googleevent->getEnd()->dateTime;
                if (empty($end)) {
                    $end = $googleevent->getEnd()->date;
                }
                $start = strtotime($start);
                $end = strtotime($end);

                $modeldata = accessProtected($googleevent, 'modelData');
                // TODO template.
                $summary = html_writer::tag('p', $googleevent->getDescription());
                // TODO replace link to Meet Conference for a Zoom, BigBlue, etc resource.
                if (isset($modeldata['conferenceData']['entryPoints']) && count($modeldata['conferenceData']['entryPoints']) > 0) {
                    foreach($modeldata['conferenceData']['entryPoints'] as $entrypoint) {
                        if ($entrypoint['entryPointType'] === 'video') {
                            $summary.= '<hr>';
                            $summary .= html_writer::link($entrypoint['uri'], get_string('conference', 'local_tresipuntimportgc'), ['target' => 'blank']);
                        }
                    }
                }
                // TODO replace files of event link for.... ???
                if (isset($modeldata['attachments']) && count($modeldata['attachments']) > 0) {
                    $summary.= '<hr>';
                    $summary .= html_writer::tag('h5', get_string('files'));
                    foreach($modeldata['attachments'] as $attachment) {
                        $summary .= html_writer::link($attachment['fileUrl'], $attachment['title'], ['target' => '_blank']);
                    }
                }
                if ($googleevent->location !== '') {
                    $summary.= '<hr>';
                    $summary .= html_writer::tag('h5', get_string('location', 'moodle'));
                    $summary .= html_writer::tag('p', $googleevent->location);
                    $summary.= '<br>';
                }

                $event = new stdClass();
                $event->eventtype = 'course';
                $event->checkcapability  = false; // user must have event creation permissions
                $event->type = CALENDAR_EVENT_COURSE;
                $event->name = $googleevent->getSummary();
                $event->description = $summary;
                $event->format = FORMAT_HTML;
                $event->courseid = $courseid;
                $event->groupid = 0;
                $event->userid = $USER->id;
                $event->modulename = '0';
                $event->instance = 0;
                $event->timestart = $start;
                $event->visible = 1;
                $event->timeduration = $end - $start;
                calendar_event::create($event);
            }
        }
        $errors = [];

        // TODO response
        return [
            'success' => true,
            'errors' => $errors,
            'id' => $courseid
        ];
    }
        /**
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
