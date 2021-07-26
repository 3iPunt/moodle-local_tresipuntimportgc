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
 * Class module_url
 *
 * @package     local_tresipuntimportgc
 * @copyright   2021 Tresipunt
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_tresipuntimportgc\models;


use coding_exception;
use dml_exception;
use local_tresipuntimportgc\api\error;
use local_tresipuntimportgc\api\response_module;
use mod_url_generator;

defined('MOODLE_INTERNAL') || die;

/**
 * Class module_url
 *
 * @package     local_tresipuntimportgc
 * @copyright   2021 Tresipunt
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class module_url extends module {

    /** @var mod_url_generator Generator */
    protected $generator;

    /**
     * constructor.
     *
     * @param int $course_id
     * @throws dml_exception
     * @throws coding_exception
     */
    public function __construct(int $course_id) {
        parent::__construct($course_id, 'mod_url');
    }

    /**
     * Create.
     *
     * @param string $title
     * @param string $intro
     * @param string $link
     * @param bool $visible
     * @return response_module
     */
    public function create(string $title, string $intro, string $link, bool $visible): response_module {
        $record = [
            'course' => $this->course,
            'name' => $title,
            'externalurl' => $link,
            'intro' => $intro,
            'introformat' => FORMAT_HTML,
            'files' => file_get_unused_draft_itemid(),
        ];
        $options = ['section' => 0, 'visible' => $visible, 'showdescription' => true];
        $res = $this->generator->create_instance($record, $options);
        if (isset($res)) {
            return new response_module(true, $this, null);
        } else {
            return new response_module(false, null, new error('10001', 'MODULE_NOT_CREATED'));
        }
    }


}
