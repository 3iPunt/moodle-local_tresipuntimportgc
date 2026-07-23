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

use core\task\manager;
use local_tresipuntimportgc\local\importer;
use local_tresipuntimportgc\models\import;
use local_tresipuntimportgc\models\import_course;
use local_tresipuntimportgc\providers\provider;
use local_tresipuntimportgc\task\import_course_task;

/**
 * Tests of the import orchestrator (no network: Google is never reached).
 *
 * @package    local_tresipuntimportgc
 * @copyright  2026 3iPunt (contacte@tresipunt.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers     \local_tresipuntimportgc\local\importer
 */
final class importer_test extends \advanced_testcase {

    /**
     * Builds a provider stub with a connected account and token.
     *
     * @return provider
     */
    private function provider_stub(): provider {
        $provider = $this->createMock(provider::class);
        $provider->method('get_account_email')->willReturn('prof@example.com');
        $provider->method('get_refresh_token')->willReturn('1//refresh-token');
        return $provider;
    }

    /**
     * queue() persists the run, its courses and one adhoc task per course.
     */
    public function test_queue_creates_run_courses_and_tasks(): void {
        $this->resetAfterTest();
        $user = $this->getDataGenerator()->create_user();

        $import = importer::queue((int) $user->id, $this->provider_stub(), [
            ['providerid' => 'gc-1', 'fullname' => 'Bio 1', 'shortname' => 'bio1',
                'categoryid' => 1, 'visible' => 1, 'importfiles' => 1, 'calendarimport' => 2],
            ['providerid' => 'gc-2', 'fullname' => 'Phy 2', 'shortname' => 'phy2',
                'categoryid' => 1, 'visible' => 0],
        ]);

        $this->assertSame('prof@example.com', $import->get('googleaccount'));
        $this->assertSame('1//refresh-token', $import->get_refresh_token());
        $this->assertSame(import::STATUS_QUEUED, $import->get_status());

        $courses = $import->get_courses();
        $this->assertCount(2, $courses);
        $this->assertSame('bio1', $courses[0]->get('shortname'));
        $this->assertSame(1, (int) $courses[0]->get('importfiles'));
        $this->assertSame(2, (int) $courses[0]->get('calendarimport'));
        $this->assertSame(0, (int) $courses[1]->get('visible'));
        $this->assertSame(import_course::STATUS_PENDING, $courses[0]->get('status'));

        $tasks = manager::get_adhoc_tasks(import_course_task::class);
        $this->assertCount(2, $tasks);
        $queuedids = array_map(static function ($task) {
            return (int) $task->get_custom_data()->importcourseid;
        }, array_values($tasks));
        sort($queuedids);
        $this->assertSame([(int) $courses[0]->get('id'), (int) $courses[1]->get('id')],
            $queuedids);
    }

    /**
     * run_course() ignores courses that are not pending.
     */
    public function test_run_course_ignores_non_pending(): void {
        $this->resetAfterTest();
        $generator = $this->getDataGenerator()->get_plugin_generator('local_tresipuntimportgc');
        $user = $this->getDataGenerator()->create_user();

        $done = $generator->create_import_course(
            ['userid' => $user->id, 'status' => import_course::STATUS_SUCCESS]);
        importer::run_course($done);

        $reloaded = new import_course($done->get('id'));
        $this->assertSame(import_course::STATUS_SUCCESS, $reloaded->get('status'));
        $this->assertCount(0, $reloaded->get_logs());
    }

    /**
     * Without a stored token the course fails with an error trace and the
     * failure is persisted (the adhoc task must not retry).
     */
    public function test_run_course_without_token_marks_error(): void {
        $this->resetAfterTest();
        $generator = $this->getDataGenerator()->get_plugin_generator('local_tresipuntimportgc');
        $user = $this->getDataGenerator()->create_user();

        $course = $generator->create_import_course(['userid' => $user->id]);
        importer::run_course($course);

        $reloaded = new import_course($course->get('id'));
        $this->assertSame(import_course::STATUS_ERROR, $reloaded->get('status'));
        $this->assertNotNull($reloaded->get('timestarted'));
        $this->assertNotNull($reloaded->get('timefinished'));

        $logs = $reloaded->get_logs();
        $this->assertNotEmpty($logs);
        $this->assertSame('error', end($logs)->get('level'));

        $this->assertSame(import::STATUS_ERROR, $reloaded->get_import()->get_status());
    }

    /**
     * The stored token survives while the run has open courses and is wiped
     * when the last one reaches a terminal state.
     */
    public function test_token_wiped_only_when_run_finishes(): void {
        $this->resetAfterTest();
        $generator = $this->getDataGenerator()->get_plugin_generator('local_tresipuntimportgc');
        $user = $this->getDataGenerator()->create_user();

        $import = $generator->create_import(['userid' => $user->id]);
        $import->set_refresh_token('1//token');
        $import->update();
        $first = $generator->create_import_course(['importid' => $import->get('id')]);
        $second = $generator->create_import_course(['importid' => $import->get('id')]);

        $finish = new \ReflectionMethod(importer::class, 'finish_run_if_done');

        $first->mark_running();
        $first->mark_success(99);
        $finish->invoke(null, new import($import->get('id')));
        $this->assertSame('1//token', (new import($import->get('id')))->get_refresh_token(),
            'The token must survive while a course is still open');

        $second->mark_running();
        $second->mark_error();
        $finish->invoke(null, new import($import->get('id')));
        $this->assertNull((new import($import->get('id')))->get_refresh_token(),
            'The token must be wiped when the run finishes');
    }

    /**
     * A discarded course also closes the run for token purposes.
     */
    public function test_token_wiped_when_last_course_discarded(): void {
        $this->resetAfterTest();
        $generator = $this->getDataGenerator()->get_plugin_generator('local_tresipuntimportgc');
        $user = $this->getDataGenerator()->create_user();

        $import = $generator->create_import(['userid' => $user->id]);
        $import->set_refresh_token('1//token');
        $import->update();
        $course = $generator->create_import_course(['importid' => $import->get('id')]);
        $course->mark_discarded();

        $finish = new \ReflectionMethod(importer::class, 'finish_run_if_done');
        $finish->invoke(null, new import($import->get('id')));
        $this->assertNull((new import($import->get('id')))->get_refresh_token());
    }
}
