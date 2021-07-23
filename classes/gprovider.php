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

namespace local_tresipuntimportgc;

use Google_Client;
use Google_Exception;
use Google_Service_Classroom;
use Google_Service_Oauth2;
use moodle_exception;
use moodle_url;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->libdir . '/google/src/Google/autoload.php');
require_once($CFG->libdir . '/google/lib.php');
require_once($CFG->libdir . '/google/src/Google/Service/Drive.php');

abstract class gprovider {

    /**
     * @param string $json
     * @param string $clientid
     * @param string $secretkey
     * @return Google_Client
     * @throws Google_Exception
     * @throws moodle_exception
     */
    public static function get_client(string $json, string $clientid, string $secretkey): Google_Client {
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
            // TODO get error.
            echo "<script type='text/javascript'>alert('error')</script>";
            redirect((new moodle_url('/admin/settings.php', ['section' => 'local_tresipuntimportgc']))->out(false));
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

    /**
     * @param Google_Client $client
     * @return array
     */
    public static function get_classroom_courses(Google_Client $client): array {
        $service =  new Google_Service_Classroom($client);
        // TODO paginate logic.
        $optParams = ['pageSize' => 99];
        $results = $service->courses->listCourses($optParams);
        $courses = $results->getCourses();
        return count($courses) === 0 ? [] : $courses;
    }
}
