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
 * Class factory
 *
 * @package     local_tresipuntimportgc
 * @copyright   2021 3iPunt (contacte@tresipunt.com)
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_tresipuntimportgc\factory;

use coding_exception;
use dml_exception;
use local_tresipuntimportgc\local\trace_router;
use local_tresipuntimportgc\providers\provider;
use local_tresipuntimportgc\responses\errors;
use local_tresipuntimportgc\responses\response;
use moodle_exception;

/**
 * Class factory
 *
 * @package     local_tresipuntimportgc
 * @copyright   2021 3iPunt (contacte@tresipunt.com)
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class factory {

    /** @var provider Provider */
    protected $provider;

    /**
     * constructor.
     *
     * @param provider $provider
     */
    public function __construct(provider $provider) {
        $this->provider = $provider;
    }

    /**
     * Create Course.
     *
     * @param string $providerid
     * @param int $categoryid
     * @param string $fullname
     * @param string $shortname
     * @param bool $visible
     * @param int $importfiles
     * @return response
     * @throws coding_exception
     * @throws dml_exception
     * @throws moodle_exception
     */
    public function create_course(
        string $providerid, int $categoryid, string $fullname, string $shortname, bool $visible, int $importfiles
    ): response {
        trace_router::trace('startingcourse', 'light', $fullname);
        section::reset_map();
        $errors = [];
        $res = $this->provider->get_course($providerid);
        if ($res->success) {
            trace_router::trace('recoverycourse', 'success', null);
            $course = $res->data;
            // Create Course.
            $createres = $course->create_course($categoryid, $fullname, $shortname, $visible);
            if ($createres->success) {
                $courseid = (int)$createres->data->get_id();
                trace_router::trace('coursebasecreated', 'success', $courseid);
                // TODO add cover image if a non-generic Classroom image is associated with it
                // Create Teacher Resource if config.
                if ($importfiles === 0) {
                    // Descargar la carpeta del profesor a una carpeta del curso,
                    // oculta para estudiantes (E10.8): los ficheros viven en Moodle.
                    $restf = $this->provider->get_teacher_folder($providerid);
                    if ($restf->success) {
                        $resfiles = $this->provider->list_drive_folder($restf->data->get_providerid());
                        $files = $resfiles->success ? $resfiles->data : [];
                        // Nombre traducido al idioma del usuario que lanza la
                        // importación (no el nombre interno que trae Google).
                        $folder = new module_teacher_folder(
                            get_string('teacher_folder', 'local_tresipuntimportgc'), $files, $this->provider);
                        $folder->create($courseid);
                        trace_router::trace('teacherfoldercreated', 'success', null);
                    } else {
                        $errors[] = $restf->error;
                        trace_router::trace('teacherfoldererrorcreated', 'warning', null);
                    }
                }
                // Create Sections.
                $ressections = $this->provider->get_sections($providerid);
                if ($ressections->success) {
                    trace_router::trace('recoverysections', 'success', count($ressections->data));
                    foreach ($ressections->data as $section) {
                        if (!is_null($section)) {
                            $ressect = $section->create($courseid);
                            if ($ressect->success) {
                                trace_router::trace('sectioncreated', 'success', $ressect->data->get_name());
                            } else {
                                $errors[] = $ressect->error;
                                trace_router::trace('sectionerrorcreated', 'warning', $ressect->data->get_name());
                            }
                        }
                    }
                    // Create Modules.
                    $resmods = $this->provider->get_modules($providerid);
                    if ($resmods->success) {
                        trace_router::trace('recoverymodules', 'success', count($resmods->data));
                        // TODO sort mods by Classroom appearance, they now come unordered.
                        foreach ($resmods->data as $mod) {
                            if (!is_null($mod)) {
                                $resmod = $mod->create($courseid);
                                if ($resmod->success) {
                                    trace_router::trace('modulecreated', 'success',
                                        ['type' => $resmod->data->get_modname(),
                                            'title' => $resmod->data->get_title()]);
                                } else {
                                    $errors[] = $resmod->error;
                                    trace_router::trace('moduleerrorcreated', 'warning',
                                        ['type' => $mod->get_modname(), 'title' => $mod->get_title()]);
                                }
                            }
                        }
                    } else {
                        $errors[] = $resmods->error;
                        trace_router::trace('recoverymoduleserror', 'warning', $resmods->error->to_string());
                    }
                    // Enrol teacher.
                    $resenrol = $course->enrol_user_as_teacher();
                    if ($resenrol->success) {
                        trace_router::trace('enrolteacher', 'success', null);
                    } else {
                        $errors[] = $resenrol->error;
                        trace_router::trace('enrolteachererror', 'warning', $resenrol->error->to_string());
                    }
                    // Clean intro sections.
                    $resclean = $course->clean_sections_intro();
                    if ($resclean->success) {
                        trace_router::trace('cleancourse', 'success', null);
                    } else {
                        $errors[] = $resclean->error;
                        trace_router::trace('cleancourseerror', 'warning', $resclean->error->to_string());
                    }
                    trace_router::trace('creationcoursecompleted', 'info', null);
                    // Response.
                    return new response(
                        true,
                        $res->data->get_id(),
                        count($errors) > 0 ? new errors('00004', 'NOTICE_WITH_ERRORS', $errors) : null);
                }
                $errors[] = $ressections->error;
                trace_router::trace('recoverysectionserror', 'warning', $ressections->error->to_string());
                trace_router::trace('creationcoursecompletederror', 'danger', null);
                return new response(false, $courseid, new errors('00003', 'WARNING_GET_SECTIONS', $errors));
            }
            $errors[] = $createres->error;
            trace_router::trace('coursebasecreatederror', 'warning', $createres->error->to_string());
            trace_router::trace('creationcoursecompletederror', 'danger', null);
            return new response(false, '', new errors('00002', 'ERROR_CREATE', $errors));
        }
        $errors[] = $res->error;
        trace_router::trace('recoverycourseerror', 'warning', $res->error->to_string());
        trace_router::trace('creationcoursecompletederror', 'danger', null);
        mtrace(PHP_EOL . '*** FIN CON ERRORES ***');
        return new response(false, '', new errors('00001', 'ERROR_GET', $errors));
    }

}
