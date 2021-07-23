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

        $settingspage->add(
            new admin_setting_heading(
                'local_tresipuntimportgc_gc',
                new lang_string('gcheading', 'local_tresipuntimportgc'),
                '')
        );

        // Credentials Json
        $settingspage->add(new admin_setting_configtext('tool_timestats/credentialsjson',
            new lang_string('credentialsjson', 'local_tresipuntimportgc'),
            new lang_string('credentialsjson_help', 'local_tresipuntimportgc'),
            '',
            PARAM_RAW
        ));

        // Client ID
        $settingspage->add(new admin_setting_configtext('tool_timestats/clientid',
            new lang_string('clientid', 'local_tresipuntimportgc'),
            new lang_string('clientid_help', 'local_tresipuntimportgc'),
            '',
            PARAM_RAW
        ));

        // Secret Key
        $settingspage->add(new admin_setting_configtext('tool_timestats/secretkey',
            new lang_string('secretkey', 'local_tresipuntimportgc'),
            new lang_string('secretkey_help', 'local_tresipuntimportgc'),
            '',
            PARAM_RAW
        ));
    }
    $ADMIN->add('local_tresipuntimportgc_category', $settingspage);

    $ADMIN->add('courses', new admin_externalpage('local_tresipuntimportgc_import',
        new lang_string('import_page', 'local_tresipuntimportgc'),
        $CFG->wwwroot . '/local/tresipuntimportgc/import.php'));
}
