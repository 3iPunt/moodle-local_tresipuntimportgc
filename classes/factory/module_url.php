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
 * @copyright   2021 3iPunt (contacte@tresipunt.com)
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_tresipuntimportgc\factory;

use coding_exception;
use dml_exception;
use local_tresipuntimportgc\responses\error;
use local_tresipuntimportgc\responses\response_module;
use mod_url_generator;

/**
 * Class module_url
 *
 * @package     local_tresipuntimportgc
 * @copyright   2021 3iPunt (contacte@tresipunt.com)
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class module_url extends module {

    /** @var string Mod Name */
    protected $modname = 'url';

    /** @var mod_url_generator Generator */
    protected $generator;

    /** @var string Link */
    protected $link;

    /**
     * constructor.
     *
     * @param string $providersection
     * @param string $title
     * @param string $intro
     * @param bool $visible
     * @param string $link
     * @throws coding_exception
     */
    public function __construct(string $providersection, string $title, string $intro, bool $visible, string $link) {
        parent::__construct('mod_url', $providersection, $title, $intro, $visible);
        $this->link = $link;
    }

    /**
     * Create.
     *
     * @param int $courseid
     * @return response_module
     * @throws dml_exception
     */
    public function create(int $courseid): response_module {
        $course = get_course($courseid);
        $record = [
            'course' => $course,
            'name' => $this->title,
            'externalurl' => $this->link,
            'introeditor' => $this->intro_editor(),
            'files' => file_get_unused_draft_itemid(),
        ];
        $options = ['section' => $this->get_section($courseid), 'visible' => $this->visible, 'showdescription' => true];
        $res = $this->generator->create_instance($record, $options);
        if (isset($res)) {
            return new response_module(true, $this, null);
        }
        return new response_module(false, null, new error('15000', 'MODULE_NOT_CREATED'));
    }


}
