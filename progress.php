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
 * Import run progress page (minimal: refreshes itself while the run is open).
 *
 * @package    local_tresipuntimportgc
 * @copyright  2026 3iPunt (contacte@tresipunt.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../config.php');
require_once('./lib.php');

global $PAGE, $OUTPUT, $DB;

use local_tresipuntimportgc\models\import;
use local_tresipuntimportgc\models\import_course;
use local_tresipuntimportgc\output\progress_view;

// Parameters.
$importid = required_param('id', PARAM_INT);

// Security.
require_login();
require_capability('local/tresipuntimportgc:import', context_system::instance());

$import = new import($importid);

// Page setup.
$title = get_string('progress_title', 'local_tresipuntimportgc',
    userdate($import->get('timecreated'), get_string('strftimedatetimeshort', 'langconfig')));
$PAGE->set_context(context_system::instance());
$PAGE->set_url('/local/tresipuntimportgc/progress.php', ['id' => $importid]);
$PAGE->set_title($title);
$PAGE->set_heading('');

// Status maps (view stays dumb).
$coursestatus = [
    import_course::STATUS_PENDING => ['status_pending', 'tipgc-badge-pending'],
    import_course::STATUS_RUNNING => ['status_running', 'tipgc-badge-running'],
    import_course::STATUS_SUCCESS => ['status_success', 'tipgc-badge-success'],
    import_course::STATUS_ERROR => ['status_error', 'tipgc-badge-error'],
    import_course::STATUS_DISCARDED => ['status_discarded', 'tipgc-badge-pending'],
];
$runstatus = [
    import::STATUS_QUEUED => ['istatus_queued', 'tipgc-badge-pending'],
    import::STATUS_RUNNING => ['istatus_running', 'tipgc-badge-running'],
    import::STATUS_COMPLETED => ['istatus_completed', 'tipgc-badge-success'],
    import::STATUS_PARTIAL => ['istatus_partial', 'tipgc-badge-warning'],
    import::STATUS_ERROR => ['istatus_error', 'tipgc-badge-error'],
];

$state = $import->get_status();
$finished = in_array($state, [import::STATUS_COMPLETED, import::STATUS_PARTIAL, import::STATUS_ERROR], true);
if (!$finished) {
    // Cheap live view until the polling page lands: reload every 5 seconds.
    $PAGE->set_periodic_refresh_delay(5);
}

$launcher = $DB->get_record('user', ['id' => $import->get('userid')]);
$run = (object) [
    'statuslabel' => get_string($runstatus[$state][0], 'local_tresipuntimportgc'),
    'statusclass' => $runstatus[$state][1],
    'account' => (string) $import->get('googleaccount'),
    'launchedby' => $launcher ? fullname($launcher) : '',
    'startedon' => userdate($import->get('timecreated'), get_string('strftimedatetimeshort', 'langconfig')),
];

$courses = [];
foreach ($import->get_courses() as $course) {
    $status = $course->get('status');
    $logs = [];
    foreach ($course->get_logs() as $log) {
        $logs[] = [
            'time' => userdate($log->get('timecreated'), get_string('strftimetime24', 'langconfig')),
            'level' => $log->get('level'),
            'message' => format_text($log->get('message'), FORMAT_HTML, ['context' => context_system::instance()]),
        ];
    }
    $courses[] = [
        'fullname' => format_string($course->get('fullname')),
        'statuslabel' => get_string($coursestatus[$status][0], 'local_tresipuntimportgc'),
        'statusclass' => $coursestatus[$status][1],
        'courseurl' => $course->get('courseid')
            ? (new moodle_url('/course/view.php', ['id' => $course->get('courseid')]))->out(false) : '',
        'haslogs' => count($logs) > 0,
        'logs' => $logs,
    ];
}

$renderer = $PAGE->get_renderer('local_tresipuntimportgc');

echo $OUTPUT->header();
echo $renderer->render_progress_view(new progress_view($run, $courses));
echo $OUTPUT->footer();
