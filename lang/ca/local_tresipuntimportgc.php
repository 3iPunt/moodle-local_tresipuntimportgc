<?php
// This file is part of a plugin for Moodle - http://moodle.org/
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
 * Plugin strings are defined here 'ca'.
 *
 * @package     local_tresipuntimportgc
 * @category    string
 * @copyright   2021 Tresipunt
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['pluginname'] = 'Tresipunt Importar Google Classroom';
$string['pluginconfig'] = 'Configuració Tresipunt Importació Google Classroom';
$string['import_page'] = 'Importa Cursos de Google Classroom';
$string['not_capability'] = 'No tens autorització per importar cursos de Google Classroom';
$string['token'] = 'Access Token';
$string['gcheading'] = 'Google Classroom API';
$string['credentialsjson'] = 'Credentials Json';
$string['credentialsjson_help'] = 'Add the content of the file credentials.json.<br>Credentials is a JSON file that you get when you configure the connection API in Google, in the credentials panel, at https://console.developers.google.com/';
$string['clientid'] = 'Client ID';
$string['clientid_help'] = 'Corresponds to "client_id" in the json file';
$string['secretkey'] = 'Secret Key';
$string['secretkey_help'] = 'This value is obtained by setting the Google Api to https://console.cloud.google.com/apis/credentials';

$string['error_client'] = 'Error when generating the client';
