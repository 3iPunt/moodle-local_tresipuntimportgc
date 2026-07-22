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
 * Class module_folder
 *
 * @package     local_tresipuntimportgc
 * @copyright   2021 Tresipunt
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_tresipuntimportgc\factory;

use coding_exception;
use context_module;
use dml_exception;
use local_tresipuntimportgc\providers\google;
use local_tresipuntimportgc\responses\error;
use local_tresipuntimportgc\responses\response_module;
use mod_folder_generator;

defined('MOODLE_INTERNAL') || die;

/**
 * Class module_folder
 *
 * @package     local_tresipuntimportgc
 * @copyright   2021 Tresipunt
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class module_folder extends module {

    /** @var string Mod Name */
    protected $modname = 'folder';

    /** @var mod_folder_generator Generator */
    protected $generator;

    /** array material */
    protected $materials;

    /**
     * constructor.
     *
     * @param string $providersection
     * @param string $title
     * @param string $intro
     * @param bool $visible
     * @param array $materials
     * @throws coding_exception
     */
    public function __construct(string $providersection, string $title, string $intro, bool $visible, array $materials) {
        parent::__construct('mod_folder', $providersection, $title, $intro, $visible);
        $this->materials = $materials;
    }

    /**
     * Create.
     *
     * @param int $course_id
     * @return response_module
     * @throws dml_exception
     */
    public function create(int $course_id): response_module {
        $course = get_course($course_id);
        $record = [
            'course' => $course,
            'name' => $this->title,
            'intro' => $this->intro,
            'introformat' => FORMAT_HTML,
            'showexpanded' => true,
            'files' => file_get_unused_draft_itemid()
        ];
        $options = [
            'section' => $this->get_section($course_id),
            'visible' => $this->visible,
            'showdescription' => false
        ];
        $res = $this->generator->create_instance($record, $options);
        if (isset($res)) {
            if (count($this->materials) > 0) {
                $this->add_files($res);
            }
            return new response_module(true, $this, null);
        }

        return new response_module(false, null, new error('12000', 'MODULE_NOT_CREATED'));
    }

    /**
     * Imports the Drive files of the materials into the folder file area.
     *
     * @param  object $res Generator result (with cmid).
     * @return void
     * @throws coding_exception
     */
    private function add_files($res): void {
        global $USER;

        $context = context_module::instance($res->cmid);
        $provider = new google();
        foreach ($this->materials as $material) {
            if (array_key_first_compatible($material) === 'driveFile') {
                local_tresipuntimportgc_import_drive_file(
                    $provider,
                    $material['driveFile']['driveFile']['id'],
                    $context->id,
                    (int) $USER->id,
                    'mod_folder',
                    'content',
                    '/'
                );
            }
        }
    }


}
