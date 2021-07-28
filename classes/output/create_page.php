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
 * Class import_page
 *
 * @package    local_tresipuntimportgc
 * @copyright  2021 Tresipunt
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_tresipuntimportgc\output;

use Google_Client;
use Google_Service_Classroom;
use local_tresipuntimportgc\gprovider;
use local_tresipuntimportgc\providers\provider;
use local_tresipuntimportgc\responses\response;
use moodle_exception;
use moodle_url;
use renderable;
use renderer_base;
use stdClass;
use templatable;

defined('MOODLE_INTERNAL') || die;
global $CFG;
require_once($CFG->libdir . '/google/src/Google/autoload.php');
require_once($CFG->libdir . '/google/lib.php');
require_once($CFG->libdir . '/google/src/Google/Service/Drive.php');
/**
 * Class import_page
 *
 * @package     local_tresipuntimportgc
 * @copyright   2021 Tresipunt
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class create_page implements renderable, templatable {

    /** @var int|null Response */
    private $courseid;

    /** @var bool Success */
    private $success;

    /** @var string[] Response */
    private $errors;

    /**
     * import_page constructor.
     *
     * @param string[] $response
     */
    public function __construct(array $response) {
        $this->courseid = isset($response['id']) ? (int)$response['id'] : null;
        if (isset($response['errors']) && $response['errors'] !== '') {
            $this->errors = explode( PHP_EOL, $response['errors']);
        } else {
            $this->errors = [];
        }
        $this->success = $response['success'];
    }

    /**
     * Export for template
     *
     * @param renderer_base $output
     * @return stdClass
     * @throws moodle_exception
     */
    public function export_for_template(renderer_base $output): stdClass {
        $has_errors = false;
        $view_url = '';
        $level = 'danger';
        if ($this->success) {
            $level = 'warning';
            $view_url = new moodle_url('/course/view.php', ['id' => $this->courseid]);
            if (count($this->errors) > 0) {
                $has_errors = true;
            }
        }
        $data = new stdClass();
        $data->view_url = $view_url;
        $data->errors = $this->errors;
        $data->has_errors = $has_errors;
        $data->error_level = $level;
        $data->success = $this->success;
        return $data;
    }
}
