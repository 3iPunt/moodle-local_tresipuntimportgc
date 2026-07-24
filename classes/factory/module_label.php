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
 * Class module_label
 *
 * @package     local_tresipuntimportgc
 * @copyright   2021 Tresipunt
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_tresipuntimportgc\factory;

use coding_exception;
use dml_exception;
use html_writer;
use local_tresipuntimportgc\responses\error;
use local_tresipuntimportgc\responses\response_module;
use mod_label_generator;
use moodle_exception;
use stdClass;

defined('MOODLE_INTERNAL') || die;

/**
 * Class module_label
 *
 * @package     local_tresipuntimportgc
 * @copyright   2021 Tresipunt
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class module_label extends module {

    /** @var string Mod Name */
    protected $modname = 'label';

    /** @var mod_label_generator Generator */
    protected $generator;

    /** @var array $materials */
    protected $material;

    /**
     * constructor.
     *
     * @param string $providersection
     * @param string $title
     * @param string $intro
     * @param bool $visible
     * @param array $material
     * @throws coding_exception
     */
    public function __construct(string $providersection, string $title, string $intro, bool $visible, array $material) {
        parent::__construct('mod_label', $providersection, $title, $intro, $visible);
        $this->material = $material;
    }

    /**
     * Create.
     *
     * @param int $course_id
     * @return response_module
     * @throws coding_exception
     * @throws dml_exception
     * @throws moodle_exception
     */
    public function create(int $course_id): response_module {
        $course = get_course($course_id);
        $record = [
            'course' => $course,
            'name' => $this->title,
            'introeditor' => $this->intro_editor(),
            'files' => file_get_unused_draft_itemid(),
        ];
        $options = [
            'section' => $this->get_section($course_id),
            'visible' => $this->visible,
            'showdescription' => true
        ];
        if (count($this->material) > 0 && array_key_first($this->material) === 'form') {
            $record = $this->add_form($record);
        }
        $res = $this->generator->create_instance($record, $options);
        if (isset($res)) {
            return new response_module(true, $this, null);
        }
        return new response_module(false, null, new error('15000', 'MODULE_NOT_CREATED'));
    }

    /**
     * @param array $res
     * @return array
     * @throws coding_exception
     */
    private function add_form(array $res): array {
        $res['intro'] = html_writer::tag('h4', $this->title, ['class' => 'card-title']);
        $res['intro'] .= html_writer::tag('h6', get_string('form', 'local_tresipuntimportgc'), ['class' => 'card-subtitle mb-2 text-muted']);
        $res['intro'] .= html_writer::tag('iframe', get_string('loading'), [
            'src' => $this->material['form']['formUrl'],
            'width' => '640',
            'height' => '378',
            'frameborder' => '0',
            'marginheight' => '0',
            'marginwidth' => '0',
        ]);
        return $res;
    }
}
