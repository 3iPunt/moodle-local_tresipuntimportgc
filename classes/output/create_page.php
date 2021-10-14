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

use coding_exception;
use dml_exception;
use file_exception;
use Google_Exception;
use invalid_parameter_exception;
use local_tresipuntimportgc\external\course_external;
use local_tresipuntimportgc\external\importcalendar_external;
use local_tresipuntimportgc\external\importfiles_external;
use moodle_exception;
use moodle_url;
use ReflectionException;
use renderable;
use renderer_base;
use stdClass;
use stored_file_creation_exception;
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

    /** @var string[]|null Courses */
    private $courses;

    /**
     * import_page constructor.
     *
     * @param string[] $courses
     */
    public function __construct(array $courses) {
        $this->courses = $courses;
    }

    /**
     * Export for template
     *
     * @param renderer_base $output
     * @return stdClass
     * @throws Google_Exception
     * @throws ReflectionException
     * @throws coding_exception
     * @throws dml_exception
     * @throws file_exception
     * @throws invalid_parameter_exception
     * @throws stored_file_creation_exception
     * @throws moodle_exception
     */
    public function export_for_template(renderer_base $output): stdClass {
        global $DB;
        $time = microtime(true);
        $initdate = date('H:i:s');
        $countcourses = 0;
        $coursesids = [];
        $coursestitle = [];
        if (count($this->courses) > 0) {
            $courseservice = new course_external();
            @ini_set('zlib.output_compression',0);
            @ini_set('implicit_flush',1);
            @ob_end_clean();
            set_time_limit(0);
            ob_start();
            flush();
            print_trace('generatingcourses_help', 'info', null, $time);
            echo "<script>
const resize_ob = new ResizeObserver(function() {
    window.scrollTo(0, document.getElementById('page-local-tresipuntimportgc-import').scrollHeight)
});
resize_ob.observe(document.querySelector('#region-main'));
</script>";
            echo '<div id="content_traces">';
            foreach($this->courses as $key => $course) {
                $coursedata = json_decode($_COOKIE[$course]);
                $shortname = $coursedata->shortname;
                if ($shortname === '') {
                    $shortname = str_replace(' ', '_', normalize_str($coursedata->fullname));
                }
                $coursedata->importfiles = $coursedata->importfiles ?? (int)get_config('local_tresipuntimportgc', 'importfiles');
                $coursedata->calendarimport = $coursedata->calendarimport ?? (int)get_config('local_tresipuntimportgc', 'calendarimport');
                if ($DB->get_record('course', ['shortname' => $shortname]) === false) {
                    // TODO refactor with html_writer??? (the content is not known at any time)
                    // I hate having to do these things, but it is not possible to do it with mustache, the traces is displayed thanks to ob_flush()
                    // a mustache is called at the end of the process
                    echo '<div class="card">';
                    echo '<div class="card-header" id="heading_' . $coursedata->providerid . '">';
                    echo '<h5 class="mb-0">';
                    echo '<button class="btn btn-link" data-toggle="collapse" data-target="#gcid_' . $coursedata->providerid . '" aria-expanded="true" aria-controls="' . $coursedata->providerid . '">';
                    echo '<h4>' . $coursedata->fullname . '</h4>';
                    echo '</button>';
                    echo '</h5>';
                    echo '</div>';
                    echo '<div id="gcid_' . $coursedata->providerid . '" class="collapse show" aria-labelledby="heading_' . $coursedata->providerid . '" data-parent="#content_traces">';
                        $courseresponse = $courseservice::create_course(
                            $coursedata->providerid,
                            $coursedata->fullname,
                            $shortname,
                            (int)$coursedata->categoryid,
                            $coursedata->visible === 'on',
                            $coursedata->importfiles
                        );
                        if ((int)$coursedata->importfiles === 1) {
                            print_trace('importingfiles', 'info');
                            $importfilesservice = new importfiles_external();
                            $importfilesservice::importfiles($coursedata->providerid, (int)$courseresponse['id'], $shortname);
                        }
                        if ((int)$coursedata->calendarimport === 1) {
                            print_trace('importingcalendar', 'info');
                            $importfilesservice = new importcalendar_external();
                            $importfilesservice::importcalendar($coursedata->providerid, (int)$courseresponse['id']);
                        }
                    // TODO import calendar right here (it can't be anywhere else).
                    echo '</div>';
                    echo '</div>';
                    $coursesids[$key] = $courseresponse['id'];
                    $coursestitle[$key] = $coursedata->fullname;
                    $countcourses++;
                } else {
                    print_trace('shortnamealreadyexist', 'danger', $shortname, $time);
                }
            }
            echo '<script>resize_ob.disconnect()</script>';
            echo '</div>';
            print_trace('generatingcoursesfinish', 'info', $time);
            ob_end_clean();
        } else {
            // TODO add notification for error, and charge de course list again
            echo 'Ningún curso seleccionado';
        }
        $data = new stdClass();
        $data->returnulr = (new moodle_url('/local/tresipuntimportgc/import.php'))->out();
        $data->timespent = round(microtime(true) - $time, 2) . 's';
        $data->memoryusage = display_size(memory_get_usage());
        $data->initdate = $initdate;
        $data->countcourses = $countcourses;
        $data->enddate = date('H:i:s');
        $data->courselinks = [];
        foreach($coursesids as $key => $coursesid) {
            $data->courselinks[$key]['courselink'] = (new moodle_url('/course/view.php', ['id' => $coursesid]))->out();
            $data->courselinks[$key]['coursetitle'] = $coursestitle[$key];
        }
        $data->courselinks = array_values($data->courselinks);
        // TODO exporter
        return $data;
    }
}
