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
 * @copyright   2021 3iPunt (contacte@tresipunt.com)
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_tresipuntimportgc\factory;

use dml_exception;
use local_tresipuntimportgc\responses\error;
use local_tresipuntimportgc\responses\response_section;
use moodle_exception;
use stdClass;

// The guard goes BEFORE the global state change: this file has side
// effects (require_once), so it does need it.
defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/lib/phpunit/classes/util.php');

/**
 * Class section
 *
 * @package     local_tresipuntimportgc
 * @copyright   2021 3iPunt (contacte@tresipunt.com)
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class section {
    /** @var stdClass Name */
    protected $name;

    /** @var stdClass Provider ID */
    protected $providerid;

    /**
     * @var array<string,int> Map of topicId to section number of the running
     * import. Avoids using the visible `summary` as a key (§6.3). Reset per
     * course with reset_map().
     */
    private static $sectionmap = [];

    /**
     * Resets the topicId to section map. Call it when a course starts.
     *
     * @return void
     */
    public static function reset_map(): void {
        self::$sectionmap = [];
    }

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
    public static function get_section(int $courseid, string $providerid): int {
        if (!isset(self::$sectionmap[$providerid])) {
            mtrace('    -- ERROR: SECTION_NOT_FOUND: ' . $providerid);
            return 0;
        }
        return self::$sectionmap[$providerid];
    }

    /**
     * Create.
     *
     * @param int $courseid
     * @return response_section
     */
    public function create(int $courseid): response_section {
        try {
            $newsection = course_create_section($courseid, 1000);
            // The topic name goes in the name; the topicId is NO longer stored in the
            // visible summary (§6.3): the map lives in $sectionmap.
            course_update_section($courseid, $newsection, ['name' => $this->name]);
            self::$sectionmap[$this->providerid] = (int) $newsection->section;
            return new response_section(true, $this, null);
        } catch (moodle_exception $e) {
            return new response_section(false, null, new error('16000', $e->getMessage()));
        }
    }
}
