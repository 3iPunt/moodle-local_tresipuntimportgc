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
 * Class module_folder
 *
 * @package     local_tresipuntimportgc
 * @copyright   2021 Tresipunt
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_tresipuntimportgc\factory;

use coding_exception;
use context_module;
use dml_exception;
use file_exception;
use Google_Exception;
use Google_Http_Request;
use Google_Service_Drive;
use local_tresipuntimportgc\providers\gclassroom;
use local_tresipuntimportgc\responses\error;
use local_tresipuntimportgc\responses\response_module;
use mod_folder_generator;
use moodle_exception;
use stored_file_creation_exception;

defined('MOODLE_INTERNAL') || die;

/**
 * Class module_folder
 *
 * @package     local_tresipuntimportgc
 * @copyright   2021 Tresipunt
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class module_folder extends module {

    /** @var string Mod Name */
    protected $modname = 'folder';

    /** @var mod_folder_generator Generator */
    protected $generator;

    /** array material */
    protected $materials;

    /**
     * constructor.
     *
     * @param string $providersection
     * @param string $title
     * @param string $intro
     * @param bool $visible
     * @param array $materials
     * @throws coding_exception
     */
    public function __construct(string $providersection, string $title, string $intro, bool $visible, array $materials) {
        parent::__construct('mod_folder', $providersection, $title, $intro, $visible);
        $this->materials = $materials;
    }

    /**
     * Create.
     *
     * @param int $course_id
     * @return response_module
     * @throws dml_exception
     */
    public function create(int $course_id): response_module {
        $course = get_course($course_id);
        $record = [
            'course' => $course,
            'name' => $this->title,
            'intro' => $this->intro,
            'introformat' => FORMAT_HTML,
            'showexpanded' => true,
            'files' => file_get_unused_draft_itemid()
        ];
        $options = [
            'section' => $this->get_section($course_id),
            'visible' => $this->visible,
            'showdescription' => false
        ];
        $res = $this->generator->create_instance($record, $options);
        if (isset($res)) {
            if (count($this->materials) > 0) {
                $this->add_files($res);
            }
            return new response_module(true, $this, null);
        }

        return new response_module(false, null, new error('12000', 'MODULE_NOT_CREATED'));
    }

    /**
     * @param $res
     * @throws Google_Exception
     * @throws file_exception
     * @throws moodle_exception
     * @throws stored_file_creation_exception
     * @throws coding_exception
     */
    private function add_files($res) {
        $context = context_module::instance($res->cmid);
        $fs = get_file_storage();
        $filerecord = array(
            'contextid' => $context->id,
            'component' => 'mod_folder',
            'filearea'  => 'content',
            'itemid'    => 0,
            'filepath'  => '/'
        );
        $provider = new gclassroom();
        $gdrvieclient = $provider->get_client();
        $tokenjson = json_decode($gdrvieclient->getAccessToken(), true);
        $service = new Google_Service_Drive($gdrvieclient);
        foreach ($this->materials as $material) {
            if (array_key_first_compatible($material) === 'driveFile') {
                try {
                    $file = $service->files->get($material['driveFile']['driveFile']['id']);
                    $downloadUrl = $file->getDownloadUrl();
                    if ($downloadUrl) {
                        $filerecord['filename'] = $file->getTitle();
                        $request = new Google_Http_Request($downloadUrl, 'GET', null, null);
                        $httpRequest = $service->getClient()->getAuth()->authenticatedRequest($request);
                        $response = $httpRequest->getResponseHttpCode();
                        if (($response === 200) && $fs->get_file($context->id, 'mod_assign', ASSIGN_INTROATTACHMENT_FILEAREA, 0, '/', $file->getTitle()) === false) {
                            $fs->create_file_from_string($filerecord, $httpRequest->getResponseBody());
                        }
                    } else {
                        // Files of Google Apps
                        $mimetype = $file->getMimeType();
                        $exportlinks = $file->getExportLinks();
                        $url = '';
                        $format = '';
                        switch ($mimetype) {
                            case 'application/vnd.google-apps.document':
                                $format = '.docx';
                                print_trace('convertdocumentto', 'info', ['title' => $file->getTitle(), 'format' => $format]);
                                $filerecord['filename'] = $file->getTitle() . $format; // .odt ??
                                $url = $exportlinks['application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
                                $url .= '&access_token=' . $tokenjson['access_token'];
                                break;
                            case 'application/vnd.google-apps.presentation':
                                $format = '.pptx';
                                print_trace('convertdocumentto', 'info', ['title' => $file->getTitle(), 'format' => $format]);
                                $filerecord['filename'] = $file->getTitle() . $format; // .odp ??
                                $url = $exportlinks['application/vnd.openxmlformats-officedocument.presentationml.presentation'];
                                $url .= '&access_token=' . $tokenjson['access_token'];
                                break;
                            case 'application/vnd.google-apps.spreadsheet':
                                $format = '.xlsx';
                                print_trace('convertdocumentto', 'info', ['title' => $file->getTitle(), 'format' => $format]);
                                $filerecord['filename'] = $file->getTitle() . $format; // .ods ??
                                $url = $exportlinks['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'];
                                $url .= '&access_token=' . $tokenjson['access_token'];
                                break;
                            case 'application/vnd.google-apps.drawing':
                                $format = '.svg';
                                print_trace('convertdocumentto', 'info', ['title' => $file->getTitle(), 'format' => $format]);
                                $filerecord['filename'] = $file->getTitle() . $format;
                                $url = $exportlinks['image/svg+xml'];
                                $url .= '&access_token=' . $tokenjson['access_token'];
                                break;
                            case 'application/vnd.google-apps.form':
                                // TODO QUIZ or feedback
                                print_trace('importfileerrorcontent', 'error', $file->getTitle());
                                break;
                            default:
                                $format = '.pdf';
                                print_trace('convertdocumentto', 'info', ['title' => $file->getTitle(), 'format' => $format]);
                                $filerecord['filename'] = $file->getTitle() . $format;
                                $url = $exportlinks['application/pdf'];
                                $url .= '&access_token=' . $tokenjson['access_token'];
                                break;
                        }
                        // TODO forms in drive?? how to convert?? template feedback?
                        if (($mimetype !== 'application/vnd.google-apps.form') && $fs->get_file($context->id, 'mod_assign', ASSIGN_INTROATTACHMENT_FILEAREA, 0, '/', $file->getTitle() . $format) === false) {
                            $fs->create_file_from_url($filerecord, $url);
                        }
                    }
                } catch (Exception $e) {
                    print "An error occurred: " . $e->getMessage();
                }
            }
        }
    }


}
