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
use local_tresipuntimportgc\factory\module_label;
use local_tresipuntimportgc\factory\module_resource;
use local_tresipuntimportgc\factory\module_url;
use local_tresipuntimportgc\providers\provider;

defined('MOODLE_INTERNAL') || die();

/**
 * Class gc_mod_courseworkmaterials_map
 *
 * @package     local_tresipuntimportgc
 * @copyright   2021 Tresipunt
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class gc_mod_courseworkmaterials_map extends gc_mod_map  {

    /**
     * Get Module.
     *
     * @param $module
     * @param provider $provider
     * @return module
     * @throws coding_exception
     */
    public function get_mod($module, provider $provider) {
        $visible = $module['state'] === 'PUBLISHED';
        $section = $module['topicId'] ?? '';
        $mats = $module['materials'] ?? [];
        $desc = isset($module['description']) ? self::get_desc_rich($module['description'], $mats) : self::get_desc_rich('', $mats);
        // TODO rethink logic, this is rubbish for understanding how it is supposed to work.
        $firstkey = '';
        if (isset($module['materials'][0])) {
            $firstkey = array_key_first($module['materials'][0]);
        }

        if ($firstkey === 'driveFile' && count($module['materials']) === 1) {
            return new module_resource(
                $section, $module['title'], $desc, $visible, reset($module['materials'])
            );
        }
        if ($firstkey === 'form' && count($module['materials']) === 1) {
            // Formulario adjunto → embed en etiqueta (E10.2); nunca un quiz vacío.
            // La conversión a cuestionario con preguntas es futura (Forms API).
            return new module_label(
                $section, $module['title'], $desc, $visible, reset($module['materials'])
            );
        }
        if ($firstkey === 'link' && count($module['materials']) === 1) {
            return new module_url(
                $section, $module['title'], $desc, $visible, $module['alternateLink']
            );
        }
        if (isset($module['materials']) && count($module['materials']) > 1) {
            // Materiales combinados → un recurso por material (E10.11): los
            // ficheros a Moodle, los enlaces/vídeos como URL, el formulario embebido.
            $mods = [];
            foreach ($module['materials'] as $material) {
                $key = array_key_first($material);
                if ($key === 'driveFile') {
                    $title = $material['driveFile']['driveFile']['title'] ?? $module['title'];
                    $mods[] = new module_resource($section, $title, '', $visible, $material);
                } else if ($key === 'link') {
                    $title = $material['link']['title'] ?? $module['title'];
                    $mods[] = new module_url($section, $title, '', $visible, $material['link']['url'] ?? '');
                } else if ($key === 'youtubeVideo') {
                    $title = $material['youtubeVideo']['title'] ?? $module['title'];
                    $mods[] = new module_url($section, $title, '', $visible,
                        $material['youtubeVideo']['alternateLink'] ?? '');
                } else if ($key === 'form') {
                    $mods[] = new module_label($section, $module['title'], $desc, $visible, $material);
                }
            }
            // Si nada encajó, cae a etiqueta con el conjunto (comportamiento previo).
            return $mods !== [] ? $mods
                : new module_label($section, $module['title'], $desc, $visible, $module['materials']);
        }
        if (!empty($mats)) {
            $mats = reset($mats);
        }
        return new module_label(
            $section, $module['title'], $desc, $visible, $mats
        );
    }

}
