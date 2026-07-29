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
 * @copyright   2026 3iPunt (contacte@tresipunt.com)
 */

namespace local_tresipuntimportgc\external;

use coding_exception;
use context_user;
use core_external\external_api;
use core_external\external_function_parameters;
use core_external\external_single_structure;
use core_external\external_value;
use invalid_parameter_exception;
use local_tresipuntimportgc\local\drive_files;
use local_tresipuntimportgc\local\trace_router;
use local_tresipuntimportgc\providers\google;
use moodle_exception;

class importfiles_external extends external_api {

    /**
     * Import Files Parameters.
     *
     * @return external_function_parameters
     */
    public static function importfiles_parameters(): external_function_parameters {
        return new external_function_parameters(
            array(
                'providerid' => new external_value(PARAM_TEXT, 'Course ID Provider', VALUE_REQUIRED),
                'courseid' => new external_value(PARAM_INT, 'Course id for import files', VALUE_REQUIRED),
                // PARAM_SAFEPATH: se usa como nombre de carpeta en el área de
                // ficheros, así que no puede traer separadores de ruta.
                'shortname' => new external_value(PARAM_SAFEPATH, 'Short name of course', VALUE_REQUIRED),
            )
        );
    }

    /**
     * Imports the Drive files of the course teacher folder into the private
     * files area of the current user.
     *
     * @param  string $providerid Classroom course id.
     * @param  int    $courseid   Moodle course id (echoed back).
     * @param  string $shortname  Course shortname (private files subfolder).
     * @return array
     * @throws coding_exception
     * @throws invalid_parameter_exception
     * @throws moodle_exception
     */
    public static function importfiles(string $providerid, int $courseid, string $shortname): array {
        global $USER;

        $syscontext = \context_system::instance();
        self::validate_context($syscontext);
        require_capability('local/tresipuntimportgc:import', $syscontext);
        $params = self::validate_parameters(
            self::importfiles_parameters(), [
                'providerid' => $providerid,
                'courseid' => $courseid,
                'shortname' => $shortname
            ]
        );
        $providerid = $params['providerid'];
        $courseid = $params['courseid'];
        $shortname = $params['shortname'];
        $provider = new google();
        $errors = [];

        $resfolder = $provider->get_teacher_folder($providerid);
        if (!$resfolder->success || $resfolder->data === null) {
            trace_router::trace('teacherfoldererrorcreated', 'warning');
            return ['success' => false, 'errors' => $resfolder->error ? $resfolder->error->to_string() : '', 'id' => $courseid];
        }
        $resfiles = $provider->list_drive_folder($resfolder->data->get_providerid());
        if (!$resfiles->success) {
            trace_router::trace('importfileerror', 'danger', ['name' => $shortname, 'error' => $resfiles->error->to_string()]);
            return ['success' => false, 'errors' => $resfiles->error->to_string(), 'id' => $courseid];
        }

        $files = $resfiles->data;
        $context = context_user::instance($USER->id);
        trace_router::trace('filesfound', 'info', count($files));
        if (count($files) > 0) {
            get_file_storage()->create_directory($context->id, 'user', 'private', 0, '/' . $shortname . '/');
            foreach ($files as $filemeta) {
                drive_files::store(
                    $provider,
                    $filemeta,
                    $context->id,
                    (int) $USER->id,
                    'user',
                    'private',
                    '/' . $shortname . '/'
                );
            }
        }
        return [
            'success' => true,
            'errors' => implode('; ', $errors),
            'id' => $courseid
        ];
    }

    /**
     * Imporet Files Returns.
     *
     * @return external_single_structure
     */
    public static function importfiles_returns(): external_single_structure {
        return new external_single_structure(
            array(
                'success' => new external_value(PARAM_BOOL, 'Was it a success?'),
                'errors' => new external_value(PARAM_TEXT, 'Error message'),
                'id' => new external_value(PARAM_INT, 'Course ID', false)
            )
        );
    }
}
