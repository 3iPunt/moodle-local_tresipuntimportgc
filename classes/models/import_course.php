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
 * Import course model.
 *
 * @package    local_tresipuntimportgc
 * @copyright  2026 3iPunt (contacte@tresipunt.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_tresipuntimportgc\models;

use coding_exception;
use core\persistent;

/**
 * One Google Classroom course inside an import run.
 *
 * Holds the configuration chosen for the course (target names, category,
 * visibility, files and calendar handling) and its lifecycle status:
 * pending -> running -> success | error, plus discarded (from pending) and
 * back to pending on retry (from error).
 *
 * @package    local_tresipuntimportgc
 * @copyright  2026 3iPunt (contacte@tresipunt.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class import_course extends persistent {

    /** @var string Database table. */
    public const TABLE = 'local_tresipuntimportgc_course';

    /** @var string Queued, not started yet. */
    public const STATUS_PENDING = 'pending';

    /** @var string Being imported right now. */
    public const STATUS_RUNNING = 'running';

    /** @var string Imported without fatal errors. */
    public const STATUS_SUCCESS = 'success';

    /** @var string Import failed. */
    public const STATUS_ERROR = 'error';

    /** @var string Discarded by a manager before running. */
    public const STATUS_DISCARDED = 'discarded';

    /**
     * Persistent properties definition.
     *
     * @return array
     */
    protected static function define_properties(): array {
        return [
            'importid' => [
                'type' => PARAM_INT,
            ],
            'providerid' => [
                'type' => PARAM_RAW_TRIMMED,
            ],
            'fullname' => [
                'type' => PARAM_TEXT,
            ],
            'shortname' => [
                'type' => PARAM_TEXT,
            ],
            'categoryid' => [
                'type' => PARAM_INT,
            ],
            'visible' => [
                'type' => PARAM_INT,
                'default' => 1,
            ],
            'importfiles' => [
                'type' => PARAM_INT,
                'default' => 0,
            ],
            'calendarimport' => [
                'type' => PARAM_INT,
                'default' => 0,
            ],
            'status' => [
                'type' => PARAM_ALPHA,
                'default' => self::STATUS_PENDING,
                'choices' => [
                    self::STATUS_PENDING,
                    self::STATUS_RUNNING,
                    self::STATUS_SUCCESS,
                    self::STATUS_ERROR,
                    self::STATUS_DISCARDED,
                ],
            ],
            'courseid' => [
                'type' => PARAM_INT,
                'null' => NULL_ALLOWED,
                'default' => null,
            ],
            'timestarted' => [
                'type' => PARAM_INT,
                'null' => NULL_ALLOWED,
                'default' => null,
            ],
            'timefinished' => [
                'type' => PARAM_INT,
                'null' => NULL_ALLOWED,
                'default' => null,
            ],
        ];
    }

    /**
     * Whether the course reached a terminal status.
     *
     * @return bool
     */
    public function is_terminal(): bool {
        return in_array($this->get('status'), [
            self::STATUS_SUCCESS,
            self::STATUS_ERROR,
            self::STATUS_DISCARDED,
        ], true);
    }

    /**
     * Marks the course as running (only from pending).
     *
     * @return void
     * @throws coding_exception If the transition is not allowed.
     */
    public function mark_running(): void {
        $this->assert_transition([self::STATUS_PENDING], self::STATUS_RUNNING);
        $this->set('status', self::STATUS_RUNNING);
        $this->set('timestarted', time());
        $this->update();
    }

    /**
     * Marks the course as successfully imported (only from running).
     *
     * @param  int $courseid Id of the Moodle course created.
     * @return void
     * @throws coding_exception If the transition is not allowed.
     */
    public function mark_success(int $courseid): void {
        $this->assert_transition([self::STATUS_RUNNING], self::STATUS_SUCCESS);
        $this->set('status', self::STATUS_SUCCESS);
        $this->set('courseid', $courseid);
        $this->set('timefinished', time());
        $this->update();
    }

    /**
     * Marks the course as failed (only from running).
     *
     * @return void
     * @throws coding_exception If the transition is not allowed.
     */
    public function mark_error(): void {
        $this->assert_transition([self::STATUS_RUNNING], self::STATUS_ERROR);
        $this->set('status', self::STATUS_ERROR);
        $this->set('timefinished', time());
        $this->update();
    }

    /**
     * Discards a pending course (a manager decided not to import it).
     *
     * @return void
     * @throws coding_exception If the transition is not allowed.
     */
    public function mark_discarded(): void {
        $this->assert_transition([self::STATUS_PENDING], self::STATUS_DISCARDED);
        $this->set('status', self::STATUS_DISCARDED);
        $this->set('timefinished', time());
        $this->update();
    }

    /**
     * Re-queues a failed course so it can be retried.
     *
     * @return void
     * @throws coding_exception If the transition is not allowed.
     */
    public function mark_retried(): void {
        $this->assert_transition([self::STATUS_ERROR], self::STATUS_PENDING);
        $this->set('status', self::STATUS_PENDING);
        $this->set('courseid', null);
        $this->set('timestarted', null);
        $this->set('timefinished', null);
        $this->update();
    }

    /**
     * Returns the import run this course belongs to.
     *
     * @return import
     */
    public function get_import(): import {
        return new import($this->get('importid'));
    }

    /**
     * Returns the trace lines of this course.
     *
     * @param  int $fromid Only traces with id greater than this (0 = all).
     * @return import_log[]
     */
    public function get_logs(int $fromid = 0): array {
        global $DB;

        $select = 'importcourseid = :importcourseid AND id > :fromid';
        $params = ['importcourseid' => $this->get('id'), 'fromid' => $fromid];
        $records = $DB->get_records_select(import_log::TABLE, $select, $params, 'id');
        return array_map(static function ($record) {
            return new import_log(0, $record);
        }, array_values($records));
    }

    /**
     * Throws unless the current status is one of the expected ones.
     *
     * @param  string[] $from     Statuses the transition is allowed from.
     * @param  string   $to       Target status (for the error message).
     * @return void
     * @throws coding_exception If the current status is not in $from.
     */
    private function assert_transition(array $from, string $to): void {
        if (!in_array($this->get('status'), $from, true)) {
            throw new coding_exception('Invalid import course status transition: '
                . $this->get('status') . ' -> ' . $to);
        }
    }
}
