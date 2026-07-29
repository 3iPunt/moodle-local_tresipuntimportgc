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
use local_tresipuntimportgc\providers\provider;

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
     * @param array $course
     * @return course
     */
    abstract public static function course(array $course): course;

    /**
     * Courses.
     *
     * @param array $courses
     * @return course[]
     */
    abstract public static function courses(array $courses): array;

    /**
     * Teacher Folder.
     *
     * @param string[] $folder
     * @return folder|null
     */
    abstract public static function teacher_folder(array $folder): ?folder;

    /**
     * Section.
     *
     * @param string[] $section
     * @return section|null
     */
    abstract public static function section(array $section): ?section;

    /**
     * Sections.
     *
     * @param array $sections
     * @return section[]
     */
    abstract public static function sections(array $sections): array;

    /**
     * Module.
     *
     * Returns a single module, an array of modules (one Classroom item that
     * maps to several Moodle activities, E10.11) or null.
     *
     * @param  array $module
     * @param  provider $provider
     * @param  string $type
     * @return module|module[]|null
     */
    abstract public static function module(array $module, provider $provider, string $type = '');

    /**
     * Modules.
     *
     * @param array $modules
     * @param provider $provider
     * @param string $type
     * @return module[]
     */
    abstract public static function modules(array $modules, provider $provider, string $type = ''): array;

}
