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

use Throwable;
use local_tresipuntimportgc\factory\course;
use local_tresipuntimportgc\factory\folder;
use local_tresipuntimportgc\factory\module;
use local_tresipuntimportgc\factory\section;
use local_tresipuntimportgc\local\run_config;
use local_tresipuntimportgc\maps\modules\gc_mod_map;
use local_tresipuntimportgc\providers\provider;

/**
 * Class Google Classroom Map.
 *
 * @package     local_tresipuntimportgc
 * @copyright   2021 3iPunt (contacte@tresipunt.com)
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
     * @param  string[] $module
     * @param  provider $provider
     * @param  string $type
     * @return module|module[]|null
     */
    public static function module(array $module, provider $provider, string $type = '') {
        // Content aimed at specific students (E10.9, §6.9): not imported by
        // default; with the setting on they are imported hidden and with a note
        // for the teacher. It applies to the three types.
        $individual = isset($module['assigneeMode']) && $module['assigneeMode'] !== 'ALL_STUDENTS';
        if ($individual) {
            if ((int) run_config::get('importindividual', 0) !== 1) {
                return null;
            }
            $module['state'] = 'DRAFT'; // Hidden from students.
            $note = get_string('individual_note', 'local_tresipuntimportgc');
            $module['description'] = $note . (isset($module['description']) && $module['description'] !== ''
                ? "\n\n" . $module['description'] : '');
        }
        if (isset(gc_mod_map::GC_MODS[$type])) {
            $modtypes = gc_mod_map::GC_MODS[$type];
            $class = is_array($modtypes) ? ($modtypes[$module['workType']] ?? null) : $modtypes;
            if (!empty($class)) {
                try {
                    $modmap = new $class();
                    return $modmap->get_mod($module, $provider);
                } catch (Throwable $e) {
                    // Robustness (§6.5): an Error while transforming one module must not
                    // bring down the import of the rest.
                    mtrace('    -- ERROR: GET_MODULE: ' . $module['id'] . ' - ' . $e->getMessage());
                    return null;
                }
            }
        } else {
            mtrace('    -- ERROR: GET_MODULE_TYPE: ' . $module['id'] . ' - ' . $type);
        }
        return null;
    }

    /**
     * Modules.
     *
     * @param array $modules
     * @param provider $provider
     * @param string $type
     * @return module[]
     */
    public static function modules(array $modules, provider $provider, string $type = ''): array {
        // Stable order by creation date (§6.8): the API returns them with no
        // guaranteed order. Sorted within each type; the types are grouped
        // later when the provider concatenates them.
        usort($modules, static function ($a, $b) {
            return strcmp($a['creationTime'] ?? '', $b['creationTime'] ?? '');
        });
        $data = [];
        foreach ($modules as $module) {
            $m = self::module($module, $provider, $type);
            // One item can map to several modules (combined materials, E10.11).
            if (is_array($m)) {
                foreach ($m as $sub) {
                    $data[] = $sub;
                }
            } else {
                $data[] = $m;
            }
        }
        return $data;
    }
}
