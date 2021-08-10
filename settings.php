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
 * Plugin administration pages are defined here.
 *
 * @package     local_tresipuntimportgc
 * @copyright   2021 Tresipunt
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

if ($hassiteconfig) {
    global $ADMIN, $CFG;
    $ADMIN->add('modules', new admin_category('local_tresipuntimportgc_category',
        new lang_string('pluginname', 'local_tresipuntimportgc')));
    $settingspage = new admin_settingpage(
        'local_tresipuntimportgc_config',
        new lang_string('pluginconfig', 'local_tresipuntimportgc'));
    if ($ADMIN->fulltree) {
        /* GOOGLE API */
        $settingspage->add(
            new admin_setting_heading(
                'local_tresipuntimportgc_gc',
                new lang_string('gcheading', 'local_tresipuntimportgc'),
                '')
        );
        // Credentials Json
        $settingspage->add(new admin_setting_configtext('local_tresipuntimportgc/credentialsjson',
            new lang_string('credentialsjson', 'local_tresipuntimportgc'),
            new lang_string('credentialsjson_help', 'local_tresipuntimportgc'),
            '',
            PARAM_RAW
        ));
        // Client ID
        $settingspage->add(new admin_setting_configtext('local_tresipuntimportgc/clientid',
            new lang_string('clientid', 'local_tresipuntimportgc'),
            new lang_string('clientid_help', 'local_tresipuntimportgc'),
            '',
            PARAM_RAW
        ));
        // Secret Key
        $settingspage->add(new admin_setting_configtext('local_tresipuntimportgc/secretkey',
            new lang_string('secretkey', 'local_tresipuntimportgc'),
            new lang_string('secretkey_help', 'local_tresipuntimportgc'),
            '',
            PARAM_RAW
        ));

        // TODO link or button for check connection to Google API.

        /* GENERAL CONFIG */
        $settingspage->add(
            new admin_setting_heading(
                'local_tresipuntimportgc_config_import',
                new lang_string('configimportheading', 'local_tresipuntimportgc'),
                '')
        );

        // Allow users to configure courses
        $settingspage->add(
            new admin_setting_configcheckbox(
                'local_tresipuntimportgc/allowconfig',
                new lang_string('allowconfig','local_tresipuntimportgc'),
                new lang_string('allowconfig_help','local_tresipuntimportgc'), 0));

        // Google Drive Files
        $options = [
            0 => get_string('generategdlink', 'local_tresipuntimportgc'),
            1 => get_string('importtoprivatearea', 'local_tresipuntimportgc'),
            2 => get_string('importtonextcloud', 'local_tresipuntimportgc'),
            3 => get_string('notimport', 'local_tresipuntimportgc'),
        ];
        $settingspage->add(
            new admin_setting_configselect(
                'local_tresipuntimportgc/importfiles',
                new lang_string('importfiles','local_tresipuntimportgc'),
                new lang_string('importfiles_help','local_tresipuntimportgc'), 0, $options));

        // Teacher folder
        // TODO the teacher's folder does not exist in classroom, it would be the user's private drive.
        /*$options = [
            0 => get_string('teacherfoldergenerategdlink', 'local_tresipuntimportgc'),
            1 => get_string('teacherfolderimporttoprivatefiles', 'local_tresipuntimportgc'),
            2 => get_string('teacherfolderimporttonextcloud', 'local_tresipuntimportgc'),
        ];
        $settingspage->add(
            new admin_setting_configselect(
                'local_tresipuntimportgc/teacherfolderimportfiles',
                new lang_string('teacherfolderimportfiles','local_tresipuntimportgc'),
                new lang_string('teacherfolderimportfiles_help','local_tresipuntimportgc'), 0, $options));*/

        // Google Calendar
        $options = [
            0 => get_string('calendargenerategdlink', 'local_tresipuntimportgc'),
            1 => get_string('calendarimport', 'local_tresipuntimportgc'),
            2 => get_string('notimport', 'local_tresipuntimportgc'),
        ];
        $settingspage->add(
            new admin_setting_configselect(
                'local_tresipuntimportgc/calendarimport',
                new lang_string('googlecalendarimport','local_tresipuntimportgc'),
                new lang_string('googlecalendarimport_help','local_tresipuntimportgc'), 0, $options));
    }
    $ADMIN->add('local_tresipuntimportgc_category', $settingspage);
    $ADMIN->add('courses', new admin_externalpage('local_tresipuntimportgc_import',
        new lang_string('import_page', 'local_tresipuntimportgc'),
        $CFG->wwwroot . '/local/tresipuntimportgc/import.php'));
}
