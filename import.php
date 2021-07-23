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
    $clientid = get_config('tool_timestats', 'clientid');
    $json = get_config('tool_timestats', 'credentialsjson');
    $secretkey = get_config('tool_timestats', 'secretkey');
    if ($json !== '' && $json !== false && $clientid !== '' && $clientid !== false && $secretkey !== '' && $secretkey !== false) {
        $client = get_classroom_client($json, $clientid, $secretkey);
        $service =  new Google_Service_Classroom($client);
        // Print the first 10 courses the user has access to.
        $optParams = array(
            'pageSize' => 99
        );
        $results = $service->courses->listCourses($optParams);
        if (count($results->getCourses()) === 0) {
            echo "No courses found.\n";
        } else {
            foreach ($results->getCourses() as $course) {
                print_object($course);
            }
        }
        die();
    }
    $page = new import_page();
    echo $output->render($page);
} else {
    throw new moodle_exception(
        get_string('not_capability', 'local_tresipuntimportgc')
    );
}

echo $OUTPUT->footer();


/**
 * @param string $json
 * @param string $clientid
 * @param string $secretkey
 * @return Google_Client
 * @throws Google_Exception
 */
function get_classroom_client(string $json, string $clientid, string $secretkey): Google_Client {
    global $CFG;
    require_once($CFG->libdir . '/google/src/Google/autoload.php');
    require_once($CFG->libdir . '/google/lib.php');
    require_once($CFG->libdir . '/google/src/Google/Service/Drive.php');
    $client = new Google_Client();
    $client->setApplicationName("Tresipunt Import Google Classroom");
    $client->setClientId($clientid);
    $client->setClientSecret($secretkey);
    $client->setAuthConfig($json);
    $client->setAccessType('offline');
    $client->setPrompt('select_account consent');
    $client->setScopes([
        Google_Service_Classroom::CLASSROOM_COURSES_READONLY,
        'https://www.googleapis.com/auth/userinfo.email',
        'https://www.googleapis.com/auth/userinfo.profile']
    );
    $oauth2 = new Google_Service_Oauth2($client);
    if (isset($_GET["code"])) {
        $client->authenticate($_GET['code']);
        $_SESSION['token'] = $client->getAccessToken();
    }
    if (isset($_SESSION['token'])) {
        $client->setAccessToken($_SESSION['token']);
    }
    if (isset($_REQUEST['error'])) {
        echo "<script type='text/javascript'>alert('error')</script>";
        exit;
    }
    if ($client->getAccessToken()) {
        $user = $oauth2->userinfo->get();
        $_SESSION['User'] = $user;
        $_SESSION['token'] = $client->getAccessToken();
    } else {
        $authUrl = $client->createAuthUrl();
        echo '<script>window.open("' . $authUrl . '", "_self");</script>';
    }
    return $client;
}
