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

use local_tresipuntimportgc\providers\provider;
use local_tresipuntimportgc\responses\error_errors;
use local_tresipuntimportgc\responses\errors;
use local_tresipuntimportgc\responses\response;
use local_tresipuntimportgc\responses\response_course;
use moodle_exception;

defined('MOODLE_INTERNAL') || die;

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
     * @return response
     * @throws moodle_exception
     */
    function create_course(
        string $providerid, int $categoryid, string $fullname, string $shortname, bool $visible
    ): response {
        mtrace('*** INICIO IMPORTACIÓN DEL CURSO ***' . PHP_EOL);
        $errors = [];
        $res = $this->provider->get_course($providerid);
        if ($res->success) {
            mtrace('RECUPERACIÓN DEL CURSO: OK');
            $course = $res->data;
            // Create Course.
            $createres = $course->create_course($categoryid, $fullname, $shortname, $visible);
            if ($createres->success) {
                mtrace('CREACIÓN DEL CURSO: OK');
                $courseid = $createres->data->get_id();
                // Create Teacher Resource.
                $restf = $this->provider->get_teacher_folder($providerid);
                if ($restf->success) {
                    $course->create_teacher_folder($restf->data->title, $restf->data->link);
                    mtrace('  CREACIÓN DE LA CARPETA DEL PROFESOR: OK');
                } else {
                    $errors[] = $restf->error;
                    mtrace('  CREACIÓN DE LA CARPETA DEL PROFESOR: ERROR - ' . $restf->error->to_string());
                }
                // Create Sections.
                $ressections = $this->provider->get_sections($providerid);
                if ($ressections->success) {
                    mtrace('  RECUPERACIÓN DE LAS SECCIONES: OK - (' . count($ressections->data) . ')');
                    foreach ($ressections->data as $section) {
                        if (!is_null($section)) {
                            $ressect = $section->create($courseid);
                            if ($ressect->success) {
                                mtrace('    CREACIÓN DE SECCIÓN: OK - ' . $ressect->data->get_name());
                            } else {
                                $errors[] = $ressect->error;
                                mtrace('    CREACIÓN DE SECCIÓN: ERROR - ' . $ressect->error->to_string());
                            }
                        }
                    }
                    // Create Modules.
                    $resmods = $this->provider->get_modules($providerid);
                    if ($resmods->success) {
                        mtrace('  RECUPERACIÓN DE LOS MÓDULOS: OK - (' . count($resmods->data) . ')');
                        foreach ($resmods->data as $mod) {
                            if (!is_null($mod)) {
                                $resmod = $mod->create($courseid);
                                if ($resmod->success) {
                                    mtrace('    CREACIÓN DE MÓDULO: OK - (' . $resmod->data->get_modname() . ') ' . $resmod->data->get_title());
                                } else {
                                    $errors[] = $resmod->error;
                                    mtrace('    CREACIÓN DE MÓDULO: ERROR - (' . $mod->get_modname() . ') - ' . $resmod->error->to_string());
                                }
                            }
                        }
                    } else {
                        $errors[] = $resmods->error;
                        mtrace('  RECUPERACIÓN DE LOS MÓDULOS: ERROR - ' . $resmods->error->to_string());
                    }
                    // Enrol teacher.
                    $resenrol = $course->enrol_user_as_teacher();
                    if ($resenrol->success) {
                        mtrace('  MATRICULACIÓN PROFESOR: OK');
                    } else {
                        $errors[] = $resenrol->error;
                        mtrace('  MATRICULACIÓN PROFESOR: ERROR - ' . $resenrol->error->to_string());
                    }
                    // Clean intro sections.
                    $resclean = $course->clean_sections_intro();
                    if ($resclean->success) {
                        mtrace('  LIMPIEZA CURSO: OK');
                    } else {
                        $errors[] = $resclean->error;
                        mtrace('  LIMPIEZA CURSO: ERROR - ' . $resclean->error->to_string());
                    }
                    mtrace(PHP_EOL . '*** FIN COMPLETADO ***');
                    // Response.
                    return new response(
                        true,
                        $res->data->get_id(),
                        count($errors) > 0 ? new errors('00004', 'NOTICE_WITH_ERRORS', $errors) : null);

                } else {
                    $errors[] = $ressections->error;
                    mtrace('RECUPERACIÓN DE LAS SECCIONES: ERROR - ' . $ressections->error->to_string());
                    mtrace(PHP_EOL . '*** FIN CON ERRORES ***');
                    return new response(false,'', new errors('00003', 'WARNING_GET_SECTIONS', $errors));
                }
            } else {
                $errors[] = $createres->error;
                mtrace('CREACIÓN DEL CURSO: ERROR - ' . $createres->error->to_string());
                mtrace(PHP_EOL . '*** FIN CON ERRORES ***');
                return new response(false,'', new errors('00002', 'ERROR_CREATE', $errors));
            }
        } else {
            $errors[] = $res->error;
            mtrace('RECUPERACIÓN DEL CURSO: ERROR - ' . $res->error->to_string());
            mtrace(PHP_EOL . '*** FIN CON ERRORES ***');
            return new response(false,'', new errors('00001', 'ERROR_GET', $errors));
        }
    }

}
