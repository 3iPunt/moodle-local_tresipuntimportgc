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
 * Provider contract: every access to the external platform goes through here.
 *
 * @package    local_tresipuntimportgc
 * @copyright  2026 3iPunt (contacte@tresipunt.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_tresipuntimportgc\providers;

use local_tresipuntimportgc\responses\response_course;
use local_tresipuntimportgc\responses\response_courses;
use local_tresipuntimportgc\responses\response_data;
use local_tresipuntimportgc\responses\response_folder;
use local_tresipuntimportgc\responses\response_modules;
use local_tresipuntimportgc\responses\response_sections;

/**
 * Strict provider contract.
 *
 * Implementations (e.g. google) encapsulate the third-party client library
 * completely: every method receives and returns plugin types only
 * (responses\* DTOs, arrays of plain data, scalars). No class of the
 * underlying library may appear in a signature nor leak inside a response,
 * so swapping the library (or moving to plain REST) only touches the
 * implementation.
 *
 * @package    local_tresipuntimportgc
 * @copyright  2026 3iPunt (contacte@tresipunt.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class provider {

    // Connection and authentication.

    /**
     * Whether the site-level connection (OAuth client) is configured.
     *
     * @return bool
     */
    abstract public function is_configured(): bool;

    /**
     * Whether the current user session holds a valid access token.
     *
     * @return bool
     */
    abstract public function has_token(): bool;

    /**
     * URL to start the OAuth consent flow for the current user.
     *
     * @return string
     */
    abstract public function get_auth_url(): string;

    /**
     * Exchanges the OAuth callback code for an access token and stores it
     * in the user session.
     *
     * @param  string $code Authorisation code returned by the provider.
     * @return response_data success=true on token stored.
     */
    abstract public function authenticate_with_code(string $code): response_data;

    /**
     * Email of the connected account, if any.
     *
     * @return string|null
     */
    abstract public function get_account_email(): ?string;

    /**
     * Drops the current session token (change account / disconnect).
     *
     * @return void
     */
    abstract public function logout(): void;

    /**
     * Long-lived refresh token of the current session, if Google granted one.
     *
     * @return string|null
     */
    abstract public function get_refresh_token(): ?string;

    /**
     * Authenticates without a web session (cron) using a refresh token.
     *
     * @param  string $refreshtoken Refresh token previously obtained.
     * @return response_data success=true when a fresh access token was obtained.
     */
    abstract public function authenticate_with_refresh_token(string $refreshtoken): response_data;

    // Classroom.

    /**
     * Lists the courses available to the connected account.
     *
     * @return response_courses
     */
    abstract public function get_courses(): response_courses;

    /**
     * Gets one course.
     *
     * @param  string $id Provider course id.
     * @return response_course
     */
    abstract public function get_course(string $id): response_course;

    /**
     * Gets the teacher folder of a course.
     *
     * @param  string $id Provider course id.
     * @return response_folder
     */
    abstract public function get_teacher_folder(string $id): response_folder;

    /**
     * Gets the sections (topics) of a course.
     *
     * @param  string $id Provider course id.
     * @return response_sections
     */
    abstract public function get_sections(string $id): response_sections;

    /**
     * Gets the modules (coursework, materials and announcements) of a course.
     *
     * @param  string $id Provider course id.
     * @return response_modules
     */
    abstract public function get_modules(string $id): response_modules;

    /**
     * Gets the rubric of a Classroom coursework.
     *
     * @param  string $courseid     Classroom course id.
     * @param  string $courseworkid Classroom coursework id.
     * @return response_data
     */
    abstract public function get_rubric(string $courseid, string $courseworkid): response_data;

    // Drive.

    /**
     * Gets the metadata of one Drive file.
     *
     * @param  string $fileid Drive file id.
     * @return response_data data = stdClass{id, name, mimetype, weblink, size}.
     */
    abstract public function get_drive_file(string $fileid): response_data;

    /**
     * Lists the files inside a Drive folder.
     *
     * @param  string $folderid Drive folder id.
     * @return response_data data = stdClass[] (same shape as get_drive_file()).
     */
    abstract public function list_drive_folder(string $folderid): response_data;

    /**
     * Downloads (or exports, for native Google documents) a Drive file into
     * the Moodle file storage.
     *
     * @param  \stdClass $filemeta   File metadata as returned by get_drive_file().
     * @param  array     $filerecord Moodle file record (contextid, component,
     *                               filearea, itemid, filepath, filename, userid).
     * @return response_data success=true and data = stored_file on import;
     *                  success=true and data = null when it already existed;
     *                  success=false with error otherwise.
     */
    abstract public function save_drive_file_to_storage(\stdClass $filemeta, array $filerecord): response_data;

    // Calendar.

    /**
     * Lists the calendar events of a course not linked to course content.
     *
     * @param  string $id Provider course id.
     * @return response_data data = stdClass[]{title, description, timestart, timeduration, location}.
     */
    abstract public function get_calendar_events(string $id): response_data;

    // Forms.

    /**
     * Finds a form by its public (responder) URL and returns a summary.
     *
     * @param  string $formurl Public URL of the form.
     * @return response_data data = stdClass{id, title, description, isquiz} or null if not accessible.
     */
    abstract public function get_form_by_url(string $formurl): response_data;
}
