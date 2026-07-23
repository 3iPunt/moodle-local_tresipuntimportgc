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
 * Imports panel: site-wide history with status filters, search and paging.
 *
 * @package    local_tresipuntimportgc
 * @copyright  2026 3iPunt (contacte@tresipunt.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../config.php');
require_once('./lib.php');

global $PAGE, $OUTPUT, $DB, $USER;

use local_tresipuntimportgc\models\import;
use local_tresipuntimportgc\models\import_course;
use local_tresipuntimportgc\output\panel_view;

// Parameters.
$statusfilter = optional_param('status', '', PARAM_ALPHA);
$search = trim(optional_param('search', '', PARAM_TEXT));
$page = optional_param('page', 0, PARAM_INT);

// Security.
require_login();
require_capability('local/tresipuntimportgc:viewreports', context_system::instance());

// Page setup.
$title = get_string('panel_title', 'local_tresipuntimportgc');
$baseurl = new moodle_url('/local/tresipuntimportgc/panel.php',
    array_filter(['status' => $statusfilter, 'search' => $search]));
$PAGE->set_context(context_system::instance());
$PAGE->set_url($baseurl);
$PAGE->set_title($title);
$PAGE->set_heading('');

$perpage = (int) get_config('local_tresipuntimportgc', 'panelpagesize');
if ($perpage < 1) {
    $perpage = 25;
}
$showaccount = has_capability('moodle/site:config', context_system::instance());

$runstatus = [
    import::STATUS_QUEUED => ['istatus_queued', 'tipgc-badge-pending'],
    import::STATUS_RUNNING => ['istatus_running', 'tipgc-badge-running'],
    import::STATUS_COMPLETED => ['istatus_completed', 'tipgc-badge-success'],
    import::STATUS_PARTIAL => ['istatus_partial', 'tipgc-badge-warning'],
    import::STATUS_ERROR => ['istatus_error', 'tipgc-badge-error'],
];

// One aggregated query: imports + course status counts + launcher name.
$userfields = \core_user\fields::for_name()->get_sql('u', false, '', '', false)->selects;
$sql = "SELECT i.id, i.userid, i.googleaccount, i.timecreated, $userfields,
               COALESCE(SUM(CASE WHEN c.status = :stpending THEN 1 ELSE 0 END), 0) AS npending,
               COALESCE(SUM(CASE WHEN c.status = :strunning THEN 1 ELSE 0 END), 0) AS nrunning,
               COALESCE(SUM(CASE WHEN c.status = :stsuccess THEN 1 ELSE 0 END), 0) AS nsuccess,
               COALESCE(SUM(CASE WHEN c.status = :sterror THEN 1 ELSE 0 END), 0) AS nerror,
               COALESCE(SUM(CASE WHEN c.status = :stdiscarded THEN 1 ELSE 0 END), 0) AS ndiscarded
          FROM {local_tresipuntimportgc_import} i
          JOIN {user} u ON u.id = i.userid
     LEFT JOIN {local_tresipuntimportgc_course} c ON c.importid = i.id
      GROUP BY i.id, i.userid, i.googleaccount, i.timecreated, $userfields
      ORDER BY i.timecreated DESC";
$params = [
    'stpending' => import_course::STATUS_PENDING,
    'strunning' => import_course::STATUS_RUNNING,
    'stsuccess' => import_course::STATUS_SUCCESS,
    'sterror' => import_course::STATUS_ERROR,
    'stdiscarded' => import_course::STATUS_DISCARDED,
];
$records = $DB->get_records_sql($sql, $params);
$emptysite = count($records) === 0;

// Derive status, filter and paginate in PHP (bounded volume: log retention).
$hasopenruns = false;
$filtered = [];
foreach ($records as $record) {
    $counts = [
        import_course::STATUS_PENDING => (int) $record->npending,
        import_course::STATUS_RUNNING => (int) $record->nrunning,
        import_course::STATUS_SUCCESS => (int) $record->nsuccess,
        import_course::STATUS_ERROR => (int) $record->nerror,
        import_course::STATUS_DISCARDED => (int) $record->ndiscarded,
    ];
    $record->derivedstatus = import::derive_status($counts);
    $hasopenruns = $hasopenruns
        || in_array($record->derivedstatus, [import::STATUS_QUEUED, import::STATUS_RUNNING], true);
    if ($statusfilter !== '' && $record->derivedstatus !== $statusfilter) {
        continue;
    }
    $record->launchername = fullname($record);
    if ($search !== ''
            && stripos($record->launchername . ' ' . $record->googleaccount, $search) === false) {
        continue;
    }
    $filtered[] = $record;
}
$total = count($filtered);
$pageslice = array_slice($filtered, $page * $perpage, $perpage);

$rows = [];
foreach ($pageslice as $record) {
    $ntotal = (int) $record->npending + (int) $record->nrunning + (int) $record->nsuccess + (int) $record->nerror;
    $pct = static fn(int $n): float => $ntotal > 0 ? round($n / $ntotal * 100, 2) : 0;
    $rows[] = [
        'id' => (int) $record->id,
        'date' => userdate($record->timecreated, get_string('strftimedatetimeshort', 'langconfig')),
        'user' => $record->launchername,
        'account' => $showaccount ? (string) $record->googleaccount : '',
        'ratio' => $record->nsuccess . '/' . $ntotal,
        'psuccess' => $pct((int) $record->nsuccess),
        'perror' => $pct((int) $record->nerror),
        'prunning' => $pct((int) $record->nrunning),
        'statuslabel' => get_string($runstatus[$record->derivedstatus][0], 'local_tresipuntimportgc'),
        'statusclass' => $runstatus[$record->derivedstatus][1],
        'active' => in_array($record->derivedstatus, [import::STATUS_QUEUED, import::STATUS_RUNNING], true),
        'detailurl' => (new moodle_url('/local/tresipuntimportgc/progress.php',
            ['id' => $record->id]))->out(false),
    ];
}

$statuschips = [['key' => '', 'label' => get_string('filter_all', 'local_tresipuntimportgc'),
    'active' => $statusfilter === '']];
foreach ($runstatus as $key => $def) {
    $statuschips[] = [
        'key' => $key,
        'label' => get_string($def[0], 'local_tresipuntimportgc'),
        'active' => $statusfilter === $key,
    ];
}

// Cron health only matters while something is queued or running.
$lastcron = (int) get_config('core', 'lastcronstart');
$cronstalled = $hasopenruns && (time() - $lastcron > 600);

$from = $total === 0 ? 0 : $page * $perpage + 1;
$to = min($total, ($page + 1) * $perpage);
$pagingnote = get_string('pagingnote', 'local_tresipuntimportgc',
    (object) ['from' => $from, 'to' => $to, 'total' => $total]);
$pagingbar = $OUTPUT->paging_bar($total, $page, $perpage, $baseurl);

$renderer = $PAGE->get_renderer('local_tresipuntimportgc');
$view = new panel_view($rows, $statuschips, $search, $emptysite, $cronstalled, $pagingnote, $pagingbar);

echo $OUTPUT->header();
echo $renderer->render_panel_view($view);
echo $OUTPUT->footer();
