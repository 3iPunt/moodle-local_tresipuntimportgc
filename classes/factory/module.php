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
 * Class module
 *
 * @package     local_tresipuntimportgc
 * @copyright   2021 Tresipunt
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_tresipuntimportgc\factory;

use coding_exception;
use dml_exception;
use local_tresipuntimportgc\responses\response_module;
use phpunit_util;
use testing_data_generator;

global $CFG;

require_once($CFG->dirroot . '/lib/phpunit/classes/util.php');

defined('MOODLE_INTERNAL') || die;

/**
 * Class module
 *
 * @package     local_tresipuntimportgc
 * @copyright   2021 Tresipunt
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class module  {

    /** @var string Mod Name */
    protected $modname = '';

    /** @var testing_data_generator Generator */
    protected $generator;

    /** @var string Provider Section */
    protected $provider_section;

    /** @var string Title */
    protected $title;

    /** @var string Intro */
    protected $intro;

    /** @var bool Visible */
    protected $visible;

    /**
     * constructor.
     *
     * @param string $component
     * @param string $providersection
     * @param string $title
     * @param string $intro
     * @param bool $visible
     * @throws coding_exception
     */
    public function __construct(string $component, string $providersection, string $title, string $intro, bool $visible) {
        $generator = phpunit_util::get_data_generator();
        $this->generator = $generator->get_plugin_generator($component);
        $this->title = $title;
        $this->intro = $intro;
        $this->visible = $visible;
        $this->provider_section = $providersection;
    }

    /**
     * Get Title.
     *
     * @return string
     */
    public function get_title(): string {
        return $this->title;
    }

    /**
     * Get Modname.
     *
     * @return string
     */
    public function get_modname(): string {
        return $this->modname;
    }

    /**
     * Get Section.
     *
     * @param int $course_id
     * @return int
     * @throws dml_exception
     */
    public function get_section(int $course_id): int {
        if ($this->provider_section === '') {
            return 0;
        } else {
            return section::get_section($course_id, $this->provider_section);
        }
    }

    /**
     * Create.
     *
     * @param int $course_id
     * @return response_module
     */
    abstract public function create(int $course_id): response_module;

    /**
     * Builds a Moodle availability restriction «available from» the Classroom
     * scheduled publication time, if present (E10.5).
     *
     * @param  array $module The Classroom module.
     * @return string|null Availability JSON, or null if there is no scheduledTime.
     */
    protected static function scheduled_availability(array $module): ?string {
        if (empty($module['scheduledTime'])) {
            return null;
        }
        $timestamp = strtotime($module['scheduledTime']);
        if (!$timestamp) {
            return null;
        }
        return json_encode([
            'op' => '&',
            'c' => [['type' => 'date', 'd' => '>=', 't' => $timestamp]],
            'showc' => [true],
        ]);
    }

}
