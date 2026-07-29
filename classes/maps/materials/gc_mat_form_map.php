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

namespace local_tresipuntimportgc\maps\materials;

use coding_exception;
use local_tresipuntimportgc\output\gc_desc_form_component;

/**
 * Class gc_mat_form_map
 *
 * @package     local_tresipuntimportgc
 * @copyright   2021 3iPunt (contacte@tresipunt.com)
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class gc_mat_form_map extends gc_mat_map {

    /**
     * Get Render.
     *
     * @param $mat
     * @return string
     * @throws coding_exception
     */
    public function get_render($mat): string {
        $component = new gc_desc_form_component($mat);
        return $this->get_renderer()->render($component);
    }

}
