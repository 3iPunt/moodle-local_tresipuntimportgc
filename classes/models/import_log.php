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
 * Import trace line model.
 *
 * @package    local_tresipuntimportgc
 * @copyright  2026 3iPunt (contacte@tresipunt.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_tresipuntimportgc\models;

use core\persistent;

/**
 * One trace line of an imported course (info, warning or error).
 *
 * @package    local_tresipuntimportgc
 * @copyright  2026 3iPunt (contacte@tresipunt.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class import_log extends persistent {

    /** @var string Database table. */
    public const TABLE = 'local_tresipuntimportgc_log';

    /** @var string Informative trace. */
    public const LEVEL_INFO = 'info';

    /** @var string Something worked partially or was skipped. */
    public const LEVEL_WARNING = 'warning';

    /** @var string Something failed. */
    public const LEVEL_ERROR = 'error';

    /**
     * Persistent properties definition.
     *
     * @return array
     */
    protected static function define_properties(): array {
        return [
            'importcourseid' => [
                'type' => PARAM_INT,
            ],
            'level' => [
                'type' => PARAM_ALPHA,
                'default' => self::LEVEL_INFO,
                'choices' => [
                    self::LEVEL_INFO,
                    self::LEVEL_WARNING,
                    self::LEVEL_ERROR,
                ],
            ],
            'message' => [
                'type' => PARAM_RAW,
            ],
        ];
    }
}
