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

namespace local_tresipuntimportgc\factory;

use coding_exception;
use course_modinfo;
use dml_exception;
use local_tresipuntimportgc\responses\error;
use local_tresipuntimportgc\responses\response;
use local_tresipuntimportgc\responses\response_course;
use local_tresipuntimportgc\responses\response_module;
use moodle_exception;
use stdClass;

defined('MOODLE_INTERNAL') || die;

global $CFG;
require_once($CFG->dirroot . '/lib/modinfolib.php');

/**
 * Class course
 *
 * @package     local_tresipuntimportgc
 * @copyright   2021 Tresipunt
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class course {

    /** @var string ID course Provider */
    protected $providerid;

    /** @var string Provider Data */
    public $providerdata;

    /** @var int Course ID Moodle */
    protected $id = null;

    /** @var string Description */
    protected $description = null;

    /**
     * constructor.
     *
     * @param string $providerid
     * @param string $desc
     * @param object|null $providerdata
     */
    public function __construct(string $providerid, string $desc, object $providerdata = null) {
        $this->providerid = $providerid;
        $this->providerdata = $providerdata;
        $this->description = $desc;
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
    public function create_course(
        int $categoryid, string $fullname, string $shortname, bool $visible): response_course {
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
            return new response_course(false, null, new error('11000', $e->getMessage()));
        }
    }

    /**
     * Create Teacher Folder.
     *
     * @param string $title
     * @param string $link
     * @return response_module
     * @throws coding_exception|dml_exception
     */
    public function create_teacher_folder(string $title, string $link): response_module {
        if (!is_null($this->get_id())) {
            $modurl = new module_url(
                '',
                $title . ': ' . get_string('teacher_folder', 'local_tresipuntimportgc'),
                '',
                false,
                $link);
            return $modurl->create($this->get_id());
        } else {
            return new response_module(false, null, new error('11010', 'COURSE NOT CREATED'));
        }

    }

    /**
     * Enrol Current User as Teacher.
     *
     * @return response
     */
    public function enrol_user_as_teacher(): response {
        global $USER, $DB;
        if (!is_null($this->get_id())) {
            try {
                $plugin_instance = $DB->get_record("enrol",
                    array('courseid' => $this->get_id(), 'enrol'=>'manual'));
                $plugin = enrol_get_plugin('manual');
                $roleid = $DB->get_field('role', 'id', array('shortname' => 'editingteacher'));
                $plugin->enrol_user($plugin_instance, $USER->id, $roleid);
                return new response(true, '');
            } catch (moodle_exception $e) {
                return new response(false, '', new error('11021', $e->getMessage()));
            }
        } else {
            return new response(false, null, new error('11020', 'COURSE NOT CREATED'));
        }
    }

    /**
     * Clean sections intro.
     *
     * @return response
     * @throws moodle_exception
     */
    public function clean_sections_intro(): response {
        global $DB;
        if (!is_null($this->get_id())) {
            $course = get_course($this->get_id());
            /** @var course_modinfo $modinfo */
            $modinfo = get_fast_modinfo($course->id);
            $sections = $modinfo->get_section_info_all();
            foreach ($sections as $section) {
                $updatesection = new stdClass();
                $updatesection->id = $section->id;
                $updatesection->summary = '';
                $DB->update_record('course_sections', $updatesection);
                course_modinfo::clear_instance_cache($course);
                rebuild_course_cache($course->id);
            }
            return new response(true, '');
        } else {
            return new response(false, null, new error('11030', 'COURSE NOT CREATED'));
        }

    }

}
