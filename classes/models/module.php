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

namespace local_tresipuntimportgc\models;

use coding_exception;
use dml_exception;
use local_tresipuntimportgc\api\response_module;
use phpunit_util;
use stdClass;
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

    /** @var stdClass Course */
    protected $course;

    /** @var testing_data_generator Generator */
    protected $generator;

    /**
     * constructor.
     *
     * @param int $course_id
     * @param string $component
     * @throws coding_exception
     * @throws dml_exception
     */
    public function __construct(int $course_id, string $component) {
        $this->course = get_course($course_id);
        $generator = phpunit_util::get_data_generator();
        $this->generator = $generator->get_plugin_generator($component);
    }


}
