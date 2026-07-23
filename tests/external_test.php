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

use local_tresipuntimportgc\external\import_external;
use local_tresipuntimportgc\models\import_course;

/**
 * Permission matrix and behaviour of the import web services.
 *
 * @package    local_tresipuntimportgc
 * @copyright  2026 3iPunt (contacte@tresipunt.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers     \local_tresipuntimportgc\external\import_external
 */
final class external_test extends \advanced_testcase {

    /**
     * Seeds one import with one course in the given status and returns the course.
     *
     * @param  string $status Course status.
     * @return import_course
     */
    private function seed_course(string $status): import_course {
        $generator = $this->getDataGenerator()->get_plugin_generator('local_tresipuntimportgc');
        $user = $this->getDataGenerator()->create_user();
        return $generator->create_import_course(['userid' => $user->id, 'status' => $status]);
    }

    /**
     * Grants the import capability to a fresh user and logs them in.
     *
     * @return \stdClass The user.
     */
    private function login_user_with_capability(): \stdClass {
        $user = $this->getDataGenerator()->create_user();
        $roleid = $this->getDataGenerator()->create_role();
        assign_capability('local/tresipuntimportgc:import', CAP_ALLOW,
            $roleid, \context_system::instance());
        role_assign($roleid, $user->id, \context_system::instance());
        $this->setUser($user);
        return $user;
    }

    /**
     * A user without the capability cannot poll the status.
     */
    public function test_get_status_requires_capability(): void {
        $this->resetAfterTest();
        $course = $this->seed_course(import_course::STATUS_PENDING);
        $this->setUser($this->getDataGenerator()->create_user());

        $this->expectException(\required_capability_exception::class);
        import_external::get_status((int) $course->get('importid'), 0);
    }

    /**
     * A user without the capability cannot retry nor discard.
     */
    public function test_retry_and_discard_require_capability(): void {
        $this->resetAfterTest();
        $course = $this->seed_course(import_course::STATUS_ERROR);
        $this->setUser($this->getDataGenerator()->create_user());

        try {
            import_external::retry_course((int) $course->get('id'));
            $this->fail('retry_course must require the import capability');
        } catch (\required_capability_exception $e) {
            $this->assertInstanceOf(\required_capability_exception::class, $e);
        }
        $this->expectException(\required_capability_exception::class);
        import_external::discard_course((int) $course->get('id'));
    }

    /**
     * With the capability, get_status returns statuses and incremental traces.
     */
    public function test_get_status_returns_courses_and_traces(): void {
        $this->resetAfterTest();
        $generator = $this->getDataGenerator()->get_plugin_generator('local_tresipuntimportgc');
        $course = $this->seed_course(import_course::STATUS_SUCCESS);
        $first = $generator->create_log(
            ['importcourseid' => $course->get('id'), 'message' => 'one']);
        $generator->create_log(
            ['importcourseid' => $course->get('id'), 'level' => 'error', 'message' => 'two']);
        $this->login_user_with_capability();

        $result = import_external::get_status((int) $course->get('importid'), 0);
        $result = \core_external\external_api::clean_returnvalue(
            import_external::get_status_returns(), $result);
        $this->assertSame('completed', $result['status']);
        $this->assertTrue($result['finished']);
        $this->assertCount(1, $result['courses']);
        $this->assertCount(2, $result['courses'][0]['logs']);

        // Incremental: only traces newer than the first one.
        $result = import_external::get_status((int) $course->get('importid'),
            (int) $first->get('id'));
        $this->assertCount(1, $result['courses'][0]['logs']);
    }

    /**
     * Asking for a run that does not exist fails loudly, not silently.
     */
    public function test_get_status_unknown_import_throws(): void {
        $this->resetAfterTest();
        $this->login_user_with_capability();

        $this->expectException(\dml_missing_record_exception::class);
        import_external::get_status(999999, 0);
    }

    /**
     * Discard works only on pending courses.
     */
    public function test_discard_course_only_pending(): void {
        $this->resetAfterTest();
        $pending = $this->seed_course(import_course::STATUS_PENDING);
        $done = $this->seed_course(import_course::STATUS_SUCCESS);
        $this->login_user_with_capability();

        $result = import_external::discard_course((int) $pending->get('id'));
        $this->assertTrue($result['success']);
        $this->assertSame(import_course::STATUS_DISCARDED,
            (new import_course($pending->get('id')))->get('status'));

        $result = import_external::discard_course((int) $done->get('id'));
        $this->assertFalse($result['success']);
        $this->assertNotEmpty($result['message']);
    }

    /**
     * Retry refuses non-failed courses and requires a Google connection.
     */
    public function test_retry_course_guards(): void {
        $this->resetAfterTest();
        $pending = $this->seed_course(import_course::STATUS_PENDING);
        $failed = $this->seed_course(import_course::STATUS_ERROR);
        $this->login_user_with_capability();

        $result = import_external::retry_course((int) $pending->get('id'));
        $this->assertFalse($result['success']);

        // Failed course, but no Google session token: refused with a reason.
        $result = import_external::retry_course((int) $failed->get('id'));
        $this->assertFalse($result['success']);
        $this->assertSame(get_string('retry_needsconnection', 'local_tresipuntimportgc'),
            $result['message']);
        $this->assertSame(import_course::STATUS_ERROR,
            (new import_course($failed->get('id')))->get('status'));
    }
}
