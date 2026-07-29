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
 * Class module_feedback
 *
 * @package     local_tresipuntimportgc
 * @copyright   2026 3iPunt (contacte@tresipunt.com)
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_tresipuntimportgc\factory;

use dml_exception;
use local_tresipuntimportgc\responses\error;
use local_tresipuntimportgc\responses\response_module;
use mod_feedback_generator;
use moodle_exception;

/**
 * Maps a Classroom SHORT_ANSWER_QUESTION to a Moodle Feedback activity with an
 * open-text item carrying the question statement (E10.1).
 *
 * @package     local_tresipuntimportgc
 * @copyright   2026 3iPunt (contacte@tresipunt.com)
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class module_feedback extends module {

    /** @var string Mod Name */
    protected $modname = 'feedback';

    /** @var mod_feedback_generator Generator */
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
     */
    public function __construct(string $providersection, array $module, string $intro, bool $visible) {
        parent::__construct('mod_feedback', $providersection, $module['title'], $intro, $visible);
        $this->module = $module;
    }

    /**
     * Create.
     *
     * @param  int $courseid
     * @return response_module
     * @throws dml_exception|moodle_exception
     */
    public function create(int $courseid): response_module {
        $course = get_course($courseid);
        $record = [
            'course' => $course,
            'name' => $this->title,
            'introeditor' => $this->intro_editor(),
        ];
        if ($availability = self::scheduled_availability($this->module)) {
            $record['availability'] = $availability;
        }
        $options = [
            'section' => $this->get_section($courseid),
            'visible' => $this->visible,
            'showdescription' => false,
        ];
        $res = $this->generator->create_instance($record, $options);
        if (!isset($res)) {
            return new response_module(false, null, new error('19000', 'MODULE_NOT_CREATED'));
        }
        // The question statement (the class asks it) becomes an open-text item.
        $question = trim((string) ($this->module['description'] ?? ''));
        if ($question === '') {
            $question = $this->title;
        }
        $this->generator->create_item_textarea($res, ['name' => $question, 'required' => 1]);
        return new response_module(true, $this, null);
    }
}
