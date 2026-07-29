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
 * Capabilities of the plugin.
 *
 * Only CAP_ALLOW is declared: anything not granted is denied by default, and
 * CAP_PROHIBIT here would be irrevocable per context — it would stop a site
 * administrator from granting the capability to a role with that archetype.
 *
 * @package     local_tresipuntimportgc
 * @copyright   2021 3iPunt (contacte@tresipunt.com)
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// Este fichero define $capabilities (estado global), así que sí necesita el guard.
defined('MOODLE_INTERNAL') || die();

$capabilities = [

    // Import a class from Google Classroom. Granted to managers and course
    // creators, not only to site administrators: on many sites the people who
    // manage courses are not administrators.
    'local/tresipuntimportgc:import' => [
        'captype' => 'write',
        'contextlevel' => CONTEXT_SYSTEM,
        'archetypes' => [
            'coursecreator' => CAP_ALLOW,
            'manager' => CAP_ALLOW,
        ],
    ],

    // View the imports panel (history, detail and traces), including the runs of
    // other users. Same roles.
    'local/tresipuntimportgc:viewreports' => [
        'captype' => 'read',
        'contextlevel' => CONTEXT_SYSTEM,
        'archetypes' => [
            'coursecreator' => CAP_ALLOW,
            'manager' => CAP_ALLOW,
        ],
    ],

];
