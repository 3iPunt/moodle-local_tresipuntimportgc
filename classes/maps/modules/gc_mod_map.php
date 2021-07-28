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

namespace local_tresipuntimportgc\maps\modules;

use coding_exception;
use local_tresipuntimportgc\factory\module;
use local_tresipuntimportgc\maps\materials\gc_mat_map;

defined('MOODLE_INTERNAL') || die();

/**
 * Class gc_mod_map
 *
 * @package     local_tresipuntimportgc
 * @copyright   2021 Tresipunt
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class gc_mod_map {

    const GC_MODS = [
        'courseWork' => [
            'ASSIGNMENT' => gc_mod_coursework_assignment_map::class,
            'SHORT_ANSWER_QUESTION' => gc_mod_coursework_shortq_map::class,
            'MULTIPLE_CHOICE_QUESTION' => gc_mod_coursework_multipleq_map::class
        ],
        'courseWorkMaterials' => gc_mod_courseworkmaterials_map::class,
        'announcements' => gc_mod_announcements_map::class
    ];

    /**
     * Get Description Rich.
     *
     * @param string $desc
     * @param string[][][] $materials
     * @return string
     */
    static public function get_desc_rich(string $desc, array $materials = []): string {
        $html = '';
        $html .= $desc;
        foreach ($materials as $mat) {
            $key = array_key_first($mat);
            if (isset(gc_mat_map::GC_MATS[$key])) {
                $class = gc_mat_map::GC_MATS[$key];
                if (!empty($class)) {
                    /** @var gc_mat_map $item */
                    $item = new $class;
                    $html .= $item->get_render($mat[$key]);
                } else {
                    mtrace('    -- ERROR: GET_MATERIAL_CLASS: ' . json_encode($mat));
                }
            } else {
                mtrace('    -- ERROR: GET_MATERIAL_KEY: ' . json_encode($mat));
            }
        }
        return $html;
    }

    /**
     * Get Module.
     *
     * @param $module
     * @return module
     * @throws coding_exception
     */
    abstract public function get_mod($module): module;

}
