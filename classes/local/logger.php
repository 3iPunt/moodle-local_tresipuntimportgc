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
 * Persistent trace logger for imported courses.
 *
 * @package    local_tresipuntimportgc
 * @copyright  2026 3iPunt (contacte@tresipunt.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_tresipuntimportgc\local;

use local_tresipuntimportgc\models\import_course;
use local_tresipuntimportgc\models\import_log;

/**
 * Writes trace lines for one course of an import run.
 *
 * Replaces the legacy print_trace() output buffering: traces are persisted in
 * local_tresipuntimportgc_log and read back by the progress page and the panel.
 *
 * @package    local_tresipuntimportgc
 * @copyright  2026 3iPunt (contacte@tresipunt.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class logger {

    /** @var int Id of the import course the traces belong to. */
    private $importcourseid;

    /**
     * Constructor.
     *
     * @param int $importcourseid Id of the import course to log against.
     */
    public function __construct(int $importcourseid) {
        $this->importcourseid = $importcourseid;
    }

    /**
     * Builds a logger for an import course model.
     *
     * @param  import_course $course The import course.
     * @return self
     */
    public static function for_course(import_course $course): self {
        return new self((int) $course->get('id'));
    }

    /**
     * Writes an informative trace.
     *
     * @param  string $message Localised message.
     * @return import_log
     */
    public function info(string $message): import_log {
        return $this->log(import_log::LEVEL_INFO, $message);
    }

    /**
     * Writes a warning trace.
     *
     * @param  string $message Localised message.
     * @return import_log
     */
    public function warning(string $message): import_log {
        return $this->log(import_log::LEVEL_WARNING, $message);
    }

    /**
     * Writes an error trace.
     *
     * @param  string $message Localised message.
     * @return import_log
     */
    public function error(string $message): import_log {
        return $this->log(import_log::LEVEL_ERROR, $message);
    }

    /**
     * Persists one trace line.
     *
     * @param  string $level   One of the import_log::LEVEL_* constants.
     * @param  string $message Localised message.
     * @return import_log
     */
    private function log(string $level, string $message): import_log {
        $log = new import_log(0, (object) [
            'importcourseid' => $this->importcourseid,
            'level' => $level,
            'message' => $message,
        ]);
        $log->create();
        return $log;
    }
}
