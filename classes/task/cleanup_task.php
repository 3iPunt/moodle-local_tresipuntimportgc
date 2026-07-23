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
 * Scheduled task: purges import history older than the retention period.
 *
 * @package    local_tresipuntimportgc
 * @copyright  2026 3iPunt (contacte@tresipunt.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_tresipuntimportgc\task;

use core\task\scheduled_task;

defined('MOODLE_INTERNAL') || die();

/**
 * Deletes import runs (with their courses and traces) older than the
 * configured retention. Moodle courses already created are never touched.
 *
 * @package    local_tresipuntimportgc
 * @copyright  2026 3iPunt (contacte@tresipunt.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class cleanup_task extends scheduled_task {

    /**
     * Task name shown in the scheduled tasks admin page.
     *
     * @return string
     */
    public function get_name(): string {
        return get_string('cleanuptask', 'local_tresipuntimportgc');
    }

    /**
     * Purges runs older than the retention period (0 = keep forever).
     *
     * @return void
     */
    public function execute(): void {
        global $DB;

        $days = (int) get_config('local_tresipuntimportgc', 'logretention');
        if ($days <= 0) {
            mtrace('local_tresipuntimportgc: retention disabled (0), nothing to purge.');
            return;
        }
        $cutoff = time() - $days * DAYSECS;
        $imports = $DB->get_fieldset_select('local_tresipuntimportgc_import', 'id',
            'timecreated < :cutoff', ['cutoff' => $cutoff]);
        if ($imports === []) {
            mtrace('local_tresipuntimportgc: no import runs older than ' . $days . ' days.');
            return;
        }
        [$insql, $params] = $DB->get_in_or_equal($imports, SQL_PARAMS_NAMED);
        $courses = $DB->get_fieldset_select('local_tresipuntimportgc_course', 'id',
            "importid $insql", $params);
        if ($courses !== []) {
            [$coursesql, $courseparams] = $DB->get_in_or_equal($courses, SQL_PARAMS_NAMED);
            $DB->delete_records_select('local_tresipuntimportgc_log', "importcourseid $coursesql", $courseparams);
        }
        $DB->delete_records_select('local_tresipuntimportgc_course', "importid $insql", $params);
        $DB->delete_records_select('local_tresipuntimportgc_import', "id $insql", $params);
        mtrace('local_tresipuntimportgc: purged ' . count($imports) . ' import runs ('
            . count($courses) . ' courses) older than ' . $days . ' days.');
    }
}
