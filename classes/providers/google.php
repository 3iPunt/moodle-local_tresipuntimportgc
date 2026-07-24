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
 * Google implementation of the provider contract (google/apiclient v2).
 *
 * @package    local_tresipuntimportgc
 * @copyright  2026 3iPunt (contacte@tresipunt.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_tresipuntimportgc\providers;

use Exception;
use Throwable;
use Google\Client;
use Google\Service\Calendar;
use Google\Service\Classroom;
use Google\Service\Drive;
use Google\Service\Forms;
use Google\Service\Oauth2;
use local_tresipuntimportgc\maps\gc_map;
use local_tresipuntimportgc\responses\error;
use local_tresipuntimportgc\responses\response_course;
use local_tresipuntimportgc\responses\response_courses;
use local_tresipuntimportgc\responses\response_data;
use local_tresipuntimportgc\responses\response_folder;
use local_tresipuntimportgc\responses\response_modules;
use local_tresipuntimportgc\responses\response_sections;
use moodle_url;
use stdClass;

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/../../.extlib/vendor/autoload.php');

/**
 * Google provider: Classroom, Drive, Calendar and Forms behind the contract.
 *
 * The only class of the plugin allowed to reference the vendored
 * google/apiclient library. Everything it returns is a plugin type.
 *
 * @package    local_tresipuntimportgc
 * @copyright  2026 3iPunt (contacte@tresipunt.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class google extends provider {

    /** @var string Session property holding the OAuth token array. */
    private const SESSION_TOKEN = 'local_tresipuntimportgc_token';

    /** @var string Session property holding the connected account email. */
    private const SESSION_EMAIL = 'local_tresipuntimportgc_email';

    /** @var string OAuth client id. */
    private $clientid;

    /** @var string OAuth client secret. */
    private $secretkey;

    /** @var Client|null Google client (lazy). */
    private $client = null;

    /** @var Classroom|null Classroom service (lazy). */
    private $classroom = null;

    /** @var Drive|null Drive service (lazy). */
    private $drive = null;

    /**
     * Constructor. Reads the site configuration; no side effects.
     */
    public function __construct() {
        $this->clientid = (string) get_config('local_tresipuntimportgc', 'clientid');
        $this->secretkey = (string) get_config('local_tresipuntimportgc', 'secretkey');
    }

    // Connection and authentication.

    /**
     * Whether the site-level OAuth client is configured.
     *
     * @return bool
     */
    public function is_configured(): bool {
        return $this->clientid !== '' && $this->secretkey !== '';
    }

    /**
     * Whether the current session holds a usable access token.
     *
     * Refreshes the token silently when it expired and a refresh token is
     * available.
     *
     * @return bool
     */
    public function has_token(): bool {
        global $SESSION;

        $key = self::SESSION_TOKEN;
        if (empty($SESSION->{$key})) {
            return false;
        }
        $client = $this->get_client();
        $client->setAccessToken($SESSION->{$key});
        if (!$client->isAccessTokenExpired()) {
            return true;
        }
        $refresh = $client->getRefreshToken();
        if ($refresh) {
            try {
                $token = $client->fetchAccessTokenWithRefreshToken($refresh);
                if (empty($token['error'])) {
                    $SESSION->{$key} = $client->getAccessToken();
                    return true;
                }
            } catch (Throwable $e) {
                unset($SESSION->{$key});
                return false;
            }
        }
        unset($SESSION->{$key});
        return false;
    }

    /**
     * URL of the Google consent screen for the current user.
     *
     * @return string
     */
    public function get_auth_url(): string {
        return $this->get_client()->createAuthUrl();
    }

    /**
     * Exchanges the OAuth callback code for a token and stores it in session.
     *
     * @param  string $code Authorisation code from the callback.
     * @return response_data
     */
    public function authenticate_with_code(string $code): response_data {
        global $SESSION;

        try {
            $client = $this->get_client();
            $token = $client->fetchAccessTokenWithAuthCode($code);
            if (!empty($token['error'])) {
                return new response_data(false, null,
                    new error('02000', $token['error'] . ': ' . ($token['error_description'] ?? '')));
            }
            $SESSION->{self::SESSION_TOKEN} = $client->getAccessToken();
            $oauth2 = new Oauth2($client);
            $SESSION->{self::SESSION_EMAIL} = $oauth2->userinfo->get()->getEmail();
            return new response_data(true);
        } catch (Throwable $e) {
            return new response_data(false, null, new error('02001', $e->getMessage()));
        }
    }

    /**
     * Email of the connected Google account.
     *
     * @return string|null
     */
    public function get_account_email(): ?string {
        global $SESSION;

        return $SESSION->{self::SESSION_EMAIL} ?? null;
    }

    /**
     * Drops the session token (change account).
     *
     * @return void
     */
    public function logout(): void {
        global $SESSION;

        unset($SESSION->{self::SESSION_TOKEN});
        unset($SESSION->{self::SESSION_EMAIL});
        $this->client = null;
        $this->classroom = null;
        $this->drive = null;
    }

    /**
     * Long-lived refresh token of the current session, if Google granted one.
     *
     * @return string|null
     */
    public function get_refresh_token(): ?string {
        global $SESSION;

        $token = $SESSION->{self::SESSION_TOKEN} ?? null;
        return is_array($token) ? ($token['refresh_token'] ?? null) : null;
    }

    /**
     * Authenticates without a web session (cron) using a refresh token.
     *
     * @param  string $refreshtoken Refresh token previously obtained.
     * @return response_data
     */
    public function authenticate_with_refresh_token(string $refreshtoken): response_data {
        global $SESSION;

        try {
            $client = $this->get_client();
            $token = $client->fetchAccessTokenWithRefreshToken($refreshtoken);
            if (!empty($token['error'])) {
                return new response_data(false, null,
                    new error('02002', $token['error'] . ': ' . ($token['error_description'] ?? '')));
            }
            // Share the token with every other provider instance of this run
            // (in cron the session is ephemeral and belongs to the task).
            $SESSION->{self::SESSION_TOKEN} = $client->getAccessToken();
            return new response_data(true);
        } catch (Throwable $e) {
            return new response_data(false, null, new error('02003', $e->getMessage()));
        }
    }

    // Classroom.

    /**
     * Lists the Classroom courses of the connected account.
     *
     * @return response_courses
     */
    public function get_courses(): response_courses {
        try {
            $results = $this->get_classroom()->courses->listCourses(['pageSize' => 99]);
            $courses = array_map([self::class, 'to_array'], $results->getCourses() ?? []);
            return new response_courses(true, gc_map::courses($courses), null);
        } catch (Throwable $e) {
            return new response_courses(false, [], new error('01000', $e->getMessage()));
        }
    }

    /**
     * Gets one Classroom course.
     *
     * @param  string $id Classroom course id.
     * @return response_course
     */
    public function get_course(string $id): response_course {
        try {
            $course = $this->get_classroom()->courses->get($id);
            $data = gc_map::course(self::to_array($course));
            return new response_course(true, $data, null);
        } catch (Throwable $e) {
            return new response_course(false, null, new error('01010', $e->getMessage()));
        }
    }

    /**
     * Gets the teacher folder of a Classroom course.
     *
     * @param  string $id Classroom course id.
     * @return response_folder
     */
    public function get_teacher_folder(string $id): response_folder {
        try {
            $course = $this->get_classroom()->courses->get($id);
            $tr = $course->toSimpleObject()->teacherFolder ?? null;
            if (isset($tr)) {
                $data = gc_map::teacher_folder((array) $tr);
                return new response_folder(true, $data, null);
            }
            return new response_folder(false, null, new error('01020', 'No teacher folder'));
        } catch (Throwable $e) {
            return new response_folder(false, null, new error('01021', $e->getMessage()));
        }
    }

    /**
     * Gets the topics of a Classroom course as plugin sections.
     *
     * @param  string $id Classroom course id.
     * @return response_sections
     */
    public function get_sections(string $id): response_sections {
        try {
            $result = $this->get_classroom()->courses_topics->listCoursesTopics($id);
            $topics = array_map([self::class, 'to_array'], $result->getTopic() ?? []);
            return new response_sections(true, gc_map::sections($topics));
        } catch (Throwable $e) {
            return new response_sections(false, [], new error('01030', $e->getMessage()));
        }
    }

    /**
     * Gets coursework, materials and announcements as plugin modules.
     *
     * Keeps the aggregation semantics of the legacy implementation: partial
     * failures return the modules recovered so far plus the error.
     *
     * @param  string $id Classroom course id.
     * @return response_modules
     */
    public function get_modules(string $id): response_modules {
        $resworks = $this->get_course_works($id);
        if (!$resworks->success) {
            return $resworks;
        }
        $mods = $resworks->data;
        $resmats = $this->get_course_work_materials($id);
        if (!$resmats->success) {
            return new response_modules(true, $mods, $resmats->error);
        }
        $resanoun = $this->get_course_announcements($id);
        if (!$resanoun->success) {
            return new response_modules(true, array_merge($mods, $resmats->data), $resanoun->error);
        }
        return new response_modules(true, array_merge($mods, $resmats->data, $resanoun->data), null);
    }

    /**
     * Gets the coursework of a Classroom course.
     *
     * @param  string $id Classroom course id.
     * @return response_modules
     */
    public function get_course_works(string $id): response_modules {
        try {
            $result = $this->get_classroom()->courses_courseWork->listCoursesCourseWork($id);
            $works = array_map([self::class, 'to_array'], $result->getCourseWork() ?? []);
            return new response_modules(true, gc_map::modules($works, $this, 'courseWork'));
        } catch (Throwable $e) {
            return new response_modules(false, [], new error('01040', $e->getMessage()));
        }
    }

    /**
     * Gets the coursework materials of a Classroom course.
     *
     * @param  string $id Classroom course id.
     * @return response_modules
     */
    public function get_course_work_materials(string $id): response_modules {
        try {
            $result = $this->get_classroom()->courses_courseWorkMaterials->listCoursesCourseWorkMaterials($id);
            $materials = array_map([self::class, 'to_array'], $result->getCourseWorkMaterial() ?? []);
            return new response_modules(true, gc_map::modules($materials, $this, 'courseWorkMaterials'));
        } catch (Throwable $e) {
            return new response_modules(false, [], new error('01050', $e->getMessage()));
        }
    }

    /**
     * Gets the announcements of a Classroom course.
     *
     * @param  string $id Classroom course id.
     * @return response_modules
     */
    public function get_course_announcements(string $id): response_modules {
        try {
            $result = $this->get_classroom()->courses_announcements->listCoursesAnnouncements($id);
            $announcements = array_map([self::class, 'to_array'], $result->getAnnouncements() ?? []);
            return new response_modules(true, gc_map::modules($announcements, $this, 'announcements'));
        } catch (Throwable $e) {
            return new response_modules(false, [], new error('01060', $e->getMessage()));
        }
    }

    // Drive.

    /**
     * Gets the metadata of one Drive file.
     *
     * @param  string $fileid Drive file id.
     * @return response_data data = stdClass{id, name, mimetype, weblink, size}.
     */
    public function get_drive_file(string $fileid): response_data {
        try {
            $file = $this->get_drive()->files->get($fileid, ['fields' => self::DRIVE_FIELDS]);
            return new response_data(true, $this->file_meta($file));
        } catch (Throwable $e) {
            return new response_data(false, null, new error('02010', $e->getMessage()));
        }
    }

    /**
     * Lists the files of a Drive folder.
     *
     * @param  string $folderid Drive folder id.
     * @return response_data data = stdClass[] with the get_drive_file() shape.
     */
    public function list_drive_folder(string $folderid): response_data {
        try {
            $result = $this->get_drive()->files->listFiles([
                'q' => sprintf("'%s' in parents and trashed = false", str_replace("'", "\\'", $folderid)),
                'pageSize' => 1000,
                'fields' => 'files(' . self::DRIVE_FIELDS . ')',
            ]);
            $files = array_map([$this, 'file_meta'], $result->getFiles() ?? []);
            return new response_data(true, $files);
        } catch (Throwable $e) {
            return new response_data(false, null, new error('02020', $e->getMessage()));
        }
    }

    /**
     * Downloads or exports a Drive file into the Moodle file storage.
     *
     * Binary files are downloaded as-is (Drive v3, alt=media); native Google
     * documents are exported to an Office/SVG format. Google Forms and
     * folders are not importable as files.
     *
     * @param  stdClass $filemeta   Metadata from get_drive_file()/list_drive_folder().
     * @param  array    $filerecord Moodle file record (without filename to use the Drive name).
     * @return response_data data = stdClass{status: imported|exists, filename}.
     */
    public function save_drive_file_to_storage(stdClass $filemeta, array $filerecord): response_data {
        $fs = get_file_storage();
        $export = self::EXPORTS[$filemeta->mimetype] ?? null;
        $isnative = strpos($filemeta->mimetype, 'application/vnd.google-apps') === 0;

        if ($isnative && $export === null) {
            // Forms, folders and other non-exportable Google types.
            return new response_data(false, null, new error('02031', 'Not exportable: ' . $filemeta->mimetype));
        }

        $filename = $filerecord['filename'] ?? ($filemeta->name . ($export['ext'] ?? ''));
        $filerecord['filename'] = $filename;
        $filerecord['itemid'] = $filerecord['itemid'] ?? 0;

        $existing = $fs->get_file($filerecord['contextid'], $filerecord['component'], $filerecord['filearea'],
            $filerecord['itemid'], $filerecord['filepath'], $filename);
        if ($existing !== false) {
            return new response_data(true, (object) ['status' => 'exists', 'filename' => $filename]);
        }

        try {
            if ($isnative) {
                $response = $this->get_drive()->files->export($filemeta->id, $export['mime']);
            } else {
                $response = $this->get_drive()->files->get($filemeta->id, ['alt' => 'media']);
            }
            $fs->create_file_from_string($filerecord, (string) $response->getBody());
            return new response_data(true, (object) ['status' => 'imported', 'filename' => $filename]);
        } catch (Throwable $e) {
            return new response_data(false, null, new error('02030', $e->getMessage()));
        }
    }

    // Calendar.

    /**
     * Lists the calendar events of a Classroom course.
     *
     * @param  string $id Classroom course id.
     * @return response_data data = stdClass[]{id, title, description, location,
     *                       timestart, timeduration, haslink}.
     */
    public function get_calendar_events(string $id): response_data {
        try {
            $course = $this->get_classroom()->courses->get($id);
            $calendarid = $course->getCalendarId();
            if (empty($calendarid)) {
                return new response_data(true, []);
            }
            $calendar = new Calendar($this->get_authenticated_client());
            $result = $calendar->events->listEvents($calendarid, [
                'singleEvents' => true,
                'orderBy' => 'startTime',
                'maxResults' => 100,
                'timeMin' => date('c'),
            ]);
            $events = [];
            foreach ($result->getItems() ?? [] as $event) {
                $start = $event->getStart();
                $end = $event->getEnd();
                $timestart = $start ? strtotime($start->getDateTime() ?: $start->getDate()) : 0;
                $timeend = $end ? strtotime($end->getDateTime() ?: $end->getDate()) : $timestart;
                $description = (string) $event->getDescription();
                $conferencelinks = [];
                if ($event->getConferenceData() && $event->getConferenceData()->getEntryPoints()) {
                    foreach ($event->getConferenceData()->getEntryPoints() as $entrypoint) {
                        if ($entrypoint->getEntryPointType() === 'video') {
                            $conferencelinks[] = (string) $entrypoint->getUri();
                        }
                    }
                }
                $attachments = [];
                foreach ($event->getAttachments() ?? [] as $attachment) {
                    $attachments[] = (object) [
                        'url' => (string) $attachment->getFileUrl(),
                        'title' => (string) $attachment->getTitle(),
                        'fileid' => (string) $attachment->getFileId(),
                    ];
                }
                $events[] = (object) [
                    'id' => $event->getId(),
                    'title' => (string) $event->getSummary(),
                    'description' => $description,
                    'location' => (string) $event->getLocation(),
                    'timestart' => $timestart,
                    'timeduration' => max(0, $timeend - $timestart),
                    // Classroom creates one calendar event per coursework: those are
                    // already covered when the module is imported.
                    'isclassroom' => stripos($description, 'https://classroom.google.com') !== false,
                    'conferencelinks' => $conferencelinks,
                    'attachments' => $attachments,
                ];
            }
            return new response_data(true, $events);
        } catch (Throwable $e) {
            return new response_data(false, null, new error('02040', $e->getMessage()));
        }
    }

    // Forms.

    /**
     * Finds a Google Form by its public URL and returns a summary.
     *
     * Only forms the connected account can edit are readable through the
     * Forms API; forms owned by other teachers will not be found.
     *
     * @param  string $formurl Public (responder) URL of the form.
     * @return response_data data = stdClass{id, title, description, isquiz} or null.
     */
    public function get_form_by_url(string $formurl): response_data {
        try {
            $result = $this->get_drive()->files->listFiles([
                'q' => "mimeType = 'application/vnd.google-apps.form' and trashed = false",
                'pageSize' => 1000,
                'fields' => 'files(id)',
            ]);
            $forms = new Forms($this->get_authenticated_client());
            foreach ($result->getFiles() ?? [] as $file) {
                $form = $forms->forms->get($file->getId());
                if ($form->getResponderUri() !== $formurl) {
                    continue;
                }
                $info = $form->getInfo();
                $settings = $form->getSettings();
                $isquiz = $settings && $settings->getQuizSettings()
                    ? (bool) $settings->getQuizSettings()->getIsQuiz() : false;
                return new response_data(true, (object) [
                    'id' => $form->getFormId(),
                    'title' => $info ? (string) $info->getTitle() : '',
                    'description' => $info ? (string) $info->getDescription() : '',
                    'isquiz' => $isquiz,
                ]);
            }
            return new response_data(true, null);
        } catch (Throwable $e) {
            return new response_data(false, null, new error('02050', $e->getMessage()));
        }
    }

    // Internals: the library never leaves this class.

    /** @var string Drive metadata fields requested everywhere. */
    private const DRIVE_FIELDS = 'id,name,mimeType,webViewLink,size';

    /** @var array Export formats for native Google document types. */
    private const EXPORTS = [
        'application/vnd.google-apps.document' => [
            'mime' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'ext' => '.docx',
        ],
        'application/vnd.google-apps.presentation' => [
            'mime' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
            'ext' => '.pptx',
        ],
        'application/vnd.google-apps.spreadsheet' => [
            'mime' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'ext' => '.xlsx',
        ],
        'application/vnd.google-apps.drawing' => [
            'mime' => 'image/svg+xml',
            'ext' => '.svg',
        ],
    ];

    /**
     * Builds (once) the Google client with the plugin scopes.
     *
     * @return Client
     */
    private function get_client(): Client {
        if ($this->client !== null) {
            return $this->client;
        }
        $client = new Client();
        $client->setApplicationName(get_string('pluginname', 'local_tresipuntimportgc'));
        $client->setClientId($this->clientid);
        $client->setClientSecret($this->secretkey);
        $client->setRedirectUri((new moodle_url('/local/tresipuntimportgc/import.php'))->out(false));
        $client->setAccessType('offline');
        $client->setPrompt('select_account consent');
        $client->setScopes([
            Classroom::CLASSROOM_COURSES_READONLY,
            'https://www.googleapis.com/auth/userinfo.email',
            'https://www.googleapis.com/auth/userinfo.profile',
            'https://www.googleapis.com/auth/classroom.coursework.me.readonly',
            'https://www.googleapis.com/auth/classroom.coursework.students.readonly',
            'https://www.googleapis.com/auth/classroom.courseworkmaterials.readonly',
            'https://www.googleapis.com/auth/classroom.announcements.readonly',
            'https://www.googleapis.com/auth/classroom.topics.readonly',
            Drive::DRIVE_READONLY,
            Calendar::CALENDAR_READONLY,
            'https://www.googleapis.com/auth/forms.body.readonly',
        ]);
        $this->client = $client;
        return $this->client;
    }

    /**
     * Client with a usable token loaded (web session or refresh-token auth).
     *
     * @return Client
     * @throws Exception If there is no usable token available.
     */
    private function get_authenticated_client(): Client {
        $client = $this->get_client();
        // Cron path: authenticate_with_refresh_token() already loaded a token
        // into the client without touching the session.
        if ($client->getAccessToken() && !$client->isAccessTokenExpired()) {
            return $client;
        }
        if (!$this->has_token()) {
            throw new Exception('No Google access token available');
        }
        return $client;
    }

    /**
     * Classroom service (lazy).
     *
     * @return Classroom
     * @throws Exception If there is no usable token in the session.
     */
    protected function get_classroom(): Classroom {
        if ($this->classroom === null) {
            $this->classroom = new Classroom($this->get_authenticated_client());
        }
        return $this->classroom;
    }

    /**
     * Drive service (lazy).
     *
     * @return Drive
     * @throws Exception If there is no usable token in the session.
     */
    private function get_drive(): Drive {
        if ($this->drive === null) {
            $this->drive = new Drive($this->get_authenticated_client());
        }
        return $this->drive;
    }

    /**
     * Converts a library model to a plain associative array.
     *
     * @param  object $model Library model.
     * @return array
     */
    private static function to_array(object $model): array {
        return json_decode(json_encode($model->toSimpleObject()), true);
    }

    /**
     * Maps a Drive file model to the plugin metadata DTO.
     *
     * @param  object $file Drive file model.
     * @return stdClass
     */
    private function file_meta(object $file): stdClass {
        return (object) [
            'id' => $file->getId(),
            'name' => (string) $file->getName(),
            'mimetype' => (string) $file->getMimeType(),
            'weblink' => (string) $file->getWebViewLink(),
            'size' => (int) $file->getSize(),
        ];
    }
}
