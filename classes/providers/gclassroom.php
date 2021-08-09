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

use curl;
use dml_exception;
use Google_Client;
use Google_Exception;
use Google_Service_Classroom;
use Google_Service_Drive;
use Google_Service_Oauth2;
use local_tresipuntimportgc\maps\gc_map;
use local_tresipuntimportgc\responses\error;
use local_tresipuntimportgc\responses\response_course;
use local_tresipuntimportgc\responses\response_courses;
use local_tresipuntimportgc\responses\response_folder;
use local_tresipuntimportgc\responses\response_modules;
use local_tresipuntimportgc\responses\response_sections;
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

    public const TIMEOUT = 30;
    public const GOOGLE_CLASSROOM_URL = 'https://classroom.googleapis.com/v1/courses/';

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
     * @return Google_Client
     * @throws Google_Exception
     * @throws moodle_exception
     */
    public function get_client(): Google_Client {
        if (!isset($this->client)) {
            $this->set_client();
        }
        return $this->client;
    }

    /**
     * Get Token.
     *
     * @return mixed
     */
    protected function get_token() {
        $accesstokenjson = $this->client->getAccessToken();
        $accesstoken = json_decode($accesstokenjson);
        return $accesstoken->access_token;
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
                'https://www.googleapis.com/auth/userinfo.profile',
                'https://www.googleapis.com/auth/classroom.courses',
                'https://www.googleapis.com/auth/classroom.coursework.me.readonly',
                'https://www.googleapis.com/auth/classroom.coursework.students.readonly ',
                'https://www.googleapis.com/auth/classroom.courseworkmaterials.readonly',
                'https://www.googleapis.com/auth/classroom.announcements.readonly',
                'https://www.googleapis.com/auth/classroom.topics.readonly',
                // DRIVE_READONLY ??
                Google_Service_Drive::DRIVE_READONLY]
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
            redirect((new moodle_url('/admin/settings.php',
                ['section' => 'local_tresipuntimportgc']))->out(false));
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
     * @return Google_Service_Classroom
     */
    protected function get_service(): Google_Service_Classroom {
        if (empty($this->service)) {
            $this->set_service();
            return $this->service;
        }
        return $this->service;
    }

    /**
     * Set Service.
     */
    protected function set_service(): void {
        $this->service = new Google_Service_Classroom($this->client);
    }

    /**
     * Get Courses.
     *
     * @return array
     */
    public function get_courses(): response_courses {
        // TODO paginate logic.
        try {
            $optParams = ['pageSize' => 99];
            $results = $this->get_service()->courses->listCourses($optParams);
            $courses = $results->getCourses();
            $data = gc_map::courses($courses);
            return new response_courses(true, $data, null);
        } catch (\Exception $e) {
            return new response_courses(false, [], new error('01000', $e->getMessage()));
        }
    }

    /**
     * Get Course.
     *
     * @param string $id
     * @return response_course
     */
    public function get_course(string $id): response_course {
        try {
            $course = $this->get_service()->courses->get($id);
            $data = gc_map::course($course);
            return new response_course(true, $data, null);
        } catch (\Exception $e) {
            return new response_course(false, null, new error('01010', $e->getMessage()));
        }
    }

    /**
     * Get Teacher Folder.
     *
     * @param string $id
     * @return response_folder
     */
    public function get_teacher_folder(string $id): response_folder {
        $course = $this->get_service()->courses->get($id);
        $tr = isset($course->toSimpleObject()->teacherFolder) ? $course->toSimpleObject()->teacherFolder : null;
        if (isset($tr)) {
            $data = gc_map::teacher_folder($tr);
            return new response_folder(true, $data, null);
        } else {
            return new response_folder(false, null, new error('01020', 'Not teacher foler'));
        }
    }

    /**
     * Get Sections.
     *
     * @param string $id
     * @return response_sections
     */
    public function get_sections(string $id): response_sections {
        $curl = new curl();
        $url = self::GOOGLE_CLASSROOM_URL . $id .  '/topics';
        $curl->setHeader($this->get_headers());
        $params = [];
        try {
            $req = $curl->get($url, $params, $this->get_options_curl('GET'));
            $res = $curl->getResponse();
            $data = json_decode($req, true);
            if (isset($res['HTTP/1.0'])) {
                if ($res['HTTP/1.0'] === '200 OK' && !empty($data)) {
                    $data = gc_map::sections($data['topic']);
                    $response = new response_sections(true, $data);
                } else {
                    $msg = $this->get_msg_curl($data, $res);
                    $response = new response_sections(true, [], new error('01032', $msg));
                }
            } else {
                $response = new response_sections(false, [], new error('01031', json_encode($res)));
            }
        } catch (\Exception $e) {
            $response = new response_sections(false, [], new error('01030', $e->getMessage()));
        }
        return $response;

    }

    /**
     * Get Modules.
     *
     * @param string $id
     * @return response_modules
     */
    public function get_modules(string $id): response_modules {
        $resworks = $this->get_course_works($id);
        if ($resworks->success) {
            $resmats = $this->get_course_work_materials($id);
            $mods = $resworks->data;
            if ($resmats->success) {
                $resanoun =$this->get_course_announcements($id);
                if ($resanoun->success) {
                    $mods = array_merge($resworks->data, $resmats->data, $resanoun->data);
                    return new response_modules(true, $mods, null);
                } else {
                    $mods = array_merge($resworks->data, $resmats->data);
                    mtrace('  -- ERROR: GET_ANNOUNCEMENTS: ' . $resanoun->error->to_string());
                    return new response_modules(true, $mods, $resanoun->error);
                }
            } else {
                mtrace('  -- ERROR: GET_WORK_MATERIALS: ' . $resmats->error->to_string());
                return new response_modules(true, $mods, $resmats->error);
            }
        } else {
            return $resworks;
        }
    }

    /**
     * Get Course Works.
     *
     * @param string $id
     * @return response_modules
     */
    public function get_course_works(string $id): response_modules {
        $curl = new curl();
        $url = self::GOOGLE_CLASSROOM_URL . $id .  '/courseWork';
        $curl->setHeader($this->get_headers());
        $params = [];
        try {
            $req = $curl->get($url, $params, $this->get_options_curl('GET'));
            $res = $curl->getResponse();
            $data = json_decode($req, true);
            if (isset($res['HTTP/1.0'])) {
                if ($res['HTTP/1.0'] === '200 OK') {
                    $mods = isset($data['courseWork']) ? gc_map::modules($data['courseWork'], 'courseWork') : gc_map::modules([], 'courseWork');
                    $response = new response_modules(true, $mods);
                } else {
                    $msg = $this->get_msg_curl($data, $res);
                    $response = new response_modules(false, [], new error('01042', $msg));
                }
            } else {
                $response = new response_modules(false, [], new error('01041', json_encode($res)));
            }
        } catch (\Exception $e) {
            $response = new response_modules(false, [], new error('01040', $e->getMessage()));
        }
        return $response;
    }

    /**
     * Get Course Work Materials.
     *
     * @param string $id
     * @return response_modules
     */
    public function get_course_work_materials(string $id): response_modules {
        $curl = new curl();
        $url = self::GOOGLE_CLASSROOM_URL . $id . '/courseWorkMaterials';
        $curl->setHeader($this->get_headers());
        $params = [];
        try {
            $req = $curl->get($url, $params, $this->get_options_curl('GET'));
            $res = $curl->getResponse();
            $data = json_decode($req, true);
            if (isset($res['HTTP/1.0'])) {
                if ($res['HTTP/1.0'] === '200 OK') {
                    $mods = gc_map::modules($data['courseWorkMaterial'], 'courseWorkMaterials');
                    $response = new response_modules(true, $mods);
                } else {
                    $msg = $this->get_msg_curl($data, $res);
                    $response = new response_modules(false, [], new error('01052', $msg));
                }
            } else {
                $response = new response_modules(false, [], new error('01051', json_encode($res)));
            }
        } catch (\Exception $e) {
            $response = new response_modules(false, [], new error('01050', $e->getMessage()));
        }
        return $response;
    }

    /**
     * Get Course Announcements.
     *
     * @param string $id
     * @return response_modules
     */
    public function get_course_announcements(string $id): response_modules {
        $curl = new curl();
        $url = self::GOOGLE_CLASSROOM_URL . $id . '/announcements';
        $curl->setHeader($this->get_headers());
        $params = [];
        try {
            $req = $curl->get($url, $params, $this->get_options_curl('GET'));
            $res = $curl->getResponse();
            $data = json_decode($req, true);
            if (isset($res['HTTP/1.0'])) {
                if ($res['HTTP/1.0'] === '200 OK') {
                    $mods = isset($data['announcements']) ? gc_map::modules($data['announcements'], 'announcements') : gc_map::modules([], 'announcements');
                    $response = new response_modules(true, $mods);
                } else {
                    $msg = $this->get_msg_curl($data, $res);
                    $response = new response_modules(false, [], new error('01062', $msg));
                }
            } else {
                $response = new response_modules(false, [], new error('01061', json_encode($res)));
            }
        } catch (\Exception $e) {
            $response = new response_modules(false, [], new error('01060', $e->getMessage()));
        }
        return $response;
    }

    /**
     * Get Headers.
     *
     * @return array
     */
    private function get_headers(): array {
        return [
            "Content-type: application/json",
            "Authorization: Bearer " . $this->get_token()
        ];
    }

    /**
     * Get Message Curl.
     *
     * @param $data
     * @param $res
     * @return string
     */
    private function get_msg_curl($data, $res): string {
        $code = $data['error']['code'] ?? '';
        $msg = $data['error']['message'] ?? '';
        $status = $data['error']['status'] ?? '';
        $message = $res['HTTP/1.0'];
        if (!empty($msg)) {
            $message .= ' - ' . $code . ': ' .  $msg . ' - ' . $status;
        }
        return $message;
    }

    /**
     * Get Options CURL.
     *
     * @param string $method
     * @return array
     */
    private function get_options_curl(string $method): array {
        return [
            'CURLOPT_RETURNTRANSFER' => true,
            'CURLOPT_TIMEOUT' => self::TIMEOUT,
            'CURLOPT_HTTP_VERSION' => CURL_HTTP_VERSION_1_0,
            'CURLOPT_CUSTOMREQUEST' => $method,
            'CURLOPT_SSL_VERIFYHOST' => 0,
            'CURLOPT_SSLVERSION' => CURL_SSLVERSION_TLSv1_2,
        ];
    }
}
