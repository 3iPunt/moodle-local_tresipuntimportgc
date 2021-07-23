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

namespace local_tresipuntimportgc\providers;

use dml_exception;
use Google_Client;
use Google_Exception;
use Google_Service_Classroom;
use Google_Service_Oauth2;
use local_tresipuntimportgc\models\course;
use moodle_exception;
use moodle_url;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->libdir . '/google/src/Google/autoload.php');
require_once($CFG->libdir . '/google/lib.php');
require_once($CFG->libdir . '/google/src/Google/Service/Drive.php');

/**
 * Class gclassroom
 *
 * @package     local_tresipuntimportgc
 * @copyright   2021 Tresipunt
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class gclassroom extends provider {

    /** @var string Json */
    protected $json;

    /** @var string Client ID */
    protected $clientid;

    /** @var string Secret Key */
    protected $secretkey;

    /** @var Google_Client Client */
    protected $client;

    /** @var Google_Service_Classroom Service */
    protected $service;

    /**
     * gclassroom constructor.
     *
     * @throws Google_Exception
     * @throws dml_exception
     * @throws moodle_exception
     */
    public function __construct() {
        $this->json = get_config('local_tresipuntimportgc', 'credentialsjson');
        $this->clientid = get_config('local_tresipuntimportgc', 'clientid');
        $this->secretkey = get_config('local_tresipuntimportgc', 'secretkey');

        if ($this->json !== '' &&
            $this->json !== false &&
            $this->clientid !== '' &&
            $this->clientid !== false &&
            $this->secretkey !== '' &&
            $this->secretkey !== false) {
            $this->set_client();
        } else {
            redirect((
                new moodle_url(
                    '/admin/settings.php', ['section' => 'local_tresipuntimportgc'])
                )->out(false)
            );
        }
    }

    /**
     * Set Client.
     *
     * @throws Google_Exception
     * @throws moodle_exception
     */
    protected function set_client() {
        $client = new Google_Client();
        $client->setApplicationName(get_string('pluginname', 'local_tresipuntimportgc'));
        $client->setClientId($this->clientid );
        $client->setClientSecret($this->secretkey);
        $client->setAuthConfig($this->json);
        $client->setRedirectUri((new moodle_url('/local/tresipuntimportgc/import.php'))->out(false));
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
        $this->client = $client;
    }

    /**
     * Set Service.
     */
    protected function get_service() {
        if (empty($this->service)) {
            $this->set_service();
            return $this->service;
        } else {
            return $this->service;
        }
    }

    /**
     * Set Service.
     */
    protected function set_service() {
        $this->service = new Google_Service_Classroom($this->client);
    }

    /**
     * Get Courses.
     *
     * @return array
     */
    public function get_courses(): array {
        // TODO paginate logic.
        $optParams = ['pageSize' => 99];
        $results = $this->get_service()->courses->listCourses($optParams);
        $courses = $results->getCourses();
        return count($courses) === 0 ? [] : $courses;
    }

    /**
     * Get Course.
     *
     * @param string $id
     * @return course
     */
    public function get_course(string $id): course {
        $course = $this->get_service()->courses->get($id);
        $course3ip = new course($id);
        return $course3ip;
    }
}
