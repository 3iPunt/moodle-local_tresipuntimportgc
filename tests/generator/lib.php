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
 * Data generator.
 *
 * @package    local_tresipuntimportgc
 * @copyright  2026 3iPunt (contacte@tresipunt.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use local_tresipuntimportgc\models\import;
use local_tresipuntimportgc\models\import_course;
use local_tresipuntimportgc\models\import_log;

/**
 * Seeds import runs, import courses and traces without touching Google.
 *
 * @package    local_tresipuntimportgc
 * @copyright  2026 3iPunt (contacte@tresipunt.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class local_tresipuntimportgc_generator extends component_generator_base {

    /**
     * Creates an import run.
     *
     * @param  array|stdClass $record userid (required), googleaccount.
     * @return import
     */
    public function create_import($record = []): import {
        $record = (array) $record;
        if (empty($record['userid'])) {
            throw new coding_exception('create_import requires a userid');
        }
        $import = new import(0, (object) [
            'userid' => (int) $record['userid'],
            'googleaccount' => $record['googleaccount'] ?? 'teacher@example.com',
        ]);
        $import->create();
        return $import;
    }

    /**
     * Creates a course inside an import run.
     *
     * If no importid is given, the latest run of the given userid is reused
     * (or created). Terminal statuses get coherent timestamps.
     *
     * @param  array|stdClass $record importid or userid, fullname, shortname,
     *                                categoryid, status, courseid...
     * @return import_course
     */
    public function create_import_course($record = []): import_course {
        $record = (array) $record;
        if (empty($record['importid'])) {
            if (empty($record['userid'])) {
                throw new coding_exception('create_import_course requires importid or userid');
            }
            $imports = import::get_records(['userid' => (int) $record['userid']], 'id', 'DESC', 0, 1);
            $import = $imports ? reset($imports) : $this->create_import($record);
            $record['importid'] = $import->get('id');
        }
        $status = $record['status'] ?? import_course::STATUS_PENDING;
        $started = in_array($status, [import_course::STATUS_RUNNING, import_course::STATUS_SUCCESS,
            import_course::STATUS_ERROR], true) ? time() - 60 : null;
        $finished = in_array($status, [import_course::STATUS_SUCCESS, import_course::STATUS_ERROR,
            import_course::STATUS_DISCARDED], true) ? time() : null;
        $course = new import_course(0, (object) [
            'importid' => (int) $record['importid'],
            'providerid' => $record['providerid'] ?? 'gc-' . random_string(8),
            'fullname' => $record['fullname'] ?? 'Test class',
            'shortname' => $record['shortname'] ?? 'testclass_' . random_string(5),
            'categoryid' => (int) ($record['categoryid'] ?? 1),
            'visible' => (int) ($record['visible'] ?? 1),
            'importfiles' => (int) ($record['importfiles'] ?? 0),
            'calendarimport' => (int) ($record['calendarimport'] ?? 0),
            'status' => $status,
            'courseid' => isset($record['courseid']) ? (int) $record['courseid'] : null,
            'timestarted' => $record['timestarted'] ?? $started,
            'timefinished' => $record['timefinished'] ?? $finished,
        ]);
        $course->create();
        return $course;
    }

    /**
     * Creates a trace line of an import course.
     *
     * The course can be referenced by importcourseid or by its shortname
     * (useful from Behat tables).
     *
     * @param  array|stdClass $record importcourseid or shortname, level, message.
     * @return import_log
     */
    public function create_log($record = []): import_log {
        $record = (array) $record;
        if (empty($record['importcourseid']) && !empty($record['shortname'])) {
            $courses = import_course::get_records(['shortname' => $record['shortname']]);
            if ($courses) {
                $record['importcourseid'] = reset($courses)->get('id');
            }
        }
        if (empty($record['importcourseid'])) {
            throw new coding_exception('create_log requires an importcourseid or a shortname');
        }
        $log = new import_log(0, (object) [
            'importcourseid' => (int) $record['importcourseid'],
            'level' => $record['level'] ?? 'info',
            'message' => $record['message'] ?? 'Test trace',
        ]);
        $log->create();
        return $log;
    }
}
