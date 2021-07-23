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
 * Import Courses GC.
 *
 * @package    local_tresipuntimportgc
 * @subpackage tresipuntimportgc
 * @copyright  2021 Tresipunt
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use local_tresipuntimportgc\gprovider;
use local_tresipuntimportgc\output\import_page;

require_once('../../config.php');

global $PAGE, $OUTPUT;

require_login();

$has_capability = has_capability('local/tresipuntimportgc:import',  context_system::instance());

$title = get_string('import_page', 'local_tresipuntimportgc');

$PAGE->set_context(context_system::instance());
$PAGE->set_url('/local/tresipuntimportgc/import.php');
$PAGE->set_title($title);
$PAGE->set_heading($title);

$output = $PAGE->get_renderer('local_tresipuntimportgc');

echo $OUTPUT->header();

if ($has_capability) {
    $json = get_config('tool_timestats', 'credentialsjson');
    $clientid = get_config('tool_timestats', 'clientid');
    $secretkey = get_config('tool_timestats', 'secretkey');
    if ($json !== '' && $json !== false && $clientid !== '' && $clientid !== false && $secretkey !== '' && $secretkey !== false) {
        try {
            $client = gprovider::get_client($json, $clientid, $secretkey);
        } catch (Google_Exception $e) {
            throw new Google_Exception(get_string('error_client', 'local_tresipuntimportgc'));
        } catch (moodle_exception $e) {
            throw new moodle_exception(get_string('error_client', 'local_tresipuntimportgc'));
        }
        $page = new import_page($client);
        echo $output->render($page);
    } else {
        // TODO get empty config.
        redirect((new moodle_url('/admin/settings.php', ['section' => 'local_tresipuntimportgc']))->out(false));
    }
} else {
    throw new moodle_exception(
        get_string('not_capability', 'local_tresipuntimportgc')
    );
}

echo $OUTPUT->footer();
