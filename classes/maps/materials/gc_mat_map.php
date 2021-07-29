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

use renderer_base;

defined('MOODLE_INTERNAL') || die();

/**
 * Class gc_mod_map
 *
 * @package     local_tresipuntimportgc
 * @copyright   2021 Tresipunt
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class gc_mat_map {

    const GC_LOGICAL = '';

    const GC_MATS = [
        'youtubeVideo' => gc_mat_youtube_map::class,
        'link' => gc_mat_link_map::class,
        'driveFile' => gc_mat_drivefile_map::class,
        'form' => gc_mat_form_map::class
    ];

    /**
     * Get Renderer.
     *
     * @return renderer_base
     */
    public function get_renderer(): renderer_base {
        global $PAGE;
        return $PAGE->get_renderer('local_tresipuntimportgc');
    }

    /**
     * Get Render.
     *
     * @param $mat
     * @return string
     */
    abstract public function get_render($mat): string;

}
