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
 * Course selection page: Google login, Classroom listing and import launch.
 *
 * Also acts as the OAuth redirect URI: Google returns here with ?code= (or
 * ?error=) after the consent screen. Launching an import is a POST with
 * sesskey that queues the run and redirects to the progress page.
 *
 * @package    local_tresipuntimportgc
 * @copyright  2026 3iPunt (contacte@tresipunt.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../config.php');
require_once('./lib.php');

global $PAGE, $OUTPUT, $USER;

use core\output\notification;
use local_tresipuntimportgc\local\importer;
use local_tresipuntimportgc\output\import_view;
use local_tresipuntimportgc\providers\google;

// Security.
require_login();
require_capability('local/tresipuntimportgc:import', context_system::instance());

// Parameters.
$action = optional_param('action', '', PARAM_ALPHA);
$code = optional_param('code', '', PARAM_RAW);
$autherror = optional_param('error', '', PARAM_TEXT);

// Page setup.
$title = get_string('import_page', 'local_tresipuntimportgc');
$PAGE->set_context(context_system::instance());
$PAGE->set_url('/local/tresipuntimportgc/import.php');
$PAGE->set_title($title);
$PAGE->set_heading('');

$provider = new google();
$selfurl = new moodle_url('/local/tresipuntimportgc/import.php');

// Launch: queue the selected courses and go to the progress page.
if ($action === 'import') {
    require_sesskey();
    if (!$provider->is_configured() || !$provider->has_token()) {
        redirect($selfurl);
    }
    $payload = required_param('courses', PARAM_RAW);
    $decoded = json_decode($payload, true);
    $defaultfiles = (int) get_config('local_tresipuntimportgc', 'importfiles');
    $defaultcalendar = (int) get_config('local_tresipuntimportgc', 'calendarimport');
    $allowconfig = (bool) get_config('local_tresipuntimportgc', 'allowconfig');
    $configs = [];
    foreach (is_array($decoded) ? $decoded : [] as $item) {
        if (empty($item['providerid']) || empty($item['fullname'])) {
            continue;
        }
        $shortname = clean_param(trim($item['shortname'] ?? ''), PARAM_TEXT);
        if ($shortname === '') {
            $shortname = strtolower(str_replace(' ', '_', normalize_str($item['fullname'])));
        }
        $configs[] = [
            'providerid' => clean_param($item['providerid'], PARAM_RAW_TRIMMED),
            'fullname' => clean_param($item['fullname'], PARAM_TEXT),
            'shortname' => $shortname,
            'categoryid' => (int) ($item['categoryid'] ?? 0),
            'visible' => !empty($item['visible']),
            'importfiles' => $allowconfig ? (int) ($item['importfiles'] ?? $defaultfiles) : $defaultfiles,
            'calendarimport' => $allowconfig ? (int) ($item['calendarimport'] ?? $defaultcalendar) : $defaultcalendar,
        ];
    }
    if ($configs === []) {
        redirect($selfurl, get_string('importqueue_novalid', 'local_tresipuntimportgc'),
            null, notification::NOTIFY_ERROR);
    }
    $import = importer::queue((int) $USER->id, $provider, $configs);
    redirect(new moodle_url('/local/tresipuntimportgc/progress.php', ['id' => $import->get('id')]),
        get_string('importqueued', 'local_tresipuntimportgc'), null, notification::NOTIFY_SUCCESS);
}

// The user denied (or Google returned) an error on the consent screen.
if ($autherror !== '') {
    redirect($selfurl, get_string('error_client', 'local_tresipuntimportgc') . ': ' . $autherror,
        null, notification::NOTIFY_ERROR);
}

// OAuth callback: exchange the code and clean the URL.
if ($code !== '' && !$provider->has_token()) {
    $res = $provider->authenticate_with_code($code);
    if (!$res->success) {
        redirect($selfurl, get_string('error_client', 'local_tresipuntimportgc') . ': ' . $res->error->to_string(),
            null, notification::NOTIFY_ERROR);
    }
    redirect($selfurl);
}

// Build the view for the current state.
$isadmin = has_capability('moodle/site:config', context_system::instance());
if (!$provider->is_configured()) {
    $view = new import_view('noconfig', [], [], false, [], [], null, '', $isadmin);
} else if (!$provider->has_token()) {
    $view = new import_view('connect', [], [], false, [], [], null, $provider->get_auth_url(), $isadmin);
} else {
    $rescourses = $provider->get_courses();
    $errormsg = $rescourses->success ? '' : $rescourses->error->to_string();
    $courses = [];
    foreach ($rescourses->data as $course) {
        $d = $course->providerdata;
        $courses[] = [
            'id' => $d->id ?? '',
            'name' => $d->name ?? '',
            'section' => $d->section ?? ($d->room ?? ''),
            'archived' => ($d->courseState ?? '') === 'ARCHIVED',
            'link' => $d->alternateLink ?? '',
            'defaultshortname' => strtolower(str_replace(' ', '_', normalize_str($d->name ?? ''))),
        ];
    }
    $categories = [];
    foreach (core_course_category::get_all(['returnhidden' => false]) as $category) {
        if (core_course_category::can_view_category($category)) {
            $categories[] = ['id' => $category->id, 'name' => $category->get_nested_name(false)];
        }
    }
    $allowconfig = (bool) get_config('local_tresipuntimportgc', 'allowconfig');
    $filesdefault = (int) get_config('local_tresipuntimportgc', 'importfiles');
    $calendardefault = (int) get_config('local_tresipuntimportgc', 'calendarimport');
    $filesoptions = [
        ['value' => 0, 'text' => get_string('generategdlink', 'local_tresipuntimportgc'),
            'selected' => $filesdefault === 0 ? 'selected' : ''],
        ['value' => 1, 'text' => get_string('importtoprivatearea', 'local_tresipuntimportgc'),
            'selected' => $filesdefault === 1 ? 'selected' : ''],
        ['value' => 3, 'text' => get_string('notimport', 'local_tresipuntimportgc'),
            'selected' => ($filesdefault === 2 || $filesdefault === 3) ? 'selected' : ''],
    ];
    $calendaroptions = [
        ['value' => 1, 'text' => get_string('calendarimport', 'local_tresipuntimportgc'),
            'selected' => $calendardefault === 1 ? 'selected' : ''],
        ['value' => 2, 'text' => get_string('notimport', 'local_tresipuntimportgc'),
            'selected' => $calendardefault !== 1 ? 'selected' : ''],
    ];
    $view = new import_view('list', $courses, $categories, $allowconfig, $filesoptions,
        $calendaroptions, $provider->get_account_email(), $provider->get_auth_url(),
        $isadmin, $errormsg);
}

$renderer = $PAGE->get_renderer('local_tresipuntimportgc');

echo $OUTPUT->header();
echo $renderer->render_import_view($view);
echo $OUTPUT->footer();
