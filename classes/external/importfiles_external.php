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

use external_api;
use external_function_parameters;
use external_single_structure;
use external_value;
use Google_Exception;
use Google_Service_Drive;
use invalid_parameter_exception;
use local_tresipuntimportgc\providers\gclassroom;
use local_tresipuntimportgc\providers\gdrive;
use moodle_exception;
use ReflectionException;

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
                'courseid' => new external_value(PARAM_INT, 'Course id for import files', VALUE_REQUIRED)
            )
        );
    }

    /**
     * @param string $providerid
     * @param int $courseid
     * @return array
     * @throws Google_Exception
     * @throws invalid_parameter_exception
     * @throws moodle_exception
     * @throws ReflectionException
     */
    public static function importfiles(string $providerid, int $courseid): array {
        global $CFG;
        require_once($CFG->dirroot . '/course/lib.php');
        require_once($CFG->dirroot . '/user/externallib.php');
        self::validate_parameters(
            self::importfiles_parameters(), [
                'providerid' => $providerid,
                'courseid' => $courseid
            ]
        );
        $provider = new gclassroom();
        $courseclassroom = $provider->get_course($providerid);
        $coursemodel = $courseclassroom->data->providerdata;
        $folderid = accessProtected($courseclassroom->data->providerdata, 'modelData')['teacherFolder']['id'];
        // Needs the same client as for the first login, if a new client or provider with other scopes is created, it skips it because it is already logged in.
        $gdrvieclient = $provider->get_client();
        $service = new Google_Service_Drive($gdrvieclient);
        $optParams =['q' => "'". $folderid ."' in parents"];
        $files = $service->files->listFiles($optParams);
        // TODO get all files and zip, or zif folder directly
        print_object($files);die();
        if (count($folders->getItems()) === 0) {
            print_trace('emptydirectory', 'warning');
        } else {
            echo count($folders->getItems()) . ' cosas encontradas en drive';
            foreach ($folders->getItems() as $folder) {
                if ($folderid === $folder->getId()) {
                    echo 'eureka!!!!';
                }
            }
        }
        print_object($courseclassroom);
        print_object($folders);die();
        return [
            'success' => true,
            'errors' => '',
            'id' => 1
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
