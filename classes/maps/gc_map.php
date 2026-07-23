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

use Exception;
use local_tresipuntimportgc\factory\course;
use local_tresipuntimportgc\factory\folder;
use local_tresipuntimportgc\factory\module;
use local_tresipuntimportgc\factory\module_assign;
use local_tresipuntimportgc\factory\module_folder;
use local_tresipuntimportgc\factory\module_label;
use local_tresipuntimportgc\factory\module_url;
use local_tresipuntimportgc\factory\section;
use local_tresipuntimportgc\maps\modules\gc_mod_map;
use local_tresipuntimportgc\providers\provider;

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
     * Course.
     *
     * @param array $course Course data as a plain associative array.
     * @return course
     */
    public static function course(array $course): course {
        $des = $course['description'] ?? '';
        return new course($course['id'], $des, (object) $course);
    }

    /**
     * Courses.
     *
     * @param array $courses
     * @return course[]
     */
    public static function courses(array $courses): array {
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
    public static function teacher_folder(array $folder): ?folder {
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
    public static function section(array $section): ?section {
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
    public static function sections(array $sections): array {
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
    public static function module(array $module, provider $provider, string $type = ''): ?module {
        $item = null;
        if (isset($module['assigneeMode']) && $module['assigneeMode'] === 'ALL_STUDENTS') {
            if (isset(gc_mod_map::GC_MODS[$type])) {
                $modtypes = gc_mod_map::GC_MODS[$type];
                $class = is_array($modtypes) ? $modtypes[$module['workType']] : $modtypes;
                if (!empty($class)) {
                    try {
                        $modmap = new $class;
                        return $modmap->get_mod($module, $provider);
                    } catch (Exception $e) {
                        mtrace('    -- ERROR: GET_MODULE: ' . $module['id'] . ' - ' . $e->getMessage());
                        return null;
                    }
                }
            } else {
                mtrace('    -- ERROR: GET_MODULE_TYPE: ' . $module['id'] . ' - ' . $type);
            }

        }
        return null;
    }

    public static function materials(array $module): array {
        $materials = [];
        if (isset($module['materials'])) {
            foreach($module['materials'] as $material) {
                $type = array_key_first($material);
                if (isset(gc_mod_map::GC_MATERIALS[$type])) {
                    $class = gc_mod_map::GC_MATERIALS[$type];
                    if (!empty($class)) {
                        try {
                            $modmap = new $class;
                            $material['section'] = $module['topicId'] ?? '';
                            $material['visible'] = $module['state'] === 'PUBLISHED';
                            $materials[] = $modmap->get_mod($material);
                        } catch (Exception $e) {
                            error_log($e->getMessage());
                            mtrace('    -- ERROR: GET_MODULE OF MATERIAL:  - ' . $e->getMessage());
                            return $materials;
                        }
                    }
                } else {
                    mtrace('    -- ERROR: GET_MODULE_TYPE: ' . $module['id'] . ' - ' . $type);
                }
            }
        }
        return $materials;
    }

    /**
     * Modules.
     *
     * @param array $modules
     * @param string $type
     * @param provider|null $provider
     * @return module[]
     */
    public static function modules(array $modules, provider $provider, string $type = ''): array {
        $data = [];
        foreach ($modules as $module) {
            $m = self::module($module, $provider, $type);
            /* TODO if it is an assignment and there are files, they will be downloaded and included within it. Otherwise, the files will become Moodle mods. */
            /*if (($m instanceof module_assign) === false && ($m instanceof module_folder) === false) {
                // if it is an assignment and there are subjects, they will be downloaded and included within it. Otherwise, the subjects will become Moodle mods.
                $materials = self::materials($module);
                foreach ($materials as $material) {
                    $data[] = $material;
                }
                if (($m instanceof module_label) === true) {
                    continue;
                }
            }*/
            $data[] = $m;
        }
        return $data;
    }
}
