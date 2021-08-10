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
 * @copyright   2021 Tresipunt
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_tresipuntimportgc\factory;

use coding_exception;
use dml_exception;
use local_tresipuntimportgc\providers\provider;
use local_tresipuntimportgc\responses\error_errors;
use local_tresipuntimportgc\responses\errors;
use local_tresipuntimportgc\responses\response;
use moodle_exception;

defined('MOODLE_INTERNAL') || die;

global $CFG;
require_once($CFG->dirroot . '/local/tresipuntimportgc/lib.php');

/**
 * Class factory
 *
 * @package     local_tresipuntimportgc
 * @copyright   2021 Tresipunt
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class factory  {

    /** @var provider Provider */
    protected $provider;
    /** @var int|null time */
    private static $time;

    /**
     * constructor.
     *
     * @param provider $provider
     */
    public function __construct(provider $provider) {
        self::$time = microtime(true);
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
    function create_course(
        string $providerid, int $categoryid, string $fullname, string $shortname, bool $visible, int $importfiles
    ): response {
        print_trace('startingcourse', 'light', $fullname, self::$time);
        //mtrace('*** INICIO IMPORTACIÓN DEL CURSO ***' . PHP_EOL);
        $errors = [];
        $res = $this->provider->get_course($providerid);
        if ($res->success) {
            print_trace('recoverycourse', 'success', null, self::$time);
            //mtrace('RECUPERACIÓN DEL CURSO: OK');
            $course = $res->data;
            // Create Course.
            $createres = $course->create_course($categoryid, $fullname, $shortname, $visible);
            if ($createres->success) {
                $courseid = (int)$createres->data->get_id();
                print_trace('coursebasecreated', 'success', $courseid, self::$time);
                //mtrace('CREACIÓN DEL CURSO: OK');
                // Create Teacher Resource if config.
                if ($importfiles === 0) {
                    $restf = $this->provider->get_teacher_folder($providerid);
                    if ($restf->success) {
                        $course->create_teacher_folder($restf->data->title, $restf->data->link);
                        print_trace('teacherfoldercreated', 'success', null, self::$time);
                    } else {
                        $errors[] = $restf->error;
                        print_trace('teacherfoldererrorcreated', 'warning', null, self::$time);
                        //mtrace('  CREACIÓN DE LA CARPETA DEL PROFESOR: ERROR - ' . $restf->error->to_string());
                    }
                }
                // Create Sections.
                $ressections = $this->provider->get_sections($providerid);
                if ($ressections->success) {
                    print_trace('recoverysections', 'success', count($ressections->data), self::$time);
                    //mtrace('  RECUPERACIÓN DE LAS SECCIONES: OK - (' . count($ressections->data) . ')');
                    foreach ($ressections->data as $section) {
                        if (!is_null($section)) {
                            $ressect = $section->create($courseid);
                            if ($ressect->success) {
                                print_trace('sectioncreated', 'success', $ressect->data->get_name(), self::$time);
                                //mtrace('    CREACIÓN DE SECCIÓN: OK - ' . $ressect->data->get_name());
                            } else {
                                $errors[] = $ressect->error;
                                print_trace('sectionerrorcreated', 'warning', $ressect->data->get_name(), self::$time);
                                //mtrace('    CREACIÓN DE SECCIÓN: ERROR - ' . $ressect->error->to_string());
                            }
                        }
                    }
                    // Create Modules.
                    $resmods = $this->provider->get_modules($providerid);
                    if ($resmods->success) {
                        print_trace('recoverymodules', 'success', count($resmods->data), self::$time);
                        //mtrace('  RECUPERACIÓN DE LOS MÓDULOS: OK - (' . count($resmods->data) . ')');
                        // TODO sort mods by Classroom appearance, they now come unordered.
                        foreach ($resmods->data as $mod) {
                            if (!is_null($mod)) {
                                $resmod = $mod->create($courseid);
                                if ($resmod->success) {
                                    print_trace('modulecreated', 'success', ['type' => $resmod->data->get_modname(), 'title' => $resmod->data->get_title()], self::$time);
                                    //mtrace('    CREACIÓN DE MÓDULO: OK - (' . $resmod->data->get_modname() . ') ' . $resmod->data->get_title());
                                } else {
                                    $errors[] = $resmod->error;
                                    print_trace('moduleerrorcreated', 'warning', ['type' => $mod->get_modname(), 'title' => $mod->get_title()], self::$time);
                                    //mtrace('    CREACIÓN DE MÓDULO: ERROR - (' . $mod->get_modname() . ') - ' . $resmod->error->to_string());
                                }
                            }
                        }
                    } else {
                        $errors[] = $resmods->error;
                        print_trace('recoverymoduleserror', 'warning', $resmods->error->to_string(), self::$time);
                        //mtrace('  RECUPERACIÓN DE LOS MÓDULOS: ERROR - ' . $resmods->error->to_string());
                    }
                    // Enrol teacher.
                    $resenrol = $course->enrol_user_as_teacher();
                    if ($resenrol->success) {
                        print_trace('enrolteacher', 'success', null, self::$time);
                        //mtrace('  MATRICULACIÓN PROFESOR: OK');
                    } else {
                        $errors[] = $resenrol->error;
                        print_trace('enrolteachererror', 'warning', $resenrol->error->to_string(), self::$time);
                        //mtrace('  MATRICULACIÓN PROFESOR: ERROR - ' . $resenrol->error->to_string());
                    }
                    // Clean intro sections.
                    $resclean = $course->clean_sections_intro();
                    if ($resclean->success) {
                        print_trace('cleancourse', 'success', null, self::$time);
                        //mtrace('  LIMPIEZA CURSO: OK');
                    } else {
                        $errors[] = $resclean->error;
                        print_trace('cleancourseerror', 'warning', $resclean->error->to_string(), self::$time);
                        //mtrace('  LIMPIEZA CURSO: ERROR - ' . $resclean->error->to_string());
                    }
                    print_trace('creationcoursecompleted', 'primaty', null, self::$time);
                    //mtrace(PHP_EOL . '*** FIN COMPLETADO ***');
                    // Response.
                    return new response(
                        true,
                        $res->data->get_id(),
                        count($errors) > 0 ? new errors('00004', 'NOTICE_WITH_ERRORS', $errors) : null);
                }
                $errors[] = $ressections->error;
                print_trace('recoverysectionserror', 'warning', $ressections->error->to_string(), self::$time);
                //mtrace('RECUPERACIÓN DE LAS SECCIONES: ERROR - ' . $ressections->error->to_string());
                print_trace('creationcoursecompletederror', 'danger', null, self::$time);
                //mtrace(PHP_EOL . '*** FIN CON ERRORES ***');
                return new response(false, $courseid, new errors('00003', 'WARNING_GET_SECTIONS', $errors));
            }
            $errors[] = $createres->error;
            print_trace('coursebasecreatederror', 'warning', $createres->error->to_string(), self::$time);
            //mtrace('CREACIÓN DEL CURSO: ERROR - ' . $createres->error->to_string());
            print_trace('creationcoursecompletederror', 'danger', null, self::$time);
            //mtrace(PHP_EOL . '*** FIN CON ERRORES ***');
            return new response(false,'', new errors('00002', 'ERROR_CREATE', $errors));
        }
        $errors[] = $res->error;
        print_trace('recoverycourseerror', 'warning', $res->error->to_string(), self::$time);
        //mtrace('RECUPERACIÓN DEL CURSO: ERROR - ' . $res->error->to_string());
        print_trace('creationcoursecompletederror', 'danger', null, self::$time);
        mtrace(PHP_EOL . '*** FIN CON ERRORES ***');
        return new response(false,'', new errors('00001', 'ERROR_GET', $errors));
    }

}
