<?php
// This file is part of  Moodle - http://moodle.org/
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
 * Plugin administration pages: three blocks (connection, defaults, log).
 *
 * @package     local_tresipuntimportgc
 * @copyright   2026 3iPunt (contacte@tresipunt.com)
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();
global $ADMIN, $CFG;

$ADMIN->add('courses', new admin_externalpage('local_tresipuntimportgc_import',
    new lang_string('import_page', 'local_tresipuntimportgc'),
    $CFG->wwwroot . '/local/tresipuntimportgc/import.php', ['local/tresipuntimportgc:import']));

// Categoría del plugin en Extensiones. Se registra siempre para que el panel
// aparezca por su capacidad (no solo para administradores del sitio).
$ADMIN->add('modules', new admin_category('local_tresipuntimportgc_category',
    new lang_string('pluginname', 'local_tresipuntimportgc')));

// Acceso a importar también desde la categoría del plugin (además de Cursos).
$ADMIN->add('local_tresipuntimportgc_category', new admin_externalpage('local_tresipuntimportgc_import_menu',
    new lang_string('import_page', 'local_tresipuntimportgc'),
    $CFG->wwwroot . '/local/tresipuntimportgc/import.php', ['local/tresipuntimportgc:import']));

// El panel de importaciones: visible para quien tenga la capacidad de consulta.
$ADMIN->add('local_tresipuntimportgc_category', new admin_externalpage('local_tresipuntimportgc_panel',
    new lang_string('panel_title', 'local_tresipuntimportgc'),
    $CFG->wwwroot . '/local/tresipuntimportgc/panel.php', ['local/tresipuntimportgc:viewreports']));

// Los ajustes del plugin son configuración de sitio: solo administradores.
if ($hassiteconfig) {
    $settingspage = new admin_settingpage(
        'local_tresipuntimportgc_config',
        new lang_string('pluginconfig', 'local_tresipuntimportgc'));
    $ADMIN->add('local_tresipuntimportgc_category', $settingspage);

    if ($ADMIN->fulltree) {
        // Bloque A: conexión con Google.
        $settingspage->add(
            new admin_setting_heading(
                'local_tresipuntimportgc_gc',
                new lang_string('gcheading', 'local_tresipuntimportgc'),
                new lang_string('gcheading_desc', 'local_tresipuntimportgc'))
        );

        // Estado de la conexión + URI de redirección copiable + probar.
        $settingspage->add(new \local_tresipuntimportgc\adminsetting\connection());

        $settingspage->add(new admin_setting_configtext('local_tresipuntimportgc/clientid',
            new lang_string('clientid', 'local_tresipuntimportgc'),
            new lang_string('clientid_help', 'local_tresipuntimportgc'),
            '',
            PARAM_RAW
        ));

        $settingspage->add(new admin_setting_configpasswordunmask('local_tresipuntimportgc/secretkey',
            new lang_string('secretkey', 'local_tresipuntimportgc'),
            new lang_string('secretkey_help', 'local_tresipuntimportgc'),
            ''
        ));

        // Bloque B: opciones de importación por defecto.
        $settingspage->add(
            new admin_setting_heading(
                'local_tresipuntimportgc_config_import',
                new lang_string('configimportheading', 'local_tresipuntimportgc'),
                new lang_string('configimportheading_help', 'local_tresipuntimportgc'))
        );

        $settingspage->add(
            new admin_setting_configcheckbox(
                'local_tresipuntimportgc/allowconfig',
                new lang_string('allowconfig', 'local_tresipuntimportgc'),
                new lang_string('allowconfig_help', 'local_tresipuntimportgc'), 0));

        // Formularios de Google: solo opciones implementadas.
        $options = [
            0 => get_string('formsiframegenerate', 'local_tresipuntimportgc'),
            2 => get_string('notimport', 'local_tresipuntimportgc'),
        ];
        $settingspage->add(
            new admin_setting_configselect(
                'local_tresipuntimportgc/formsimport',
                new lang_string('googleformsimport', 'local_tresipuntimportgc'),
                new lang_string('googleformsimport_help', 'local_tresipuntimportgc'), 0, $options));

        // Ficheros de Google Drive: solo opciones implementadas.
        $options = [
            0 => get_string('generategdlink', 'local_tresipuntimportgc'),
            1 => get_string('importtoprivatearea', 'local_tresipuntimportgc'),
            3 => get_string('notimport', 'local_tresipuntimportgc'),
        ];
        $settingspage->add(
            new admin_setting_configselect(
                'local_tresipuntimportgc/importfiles',
                new lang_string('importfiles', 'local_tresipuntimportgc'),
                new lang_string('importfiles_help', 'local_tresipuntimportgc'), 0, $options));

        // Calendario del curso: solo opciones implementadas.
        $options = [
            1 => get_string('calendarimport', 'local_tresipuntimportgc'),
            2 => get_string('notimport', 'local_tresipuntimportgc'),
        ];
        $settingspage->add(
            new admin_setting_configselect(
                'local_tresipuntimportgc/calendarimport',
                new lang_string('googlecalendarimport', 'local_tresipuntimportgc'),
                new lang_string('googlecalendarimport_help', 'local_tresipuntimportgc'), 2, $options));

        // Contenidos dirigidos a estudiantes concretos.
        $options = [
            0 => get_string('notimport', 'local_tresipuntimportgc'),
            1 => get_string('importindividualhidden', 'local_tresipuntimportgc'),
        ];
        $settingspage->add(
            new admin_setting_configselect(
                'local_tresipuntimportgc/importindividual',
                new lang_string('importindividual', 'local_tresipuntimportgc'),
                new lang_string('importindividual_help', 'local_tresipuntimportgc'), 0, $options));

        // Bloque C: registro.
        $settingspage->add(
            new admin_setting_heading(
                'local_tresipuntimportgc_log',
                new lang_string('logheading', 'local_tresipuntimportgc'),
                new lang_string('logheading_desc', 'local_tresipuntimportgc'))
        );

        $settingspage->add(new admin_setting_configtext('local_tresipuntimportgc/logretention',
            new lang_string('logretention', 'local_tresipuntimportgc'),
            new lang_string('logretention_help', 'local_tresipuntimportgc'),
            365,
            PARAM_INT
        ));

        $settingspage->add(new admin_setting_configtext('local_tresipuntimportgc/panelpagesize',
            new lang_string('panelpagesize', 'local_tresipuntimportgc'),
            new lang_string('panelpagesize_help', 'local_tresipuntimportgc'),
            25,
            PARAM_INT
        ));
    }
}
