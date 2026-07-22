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
 * Routes legacy print_trace() calls to the persistent logger.
 *
 * @package    local_tresipuntimportgc
 * @copyright  2026 3iPunt (contacte@tresipunt.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_tresipuntimportgc\local;

/**
 * Static registry: while an import course runs, its logger is set here and
 * every print_trace() call is persisted as a trace of that course.
 *
 * Bridge for the progressive adaptation: the factories still call the global
 * print_trace(); registering a logger here captures those traces without
 * touching every call site.
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
}
