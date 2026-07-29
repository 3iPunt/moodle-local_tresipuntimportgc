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
 * Behat data generator.
 *
 * @package    local_tresipuntimportgc
 * @copyright  2026 3iPunt (contacte@tresipunt.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Enables "the following "local_tresipuntimportgc > ..." exist:" steps.
 *
 * Example:
 *   And the following "local_tresipuntimportgc > imports" exist:
 *     | user  | googleaccount        |
 *     | admin | teacher@example.com  |
 *   And the following "local_tresipuntimportgc > import courses" exist:
 *     | user  | fullname | shortname | status  |
 *     | admin | Bio 1    | bio1      | success |
 *
 * @package    local_tresipuntimportgc
 * @copyright  2026 3iPunt (contacte@tresipunt.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class behat_local_tresipuntimportgc_generator extends behat_generator_base {

    /**
     * Entities this generator can create.
     *
     * @return array[]
     */
    protected function get_creatable_entities(): array {
        return [
            'imports' => [
                'singular' => 'import',
                'datagenerator' => 'import',
                'required' => ['user'],
                'switchids' => ['user' => 'userid'],
            ],
            'import courses' => [
                'singular' => 'import course',
                'datagenerator' => 'import_course',
                'required' => ['user'],
                'switchids' => ['user' => 'userid'],
            ],
            'logs' => [
                'singular' => 'log',
                'datagenerator' => 'log',
                'required' => ['shortname'],
            ],
        ];
    }
}
