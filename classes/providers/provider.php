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

namespace local_tresipuntimportgc\providers;

use local_tresipuntimportgc\api\response_course;
use local_tresipuntimportgc\api\response_file;
use local_tresipuntimportgc\models\course;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->libdir . '/google/src/Google/autoload.php');
require_once($CFG->libdir . '/google/lib.php');
require_once($CFG->libdir . '/google/src/Google/Service/Drive.php');

abstract class provider {

    /**
     * Get Courses.
     *
     * @return array
     */
    abstract public function get_courses(): array;

    /**
     * Get Course.
     *
     * @param string $id
     * @return response_course
     */
    abstract public function get_course(string $id): response_course;

    /**
     * Get Teacher Folder.
     *
     * @param string $id
     * @return response_file
     */
    abstract public function get_teacher_folder(string $id): response_file;

    /**
     * Get Modules.
     *
     * @param string $id
     * @return response_course
     */
    abstract public function get_modules(string $id): response_course;

}
