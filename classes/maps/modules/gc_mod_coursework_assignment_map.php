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
use local_tresipuntimportgc\factory\module_assign;
use local_tresipuntimportgc\factory\module_label;
use local_tresipuntimportgc\factory\module_quiz;

defined('MOODLE_INTERNAL') || die();

/**
 * Class gc_mod_coursework_assignment_map
 *
 * @package     local_tresipuntimportgc
 * @copyright   2021 Tresipunt
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class gc_mod_coursework_assignment_map extends gc_mod_map {

    /**
     * Get Module.
     *
     * @param $module
     * @return module
     * @throws coding_exception
     */
    public function get_mod($module): module {
        $visible = $module['state'] === 'PUBLISHED';
        $section = $module['topicId'] ?? '';
        $mats = $module['materials'] ?? [];
        $desc = isset($module['description']) ? self::get_desc_rich($module['description'], $mats) : self::get_desc_rich('', $mats);
        /* TODO what is mapped as module_assign can also be a form with answers (quiz), so it is impossible to isolate the
            modules here. It is only possible to know what kind of module it belongs to by reading "materials", i.e. if it
            contains "form" it is a quiz if it has answers, or feedback if it does not, and is driveFile is a type resource, etc... */
        $firstkey = array_key_first_compatible($module['materials'][0]);
        if ($firstkey === 'form' && count($module['materials']) === 1) {
            // TODO create a quiz using the google form api (not included in Moodle!!!). Provisionally the form is embedded in a tag
            /*return new module_quiz(
                $section, $module['title'], $desc, $visible, reset($mats)
            );*/
            return new module_label(
                $section, $module['title'], $desc, $visible, reset($mats)
            );
        }
        return new module_assign(
            $section, $module['title'], $desc, $visible, $mats
        );
    }

}
