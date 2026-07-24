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
 * Class module_teacher_folder
 *
 * @package     local_tresipuntimportgc
 * @copyright   2026 3iPunt (contacte@tresipunt.com)
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_tresipuntimportgc\factory;

use coding_exception;
use context_module;
use dml_exception;
use local_tresipuntimportgc\local\drive_files;
use local_tresipuntimportgc\providers\provider;
use local_tresipuntimportgc\responses\error;
use local_tresipuntimportgc\responses\response_module;
use mod_folder_generator;

defined('MOODLE_INTERNAL') || die;

/**
 * Downloads the teacher's Drive folder of the class into a hidden Folder
 * activity of the course, so the files live in Moodle and no longer depend on
 * Drive (E10.8; replaces the old link to Drive).
 *
 * @package     local_tresipuntimportgc
 * @copyright   2026 3iPunt (contacte@tresipunt.com)
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class module_teacher_folder extends module {

    /** @var string Mod Name */
    protected $modname = 'folder';

    /** @var mod_folder_generator Generator */
    protected $generator;

    /** @var \stdClass[] Drive file metadata to download. */
    protected $files;

    /** @var provider Connected provider. */
    protected $provider;

    /**
     * constructor.
     *
     * @param string     $title    Folder name.
     * @param \stdClass[] $files    Drive file metadata (from list_drive_folder).
     * @param provider   $provider Connected provider.
     * @throws coding_exception
     */
    public function __construct(string $title, array $files, provider $provider) {
        // Hidden for students, in the general section.
        parent::__construct('mod_folder', '', $title, '', false);
        $this->files = $files;
        $this->provider = $provider;
    }

    /**
     * Create.
     *
     * @param  int $course_id
     * @return response_module
     * @throws coding_exception|dml_exception
     */
    public function create(int $course_id): response_module {
        global $USER;

        $course = get_course($course_id);
        $record = [
            'course' => $course,
            'name' => $this->title,
            'intro' => '',
            'introformat' => FORMAT_HTML,
            'files' => file_get_unused_draft_itemid(),
        ];
        $options = ['section' => 0, 'visible' => 0, 'showdescription' => false];
        $res = $this->generator->create_instance($record, $options);
        if (!isset($res)) {
            return new response_module(false, null, new error('20000', 'MODULE_NOT_CREATED'));
        }
        $context = context_module::instance($res->cmid);
        foreach ($this->files as $filemeta) {
            drive_files::store($this->provider, $filemeta, $context->id,
                (int) $USER->id, 'mod_folder', 'content', '/');
        }
        return new response_module(true, $this, null);
    }
}
