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
 * Class module_choice
 *
 * @package     local_tresipuntimportgc
 * @copyright   2026 3iPunt (contacte@tresipunt.com)
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_tresipuntimportgc\factory;

use coding_exception;
use dml_exception;
use local_tresipuntimportgc\responses\error;
use local_tresipuntimportgc\responses\response_module;
use mod_choice_generator;

defined('MOODLE_INTERNAL') || die;

/**
 * Maps a Classroom MULTIPLE_CHOICE_QUESTION to a Moodle Choice activity,
 * carrying over the options that already come in the API response (E10.1).
 *
 * @package     local_tresipuntimportgc
 * @copyright   2026 3iPunt (contacte@tresipunt.com)
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class module_choice extends module {

    /** @var string Mod Name */
    protected $modname = 'choice';

    /** @var mod_choice_generator Generator */
    protected $generator;

    /** @var array The Classroom module. */
    protected $module;

    /**
     * constructor.
     *
     * @param string $providersection
     * @param array  $module
     * @param string $intro
     * @param bool   $visible
     * @throws coding_exception
     */
    public function __construct(string $providersection, array $module, string $intro, bool $visible) {
        parent::__construct('mod_choice', $providersection, $module['title'], $intro, $visible);
        $this->module = $module;
    }

    /**
     * Create.
     *
     * @param  int $course_id
     * @return response_module
     * @throws dml_exception
     */
    public function create(int $course_id): response_module {
        $course = get_course($course_id);
        $choices = $this->module['multipleChoiceQuestion']['choices'] ?? [];
        $record = [
            'course' => $course,
            'name' => $this->title,
            'intro' => $this->intro,
            'introformat' => FORMAT_HTML,
        ];
        if (!empty($choices)) {
            $record['option'] = array_values($choices);
        }
        if ($availability = self::scheduled_availability($this->module)) {
            $record['availability'] = $availability;
        }
        $options = [
            'section' => $this->get_section($course_id),
            'visible' => $this->visible,
            'showdescription' => false,
        ];
        $res = $this->generator->create_instance($record, $options);
        if (isset($res)) {
            return new response_module(true, $this, null);
        }
        return new response_module(false, null, new error('18000', 'MODULE_NOT_CREATED'));
    }
}
