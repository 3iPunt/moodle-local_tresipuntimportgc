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

namespace local_tresipuntimportgc\maps;


use local_tresipuntimportgc\factory\course;
use local_tresipuntimportgc\factory\folder;
use local_tresipuntimportgc\factory\module;
use local_tresipuntimportgc\factory\section;

defined('MOODLE_INTERNAL') || die();

/**
 * Class gclassroom_map
 *
 * @package     local_tresipuntimportgc
 * @copyright   2021 Tresipunt
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class map {

    /**
     * Course.
     *
     * @param object $course
     * @return course
     */
    abstract static public function course(object $course): course;

    /**
     * Courses.
     *
     * @param array $courses
     * @return course[]
     */
    abstract static public function courses(array $courses): array;

    /**
     * Teacher Folder.
     *
     * @param string[] $folder
     * @return folder|null
     */
    abstract static public function teacher_folder(array $folder): ?folder;

    /**
     * Section.
     *
     * @param string[] $section
     * @return section|null
     */
    abstract static public function section(array $section): ?section;

    /**
     * Sections.
     *
     * @param array $sections
     * @return section[]
     */
    abstract static public function sections(array $sections): array;

    /**
     * Module.
     *
     * @param array $module
     * @param string $type
     * @return module
     */
    abstract static public function module(array $module, string $type = ''): ?module;

    /**
     * Modules.
     *
     * @param array $modules
     * @param string $type
     * @return module[]
     */
    abstract static public function modules(array $modules, string $type = ''): array;

}
