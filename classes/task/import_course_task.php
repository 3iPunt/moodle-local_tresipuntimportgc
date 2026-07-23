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
 * Adhoc task: imports one Classroom course of an import run.
 *
 * @package    local_tresipuntimportgc
 * @copyright  2026 3iPunt (contacte@tresipunt.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_tresipuntimportgc\task;

use core\task\adhoc_task;
use local_tresipuntimportgc\local\importer;
use local_tresipuntimportgc\models\import_course;

/**
 * One task per course: failures are persisted as course status + traces by
 * the importer (never rethrown), so cron does not retry automatically —
 * retrying is a manual panel action.
 *
 * @package    local_tresipuntimportgc
 * @copyright  2026 3iPunt (contacte@tresipunt.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class import_course_task extends adhoc_task {

    /**
     * Runs the import of the course referenced in the custom data.
     *
     * @return void
     */
    public function execute(): void {
        global $CFG;

        $data = $this->get_custom_data();
        $course = import_course::get_record(['id' => (int) ($data->importcourseid ?? 0)]);
        if ($course === false) {
            mtrace('local_tresipuntimportgc: import course not found, nothing to do.');
            return;
        }
        mtrace('local_tresipuntimportgc: importing course ' . $course->get('fullname')
            . ' (import course id ' . $course->get('id') . ')');
        importer::run_course($course);
    }
}
