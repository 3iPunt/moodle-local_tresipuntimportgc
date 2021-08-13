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
use local_tresipuntimportgc\factory\module_quiz;

defined('MOODLE_INTERNAL') || die();

/**
 * Class gc_mod_coursework_multipleq_map
 *
 * @package     local_tresipuntimportgc
 * @copyright   2021 Tresipunt
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class gc_mod_coursework_multipleq_map extends gc_mod_map  {

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
        $desc = self::get_desc_rich($module['description'], $mats);
        return new module_quiz(
            $section, $module, $desc, $visible
        );
    }

}
