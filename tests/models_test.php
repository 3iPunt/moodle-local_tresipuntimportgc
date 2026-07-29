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

use local_tresipuntimportgc\models\import;
use local_tresipuntimportgc\models\import_course;

/**
 * Tests of the import and import_course models.
 *
 * @package    local_tresipuntimportgc
 * @copyright  2026 3iPunt (contacte@tresipunt.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers     \local_tresipuntimportgc\models\import
 * @covers     \local_tresipuntimportgc\models\import_course
 */
final class models_test extends \advanced_testcase {

    /**
     * Cases of the derived run status.
     *
     * @return array[] counts, expected status.
     */
    public static function derive_status_provider(): array {
        return [
            'all pending' => [['pending' => 3], import::STATUS_QUEUED],
            'one running' => [['pending' => 2, 'running' => 1], import::STATUS_RUNNING],
            'pending with finished' => [['pending' => 1, 'success' => 1], import::STATUS_RUNNING],
            'pending with errors' => [['pending' => 1, 'error' => 1], import::STATUS_RUNNING],
            'all success' => [['success' => 2], import::STATUS_COMPLETED],
            'success and discarded' => [['success' => 1, 'discarded' => 1], import::STATUS_COMPLETED],
            'mixed success and error' => [['success' => 1, 'error' => 1], import::STATUS_PARTIAL],
            'all error' => [['error' => 2], import::STATUS_ERROR],
            'error and discarded' => [['error' => 1, 'discarded' => 1], import::STATUS_ERROR],
            'only discarded' => [['discarded' => 2], import::STATUS_COMPLETED],
            'empty run' => [[], import::STATUS_COMPLETED],
        ];
    }

    /**
     * derive_status() maps course counts to the documented run status.
     *
     * @dataProvider derive_status_provider
     * @param array  $counts   Course status counts.
     * @param string $expected Expected run status.
     */
    public function test_derive_status(array $counts, string $expected): void {
        $this->assertSame($expected, import::derive_status($counts));
    }

    /**
     * get_status() derives from the actual courses in database.
     */
    public function test_get_status_from_database(): void {
        $this->resetAfterTest();
        $generator = $this->getDataGenerator()->get_plugin_generator('local_tresipuntimportgc');
        $user = $this->getDataGenerator()->create_user();

        $import = $generator->create_import(['userid' => $user->id]);
        $this->assertSame(import::STATUS_COMPLETED, $import->get_status());

        $generator->create_import_course(['importid' => $import->get('id')]);
        $this->assertSame(import::STATUS_QUEUED, $import->get_status());

        $generator->create_import_course(
            ['importid' => $import->get('id'), 'status' => import_course::STATUS_SUCCESS]);
        $this->assertSame(import::STATUS_RUNNING, $import->get_status());
        $this->assertSame(['pending' => 1, 'success' => 1], $import->get_status_counts());
    }

    /**
     * The refresh token is stored encrypted and recovered decrypted.
     */
    public function test_refresh_token_encrypted_roundtrip(): void {
        $this->resetAfterTest();
        $generator = $this->getDataGenerator()->get_plugin_generator('local_tresipuntimportgc');
        $user = $this->getDataGenerator()->create_user();

        $import = $generator->create_import(['userid' => $user->id]);
        $import->set_refresh_token('1//secret-token');
        $import->update();

        $this->assertNotSame('1//secret-token', $import->get('refreshtoken'));
        $this->assertSame('1//secret-token', $import->get_refresh_token());

        $import->wipe_refresh_token();
        $this->assertNull($import->get_refresh_token());
        $this->assertNull((new import($import->get('id')))->get_refresh_token());
    }

    /**
     * Valid lifecycle: pending -> running -> success; error -> retried.
     */
    public function test_course_status_transitions(): void {
        $this->resetAfterTest();
        $generator = $this->getDataGenerator()->get_plugin_generator('local_tresipuntimportgc');
        $user = $this->getDataGenerator()->create_user();

        $course = $generator->create_import_course(['userid' => $user->id]);
        $this->assertFalse($course->is_terminal());

        $course->mark_running();
        $this->assertNotNull($course->get('timestarted'));

        $course->mark_success(99);
        $this->assertTrue($course->is_terminal());
        $this->assertSame(99, (int) $course->get('courseid'));
        $this->assertNotNull($course->get('timefinished'));

        $failed = $generator->create_import_course(
            ['userid' => $user->id, 'status' => import_course::STATUS_ERROR]);
        $failed->mark_retried();
        $this->assertSame(import_course::STATUS_PENDING, $failed->get('status'));
        $this->assertNull($failed->get('courseid'));
        $this->assertNull($failed->get('timefinished'));
    }

    /**
     * Invalid transitions throw and do not change the status.
     */
    public function test_course_invalid_transition_throws(): void {
        $this->resetAfterTest();
        $generator = $this->getDataGenerator()->get_plugin_generator('local_tresipuntimportgc');
        $user = $this->getDataGenerator()->create_user();

        $done = $generator->create_import_course(
            ['userid' => $user->id, 'status' => import_course::STATUS_SUCCESS]);
        $this->expectException(\coding_exception::class);
        $done->mark_running();
    }

    /**
     * get_logs() returns only traces newer than the given id, in order.
     */
    public function test_get_logs_incremental(): void {
        $this->resetAfterTest();
        $generator = $this->getDataGenerator()->get_plugin_generator('local_tresipuntimportgc');
        $user = $this->getDataGenerator()->create_user();

        $course = $generator->create_import_course(['userid' => $user->id]);
        $first = $generator->create_log(
            ['importcourseid' => $course->get('id'), 'message' => 'one']);
        $generator->create_log(
            ['importcourseid' => $course->get('id'), 'level' => 'error', 'message' => 'two']);

        $this->assertCount(2, $course->get_logs());
        $newer = $course->get_logs((int) $first->get('id'));
        $this->assertCount(1, $newer);
        $this->assertSame('two', $newer[0]->get('message'));
        $this->assertSame('error', $newer[0]->get('level'));
    }
}
