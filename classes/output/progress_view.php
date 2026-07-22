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
 * Import progress view (minimal, reload based).
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

defined('MOODLE_INTERNAL') || die;

/**
 * Pure renderable of the progress page: receives the run summary and the
 * per-course rows (with traces) already resolved by the page.
 *
 * @package    local_tresipuntimportgc
 * @copyright  2026 3iPunt (contacte@tresipunt.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class progress_view implements renderable, templatable {

    /** @var stdClass Run summary (status, statuslabel, statusclass, account, launchedby, startedon, finished). */
    private $run;

    /** @var array Course rows (fullname, statuslabel, statusclass, courseurl, haslogs, logs[]). */
    private $courses;

    /**
     * Constructor.
     *
     * @param stdClass $run     Run summary.
     * @param array    $courses Course rows.
     */
    public function __construct(stdClass $run, array $courses) {
        $this->run = $run;
        $this->courses = $courses;
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
            'title' => get_string('progress_title', 'local_tresipuntimportgc', $this->run->startedon),
            'description' => get_string('progress_desc', 'local_tresipuntimportgc'),
            'hasstatus' => true,
            'statuslabel' => $this->run->statuslabel,
            'statusclass' => $this->run->statusclass,
        ];
        $data->run = $this->run;
        $data->courses = $this->courses;
        $data->selectionurl = (new moodle_url('/local/tresipuntimportgc/import.php'))->out(false);
        return $data;
    }
}
