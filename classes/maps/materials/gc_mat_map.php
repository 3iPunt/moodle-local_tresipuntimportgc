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

/**
 * Class gc_mod_map
 *
 * @package     local_tresipuntimportgc
 * @copyright   2021 3iPunt (contacte@tresipunt.com)
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class gc_mat_map {

    const GC_LOGICAL = '';

    /*
     * Regla «traer frente a enlazar» (§6.4): los ficheros de Drive se importan
     * al almacenamiento de Moodle desde su propio módulo, no se enlazan aquí;
     * YouTube y los enlaces externos se mantienen como enlace/embed en la
     * descripción (límite legal: no se descargan); el formulario se embebe.
     */
    const GC_MATS = [
        'youtubeVideo' => gc_mat_youtube_map::class, // Enlace/embed a YouTube.
        'link' => gc_mat_link_map::class,            // Enlace externo.
        // 'driveFile' NO va aquí: se trae al almacenamiento de Moodle en su módulo.
        'form' => gc_mat_form_map::class,            // Formulario embebido.
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
