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
    $json = get_config('tool_timestats', 'credentialsjson');
    if ($json !== '' && $json !== false) {
        // TODO parse moodle core function 'get_google_client() -> lib/google/lib.php' for platforms with OAuth2 set up
        $client = get_classroom_client($json);
        // TODO pass customer to import_page, else error
        $service =  new Google_Service_Classroom($client);
        // Print the first 10 courses the user has access to.
        $optParams = array(
            'pageSize' => 10
        );
        $results = $service->courses->listCourses($optParams);

        if (count($results->getCourses()) == 0) {
            print "No courses found.\n";
        } else {
            print "Courses:\n";
            foreach ($results->getCourses() as $course) {
                printf("%s (%s)\n", $course->getName(), $course->getId());
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
 * @return Google_Client
 * @throws Google_Exception
 */
function get_classroom_client(string $json): Google_Client
{
    global $CFG;
    require_once($CFG->libdir . '/google/src/Google/autoload.php');
    require_once($CFG->libdir . '/google/lib.php');
    require_once($CFG->libdir . '/google/src/Google/Service/Drive.php');
    $client = new Google_Client();
    $client->setApplicationName('Google Classroom API PHP Quickstart');
    $client->setScopes([Google_Service_Classroom::CLASSROOM_COURSES_READONLY]);
    $client->setAuthConfig($json);
    $client->setAccessType('offline');
    $client->setPrompt('select_account consent');

    // TODO path to moodledata to store a token for each user, but the api from which the credentials are obtained may cause it to error if it does not match the user.
    $tokenPath = 'token.json';
    if (file_exists($tokenPath)) {
        $accessToken = json_decode(file_get_contents($tokenPath), true);
        $client->setAccessToken($accessToken);
    }
    if ($client->isAccessTokenExpired()) {
        if ($client->getRefreshToken()) {
            $client->refreshToken($client->getRefreshToken());
        } else {
            $authUrl = $client->createAuthUrl();
            $authCode = '';
            if (isset($_GET["code"])) {
                $authCode = $_GET["code"];
            } else {
                echo '
                <script>
                    window.open("' . $authUrl . '", "_blank");
                    var code = window.prompt("Enter verification code: ", "");
                    if (code !== null && code !== "") {
                        window.location.href = window.location.href + "?code=" + code;
                    }
                </script>';
            }
            // Exchange authorization code for an access token.
            $accessToken = $client->authenticate($authCode);
            $client->setAccessToken($accessToken);

            // Check to see if there was an error.
            if (array_key_exists('error', $accessToken)) {
                throw new Exception(join(', ', $accessToken));
            }
        }
        // Save the token to a file.
        if (!file_exists(dirname($tokenPath))) {
            mkdir(dirname($tokenPath), 0700, true);
        }
        file_put_contents($tokenPath, json_encode($client->getAccessToken()));
    }
    return $client;
}
