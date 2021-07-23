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

use core_course_category;
use external_api;
use external_function_parameters;
use external_single_structure;
use external_value;
use invalid_parameter_exception;
use local_tresipuntimportgc\factory;
use local_tresipuntimportgc\providers\gclassroom;
use moodle_exception;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->libdir . '/externallib.php');
require_once($CFG->dirroot . '/webservice/lib.php');

class course_external extends external_api {

    /**
     * @return external_function_parameters
     */
    public static function create_course_parameters(): external_function_parameters {
        return new external_function_parameters(
            array(
                'providerid' => new external_value(PARAM_TEXT, 'Course ID Provider', true),
                'fullname' => new external_value(PARAM_TEXT, 'Course Fullname', true),
                'shortname' => new external_value(PARAM_TEXT, 'Course Shortname', true),
                'category' => new external_value(PARAM_INT, 'Category ID', true),
                'visible' => new external_value(PARAM_BOOL, 'Visibility', true)
            )
        );
    }

    /**
     * @param string $providerid
     * @param string $fullname
     * @param string $shortname
     * @param int $category
     * @param bool $visible
     * @return array
     * @throws invalid_parameter_exception
     * @throws moodle_exception
     */
    public static function create_course(
        string $providerid, string $fullname, string $shortname, int $category, bool $visible): array {
        global $CFG;
        require_once($CFG->dirroot . '/course/lib.php');
        require_once($CFG->dirroot . '/user/externallib.php');
        self::validate_parameters(
            self::create_course_parameters(), [
                'providerid' => $providerid,
                'fullname' => $fullname,
                'shortname' => $shortname,
                'category' => $category,
                'visible' => $visible
            ]
        );

        $category = core_course_category::get($category);
        if ($category) {
            if (core_course_category::can_view_category($category)) {
                // Factory.
                $provider = new gclassroom();
                $factory = new factory($provider);
                $res = $factory->create_course($providerid, $category->id, $fullname, $shortname, $visible);
                $success = $res->success;
                $error = $res->error;
            } else {
                $success = false;
                $error = 'El usuario no puede crear cursos en esta categoría';
            }
        } else {
            $success = false;
            $error = 'La categoría no existe';
        }

        return [
            'success' => $success,
            'error' => $error
        ];
    }

    /**
     * @return external_single_structure
     */
    public static function create_course_returns(): external_single_structure {
        return new external_single_structure(
            array(
                'success' => new external_value(PARAM_BOOL, 'Was it a success?'),
                'error' => new external_value(PARAM_TEXT, 'Error message')
            )
        );
    }


}
