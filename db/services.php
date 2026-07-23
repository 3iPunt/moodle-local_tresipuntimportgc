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
 * @package     local_tresipuntimportgc
 * @copyright   3iPunt <https://www.tresipunt.com/>
 */

use local_tresipuntimportgc\external\course_external;
use local_tresipuntimportgc\external\import_external;
use local_tresipuntimportgc\external\importcalendar_external;
use local_tresipuntimportgc\external\importfiles_external;

defined('MOODLE_INTERNAL') || die();

$functions = [
    'local_tresipuntimportgc_course_create' => [
        'classname' => course_external::class,
        'methodname' => 'create_course',
        'description' => 'Create Course',
        'type' => 'write',
        'ajax' => true,
        'loginrequired' => true,
        'capabilities' => 'local/tresipuntimportgc:import',
    ],
    'local_tresipuntimportgc_importfiles' => [
        'classname' => importfiles_external::class,
        'methodname' => 'importfiles',
        'description' => 'Import files',
        'type' => 'write',
        'ajax' => true,
        'loginrequired' => true,
        'capabilities' => 'local/tresipuntimportgc:import',
    ],
    'local_tresipuntimportgc_importcalendar' => [
        'classname' => importcalendar_external::class,
        'methodname' => 'importcalendar',
        'description' => 'Import Calendar',
        'type' => 'write',
        'ajax' => true,
        'loginrequired' => true,
        'capabilities' => 'local/tresipuntimportgc:import',
    ],
    'local_tresipuntimportgc_import_get_status' => [
        'classname' => import_external::class,
        'methodname' => 'get_status',
        'description' => 'Incremental status and traces of an import run (progress polling).',
        'type' => 'read',
        'ajax' => true,
        'loginrequired' => true,
        'capabilities' => 'local/tresipuntimportgc:import',
    ],
    'local_tresipuntimportgc_import_retry_course' => [
        'classname' => import_external::class,
        'methodname' => 'retry_course',
        'description' => 'Re-queues a failed course of an import run.',
        'type' => 'write',
        'ajax' => true,
        'loginrequired' => true,
        'capabilities' => 'local/tresipuntimportgc:import',
    ],
    'local_tresipuntimportgc_import_discard_course' => [
        'classname' => import_external::class,
        'methodname' => 'discard_course',
        'description' => 'Discards a pending course of an import run.',
        'type' => 'write',
        'ajax' => true,
        'loginrequired' => true,
        'capabilities' => 'local/tresipuntimportgc:import',
    ],
];
$services = [
    'local_tresipuntimportgc' => [
        'functions' => [
            'local_tresipuntimportgc_course_create',
            'local_tresipuntimportgc_importfiles',
            'local_tresipuntimportgc_importcalendar',
            'local_tresipuntimportgc_import_get_status',
            'local_tresipuntimportgc_import_retry_course',
            'local_tresipuntimportgc_import_discard_course',
        ],
        'restrictedusers' => 0,
        'enabled' => 1
    ]
];
