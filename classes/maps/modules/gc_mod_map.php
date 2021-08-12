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

global $CFG;
require_once($CFG->dirroot . '/local/tresipuntimportgc/classes/maps/materials/gc_mat_map.php');

/**
 * Class gc_mod_map
 *
 * @package     local_tresipuntimportgc
 * @copyright   2021 Tresipunt
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class gc_mod_map {

    public const GC_MODS = [
        'courseWork' => [
            'ASSIGNMENT' => gc_mod_coursework_assignment_map::class,
            'SHORT_ANSWER_QUESTION' => gc_mod_coursework_shortq_map::class,
            'MULTIPLE_CHOICE_QUESTION' => gc_mod_coursework_multipleq_map::class
        ],
        'courseWorkMaterials' => gc_mod_courseworkmaterials_map::class,
        'announcements' => gc_mod_announcements_map::class
    ];


   /* public const GC_MATERIALS = [
        'driveFile' => gc_material_drivefile_resource_map::class,
        //'form' => gc_material_form_quiz_map::class,
        'link' => gc_material_link_url_map::class,
        //'youtubeVideo' => gc_material_link_url_map::class
    ];*/

    /**
     * Get Description Rich.
     *
     * @param string $desc
     * @param string[][][] $materials
     * @return string
     */
    static public function get_desc_rich(string $desc, array $materials = []): string {
        $html = $desc;
        /* TODO IMPORTANT The initial purpose of this plugin is to invite the user to DELETE their Classroom account
             AND ALL CONTENT from Drive related to the course, WE CANNOT LINK ANYTHING from Google in Moodle
            (it makes no sense), we have to import everything into Moodle no matter what,
            STUDENTS WANT PRIVACY!!!! */
        foreach ($materials as $mat) {
            // array_key_first() Not working for version < 7.3 https://www.php.net/manual/es/function.array-key-first.php
            //$key = array_key_first($mat);
            /* TODO get materials and update to Moodle filestorage. In description only media files, not docs.
                Where put materials files?? Only assign allows additional files of any type */
            $key = array_key_first_compatible($mat);
            if (isset(gc_mat_map::GC_MATS[$key])) {
                $class = gc_mat_map::GC_MATS[$key];
                if (!empty($class)) {
                    /** @var gc_mat_map $item */
                    $item = new $class;
                    $html .= $item->get_render($mat[$key]);
                } else {
                    // TODO change mtrace() to print_trace()
                    mtrace('    -- ERROR: GET_MATERIAL_CLASS: ' . json_encode($mat));
                }
            }/* else {
                mtrace('    -- ERROR: GET_MATERIAL_KEY: ' . json_encode($mat));
            }*/
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
