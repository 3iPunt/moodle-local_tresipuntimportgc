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
 * Class section
 *
 * @package     local_tresipuntimportgc
 * @copyright   2021 Tresipunt
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_tresipuntimportgc\factory;

use dml_exception;
use local_tresipuntimportgc\responses\error;
use local_tresipuntimportgc\responses\response_section;
use moodle_exception;
use stdClass;

global $CFG;

require_once($CFG->dirroot . '/lib/phpunit/classes/util.php');

defined('MOODLE_INTERNAL') || die;

/**
 * Class section
 *
 * @package     local_tresipuntimportgc
 * @copyright   2021 Tresipunt
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class section  {

    /** @var stdClass Name */
    protected $name;

    /** @var stdClass Provider ID */
    protected $providerid;

    /**
     * constructor.
     *
     * @param string $name
     * @param string $providerid
     */
    public function __construct(string $name, string $providerid) {
        $this->name = $name;
        $this->providerid = $providerid;
    }

    /**
     * Get Name.
     */
    public function get_name(): string {
        return $this->name;
    }

    /**
     * Get Section.
     *
     * @param int $courseid
     * @param string $providerid
     * @return int
     * @throws dml_exception
     */
    public static function get_section(int $courseid, string $providerid) : int {
        global $DB;
        $sql = "SELECT * 
                FROM {course_sections} cs
                WHERE cs.course = ? 
                AND cs.summary = ?";
        $record = $DB->get_records_sql($sql, [$courseid, $DB->sql_compare_text($providerid)]);
        if (!$record) {
            mtrace('    -- ERROR: SECTION_NOT_FOUND: ' . $providerid);
        }
        return $record ? current($record)->section : 0;
    }

    /**
     * Create.
     *
     * @param int $course_id
     * @return response_section
     */
    public function create(int $course_id): response_section {
        try {
            $newsection = course_create_section($course_id, 1000);
            course_update_section($course_id, $newsection, array('name' => $this->name, 'summary' => $this->providerid));
            return new response_section(true, $this, null);
        } catch (moodle_exception $e) {
            return new response_section(false, null, new error('16000', $e->getMessage()));
        }
    }


}
