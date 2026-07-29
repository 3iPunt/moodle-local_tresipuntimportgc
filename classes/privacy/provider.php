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
 * Privacy provider.
 *
 * @package    local_tresipuntimportgc
 * @copyright  2026 3iPunt (contacte@tresipunt.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_tresipuntimportgc\privacy;

use context;
use context_system;
use core_privacy\local\metadata\collection;
use core_privacy\local\request\approved_contextlist;
use core_privacy\local\request\approved_userlist;
use core_privacy\local\request\contextlist;
use core_privacy\local\request\transform;
use core_privacy\local\request\userlist;
use core_privacy\local\request\writer;

/**
 * Privacy provider: declares the data sent to Google and the import history
 * stored in the plugin tables, and implements export and deletion.
 *
 * The import history lives in the system context: each run belongs to the
 * user who launched it (userid), with their Google account email, an
 * encrypted refresh token (wiped when the run finishes) and the trace lines
 * of each imported course.
 *
 * @package    local_tresipuntimportgc
 * @copyright  2026 3iPunt (contacte@tresipunt.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class provider implements
    \core_privacy\local\metadata\provider,
    \core_privacy\local\request\plugin\provider,
    \core_privacy\local\request\core_userlist_provider {

    /**
     * Declares the personal data handled by the plugin.
     *
     * @param  collection $collection Metadata collection.
     * @return collection
     */
    public static function get_metadata(collection $collection): collection {
        $collection->add_database_table('local_tresipuntimportgc_import', [
            'userid' => 'privacy:metadata:import:userid',
            'googleaccount' => 'privacy:metadata:import:googleaccount',
            'refreshtoken' => 'privacy:metadata:import:refreshtoken',
            'timecreated' => 'privacy:metadata:import:timecreated',
        ], 'privacy:metadata:import');

        $collection->add_database_table('local_tresipuntimportgc_course', [
            'fullname' => 'privacy:metadata:course:fullname',
            'status' => 'privacy:metadata:course:status',
            'timestarted' => 'privacy:metadata:course:timestarted',
            'timefinished' => 'privacy:metadata:course:timefinished',
        ], 'privacy:metadata:course');

        $collection->add_database_table('local_tresipuntimportgc_log', [
            'level' => 'privacy:metadata:log:level',
            'message' => 'privacy:metadata:log:message',
            'timecreated' => 'privacy:metadata:log:timecreated',
        ], 'privacy:metadata:log');

        $collection->add_external_location_link('google', [
            'oauthtoken' => 'privacy:metadata:google:oauthtoken',
            'account' => 'privacy:metadata:google:account',
        ], 'privacy:metadata:google');

        return $collection;
    }

    /**
     * Contexts holding data of the user: the system context if they ever
     * launched an import.
     *
     * @param  int $userid User id.
     * @return contextlist
     */
    public static function get_contexts_for_userid(int $userid): contextlist {
        $contextlist = new contextlist();
        $sql = "SELECT ctx.id
                  FROM {context} ctx
                 WHERE ctx.contextlevel = :contextlevel
                   AND EXISTS (SELECT 1
                                 FROM {local_tresipuntimportgc_import} i
                                WHERE i.userid = :userid)";
        $contextlist->add_from_sql($sql, [
            'contextlevel' => CONTEXT_SYSTEM,
            'userid' => $userid,
        ]);
        return $contextlist;
    }

    /**
     * Users with data in a context: everyone who launched an import (system
     * context only).
     *
     * @param  userlist $userlist Target userlist.
     * @return void
     */
    public static function get_users_in_context(userlist $userlist) {
        if (!$userlist->get_context() instanceof context_system) {
            return;
        }
        $userlist->add_from_sql('userid',
            'SELECT userid FROM {local_tresipuntimportgc_import}', []);
    }

    /**
     * Exports the import history of the user under the system context.
     *
     * @param  approved_contextlist $contextlist Approved contexts.
     * @return void
     */
    public static function export_user_data(approved_contextlist $contextlist) {
        global $DB;

        $hassystem = false;
        foreach ($contextlist->get_contexts() as $context) {
            if ($context instanceof context_system) {
                $hassystem = true;
            }
        }
        if (!$hassystem) {
            return;
        }
        $userid = (int) $contextlist->get_user()->id;
        $subcontext = [get_string('privacy:exportpath', 'local_tresipuntimportgc')];

        $imports = $DB->get_records('local_tresipuntimportgc_import',
            ['userid' => $userid], 'timecreated ASC');
        foreach ($imports as $import) {
            $courses = [];
            foreach ($DB->get_records('local_tresipuntimportgc_course',
                    ['importid' => $import->id], 'id ASC') as $course) {
                $logs = [];
                foreach ($DB->get_records('local_tresipuntimportgc_log',
                        ['importcourseid' => $course->id], 'id ASC') as $log) {
                    $logs[] = (object) [
                        'time' => transform::datetime($log->timecreated),
                        'level' => $log->level,
                        'message' => $log->message,
                    ];
                }
                $courses[] = (object) [
                    'classname' => $course->fullname,
                    'status' => $course->status,
                    'timestarted' => $course->timestarted ? transform::datetime($course->timestarted) : null,
                    'timefinished' => $course->timefinished ? transform::datetime($course->timefinished) : null,
                    'traces' => $logs,
                ];
            }
            writer::with_context(context_system::instance())->export_data(
                array_merge($subcontext, ['import-' . $import->id]),
                (object) [
                    'googleaccount' => $import->googleaccount,
                    'timecreated' => transform::datetime($import->timecreated),
                    'courses' => $courses,
                ]
            );
        }
    }

    /**
     * Deletes the whole import history (system context).
     *
     * @param  context $context Target context.
     * @return void
     */
    public static function delete_data_for_all_users_in_context(context $context) {
        global $DB;

        if (!$context instanceof context_system) {
            return;
        }
        $DB->delete_records('local_tresipuntimportgc_log');
        $DB->delete_records('local_tresipuntimportgc_course');
        $DB->delete_records('local_tresipuntimportgc_import');
    }

    /**
     * Deletes the import history of one user.
     *
     * @param  approved_contextlist $contextlist Approved contexts of the user.
     * @return void
     */
    public static function delete_data_for_user(approved_contextlist $contextlist) {
        foreach ($contextlist->get_contexts() as $context) {
            if ($context instanceof context_system) {
                self::delete_user_history((int) $contextlist->get_user()->id);
                return;
            }
        }
    }

    /**
     * Deletes the import history of several users (system context).
     *
     * @param  approved_userlist $userlist Approved users.
     * @return void
     */
    public static function delete_data_for_users(approved_userlist $userlist) {
        if (!$userlist->get_context() instanceof context_system) {
            return;
        }
        foreach ($userlist->get_userids() as $userid) {
            self::delete_user_history((int) $userid);
        }
    }

    /**
     * Deletes every import run of one user, with its courses and traces.
     *
     * @param  int $userid User id.
     * @return void
     */
    private static function delete_user_history(int $userid): void {
        global $DB;

        $imports = $DB->get_fieldset_select('local_tresipuntimportgc_import', 'id',
            'userid = :userid', ['userid' => $userid]);
        if ($imports === []) {
            return;
        }
        [$insql, $params] = $DB->get_in_or_equal($imports, SQL_PARAMS_NAMED);
        $courses = $DB->get_fieldset_select('local_tresipuntimportgc_course', 'id',
            "importid $insql", $params);
        if ($courses !== []) {
            [$coursesql, $courseparams] = $DB->get_in_or_equal($courses, SQL_PARAMS_NAMED);
            $DB->delete_records_select('local_tresipuntimportgc_log',
                "importcourseid $coursesql", $courseparams);
        }
        $DB->delete_records_select('local_tresipuntimportgc_course', "importid $insql", $params);
        $DB->delete_records_select('local_tresipuntimportgc_import', "id $insql", $params);
    }
}
