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

use Google_Service_Classroom_Course;
use local_tresipuntimportgc\factory\course;
use local_tresipuntimportgc\factory\folder;
use local_tresipuntimportgc\factory\module;
use local_tresipuntimportgc\factory\section;
use local_tresipuntimportgc\maps\modules\gc_mod_map;

defined('MOODLE_INTERNAL') || die();

/**
 * Class Google Classroom Map.
 *
 * @package     local_tresipuntimportgc
 * @copyright   2021 Tresipunt
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class gc_map extends map {

    /**
     * Courses.
     *
     * @param object $course
     * @return course
     */
    static public function course(object $course): course {
        /** @var Google_Service_Classroom_Course $gcourse */
        $gcourse = $course;
        $des = $gcourse->getDescription() ? $gcourse->getDescription() : '';
        return new course($gcourse->getId(), $des, $gcourse);
    }

    /**
     * Courses.
     *
     * @param array $courses
     * @return course[]
     */
    static public function courses(array $courses): array {
        $data = [];
        foreach ($courses as $course) {
            $c = self::course($course);
            $data[] = $c;
        }
        return $data;
    }

    /**
     * Teacher Folder.
     *
     * @param string[] $folder
     * @return folder|null
     */
    static public function teacher_folder(array $folder): ?folder {
        if (isset($folder['id']) && isset($folder['title']) && isset($folder['alternateLink'])) {
            return new folder($folder['id'], $folder['title'], $folder['alternateLink']);
        } else {
            return null;
        }
    }

    /**
     * Section.
     *
     * @param string[] $section
     * @return section
     */
    static public function section(array $section): ?section {
        if (isset($section['name']) && isset($section['topicId'])) {
            return new section($section['name'], $section['topicId']);
        } else {
            return null;
        }
    }

    /**
     * Sections.
     *
     * @param array $sections
     * @return section[]
     */
    static public function sections(array $sections): array {
        $data = [];
        foreach ($sections as $section) {
            $s = self::section($section);
            $data[] = $s;
        }
        return $data;
    }

    /**
     * Module.
     *
     * @param string[] $module
     * @param string $type
     * @return module
     */
    static public function module(array $module, string $type = ''): ?module {
        $item = null;
        if ($module['assigneeMode'] === 'ALL_STUDENTS') {
            $modtypes = gc_mod_map::GC_MODS[$type];
            $class = is_array($modtypes) ? $modtypes[$module['workType']] : $modtypes;
            if (!empty($class)) {
                try {
                    $modmap = new $class;
                    return $modmap->get_mod($module);
                } catch (\Exception $e) {
                    error_log($e->getMessage());
                    return null;
                }
            }
        }
        return null;
    }

    /**
     * Modules.
     *
     * @param array $modules
     * @param string $type
     * @return module[]
     */
    static public function modules(array $modules, string $type = ''): array {
        $data = [];
        foreach ($modules as $module) {
            $m = self::module($module, $type);
            $data[] = $m;
        }
        return $data;
    }
}
