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

use coding_exception;
use context_course;
use context_user;
use core_contentbank\contentbank;
use external_api;
use external_function_parameters;
use external_single_structure;
use external_value;
use file_exception;
use file_storage;
use Google_Exception;
use Google_Http_Request;
use Google_Service_Drive;
use Google_Service_Drive_DriveFile;
use Google_Service_Drive_FileList;
use invalid_parameter_exception;
use local_tresipuntimportgc\providers\gclassroom;
use local_tresipuntimportgc\providers\gdrive;
use moodle_exception;
use ReflectionException;
use stored_file_creation_exception;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->libdir . '/externallib.php');
require_once($CFG->dirroot . '/webservice/lib.php');
require_once($CFG->dirroot . '/local/tresipuntimportgc/lib.php');

class importfiles_external extends external_api {

    /**
     * @return external_function_parameters
     */
    public static function importfiles_parameters(): external_function_parameters {
        return new external_function_parameters(
            array(
                'providerid' => new external_value(PARAM_TEXT, 'Course ID Provider', VALUE_REQUIRED),
                'courseid' => new external_value(PARAM_INT, 'Course id for import files', VALUE_REQUIRED),
                'shortname' => new external_value(PARAM_RAW, 'Short name of course', VALUE_REQUIRED),
            )
        );
    }

    /**
     * @param string $providerid
     * @param int $courseid
     * @param string $shortname
     * @return array
     * @throws Google_Exception
     * @throws ReflectionException
     * @throws coding_exception
     * @throws file_exception
     * @throws stored_file_creation_exception
     * @throws invalid_parameter_exception
     * @throws moodle_exception
     */
    public static function importfiles(string $providerid, int $courseid, string $shortname): array {
        global $CFG, $USER;
        require_once($CFG->dirroot . '/course/lib.php');
        require_once($CFG->dirroot . '/user/externallib.php');
        self::validate_parameters(
            self::importfiles_parameters(), [
                'providerid' => $providerid,
                'courseid' => $courseid,
                'shortname' => $shortname
            ]
        );
        $provider = new gclassroom();
        $courseclassroom = $provider->get_course($providerid);
        $folderid = accessProtected($courseclassroom->data->providerdata, 'modelData')['teacherFolder']['id'];
        // Needs the same client as for the first login, if a new client or provider with other scopes is created, it skips it because it is already logged in.
        $gdrvieclient = $provider->get_client();
        $tokenjson = json_decode($gdrvieclient->getAccessToken(), true);
        $service = new Google_Service_Drive($gdrvieclient);

        $optParams =['q' => "'". $folderid ."' in parents"];
        $filelist = $service->files->listFiles($optParams);
        /** @var Google_Service_Drive_DriveFile[] $files */
        $files = $filelist->getItems();
        $context = context_user::instance($USER->id);
        $fs = get_file_storage();
        $errors = [];
        print_trace('filesfound', 'info', count($files));
        if (count($files) > 0) {
            $fs->create_directory($context->id, 'user', 'private', 0, '/' . $shortname . '/');
            foreach ($files as $file) {
                import_file(
                    $fs,
                    $file,
                    $service,
                    $context->id,
                    (int)$USER->id,
                    $tokenjson['access_token'],
                    'user',
                    'private',
                    '/' . $shortname . '/'
                );
            }
        }
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
