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

use local_tresipuntimportgc\event\gc_course_discarded;
use local_tresipuntimportgc\event\gc_course_imported;
use local_tresipuntimportgc\event\gc_course_retried;
use local_tresipuntimportgc\models\import_course;

/**
 * Tests of the audit events.
 *
 * @package    local_tresipuntimportgc
 * @copyright  2026 3iPunt (contacte@tresipunt.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers     \local_tresipuntimportgc\event\gc_course_imported
 * @covers     \local_tresipuntimportgc\event\gc_course_retried
 * @covers     \local_tresipuntimportgc\event\gc_course_discarded
 */
final class events_test extends \advanced_testcase {

    /**
     * Seeds one import course.
     *
     * @return import_course
     */
    private function seed_course(): import_course {
        $generator = $this->getDataGenerator()->get_plugin_generator('local_tresipuntimportgc');
        $user = $this->getDataGenerator()->create_user();
        return $generator->create_import_course(
            ['userid' => $user->id, 'providerid' => 'gc-42', 'fullname' => 'Bio 1']);
    }

    /**
     * The imported event carries the class, the course and the run link.
     */
    public function test_course_imported_event(): void {
        $this->resetAfterTest();
        $course = $this->seed_course();

        $sink = $this->redirectEvents();
        gc_course_imported::create_from_course($course, 77)->trigger();
        $events = $sink->get_events();
        $sink->close();

        $this->assertCount(1, $events);
        $event = $events[0];
        $this->assertInstanceOf(gc_course_imported::class, $event);
        $this->assertSame((int) $course->get('id'), (int) $event->objectid);
        $this->assertSame(77, (int) $event->other['courseid']);
        $this->assertSame('gc-42', $event->other['providerid']);
        $this->assertStringContainsString('gc-42', $event->get_description());
        $this->assertStringContainsString('/local/tresipuntimportgc/progress.php',
            $event->get_url()->out(false));
        $this->assertSame((int) $course->get('importid'),
            (int) $event->get_url()->get_param('id'));
        $this->assertNotEmpty($event->get_name());
    }

    /**
     * The retry and discard events point back to their import course.
     */
    public function test_retry_and_discard_events(): void {
        $this->resetAfterTest();
        $course = $this->seed_course();

        $sink = $this->redirectEvents();
        gc_course_retried::create_from_course($course)->trigger();
        gc_course_discarded::create_from_course($course)->trigger();
        $events = $sink->get_events();
        $sink->close();

        $this->assertCount(2, $events);
        $this->assertInstanceOf(gc_course_retried::class, $events[0]);
        $this->assertInstanceOf(gc_course_discarded::class, $events[1]);
        foreach ($events as $event) {
            $this->assertSame((int) $course->get('id'), (int) $event->objectid);
            $this->assertSame('u', $event->crud);
            $this->assertStringContainsString('gc-42', $event->get_description());
        }
    }
}
