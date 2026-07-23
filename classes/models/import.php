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
 * Import run model.
 *
 * @package    local_tresipuntimportgc
 * @copyright  2026 3iPunt (contacte@tresipunt.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_tresipuntimportgc\models;

use core\encryption;
use core\persistent;

/**
 * One import run: a set of Google Classroom courses queued together by a user.
 *
 * The run status is never stored: it is derived from the statuses of its
 * courses (see get_status()).
 *
 * @package    local_tresipuntimportgc
 * @copyright  2026 3iPunt (contacte@tresipunt.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class import extends persistent {

    /** @var string Database table. */
    public const TABLE = 'local_tresipuntimportgc_import';

    /** @var string Derived status: no course has started yet. */
    public const STATUS_QUEUED = 'queued';

    /** @var string Derived status: at least one course is running, or some are pending while others finished. */
    public const STATUS_RUNNING = 'running';

    /** @var string Derived status: every course finished successfully (discarded ones aside). */
    public const STATUS_COMPLETED = 'completed';

    /** @var string Derived status: some courses succeeded and some failed. */
    public const STATUS_PARTIAL = 'partial';

    /** @var string Derived status: every finished course failed. */
    public const STATUS_ERROR = 'error';

    /**
     * Persistent properties definition.
     *
     * @return array
     */
    protected static function define_properties(): array {
        return [
            'userid' => [
                'type' => PARAM_INT,
            ],
            'googleaccount' => [
                'type' => PARAM_NOTAGS,
                'null' => NULL_ALLOWED,
                'default' => null,
            ],
            'refreshtoken' => [
                'type' => PARAM_RAW,
                'null' => NULL_ALLOWED,
                'default' => null,
            ],
        ];
    }

    /**
     * Stores the Google refresh token encrypted at rest (null clears it).
     *
     * @param  string|null $plaintoken Refresh token as issued by Google.
     * @return void
     */
    public function set_refresh_token(?string $plaintoken): void {
        $this->set('refreshtoken', $plaintoken === null || $plaintoken === ''
            ? null : encryption::encrypt($plaintoken));
    }

    /**
     * Returns the Google refresh token decrypted, or null if none stored.
     *
     * @return string|null
     */
    public function get_refresh_token(): ?string {
        $stored = $this->get('refreshtoken');
        return empty($stored) ? null : encryption::decrypt($stored);
    }

    /**
     * Wipes the stored refresh token (call when the run reaches a final state).
     *
     * @return void
     */
    public function wipe_refresh_token(): void {
        if (!empty($this->get('refreshtoken'))) {
            $this->set('refreshtoken', null);
            $this->update();
        }
    }

    /**
     * Returns the courses of this import run, ordered by id.
     *
     * @return import_course[]
     */
    public function get_courses(): array {
        return import_course::get_records(['importid' => $this->get('id')], 'id');
    }

    /**
     * Returns how many courses of this run are in each status.
     *
     * @return array<string, int> Map status => count (only statuses present).
     */
    public function get_status_counts(): array {
        global $DB;

        $sql = 'SELECT status, COUNT(1) AS numcourses
                  FROM {' . import_course::TABLE . '}
                 WHERE importid = :importid
              GROUP BY status';
        $counts = [];
        foreach ($DB->get_records_sql($sql, ['importid' => $this->get('id')]) as $row) {
            $counts[$row->status] = (int) $row->numcourses;
        }
        return $counts;
    }

    /**
     * Derives the status of the run from the statuses of its courses.
     *
     * @return string One of the STATUS_* constants of this class.
     */
    public function get_status(): string {
        return self::derive_status($this->get_status_counts());
    }

    /**
     * Derives a run status from a map of course status counts.
     *
     * Discarded courses are terminal but neutral: they do not turn a run into
     * an error nor prevent it from being completed.
     *
     * @param  array $counts Map course status => count.
     * @return string One of the STATUS_* constants of this class.
     */
    public static function derive_status(array $counts): string {
        $pending = $counts[import_course::STATUS_PENDING] ?? 0;
        $running = $counts[import_course::STATUS_RUNNING] ?? 0;
        $success = $counts[import_course::STATUS_SUCCESS] ?? 0;
        $error = $counts[import_course::STATUS_ERROR] ?? 0;

        if ($running > 0 || ($pending > 0 && ($success + $error) > 0)) {
            return self::STATUS_RUNNING;
        }
        if ($pending > 0) {
            return self::STATUS_QUEUED;
        }
        if ($error > 0 && $success > 0) {
            return self::STATUS_PARTIAL;
        }
        if ($error > 0) {
            return self::STATUS_ERROR;
        }
        return self::STATUS_COMPLETED;
    }
}
