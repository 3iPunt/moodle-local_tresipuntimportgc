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
 * Import Courses GC.
 *
 * @package    local_tresipuntimportgc
 * @subpackage tresipuntimportgc
 * @copyright  2021 Tresipunt
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use local_tresipuntimportgc\external\course_external;
use local_tresipuntimportgc\output\import_page;
use local_tresipuntimportgc\providers\gclassroom;

require_once('../../config.php');
require_once('./lib.php');

global $PAGE, $OUTPUT;

require_login();

$has_capability = has_capability('local/tresipuntimportgc:import',  context_system::instance());

$title = get_string('import_page', 'local_tresipuntimportgc');

$PAGE->set_context(context_system::instance());
$PAGE->set_url('/local/tresipuntimportgc/import.php');
$PAGE->set_title($title);
$PAGE->set_heading($title);
$createcourses = optional_param('courses', false, PARAM_BOOL);
$output = $PAGE->get_renderer('local_tresipuntimportgc');

echo $OUTPUT->header();

if ($has_capability) {
    if (!$createcourses) {
        $provider = new gclassroom();
        $page = new import_page($provider);
        echo $output->render($page);
    } else if(!empty($_COOKIE)) {
        $courses = json_decode($_COOKIE["coursesConfig"]);
        if (count($courses) > 0) {
            $service = new course_external();
            @ini_set('zlib.output_compression',0);
            @ini_set('implicit_flush',1);
            @ob_end_clean();
            set_time_limit(0);
            ob_start();
            flush();
            foreach($courses as $course) {
                $shortname = $course->shortname;
                if ($shortname === '') {
                    $shortname = str_replace(' ', '_', normalize_str($course->fullname));
                }
                $service::create_course($course->providerid, $course->fullname, $shortname, (int)$course->categoryid, $course->visible === 'on');
            }
            ob_end_clean();
        } else {
            // TODO add notification for error, and charge de course list again
            echo 'Ningún curso seleccionado';
        }
    } else {
        // TODO error browser cookies or js
        throw new moodle_exception(
            'error browser cookies or js'
        );
    }
} else {
    throw new moodle_exception(
        get_string('not_capability', 'local_tresipuntimportgc')
    );
}

echo $OUTPUT->footer();
