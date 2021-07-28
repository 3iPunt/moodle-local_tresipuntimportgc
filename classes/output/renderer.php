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

/**
 * Class renderer
 *
 * @package    local_tresipuntimportgc
 * @copyright  2021 Tresipunt
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace local_tresipuntimportgc\output;

defined('MOODLE_INTERNAL') || die;

use moodle_exception;
use plugin_renderer_base;

/**
 * Class renderer
 *
 * @package     local_tresipuntimportgc
 * @copyright   2021 Tresipunt
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class renderer extends plugin_renderer_base {

    /**
     * Defer to template.
     *
     * @param import_page $page
     *
     * @return string html for the page
     * @throws moodle_exception
     */
    public function render_import_page(import_page $page): string {
        $data = $page->export_for_template($this);
        return parent::render_from_template('local_tresipuntimportgc/import_page', $data);
    }

    /**
     * Defer to template.
     *
     * @param create_page $page
     *
     * @return string html for the page
     * @throws moodle_exception
     */
    public function render_create_page(create_page $page): string {
        $data = $page->export_for_template($this);
        return parent::render_from_template('local_tresipuntimportgc/create_page', $data);
    }

    /**
     * Defer to template.
     *
     * @param gc_desc_youtube_component $component
     *
     * @return string html for the page
     * @throws moodle_exception
     */
    public function render_gc_desc_youtube_component(gc_desc_youtube_component $component): string {
        $data = $component->export_for_template($this);
        return parent::render_from_template('local_tresipuntimportgc/gc/desc_youtube_component', $data);
    }

    /**
     * Defer to template.
     *
     * @param gc_desc_link_component $component
     *
     * @return string html for the page
     * @throws moodle_exception
     */
    public function render_gc_desc_link_component(gc_desc_link_component $component): string {
        $data = $component->export_for_template($this);
        return parent::render_from_template('local_tresipuntimportgc/gc/desc_link_component', $data);
    }

    /**
     * Defer to template.
     *
     * @param gc_desc_drivefile_component $component
     *
     * @return string html for the page
     * @throws moodle_exception
     */
    public function render_gc_desc_drivefile_component(gc_desc_drivefile_component $component): string {
        $data = $component->export_for_template($this);
        return parent::render_from_template('local_tresipuntimportgc/gc/desc_drivefile_component', $data);
    }

    /**
     * Defer to template.
     *
     * @param gc_desc_form_component $component
     *
     * @return string html for the page
     * @throws moodle_exception
     */
    public function render_gc_desc_form_component(gc_desc_form_component $component): string {
        $data = $component->export_for_template($this);
        return parent::render_from_template('local_tresipuntimportgc/gc/desc_form_component', $data);
    }


}
