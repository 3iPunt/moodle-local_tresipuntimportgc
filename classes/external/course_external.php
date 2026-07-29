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
use context_coursecat;
use core_course_category;
use core_external\external_api;
use core_external\external_function_parameters;
use core_external\external_single_structure;
use core_external\external_value;
use invalid_parameter_exception;
use local_tresipuntimportgc\factory\factory;
use local_tresipuntimportgc\local\trace_router;
use local_tresipuntimportgc\providers\google;
use local_tresipuntimportgc\providers\provider;
use moodle_exception;

defined('MOODLE_INTERNAL') || die();

class course_external extends external_api {

    /**
     * @return external_function_parameters
     */
    public static function create_course_parameters(): external_function_parameters {
        return new external_function_parameters(
            array(
                'providerid' => new external_value(PARAM_TEXT, 'Course ID Provider', VALUE_REQUIRED),
                'fullname' => new external_value(PARAM_TEXT, 'Course Fullname', VALUE_REQUIRED),
                'shortname' => new external_value(PARAM_TEXT, 'Course Shortname', VALUE_REQUIRED),
                'category' => new external_value(PARAM_INT, 'Category ID', VALUE_REQUIRED),
                'visible' => new external_value(PARAM_BOOL, 'Visibility', VALUE_REQUIRED),
                'importfiles' => new external_value(PARAM_INT, 'Config for Import Google Drive files', VALUE_REQUIRED),
            )
        );
    }

    /**
     * @param string $providerid
     * @param string $fullname
     * @param string $shortname
     * @param int $category
     * @param bool $visible
     * @param int $importfiles
     * @param provider|null $provider Connected provider (defaults to a new google();
     *                                injectable so the importer reuses one authenticated
     *                                provider per run and tests can avoid the network).
     * @return array
     * @throws coding_exception
     * @throws invalid_parameter_exception
     * @throws moodle_exception
     */
    public static function create_course(
        string $providerid, string $fullname, string $shortname, int $category, bool $visible,
        int $importfiles, ?provider $provider = null): array {
        global $CFG;
        require_once($CFG->dirroot . '/course/lib.php');
        $syscontext = \context_system::instance();
        self::validate_context($syscontext);
        require_capability('local/tresipuntimportgc:import', $syscontext);
        self::validate_parameters(
            self::create_course_parameters(), [
                'providerid' => $providerid,
                'fullname' => $fullname,
                'shortname' => $shortname,
                'category' => $category,
                'visible' => $visible,
                'importfiles' => $importfiles
            ]
        );

        // New value type (bool) is not matching the resolved parameter type and might introduce types-related false-positives.
        // $category = core_course_category::get($category);
        $moodlecategory = core_course_category::get($category);
        if ($moodlecategory) {
            if (core_course_category::can_view_category($moodlecategory)) {
                // create_course() does not check capabilities: the caller must.
                // Being allowed to import is not being allowed to create a
                // course anywhere, so the category context decides.
                require_capability('moodle/course:create', context_coursecat::instance($moodlecategory->id));
                // Factory.
                $provider = $provider ?? new google();
                $factory = new factory($provider);
                $res = $factory->create_course($providerid, $moodlecategory->id, $fullname, $shortname, $visible, $importfiles);
                $success = $res->success;
                $errors = $res->error ? $res->error->to_string() : '';
                $id = $res->success ? $res->data : null;
            } else {
                $success = false;
                trace_router::trace('user_can_not_view_category', 'danger', ['category' => $moodlecategory->name, 'course' => $fullname]);
                $errors = 'USER_CAN_NOT_VIEW_CATEGORY';
                $id = null;
            }
        } else {
            $success = false;
            trace_router::trace('category_no_exist', 'danger', ['categoryid' => $category, 'course' => $fullname]);
            $errors = 'CATEGORY_NO_EXIST';
            $id = null;
        }
        return [
            'success' => $success,
            'errors' => $errors,
            'id' => $id
        ];
    }

    /**
     * @return external_single_structure
     */
    public static function create_course_returns(): external_single_structure {
        return new external_single_structure(
            array(
                'success' => new external_value(PARAM_BOOL, 'Was it a success?'),
                'errors' => new external_value(PARAM_TEXT, 'Error message'),
                'id' => new external_value(PARAM_INT, 'Course ID', false)
            )
        );
    }
}
