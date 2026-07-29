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

// El guard va ANTES del cambio de estado global: este fichero tiene efectos
// secundarios (require_once), así que sí lo necesita.
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
     * @var array<string,int> Correspondencia topicId → número de sección de la
     * importación en curso. Evita usar el `summary` visible como clave (§6.3).
     * Se reinicia por curso con reset_map().
     */
    private static $sectionmap = [];

    /**
     * Reinicia la correspondencia topicId↔sección. Llamar al empezar un curso.
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
    public static function get_section(int $courseid, string $providerid) : int {
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
            // El nombre del tema va en el name; el topicId ya NO se guarda en el
            // summary visible (§6.3): la correspondencia vive en $sectionmap.
            course_update_section($courseid, $newsection, array('name' => $this->name));
            self::$sectionmap[$this->providerid] = (int) $newsection->section;
            return new response_section(true, $this, null);
        } catch (moodle_exception $e) {
            return new response_section(false, null, new error('16000', $e->getMessage()));
        }
    }


}
