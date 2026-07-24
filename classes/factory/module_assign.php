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
 * Class module_assign
 *
 * @package     local_tresipuntimportgc
 * @copyright   2021 Tresipunt
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_tresipuntimportgc\factory;

use coding_exception;
use context_module;
use dml_exception;
use local_tresipuntimportgc\local\drive_files;
use local_tresipuntimportgc\providers\google;
use local_tresipuntimportgc\responses\error;
use local_tresipuntimportgc\responses\response_module;
use mod_assign_generator;

defined('MOODLE_INTERNAL') || die;

global $CFG;
require_once($CFG->dirroot . '/mod/assign/locallib.php');

/**
 * Class module_assign
 *
 * @package     local_tresipuntimportgc
 * @copyright   2021 Tresipunt
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class module_assign extends module {

    /** @var string Mod Name */
    protected $modname = 'assign';

    /** @var mod_assign_generator Generator */
    protected $generator;

    /** @var array $module */
    protected $module;

    /** @var array $materials */
    protected $materials;

    /**
     * constructor.
     *
     * @param string $providersection
     * @param array $module
     * @param string $intro
     * @param bool $visible
     * @throws coding_exception
     */
    public function __construct(string $providersection, array $module, string $intro, bool $visible) {
        parent::__construct('mod_assign', $providersection, $module['title'], $intro, $visible);
        $this->module = $module;
        $this->materials = $this->module['materials'] ?? [];
    }

    /**
     * Create.
     *
     * @param int $course_id
     * @return response_module
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
        // Nota máxima del trabajo → calificación por puntos de la tarea (E10.4).
        if (isset($this->module['maxPoints']) && (int) $this->module['maxPoints'] > 0) {
            $record['grade'] = (int) $this->module['maxPoints'];
        }
        // Fecha de publicación programada → restricción de acceso (E10.5).
        if ($availability = self::scheduled_availability($this->module)) {
            $record['availability'] = $availability;
        }
        if (isset($this->module['dueDate'])) {
            $hour = 0;
            $minute = 0;
            if (isset($this->module['dueTime'])) {
                $hour = $this->module['dueTime']['hours'];
                $minute = $this->module['dueTime']['minutes'] ?? 0;
            }
            $record['duedate'] = mktime($hour, $minute, 0, $this->module['dueDate']['month'], $this->module['dueDate']['day'], $this->module['dueDate']['year']);
        }
        $options = [
            'section' => $this->get_section($course_id),
            'visible' => $this->visible,
            'showdescription' => false
        ];
        $res = $this->generator->create_instance($record, $options);
        if (isset($res)) {
            if (count($this->materials) > 0) {
                $this->add_additional_files($res);
            }
            $this->import_rubric($res);
            return new response_module(true, $this, null);
        }
        return new response_module(false, null, new error('12000', 'MODULE_NOT_CREATED'));
    }

    /**
     * Imports the Classroom rubric of the coursework as a Moodle rubric on the
     * assignment (E10.3). Criteria×levels map 1:1; the level points become the
     * rubric scores.
     *
     * @param  object $res Generator result (with cmid).
     * @return void
     * @throws coding_exception|dml_exception
     */
    private function import_rubric($res): void {
        global $CFG;
        require_once($CFG->dirroot . '/grade/grading/lib.php');

        if (empty($this->module['courseId']) || empty($this->module['id'])) {
            return;
        }
        $provider = new google();
        $resrubric = $provider->get_rubric((string) $this->module['courseId'], (string) $this->module['id']);
        if (!$resrubric->success || empty($resrubric->data)) {
            return;
        }
        $context = context_module::instance($res->cmid);
        $gradingman = get_grading_manager($context, 'mod_assign', 'submissions');
        $controller = $gradingman->get_controller('rubric');
        $controller->update_definition($this->build_rubric_definition($resrubric->data));
        $gradingman->set_active_method('rubric');
    }

    /**
     * Builds a Moodle rubric definition from the Classroom criteria/levels.
     *
     * @param  array $criteria Array of {title, description, levels[]}.
     * @return \stdClass Definition for gradingform_rubric_controller::update_definition().
     */
    private function build_rubric_definition(array $criteria): \stdClass {
        $rubcriteria = [];
        $ci = 0;
        foreach ($criteria as $criterion) {
            $ci++;
            $levels = [];
            $li = 0;
            foreach ($criterion->levels as $level) {
                $li++;
                $deftext = trim($level->title
                    . ($level->description !== '' ? ' — ' . $level->description : ''));
                $levels['NEWID' . $li] = ['score' => $level->points, 'definition' => $deftext];
            }
            $desc = trim($criterion->title
                . ($criterion->description !== '' ? ' — ' . $criterion->description : ''));
            $rubcriteria['NEWID' . $ci] = [
                'sortorder' => $ci,
                'description' => $desc,
                'levels' => $levels,
            ];
        }
        return (object) [
            'name' => get_string('rubric_name', 'local_tresipuntimportgc'),
            'description_editor' => ['text' => '', 'format' => FORMAT_HTML, 'itemid' => 0],
            'rubric' => ['criteria' => $rubcriteria],
            'status' => \gradingform_controller::DEFINITION_STATUS_READY,
        ];
    }

    /**
     * Imports the Drive files of the materials as assignment intro attachments.
     *
     * @param  object $res Generator result (with cmid).
     * @return void
     * @throws coding_exception
     */
    private function add_additional_files($res): void {
        global $USER;

        $context = context_module::instance($res->cmid);
        $provider = new google();
        foreach ($this->materials as $material) {
            if (array_key_first($material) === 'driveFile') {
                drive_files::import(
                    $provider,
                    $material['driveFile']['driveFile']['id'],
                    $context->id,
                    (int) $USER->id,
                    'mod_assign',
                    ASSIGN_INTROATTACHMENT_FILEAREA,
                    '/'
                );
            }
        }
    }
}
