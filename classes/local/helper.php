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
 * Small text helpers of the plugin.
 *
 * @package    local_tresipuntimportgc
 * @copyright  2026 3iPunt (contacte@tresipunt.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_tresipuntimportgc\local;

use core_text;

/**
 * Text helpers.
 *
 * @package    local_tresipuntimportgc
 * @copyright  2026 3iPunt (contacte@tresipunt.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class helper {

    /**
     * Builds a course shortname candidate from a Classroom class name:
     * accents transliterated to ASCII, spaces to underscores, lowercased.
     *
     * The result is only a proposal: uniqueness is validated where the
     * course is created.
     *
     * @param  string $name Class name.
     * @return string Shortname candidate.
     */
    public static function shortname_slug(string $name): string {
        $slug = core_text::specialtoascii($name);
        return core_text::strtolower(str_replace(' ', '_', $slug));
    }
}
