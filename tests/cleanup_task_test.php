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

namespace local_tresipuntimportgc;

use local_tresipuntimportgc\task\cleanup_task;

/**
 * Tests of the retention cleanup task.
 *
 * @package    local_tresipuntimportgc
 * @copyright  2026 3iPunt (contacte@tresipunt.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers     \local_tresipuntimportgc\task\cleanup_task
 */
final class cleanup_task_test extends \advanced_testcase {

    /**
     * Seeds an import (with course and trace) created $days days ago.
     *
     * @param  int $userid User id.
     * @param  int $days   Age of the run in days.
     * @return int Import id.
     */
    private function seed_aged_import(int $userid, int $days): int {
        global $DB;

        $generator = $this->getDataGenerator()->get_plugin_generator('local_tresipuntimportgc');
        $import = $generator->create_import(['userid' => $userid]);
        $course = $generator->create_import_course(
            ['importid' => $import->get('id'), 'status' => 'success']);
        $generator->create_log(['importcourseid' => $course->get('id')]);
        $DB->set_field('local_tresipuntimportgc_import', 'timecreated',
            time() - $days * DAYSECS, ['id' => $import->get('id')]);
        return (int) $import->get('id');
    }

    /**
     * Runs older than the retention are purged in cascade; newer ones stay.
     */
    public function test_purges_only_runs_older_than_retention(): void {
        global $DB;
        $this->resetAfterTest();
        $user = $this->getDataGenerator()->create_user();

        $oldid = $this->seed_aged_import((int) $user->id, 400);
        $recentid = $this->seed_aged_import((int) $user->id, 10);
        set_config('logretention', 365, 'local_tresipuntimportgc');

        $this->expectOutputRegex('/purged 1 import runs/');
        (new cleanup_task())->execute();

        $this->assertFalse($DB->record_exists('local_tresipuntimportgc_import', ['id' => $oldid]));
        $this->assertTrue($DB->record_exists('local_tresipuntimportgc_import', ['id' => $recentid]));
        // Cascade: only the recent run keeps its course and trace.
        $this->assertSame(1, $DB->count_records('local_tresipuntimportgc_course'));
        $this->assertSame(1, $DB->count_records('local_tresipuntimportgc_log'));
    }

    /**
     * Retention 0 means keep the history forever.
     */
    public function test_retention_zero_keeps_everything(): void {
        global $DB;
        $this->resetAfterTest();
        $user = $this->getDataGenerator()->create_user();

        $this->seed_aged_import((int) $user->id, 4000);
        set_config('logretention', 0, 'local_tresipuntimportgc');

        $this->expectOutputRegex('/retention disabled/');
        (new cleanup_task())->execute();
        $this->assertSame(1, $DB->count_records('local_tresipuntimportgc_import'));
    }

    /**
     * Nothing to purge: the task reports it and touches nothing.
     */
    public function test_nothing_older_than_retention(): void {
        global $DB;
        $this->resetAfterTest();
        $user = $this->getDataGenerator()->create_user();

        $this->seed_aged_import((int) $user->id, 10);
        set_config('logretention', 365, 'local_tresipuntimportgc');

        $this->expectOutputRegex('/no import runs older/');
        (new cleanup_task())->execute();
        $this->assertSame(1, $DB->count_records('local_tresipuntimportgc_import'));
    }
}
