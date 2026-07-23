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
 * A Google Classroom class was imported as a Moodle course.
 *
 * @package    local_tresipuntimportgc
 * @copyright  2026 3iPunt (contacte@tresipunt.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_tresipuntimportgc\event;

use core\event\base;
use local_tresipuntimportgc\models\import_course;
use moodle_url;

defined('MOODLE_INTERNAL') || die();

/**
 * Triggered by the import task when a course finishes importing successfully.
 *
 * @package    local_tresipuntimportgc
 * @copyright  2026 3iPunt (contacte@tresipunt.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class gc_course_imported extends base {

    /**
     * Init method.
     *
     * @return void
     */
    protected function init() {
        $this->data['crud'] = 'c';
        $this->data['objecttable'] = 'local_tresipuntimportgc_course';
        $this->data['edulevel'] = self::LEVEL_OTHER;
    }

    /**
     * Builds the event from an import course record.
     *
     * @param  import_course $course   Import course (already successful).
     * @param  int           $courseid Created Moodle course id.
     * @return self
     */
    public static function create_from_course(import_course $course, int $courseid): self {
        /** @var self $event */
        $event = self::create([
            'context' => \context_system::instance(),
            'objectid' => (int) $course->get('id'),
            'other' => [
                'importid' => (int) $course->get('importid'),
                'courseid' => $courseid,
                'providerid' => (string) $course->get('providerid'),
            ],
        ]);
        return $event;
    }

    /**
     * Return localised event name.
     *
     * @return string
     */
    public static function get_name(): string {
        return get_string('event_courseimported', 'local_tresipuntimportgc');
    }

    /**
     * Returns description of what happened.
     *
     * @return string
     */
    public function get_description(): string {
        return "The user with id '$this->userid' imported the Google Classroom class " .
            "'{$this->other['providerid']}' as the course with id '{$this->other['courseid']}'.";
    }

    /**
     * Progress page of the import run.
     *
     * @return moodle_url
     */
    public function get_url(): moodle_url {
        return new moodle_url('/local/tresipuntimportgc/progress.php',
            ['id' => $this->other['importid']]);
    }

    /**
     * Custom validation.
     *
     * @return void
     * @throws \coding_exception
     */
    protected function validate_data() {
        parent::validate_data();
        if (!isset($this->other['importid'])) {
            throw new \coding_exception('The \'importid\' value must be set in other.');
        }
        if (!isset($this->other['courseid'])) {
            throw new \coding_exception('The \'courseid\' value must be set in other.');
        }
        if (!isset($this->other['providerid'])) {
            throw new \coding_exception('The \'providerid\' value must be set in other.');
        }
    }
}
