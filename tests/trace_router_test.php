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

use local_tresipuntimportgc\local\logger;
use local_tresipuntimportgc\local\trace_router;

/**
 * Tests of the trace router and the persistent logger.
 *
 * @package    local_tresipuntimportgc
 * @copyright  2026 3iPunt (contacte@tresipunt.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers     \local_tresipuntimportgc\local\trace_router
 * @covers     \local_tresipuntimportgc\local\logger
 */
final class trace_router_test extends \advanced_testcase {

    /**
     * Traces are persisted with the legacy types collapsed into three levels.
     */
    public function test_trace_levels_are_collapsed(): void {
        $this->resetAfterTest();
        // In CLI (also under PHPUnit) every trace goes to the task log too.
        $this->expectOutputRegex('/ERROR when retrieving/');
        $generator = $this->getDataGenerator()->get_plugin_generator('local_tresipuntimportgc');
        $user = $this->getDataGenerator()->create_user();
        $course = $generator->create_import_course(['userid' => $user->id]);

        trace_router::set_logger(logger::for_course($course));
        try {
            trace_router::trace('recoverycourse', 'success');
            trace_router::trace('teacherfoldererrorcreated', 'warning');
            trace_router::trace('recoverycourseerror', 'danger', 'boom');
            trace_router::trace('importingfiles', 'light');
        } finally {
            trace_router::set_logger(null);
        }

        $logs = $course->get_logs();
        $this->assertCount(4, $logs);
        $this->assertSame(['info', 'warning', 'error', 'info'], array_map(static function ($log) {
            return $log->get('level');
        }, $logs));
        // The message is resolved from the string id, with its parameter.
        $this->assertStringContainsString('boom', $logs[2]->get('message'));
    }

    /**
     * Without a registered logger nothing is persisted (and nothing fails).
     */
    public function test_trace_without_logger_is_ignored(): void {
        global $DB;
        $this->resetAfterTest();
        // The trace still reaches the CLI task log, just not the database.
        $this->expectOutputRegex('/Importing files/');

        trace_router::set_logger(null);
        trace_router::trace('importingfiles', 'info');
        $this->assertSame(0, $DB->count_records('local_tresipuntimportgc_log'));
    }

    /**
     * Every trace of a logger is bound to its import course.
     */
    public function test_logger_binds_traces_to_course(): void {
        $this->resetAfterTest();
        $generator = $this->getDataGenerator()->get_plugin_generator('local_tresipuntimportgc');
        $user = $this->getDataGenerator()->create_user();
        $first = $generator->create_import_course(['userid' => $user->id]);
        $second = $generator->create_import_course(['userid' => $user->id]);

        logger::for_course($first)->info('for the first course');
        logger::for_course($second)->error('for the second course');

        $this->assertCount(1, $first->get_logs());
        $this->assertSame('for the first course', $first->get_logs()[0]->get('message'));
        $this->assertCount(1, $second->get_logs());
        $this->assertSame('error', $second->get_logs()[0]->get('level'));
    }
}
