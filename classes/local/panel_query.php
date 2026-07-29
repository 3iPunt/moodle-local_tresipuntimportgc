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
 * Query for the imports panel.
 *
 * @package    local_tresipuntimportgc
 * @copyright  2026 3iPunt (contacte@tresipunt.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_tresipuntimportgc\local;

use local_tresipuntimportgc\models\import;
use local_tresipuntimportgc\models\import_course;

/**
 * Aggregates the import history for the panel: one row per run with its
 * course status counts, deriving the run status, then filtering (status and
 * search), ordering (newest first) and paginating in PHP.
 *
 * The volume is bounded by the log retention, so in-memory filtering is fine
 * and keeps the derived status logic in one place (import::derive_status).
 *
 * @package    local_tresipuntimportgc
 * @copyright  2026 3iPunt (contacte@tresipunt.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class panel_query {

    /**
     * Fetches the panel page.
     *
     * @param  string $statusfilter Derived status to keep ('' = all).
     * @param  string $search       Text matched against launcher name and account.
     * @param  int    $page         Zero-based page index.
     * @param  int    $perpage      Rows per page (>= 1).
     * @return object {records, total, emptysite, hasopenruns}. Each record adds
     *                derivedstatus, launchername and the n* counts.
     */
    public static function fetch(string $statusfilter, string $search, int $page,
            int $perpage): object {
        global $DB;

        $perpage = max(1, $perpage);
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
              ORDER BY i.timecreated DESC, i.id DESC";
        $params = [
            'stpending' => import_course::STATUS_PENDING,
            'strunning' => import_course::STATUS_RUNNING,
            'stsuccess' => import_course::STATUS_SUCCESS,
            'sterror' => import_course::STATUS_ERROR,
            'stdiscarded' => import_course::STATUS_DISCARDED,
        ];
        $records = $DB->get_records_sql($sql, $params);
        $emptysite = count($records) === 0;

        $hasopenruns = false;
        $filtered = [];
        foreach ($records as $record) {
            $record->derivedstatus = import::derive_status([
                import_course::STATUS_PENDING => (int) $record->npending,
                import_course::STATUS_RUNNING => (int) $record->nrunning,
                import_course::STATUS_SUCCESS => (int) $record->nsuccess,
                import_course::STATUS_ERROR => (int) $record->nerror,
                import_course::STATUS_DISCARDED => (int) $record->ndiscarded,
            ]);
            $hasopenruns = $hasopenruns || in_array($record->derivedstatus,
                [import::STATUS_QUEUED, import::STATUS_RUNNING], true);
            if ($statusfilter !== '' && $record->derivedstatus !== $statusfilter) {
                continue;
            }
            $record->launchername = fullname($record);
            if ($search !== '' && stripos(
                    $record->launchername . ' ' . $record->googleaccount, $search) === false) {
                continue;
            }
            $filtered[] = $record;
        }

        return (object) [
            'records' => array_values(array_slice($filtered, $page * $perpage, $perpage)),
            'total' => count($filtered),
            'emptysite' => $emptysite,
            'hasopenruns' => $hasopenruns,
        ];
    }
}
