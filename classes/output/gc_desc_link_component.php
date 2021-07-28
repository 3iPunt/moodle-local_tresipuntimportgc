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
 * Class gc_desc_link_component
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
 * Class gc_desc_link_component
 *
 * @package     local_tresipuntimportgc
 * @copyright   2021 Tresipunt
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class gc_desc_link_component implements renderable, templatable {

    /** @var string Thumbnail Url */
    protected $thumbnailurl;

    /** @var string Title Link */
    protected $title;

    /** @var string URL */
    protected $url;

    /**
     * constructor.
     *
     * @param string[] $material
     */
    public function __construct(array $material) {
        $this->thumbnailurl = isset($material['thumbnailUrl']) ? $material['thumbnailUrl'] : '';
        $this->title = isset($material['title']) ? $material['title'] : '';
        $this->url = isset($material['url']) ? $material['url'] : '';
    }

    /**
     * Export for template
     *
     * @param renderer_base $output
     * @return stdClass
     */
    public function export_for_template(renderer_base $output): stdClass {
        $data = new stdClass();
        $data->thumbnailurl = $this->thumbnailurl;
        $data->title = $this->title;
        $data->url = $this->url;
        return $data;
    }
}
