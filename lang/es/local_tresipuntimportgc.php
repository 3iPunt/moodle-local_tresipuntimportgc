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
 * Plugin strings are defined here 'es'.
 *
 * @package     local_tresipuntimportgc
 * @category    string
 * @copyright   2021 Tresipunt
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['pluginname'] = 'Tresipunt Importar Google Classroom';
$string['pluginconfig'] = 'Configurar Tresipunt Importación Google Classroom';
$string['import_page'] = 'Importar Cursos de Google Classroom';
$string['not_capability'] = 'No tiene permisos para importar cursos de Google Classroom';
$string['token'] = 'Access Token';
$string['gcheading'] = 'Google Classroom API';
$string['credentialsjson'] = 'Credenciales en json';
$string['credentialsjson_help'] = 'Añadir el contenido del archivo credentials.json.<br>Las credenciales son un archivo JSON que se obtiene al configurar la API de conexión en Google, en el panel de credenciales, en https://console.developers.google.com/';
$string['clientid'] = 'Cliente ID';
$string['clientid_help'] = 'Corresponde a "client_id" en el archivo json';
$string['secretkey'] = 'Secret Key';
$string['secretkey_help'] = 'Este valor se obtiene al configurar la Api de Google en https://console.cloud.google.com/apis/credentials';

$string['error_client'] = 'Error al generar el cliente';
