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
 * Import progress view (live polling + historic mode).
 *
 * @package    local_tresipuntimportgc
 * @copyright  2026 3iPunt (contacte@tresipunt.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_tresipuntimportgc\output;

use moodle_url;
use renderable;
use renderer_base;
use stdClass;
use templatable;

/**
 * Pure renderable of the progress page.
 *
 * Receives the run summary, the per-course rows (with traces) and the polling
 * bootstrap data (import id, last trace id, finished flag) already resolved
 * by the page. Live updates arrive through the get_status web service.
 *
 * @package    local_tresipuntimportgc
 * @copyright  2026 3iPunt (contacte@tresipunt.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class progress_view implements renderable, templatable {

    /** @var stdClass Run summary (see progress.php). */
    private $run;

    /** @var array Course rows (see progress.php). */
    private $courses;

    /** @var bool Whether every course reached a final state. */
    private $finished;

    /** @var bool Whether cron looks stalled (warning C6). */
    private $cronstalled;

    /** @var int Import run id (polling bootstrap). */
    private $importid;

    /** @var int Highest trace id already rendered (polling bootstrap). */
    private $lastlogid;

    /** @var bool Whether the user can view the imports panel. */
    private $canviewpanel;

    /**
     * Constructor.
     *
     * @param stdClass $run         Run summary.
     * @param array    $courses     Course rows.
     * @param bool     $finished    Every course reached a final state.
     * @param bool     $cronstalled Cron looks stalled.
     * @param int      $importid    Import run id.
     * @param int      $lastlogid   Highest trace id already rendered.
     */
    public function __construct(stdClass $run, array $courses, bool $finished,
            bool $cronstalled, int $importid, int $lastlogid, bool $canviewpanel = false) {
        $this->run = $run;
        $this->courses = $courses;
        $this->finished = $finished;
        $this->cronstalled = $cronstalled;
        $this->importid = $importid;
        $this->lastlogid = $lastlogid;
        $this->canviewpanel = $canviewpanel;
    }

    /**
     * Export for template.
     *
     * @param  renderer_base $output Renderer.
     * @return stdClass
     */
    public function export_for_template(renderer_base $output): stdClass {
        $data = new stdClass();
        $data->header = [
            'logotresipunt' => $output->image_url('tresipunt_logo', 'local_tresipuntimportgc')->out(false),
            'logotresipunticon' => $output->image_url('tresipunt_icon', 'local_tresipuntimportgc')->out(false),
            'logoclassroom' => $output->image_url('icon', 'local_tresipuntimportgc')->out(false),
            'title' => get_string('progress_header', 'local_tresipuntimportgc'),
            'description' => get_string('progress_desc', 'local_tresipuntimportgc'),
            'hasstatus' => false,
            'statuslabel' => '',
            'statusclass' => '',
        ];
        $data->runtitle = get_string('progress_title', 'local_tresipuntimportgc', $this->run->startedon);
        $data->run = $this->run;
        $data->courses = $this->courses;
        $data->finished = $this->finished;
        $data->live = !$this->finished;
        $data->cronstalled = $this->cronstalled && !$this->finished;
        $data->importid = $this->importid;
        $data->lastlogid = $this->lastlogid;
        $data->selectionurl = (new moodle_url('/local/tresipuntimportgc/import.php'))->out(false);
        $data->panelurl = (new moodle_url('/local/tresipuntimportgc/panel.php'))->out(false);
        $data->canviewpanel = $this->canviewpanel;
        $data->hascreated = !empty($this->run->created);
        return $data;
    }
}
