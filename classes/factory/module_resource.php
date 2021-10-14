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
 * Class module_resource
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
use local_tresipuntimportgc\providers\google;
use local_tresipuntimportgc\responses\error;
use local_tresipuntimportgc\responses\response_module;
use mod_resource_generator;
use moodle_exception;
use stored_file_creation_exception;

defined('MOODLE_INTERNAL') || die;

/**
 * Class module_resource
 *
 * @package     local_tresipuntimportgc
 * @copyright   2021 Tresipunt
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class module_resource extends module {

    /** @var string Mod Name */
    protected $modname = 'assign';

    /** @var mod_resource_generator Generator */
    protected $generator;

    /** array material */
    protected $material;

    /**
     * constructor.
     *
     * @param string $providersection
     * @param string $title
     * @param string $intro
     * @param bool $visible
     * @param array $material
     * @throws coding_exception
     */
    public function __construct(string $providersection, string $title, string $intro, bool $visible, array $material) {
        parent::__construct('mod_resource', $providersection, $title, $intro, $visible);
        $this->material = $material;
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
            'files' => file_get_unused_draft_itemid()
        ];
        $options = [
            'section' => $this->get_section($course_id),
            'visible' => $this->visible,
            'showdescription' => false
        ];
        $res = $this->generator->create_instance($record, $options);
        if (isset($res)) {
            if (count($this->material) > 0) {
                $this->add_file($res);
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
    private function add_file($res) {
        global $USER;
        $context = context_module::instance($res->cmid);
        $fs = get_file_storage();
        $provider = new google();
        $gdrvieclient = $provider->get_client();
        $tokenjson = json_decode($gdrvieclient->getAccessToken(), true);
        $service = new Google_Service_Drive($gdrvieclient);
        if (array_key_first_compatible($this->material) === 'driveFile') {
            try {
                $file = $service->files->get($this->material['driveFile']['driveFile']['id']);
                import_file(
                    $fs,
                    $file,
                    $service,
                    $context->id,
                    (int)$USER->id,
                    $tokenjson['access_token'],
                    'mod_resource',
                    'content',
                    '/'
                );
            } catch (Exception $e) {
                print "An error occurred: " . $e->getMessage();
            }
        }

    }


}
