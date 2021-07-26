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
 * Class course
 *
 * @package     local_tresipuntimportgc
 * @copyright   2021 Tresipunt
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_tresipuntimportgc\models;

use coding_exception;
use dml_exception;
use local_tresipuntimportgc\api\error;
use local_tresipuntimportgc\api\response_course;
use local_tresipuntimportgc\api\response_file;
use local_tresipuntimportgc\api\response_module;
use moodle_exception;
use stdClass;


defined('MOODLE_INTERNAL') || die;

/**
 * Class course
 *
 * @package     local_tresipuntimportgc
 * @copyright   2021 Tresipunt
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class course  {

    /** @var string ID course Provider */
    protected $providerid;

    /** @var string Description */
    protected $description;

    /** @var int Course ID Moodle */
    protected $id = null;

    /**
     * constructor.
     *
     * @param string $providerid
     * @param string $description
     */
    public function __construct(string $providerid, string $description) {
        $this->providerid = $providerid;
        $this->description = $description;
    }

    /**
     * Get Id.
     *
     * @return int|null
     */
    public function get_id(): ?int {
        return $this->id;
    }

    /**
     * Set Id.
     *
     * @param int $id
     */
    public function set_id(int $id) {
        $this->id = $id;
    }

    /**
     * Create Course.
     *
     * @param int $categoryid
     * @param string $fullname
     * @param string $shortname
     * @param bool $visible
     * @return response_course
     */
    public function create_course(int $categoryid, string $fullname, string $shortname, bool $visible): response_course {
        $data = new stdClass();
        $data->category = $categoryid;
        $data->idnumber = $this->providerid . '_' .uniqid();
        $data->shortname = $shortname;
        $data->fullname = $fullname;
        $data->visible = $visible;
        $data->summary = $this->description;
        try {
            $new = create_course($data);
            $this->set_id($new->id);
            return new response_course(true, $this, null);
        } catch (moodle_exception $e) {
            return new response_course(false, null, new error('10001', $e->getMessage()));
        }
    }

    /**
     * Create Teacher Folder.
     *
     * @param string $title
     * @param string $link
     * @return response_module
     * @throws coding_exception
     * @throws dml_exception
     */
    public function create_teacher_folder(string $title, string $link): response_module {
        $modurl = new module_url($this->get_id());
        return $modurl->create($title, 'Teacher Folder', $link, false);
    }

}
