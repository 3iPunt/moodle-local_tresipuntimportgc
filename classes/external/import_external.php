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
 * Import run web services: incremental status, retry and discard.
 *
 * @package    local_tresipuntimportgc
 * @copyright  2026 3iPunt (contacte@tresipunt.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_tresipuntimportgc\external;

use coding_exception;
use context_system;
use core_course_category;
use core_external\external_api;
use core_external\external_function_parameters;
use core_external\external_multiple_structure;
use core_external\external_single_structure;
use core_external\external_value;
use core_external\restricted_context_exception;
use dml_exception;
use invalid_parameter_exception;
use local_tresipuntimportgc\event\gc_course_discarded;
use local_tresipuntimportgc\event\gc_course_retried;
use local_tresipuntimportgc\local\importer;
use local_tresipuntimportgc\models\import;
use local_tresipuntimportgc\models\import_course;
use local_tresipuntimportgc\providers\google;
use moodle_exception;
use moodle_url;
use required_capability_exception;

/**
 * Web services of the import run entity.
 *
 * get_status is the polling endpoint of the progress page: it returns the
 * per-course statuses plus only the trace lines newer than lastlogid.
 *
 * @package    local_tresipuntimportgc
 * @copyright  2026 3iPunt (contacte@tresipunt.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class import_external extends external_api {

    /**
     * get_status parameters.
     *
     * @return external_function_parameters
     */
    public static function get_status_parameters(): external_function_parameters {
        return new external_function_parameters([
            'importid' => new external_value(PARAM_INT, 'Import run id', VALUE_REQUIRED),
            'lastlogid' => new external_value(PARAM_INT, 'Return only traces newer than this id', VALUE_DEFAULT, 0),
        ]);
    }

    /**
     * Returns the run status, the per-course statuses and the new traces.
     *
     * @param int $importid Import run id.
     * @param int $lastlogid Only traces with id greater than this.
     * @return array
     * @throws coding_exception
     * @throws moodle_exception
     * @throws dml_exception
     * @throws invalid_parameter_exception
     * @throws required_capability_exception
     * @throws restricted_context_exception
     */
    public static function get_status(int $importid, int $lastlogid = 0): array {
        $params = self::validate_parameters(self::get_status_parameters(),
            ['importid' => $importid, 'lastlogid' => $lastlogid]);
        $context = context_system::instance();
        self::validate_context($context);
        require_capability('local/tresipuntimportgc:import', $context);

        $import = new import($params['importid']);
        $status = $import->get_status();
        $maxlogid = $params['lastlogid'];
        $courses = [];
        foreach ($import->get_courses() as $course) {
            $logs = [];
            foreach ($course->get_logs($params['lastlogid']) as $log) {
                $maxlogid = max($maxlogid, (int) $log->get('id'));
                $logs[] = [
                    'id' => (int) $log->get('id'),
                    'timecreated' => (int) $log->get('timecreated'),
                    'level' => $log->get('level'),
                    // The progress page injects this as HTML: clean it here too.
                    // Traces are built escaped (trace_router), but error traces
                    // logged straight from an exception message — and anything
                    // already stored by an earlier release — do not go through
                    // that path.
                    'message' => format_text((string) $log->get('message'), FORMAT_HTML,
                        ['context' => $context, 'filter' => false]),
                ];
            }
            $courses[] = [
                'id' => (int) $course->get('id'),
                'status' => $course->get('status'),
                'courseid' => (int) ($course->get('courseid') ?? 0),
                'courseurl' => $course->get('courseid')
                    ? (new moodle_url('/course/view.php', ['id' => $course->get('courseid')]))->out(false) : '',
                'logs' => $logs,
            ];
        }
        return [
            'status' => $status,
            'finished' => in_array($status,
                [import::STATUS_COMPLETED, import::STATUS_PARTIAL, import::STATUS_ERROR], true),
            'maxlogid' => $maxlogid,
            'courses' => $courses,
        ];
    }

    /**
     * get_status returns.
     *
     * @return external_single_structure
     */
    public static function get_status_returns(): external_single_structure {
        return new external_single_structure([
            'status' => new external_value(PARAM_ALPHA, 'Derived run status'),
            'finished' => new external_value(PARAM_BOOL, 'Whether every course reached a final state'),
            'maxlogid' => new external_value(PARAM_INT, 'Highest trace id returned (next lastlogid)'),
            'courses' => new external_multiple_structure(new external_single_structure([
                'id' => new external_value(PARAM_INT, 'Import course id'),
                'status' => new external_value(PARAM_ALPHA, 'Course status'),
                'courseid' => new external_value(PARAM_INT, 'Moodle course id (0 if none)'),
                'courseurl' => new external_value(PARAM_URL, 'Moodle course URL (empty if none)'),
                'logs' => new external_multiple_structure(new external_single_structure([
                    'id' => new external_value(PARAM_INT, 'Trace id'),
                    'timecreated' => new external_value(PARAM_INT, 'Trace timestamp'),
                    'level' => new external_value(PARAM_ALPHA, 'info, warning or error'),
                    'message' => new external_value(PARAM_RAW, 'Trace message (HTML)'),
                ])),
            ])),
        ]);
    }

    /**
     * retry_course parameters.
     *
     * @return external_function_parameters
     */
    public static function retry_course_parameters(): external_function_parameters {
        return new external_function_parameters([
            'importcourseid' => new external_value(PARAM_INT, 'Import course id', VALUE_REQUIRED),
        ]);
    }

    /**
     * Re-queues a failed course.
     *
     * The stored refresh token is wiped when a run finishes, so the retry
     * captures a fresh one from the current session: the user must be
     * connected to Google to retry.
     *
     * @param  int $importcourseid Import course id.
     * @return array
     */
    public static function retry_course(int $importcourseid): array {
        global $USER;

        $params = self::validate_parameters(self::retry_course_parameters(),
            ['importcourseid' => $importcourseid]);
        $context = context_system::instance();
        self::validate_context($context);
        require_capability('local/tresipuntimportgc:import', $context);

        $course = new import_course($params['importcourseid']);
        if ($course->get('status') !== import_course::STATUS_ERROR) {
            return ['success' => false,
                'message' => get_string('retry_notfailed', 'local_tresipuntimportgc')];
        }
        $provider = new google();
        if (!$provider->has_token() || $provider->get_refresh_token() === null) {
            return ['success' => false,
                'message' => get_string('retry_needsconnection', 'local_tresipuntimportgc')];
        }
        $import = $course->get_import();
        $import->set_refresh_token($provider->get_refresh_token());
        $import->update();
        $course->mark_retried();
        importer::queue_course_task($course, (int) $USER->id);
        gc_course_retried::create_from_course($course)->trigger();
        return ['success' => true, 'message' => ''];
    }

    /**
     * retry_course returns.
     *
     * @return external_single_structure
     */
    public static function retry_course_returns(): external_single_structure {
        return new external_single_structure([
            'success' => new external_value(PARAM_BOOL, 'Whether the course was re-queued'),
            'message' => new external_value(PARAM_TEXT, 'Reason when it was not'),
        ]);
    }

    /**
     * discard_course parameters.
     *
     * @return external_function_parameters
     */
    public static function discard_course_parameters(): external_function_parameters {
        return new external_function_parameters([
            'importcourseid' => new external_value(PARAM_INT, 'Import course id', VALUE_REQUIRED),
        ]);
    }

    /**
     * Discards a pending course (it will not be imported).
     *
     * @param  int $importcourseid Import course id.
     * @return array
     */
    public static function discard_course(int $importcourseid): array {
        $params = self::validate_parameters(self::discard_course_parameters(),
            ['importcourseid' => $importcourseid]);
        $context = context_system::instance();
        self::validate_context($context);
        require_capability('local/tresipuntimportgc:import', $context);

        $course = new import_course($params['importcourseid']);
        if ($course->get('status') !== import_course::STATUS_PENDING) {
            return ['success' => false,
                'message' => get_string('discard_notpending', 'local_tresipuntimportgc')];
        }
        $course->mark_discarded();
        gc_course_discarded::create_from_course($course)->trigger();
        return ['success' => true, 'message' => ''];
    }

    /**
     * discard_course returns.
     *
     * @return external_single_structure
     */
    public static function discard_course_returns(): external_single_structure {
        return new external_single_structure([
            'success' => new external_value(PARAM_BOOL, 'Whether the course was discarded'),
            'message' => new external_value(PARAM_TEXT, 'Reason when it was not'),
        ]);
    }

    /**
     * search_categories parameters.
     *
     * @return external_function_parameters
     */
    public static function search_categories_parameters(): external_function_parameters {
        return new external_function_parameters([
            'query' => new external_value(PARAM_RAW, 'Search text', VALUE_DEFAULT, ''),
        ]);
    }

    /**
     * Searches viewable course categories by text, for the importer autocomplete.
     *
     * Server-side search so the selector scales with any number of categories.
     *
     * @param  string $query Search text.
     * @return array
     */
    public static function search_categories(string $query = ''): array {
        global $DB;

        $params = self::validate_parameters(self::search_categories_parameters(), ['query' => $query]);
        $context = context_system::instance();
        self::validate_context($context);
        require_capability('local/tresipuntimportgc:import', $context);

        // Búsqueda en BD con límite: escala con cualquier número de categorías.
        $search = trim($params['query']);
        $select = 'visible = 1';
        $sqlparams = [];
        if ($search !== '') {
            $select .= ' AND ' . $DB->sql_like('name', ':q', false);
            $sqlparams['q'] = '%' . $DB->sql_like_escape($search) . '%';
        }
        $records = $DB->get_records_select('course_categories', $select, $sqlparams,
            'name ASC', 'id, name', 0, 50);

        $results = [];
        foreach ($records as $record) {
            $category = core_course_category::get($record->id, IGNORE_MISSING);
            // Only categories the user can actually import into: offering one
            // where they cannot create courses would fail on submit.
            if ($category && core_course_category::can_view_category($category)
                    && has_capability('moodle/course:create', \context_coursecat::instance($record->id))) {
                $results[] = ['id' => (int) $record->id, 'name' => $category->get_nested_name(false)];
            }
        }
        return $results;
    }

    /**
     * search_categories returns.
     *
     * @return external_multiple_structure
     */
    public static function search_categories_returns(): external_multiple_structure {
        return new external_multiple_structure(new external_single_structure([
            'id' => new external_value(PARAM_INT, 'Category id'),
            'name' => new external_value(PARAM_RAW, 'Category full name'),
        ]));
    }
}
