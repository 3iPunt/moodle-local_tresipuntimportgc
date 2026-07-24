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
 * Import orchestrator: queues runs and executes one course at a time.
 *
 * @package    local_tresipuntimportgc
 * @copyright  2026 3iPunt (contacte@tresipunt.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_tresipuntimportgc\local;

use core\task\manager;
use local_tresipuntimportgc\event\gc_course_imported;
use local_tresipuntimportgc\external\course_external;
use local_tresipuntimportgc\external\importcalendar_external;
use local_tresipuntimportgc\external\importfiles_external;
use local_tresipuntimportgc\models\import;
use local_tresipuntimportgc\models\import_course;
use local_tresipuntimportgc\providers\google;
use local_tresipuntimportgc\providers\provider;
use local_tresipuntimportgc\task\import_course_task;
use Throwable;

/**
 * Queues import runs (one adhoc task per course) and runs single courses.
 *
 * This is the business logic that used to live inside the legacy creation
 * page: here it runs without output buffering, persisting statuses and
 * traces so the progress page and the panel can read them.
 *
 * @package    local_tresipuntimportgc
 * @copyright  2026 3iPunt (contacte@tresipunt.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class importer {

    /**
     * Registers an import run and queues one adhoc task per course.
     *
     * Each course config accepts: providerid, fullname, shortname, categoryid,
     * visible (bool), importfiles (int) and calendarimport (int).
     *
     * @param  int      $userid        User launching the import.
     * @param  provider $provider      Connected provider (source of account and refresh token).
     * @param  array    $courseconfigs One config per course to import.
     * @return import The queued run.
     */
    public static function queue(int $userid, provider $provider, array $courseconfigs): import {
        $import = new import(0, (object) [
            'userid' => $userid,
            'googleaccount' => $provider->get_account_email(),
        ]);
        $import->set_refresh_token($provider->get_refresh_token());
        $import->create();

        foreach ($courseconfigs as $config) {
            $course = new import_course(0, (object) [
                'importid' => $import->get('id'),
                'providerid' => (string) $config['providerid'],
                'fullname' => (string) $config['fullname'],
                'shortname' => (string) $config['shortname'],
                'categoryid' => (int) $config['categoryid'],
                'visible' => empty($config['visible']) ? 0 : 1,
                'importfiles' => (int) ($config['importfiles'] ?? 0),
                'calendarimport' => (int) ($config['calendarimport'] ?? 0),
                'status' => import_course::STATUS_PENDING,
            ]);
            $course->create();
            self::queue_course_task($course, $userid);
        }
        return $import;
    }

    /**
     * Queues the adhoc task of one import course.
     *
     * @param  import_course $course Course to run.
     * @param  int           $userid User the task runs as (the launcher).
     * @return void
     */
    public static function queue_course_task(import_course $course, int $userid): void {
        $task = new import_course_task();
        $task->set_custom_data(['importcourseid' => (int) $course->get('id')]);
        $task->set_userid($userid);
        manager::queue_adhoc_task($task);
    }

    /**
     * Imports one course of a run. Called by the adhoc task.
     *
     * Never throws: any failure is persisted as an error trace plus the error
     * status, so the task is not retried by cron (retrying is a manual action
     * from the panel).
     *
     * @param  import_course  $course   Course to import (must be pending).
     * @param  provider|null  $provider Connected provider (defaults to a new
     *                                  google(); injectable for tests).
     * @return void
     */
    public static function run_course(import_course $course, ?provider $provider = null): void {
        global $DB;

        if ($course->get('status') !== import_course::STATUS_PENDING) {
            return;
        }
        $import = $course->get_import();
        $logger = logger::for_course($course);
        trace_router::set_logger($logger);
        try {
            $provider = $provider ?? new google();
            $refreshtoken = $import->get_refresh_token();
            if ($refreshtoken === null) {
                throw new \moodle_exception('error_client', 'local_tresipuntimportgc');
            }
            $auth = $provider->authenticate_with_refresh_token($refreshtoken);
            if (!$auth->success) {
                $logger->error($auth->error->to_string());
                $course->mark_running();
                $course->mark_error();
                return;
            }

            $course->mark_running();

            $shortname = (string) $course->get('shortname');
            if ($shortname === '') {
                $shortname = helper::shortname_slug((string) $course->get('fullname'));
            }
            if ($DB->record_exists('course', ['shortname' => $shortname])) {
                trace_router::trace('shortnamealreadyexist', 'danger', $shortname);
                $course->mark_error();
                return;
            }

            $response = course_external::create_course(
                (string) $course->get('providerid'),
                (string) $course->get('fullname'),
                $shortname,
                (int) $course->get('categoryid'),
                (bool) $course->get('visible'),
                (int) $course->get('importfiles'),
                $provider
            );
            if (empty($response['success']) || empty($response['id'])) {
                if (!empty($response['errors'])) {
                    $logger->error($response['errors']);
                }
                $course->mark_error();
                return;
            }
            $courseid = (int) $response['id'];

            if ((int) $course->get('importfiles') === 1) {
                trace_router::trace('importingfiles', 'info');
                importfiles_external::importfiles((string) $course->get('providerid'), $courseid, $shortname);
            }
            if ((int) $course->get('calendarimport') === 1) {
                trace_router::trace('importingcalendar', 'info');
                importcalendar_external::importcalendar((string) $course->get('providerid'), $courseid);
            }

            $course->mark_success($courseid);
            trace_router::trace('creationcoursecompleted', 'success');
            gc_course_imported::create_from_course($course, $courseid)->trigger();
        } catch (Throwable $e) {
            $logger->error($e->getMessage());
            if ($course->get('status') === import_course::STATUS_PENDING) {
                $course->mark_running();
            }
            if ($course->get('status') === import_course::STATUS_RUNNING) {
                $course->mark_error();
            }
        } finally {
            trace_router::set_logger(null);
            self::finish_run_if_done($import);
        }
    }

    /**
     * Wipes the stored refresh token once every course reached a final state.
     *
     * @param  import $import The run to check.
     * @return void
     */
    private static function finish_run_if_done(import $import): void {
        $counts = $import->get_status_counts();
        $open = ($counts[import_course::STATUS_PENDING] ?? 0)
            + ($counts[import_course::STATUS_RUNNING] ?? 0);
        if ($open === 0) {
            $import->wipe_refresh_token();
        }
    }
}
