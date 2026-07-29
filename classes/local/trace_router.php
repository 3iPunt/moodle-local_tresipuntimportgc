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
 * Routes import traces to the persistent logger (and the task log in CLI).
 *
 * @package    local_tresipuntimportgc
 * @copyright  2026 3iPunt (contacte@tresipunt.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_tresipuntimportgc\local;

/**
 * Static registry: while an import course runs, its logger is set here and
 * every trace() call is persisted as a trace of that course. In CLI (cron,
 * adhoc tasks) the trace is also written to the task log via mtrace().
 *
 * @package    local_tresipuntimportgc
 * @copyright  2026 3iPunt (contacte@tresipunt.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class trace_router {

    /** @var logger|null Active logger, if an import course is running. */
    private static $logger = null;

    /**
     * Registers (or clears, with null) the active logger.
     *
     * @param  logger|null $logger Logger of the running import course.
     * @return void
     */
    public static function set_logger(?logger $logger): void {
        self::$logger = $logger;
    }

    /**
     * Active logger, if any.
     *
     * @return logger|null
     */
    public static function get_logger(): ?logger {
        return self::$logger;
    }

    /**
     * Persists one trace of the running import course.
     *
     * The message is a string id of the plugin; the legacy visual types
     * ('danger', 'light', 'primary'...) are collapsed into the three levels
     * of the persistent log (error, warning, info).
     *
     * @param  string $traceid String id of the message.
     * @param  string $type    Trace type (error/danger, warning, rest = info).
     * @param  mixed  $param   Optional parameter for the string.
     * @return void
     * @throws \coding_exception
     */
    public static function trace(string $traceid, string $type, $param = null): void {
        $message = get_string($traceid, 'local_tresipuntimportgc', self::escape_param($param));
        if (self::$logger !== null) {
            if ($type === 'danger' || $type === 'error') {
                self::$logger->error($message);
            } else if ($type === 'warning') {
                self::$logger->warning($message);
            } else {
                self::$logger->info($message);
            }
        }
        if (defined('CLI_SCRIPT') && CLI_SCRIPT) {
            mtrace('  ' . html_entity_decode(strip_tags($message), ENT_QUOTES, 'UTF-8'));
        }
    }

    /**
     * Escapes the data placed into a trace string.
     *
     * Trace strings carry their own markup ('<b>{$a}</b>'), the resulting
     * message is persisted, and the progress page injects it as HTML. The data
     * filling those placeholders is untrusted — file names, class and module
     * titles and error messages straight from the Google APIs — so it is
     * escaped here, at the single point where every trace is built.
     *
     * @param  mixed $param Parameter of the language string (any shape).
     * @return mixed The same shape with every string escaped.
     */
    private static function escape_param($param) {
        if (is_string($param)) {
            return s($param);
        }
        if (is_array($param)) {
            return array_map([self::class, 'escape_param'], $param);
        }
        if (is_object($param)) {
            $escaped = new \stdClass();
            foreach ((array) $param as $key => $value) {
                $escaped->$key = self::escape_param($value);
            }
            return $escaped;
        }
        return $param;
    }
}
