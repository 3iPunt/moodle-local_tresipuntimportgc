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
 * Upgrade steps for local_tresipuntimportgc.
 *
 * @package    local_tresipuntimportgc
 * @copyright  2026 3iPunt (contacte@tresipunt.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Executes the plugin upgrade steps.
 *
 * @param  int $oldversion Version installed before the upgrade.
 * @return bool            Always true.
 * @throws ddl_exception If a table cannot be created.
 * @throws downgrade_exception|upgrade_exception On savepoint errors.
 */
function xmldb_local_tresipuntimportgc_upgrade(int $oldversion): bool {
    global $DB;

    $dbman = $DB->get_manager();

    if ($oldversion < 2026072200) {
        // Import runs: one row per set of Classroom courses queued together.
        $table = new xmldb_table('local_tresipuntimportgc_import');
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('userid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('googleaccount', XMLDB_TYPE_CHAR, '255', null, null, null, null);
        $table->add_field('usermodified', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('timemodified', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);
        $table->add_key('userid', XMLDB_KEY_FOREIGN, ['userid'], 'user', ['id']);
        $table->add_key('usermodified', XMLDB_KEY_FOREIGN, ['usermodified'], 'user', ['id']);
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Courses inside an import run, with per-course configuration and status.
        $table = new xmldb_table('local_tresipuntimportgc_course');
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('importid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('providerid', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
        $table->add_field('fullname', XMLDB_TYPE_CHAR, '254', null, XMLDB_NOTNULL, null, null);
        $table->add_field('shortname', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
        $table->add_field('categoryid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('visible', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '1');
        $table->add_field('importfiles', XMLDB_TYPE_INTEGER, '2', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('calendarimport', XMLDB_TYPE_INTEGER, '2', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('status', XMLDB_TYPE_CHAR, '10', null, XMLDB_NOTNULL, null, 'pending');
        $table->add_field('courseid', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
        $table->add_field('timestarted', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
        $table->add_field('timefinished', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
        $table->add_field('usermodified', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('timemodified', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);
        $table->add_key('importid', XMLDB_KEY_FOREIGN, ['importid'], 'local_tresipuntimportgc_import', ['id']);
        $table->add_key('courseid', XMLDB_KEY_FOREIGN, ['courseid'], 'course', ['id']);
        $table->add_key('usermodified', XMLDB_KEY_FOREIGN, ['usermodified'], 'user', ['id']);
        $table->add_index('status', XMLDB_INDEX_NOTUNIQUE, ['status']);
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Trace lines of each imported course.
        $table = new xmldb_table('local_tresipuntimportgc_log');
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('importcourseid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('level', XMLDB_TYPE_CHAR, '10', null, XMLDB_NOTNULL, null, 'info');
        $table->add_field('message', XMLDB_TYPE_TEXT, null, null, XMLDB_NOTNULL, null, null);
        $table->add_field('usermodified', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('timemodified', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);
        $table->add_key('importcourseid', XMLDB_KEY_FOREIGN, ['importcourseid'], 'local_tresipuntimportgc_course', ['id']);
        $table->add_key('usermodified', XMLDB_KEY_FOREIGN, ['usermodified'], 'user', ['id']);
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        upgrade_plugin_savepoint(true, 2026072200, 'local', 'tresipuntimportgc');
    }

    if ($oldversion < 2026072201) {
        // Encrypted Google refresh token so the adhoc tasks can authenticate
        // without a web session. Wiped when the import run finishes.
        $table = new xmldb_table('local_tresipuntimportgc_import');
        $field = new xmldb_field('refreshtoken', XMLDB_TYPE_TEXT, null, null, null, null, null, 'googleaccount');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        upgrade_plugin_savepoint(true, 2026072201, 'local', 'tresipuntimportgc');
    }

    if ($oldversion < 2026072301) {
        // Settings cleanup (2.0): credentials JSON no longer exists and the
        // selects only keep the implemented options, so old values are remapped.
        unset_config('credentialsjson', 'local_tresipuntimportgc');

        if ((int) get_config('local_tresipuntimportgc', 'importfiles') === 2) {
            // Nextcloud option removed: fall back to "do not import".
            set_config('importfiles', 3, 'local_tresipuntimportgc');
        }
        if ((int) get_config('local_tresipuntimportgc', 'calendarimport') !== 1) {
            set_config('calendarimport', 2, 'local_tresipuntimportgc');
        }
        if ((int) get_config('local_tresipuntimportgc', 'formsimport') === 1) {
            // Quiz conversion was never implemented: fall back to embed.
            set_config('formsimport', 0, 'local_tresipuntimportgc');
        }

        upgrade_plugin_savepoint(true, 2026072301, 'local', 'tresipuntimportgc');
    }

    if ($oldversion < 2026072400) {
        // Per-course forms handling and individual-content handling (E9.5/E10.9).
        $table = new xmldb_table('local_tresipuntimportgc_course');
        $field = new xmldb_field('formsimport', XMLDB_TYPE_INTEGER, '2', null,
            XMLDB_NOTNULL, null, '0', 'calendarimport');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }
        $field = new xmldb_field('importindividual', XMLDB_TYPE_INTEGER, '2', null,
            XMLDB_NOTNULL, null, '0', 'formsimport');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        upgrade_plugin_savepoint(true, 2026072400, 'local', 'tresipuntimportgc');
    }

    return true;
}
