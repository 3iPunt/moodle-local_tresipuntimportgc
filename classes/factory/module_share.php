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
 * Class module_share
 *
 * @package     local_tresipuntimportgc
 * @copyright   2021 Tresipunt
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_tresipuntimportgc\factory;

use coding_exception;
use local_tresipuntimportgc\responses\error;
use local_tresipuntimportgc\responses\response_module;
use mod_url_generator;
use moodle_exception;
use stdClass;

defined('MOODLE_INTERNAL') || die;

/**
 * Class module_share
 *
 * @package     local_tresipuntimportgc
 * @copyright   2021 Tresipunt
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class module_share extends module {

    /** @var mod_url_generator Generator */
    protected $generator;

    /**
     * constructor.
     *
     * @param string $text
     * @param bool $visible
     * @throws coding_exception
     */
    public function __construct(string $text, bool $visible) {
        parent::__construct('mod_url', '', $text, $text, $visible);
    }

    /**
     * Create.
     *
     * @param int $course_id
     * @return response_module
     * @throws moodle_exception
     */
    public function create(int $course_id): response_module {
        global $USER;
        $comment_name = preg_replace("/[\r\n|\n|\r]+/", " ", $this->title);
        $name = fullname($USER) . ': ' . substr(trim($comment_name), 0, 200);
        $draftid_editor = file_get_submitted_draft_itemid('introeditor');

        $moduleinfo = new stdClass();
        $moduleinfo->modulename = 'tresipuntshare';
        $moduleinfo->section = 0;
        $moduleinfo->course = $course_id;
        $moduleinfo->teacher = $USER->id;
        $moduleinfo->name = $name;
        $moduleinfo->intro = $this->title;
        $moduleinfo->introeditor = array('text'=> $this->title, 'format'=> FORMAT_HTML, 'itemid'=>$draftid_editor);;
        $moduleinfo->visible = true;
        $cm = create_module($moduleinfo);
        if (isset($cm)) {
            return new response_module(true, $this, null);
        } else {
            return new response_module(false, null, new error('10001', 'MODULE_NOT_CREATED'));
        }
    }


}
