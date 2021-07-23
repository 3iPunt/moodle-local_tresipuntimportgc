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
class import_page implements renderable, templatable {

    /** @var Google_Client */
    private $client;
    private $courses;

    /**
     * import_page constructor.
     *
     */
    public function __construct(Google_Client $client) {
        $this->client = $client;
    }

    /**
     * Export for template
     *
     * @param renderer_base $output
     * @return stdClass
     */
    public function export_for_template(renderer_base $output): stdClass {
        $this->courses = gprovider::get_classroom_courses($this->client);
        //print_object($this->courses);
        // TODO empty courses.
        $data = new stdClass();
        $data->coursesavailables = $this->courses;
        return $data;
    }
}
