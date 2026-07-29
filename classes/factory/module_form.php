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
 * Class module_quiz
 *
 * @package     local_tresipuntimportgc
 * @copyright   2021 Tresipunt
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_tresipuntimportgc\factory;

use coding_exception;
use dml_exception;
use local_tresipuntimportgc\providers\google;
use local_tresipuntimportgc\responses\error;
use local_tresipuntimportgc\responses\response_module;
use mod_quiz_generator;

defined('MOODLE_INTERNAL') || die;

global $CFG;
require_once($CFG->dirroot . '/mod/quiz/lib.php');

/**
 * Class module_quiz
 *
 * @package     local_tresipuntimportgc
 * @copyright   2021 Tresipunt
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class module_form extends module {

    /** @var string Mod Name */
    protected $modname;

    /** @var mod_quiz_generator Generator */
    protected $generator;

    /** array module */
    protected $module;

    /** @var google $provider */
    protected $provider;

    /**
     * constructor.
     *
     * @param string $providersection
     * @param array $module
     * @param string $intro
     * @param bool $visible
     * @param google $provider
     * @throws coding_exception
     */
    public function __construct(string $providersection, array $module, string $intro, bool $visible, google $provider) {
        $this->module = $module;
        $this->provider = $provider;
        $this->modname = 'quiz';
        if ((isset($module['workType']) && $module['workType'] === 'SHORT_ANSWER_QUESTION') ||
            (isset($module['workType']) && $module['workType'] === 'MULTIPLE_CHOICE_QUESTION')) {
            $this->modname = 'feedback';
        }
        if (isset($module['materials'][0]) && count($module['materials']) === 1
                && array_key_first($module['materials'][0]) === 'form') {
            $formurl = $module['materials'][0]['form']['formUrl'];
            // Only forms the connected account can edit are readable via API;
            // forms owned by another teacher will simply not be found.
            $resform = $this->provider->get_form_by_url($formurl);
            if ($resform->success && $resform->data !== null) {
                $this->intro = $resform->data->description;
                // TODO when isquiz, import questions once the Forms API mapping lands.
            }
        }
        // El modname va sin prefijo (para la traza); el generador necesita el
        // componente con prefijo.
        parent::__construct('mod_' . $this->modname, $providersection, $module['title'], $intro, $visible);
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
            'introeditor' => $this->intro_editor(),
            'files' => file_get_unused_draft_itemid(),
            'timeopen'               => 0,
            'timeclose'              => 0,
            'preferredbehaviour'     => 'deferredfeedback',
            'attempts'               => 0,
            'attemptonlast'          => 0,
            'grademethod'            => QUIZ_GRADEHIGHEST,
            'decimalpoints'          => 2,
            'questiondecimalpoints'  => -1,
            'attemptduring'          => 1,
            'correctnessduring'      => 1,
            'marksduring'            => 1,
            'specificfeedbackduring' => 1,
            'generalfeedbackduring'  => 1,
            'rightanswerduring'      => 1,
            'overallfeedbackduring'  => 0,
            'attemptimmediately'          => 1,
            'correctnessimmediately'      => 1,
            'marksimmediately'            => 1,
            'specificfeedbackimmediately' => 1,
            'generalfeedbackimmediately'  => 1,
            'rightanswerimmediately'      => 1,
            'overallfeedbackimmediately'  => 1,
            'attemptopen'            => 1,
            'correctnessopen'        => 1,
            'marksopen'              => 1,
            'specificfeedbackopen'   => 1,
            'generalfeedbackopen'    => 1,
            'rightansweropen'        => 1,
            'overallfeedbackopen'    => 1,
            'attemptclosed'          => 1,
            'correctnessclosed'      => 1,
            'marksclosed'            => 1,
            'specificfeedbackclosed' => 1,
            'generalfeedbackclosed'  => 1,
            'rightanswerclosed'      => 1,
            'overallfeedbackclosed'  => 1,
            'questionsperpage'       => 1,
            'shuffleanswers'         => 1,
            'sumgrades'              => 10, // TODO Dinamyc from questions.
            'grade'                  => 10, // TODO derive from the questions too.
            'timecreated'            => time(),
            'timemodified'           => time(),
            'timelimit'              => 0,
            'overduehandling'        => 'autosubmit',
            'graceperiod'            => 86400,
            'quizpassword'           => '',
            'subnet'                 => '',
            'browsersecurity'        => '',
            'delay1'                 => 0,
            'delay2'                 => 0,
            'showuserpicture'        => 0,
            'showblocks'             => 0,
            'navmethod'              => QUIZ_NAVMETHOD_FREE,
        ];
        if (isset($this->module['dueDate'])) {
            $hour = 0;
            $minute = 0;
            if (isset($this->module['dueTime'])) {
                $hour = $this->module['dueTime']['hours'];
                $minute = $this->module['dueTime']['minutes'] ?? 0;
            }
            $duedate = $this->module['dueDate'];
            $record['timeclose'] = mktime($hour, $minute, 0,
                $duedate['month'], $duedate['day'], $duedate['year']);
        }
        $options = ['section' => $this->get_section($courseid), 'visible' => $this->visible, 'showdescription' => false];
        $res = $this->generator->create_instance($record, $options);
        if (isset($res)) {
            // TODO add the questions to the question bank and link them to this
            // quiz. The builder also needs the extra configuration that comes
            // with them: grading, multiple answers, and so on.
            return new response_module(true, $this, null);
        }
        return new response_module(false, null, new error('13000', 'MODULE_NOT_CREATED'));
    }


}
