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
 * Per-course effective configuration of the running import.
 *
 * @package    local_tresipuntimportgc
 * @copyright  2026 3iPunt (contacte@tresipunt.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_tresipuntimportgc\local;

/**
 * Static registry that carries the effective settings of the course being
 * imported (which can be per-course, not only global) down to the maps, which
 * are the only layer that reads settings during the transformation (§6.7).
 *
 * The importer sets it before importing each course and clears it after.
 *
 * @package    local_tresipuntimportgc
 * @copyright  2026 3iPunt (contacte@tresipunt.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class run_config {

    /** @var array<string,mixed> Effective settings of the running course. */
    private static $config = [];

    /**
     * Sets the effective configuration of the course being imported.
     *
     * @param  array<string,mixed> $config Effective settings.
     * @return void
     */
    public static function set(array $config): void {
        self::$config = $config;
    }

    /**
     * Reads one effective setting; falls back to the global plugin setting.
     *
     * @param  string $key     Setting name.
     * @param  mixed  $default Value if neither per-course nor global is set.
     * @return mixed
     */
    public static function get(string $key, $default = null) {
        if (array_key_exists($key, self::$config)) {
            return self::$config[$key];
        }
        $global = get_config('local_tresipuntimportgc', $key);
        return $global !== false ? $global : $default;
    }

    /**
     * Clears the configuration (call when the course finishes).
     *
     * @return void
     */
    public static function reset(): void {
        self::$config = [];
    }
}
