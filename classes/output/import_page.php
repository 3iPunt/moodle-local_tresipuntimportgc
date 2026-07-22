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
 * Class import_page
 *
 * @package    local_tresipuntimportgc
 * @copyright  2021 Tresipunt
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_tresipuntimportgc\output;

use coding_exception;
use core_course_category;
use dml_exception;
use local_tresipuntimportgc\providers\provider;
use moodle_exception;
use renderable;
use renderer_base;
use stdClass;
use templatable;

defined('MOODLE_INTERNAL') || die;
global $CFG;
/**
 * Class import_page
 *
 * @package     local_tresipuntimportgc
 * @copyright   2021 Tresipunt
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class import_page implements renderable, templatable {

    /** @var provider Provider */
    private $provider;

    /**
     * import_page constructor.
     *
     * @param provider $provider
     */
    public function __construct(provider $provider) {
        $this->provider = $provider;
    }

    /**
     * Export for template
     *
     * @param renderer_base $output
     * @return stdClass
     * @throws moodle_exception
     * @throws coding_exception
     * @throws dml_exception
     */
    public function export_for_template(renderer_base $output): stdClass {
        $data = new stdClass();
        $data->courses = [];
        $data->categories = $this->get_categories();
        $data->has_error = false;
        $data->error_msg = '';
        $rescourses = $this->provider->get_courses();
        if ($rescourses->success) {
            $gcourses = [];
            foreach ($rescourses->data as $c) {
                $gcourse = $c->providerdata;
                $gcourses[] = $gcourse;
            }
            $data->courses = $gcourses;
        } else {
            $data->has_error = true;
            $data->error_msg = $rescourses->error->to_string();
        }
        $data->allowconfig = get_config('local_tresipuntimportgc', 'allowconfig');
        $importfiles = (int)get_config('local_tresipuntimportgc', 'importfiles');
        $calendarimport = (int)get_config('local_tresipuntimportgc', 'calendarimport');
        $data->importfiles = [
            ['value' => 0, 'text' => get_string('generategdlink', 'local_tresipuntimportgc'), 'selected' => $importfiles === 0 ? 'selected' : ''],
            ['value' => 1, 'text' => get_string('importtoprivatearea', 'local_tresipuntimportgc'), 'selected' => $importfiles === 1 ? 'selected' : ''],
            ['value' => 2, 'text' => get_string('importtonextcloud', 'local_tresipuntimportgc'), 'selected' => $importfiles === 2 ? 'selected' : ''],
            ['value' => 3, 'text' => get_string('notimport', 'local_tresipuntimportgc'), 'selected' => $importfiles === 2 ? 'selected' : '']
        ];
        $data->calendarimport = [
            ['value' => 0, 'text' => get_string('calendargenerategdlink', 'local_tresipuntimportgc'), 'selected' => $calendarimport === 0 ? 'selected' : ''],
            ['value' => 1, 'text' => get_string('calendarimport', 'local_tresipuntimportgc'), 'selected' => $calendarimport === 1 ? 'selected' : '']
        ];
        $data->changeaccount = $this->provider->get_auth_url();
        return $data;
    }

    /**
     * Get Categories for current user.
     *
     * @return array
     */
    protected function get_categories(): array {
        $options = [];
        $options['returnhidden'] = false;
        $categories = core_course_category::get_all($options);
        $cats = [];
        foreach ($categories as $category) {
            if (core_course_category::can_view_category($category)) {
                $cat = [];
                $cat['id'] = $category->id;
                $cat['name'] = $category->name;
                $cats[] = $cat;
            }
        }
        return $cats;
    }
}
