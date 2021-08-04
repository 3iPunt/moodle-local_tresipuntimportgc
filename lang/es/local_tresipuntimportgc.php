<?php
// This file is part of a plugin for Moodle - http://moodle.org/
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
 * Plugin strings are defined here 'es'.
 *
 * @package     local_tresipuntimportgc
 * @category    string
 * @copyright   2021 Tresipunt
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['pluginname'] = 'Tresipunt Importar Google Classroom';
$string['pluginconfig'] = 'Configurar Tresipunt Importación Google Classroom';
$string['import_page'] = 'Importar Cursos de Google Classroom';
$string['create_page'] = 'Importando Cursos de Google Classroom';
$string['not_capability'] = 'No tiene permisos para importar cursos de Google Classroom';
$string['token'] = 'Access Token';
$string['gcheading'] = 'Google Classroom API';
$string['credentialsjson'] = 'Credenciales en json';
$string['credentialsjson_help'] = 'Añadir el contenido del archivo credentials.json.<br>Las credenciales son un archivo JSON que se obtiene al configurar la API de conexión en Google, en el panel de credenciales, en https://console.developers.google.com/';
$string['clientid'] = 'Cliente ID';
$string['clientid_help'] = 'Corresponde a "client_id" en el archivo json';
$string['secretkey'] = 'Secret Key';
$string['secretkey_help'] = 'Este valor se obtiene al configurar la Api de Google en https://console.cloud.google.com/apis/credentials';

$string['classroom_courses'] = 'Cursos disponibles de tu cuenta de Google Classroom';
$string['selectallcourses'] = 'Seleccionar todos los cursos';
$string['create'] = 'Crear cursos';
$string['createcourses'] = 'Crear cursos seleccionados';
$string['createcourses_help'] = 'Si continúa, se recargará la página y empezarán a generarse los cursos seleccionados, mostrando las información de la traza. <br>NO SE PODRÁ DETENER LA OPERACIÓN HASTA QUE TERMINE POR SÍ MISMA.<br>¿Esta seguro?';

$string['error_client'] = 'Error al generar el cliente';
$string['view_more'] = 'Ver más';
$string['drivefile'] = 'Archivo GoogleDrive';
$string['form'] = 'Formulario GoogleDrive';
$string['link'] = 'Enlace externo';
$string['teacher_folder'] = 'Carpeta del Profesor';
$string['uniquename_course'] = 'Nombre corto';
$string['select_category'] = 'Seleccionar categoría';
$string['course_visible'] = 'Visible para los alumnos';
$string['uniquename_course_help'] = "Se utilizará este valor para el nombre corto del curso. (Debe ser único, sin mayúsculas ni carácteres especiales, ni acentos. Si esto no se cumple, se normalizará el valor automáticamente. Si se deja vacío, se normalizará el nombre del curso).";
$string['select_category_help'] = "Seleccione la categoría donde se creará el curso importado";

// Traces
$string['generatingcourses'] = '<h4 class="alert-heading">GENERANDO CURSOS</h4>';
$string['generatingcoursesfinish'] = '<h4 class="alert-heading">GENERACIÖN DE CURRSOS FINALIZADA</h4>';
$string['generatingcourses_help'] = '<p>Se ha comenzado la creación de cursos.<br>Aunque cierre esta pestaña, seguirán creándose cursos.<br>Podrá ver la información generada tanto desde aquí, como desde el fichero log.txt del plugin.</p><hr>';
$string['shortnamealreadyexist'] = 'El nombre corto <b>{$a}</b> ya está siendo utilizado. Escoja otro nombre corto para este curso y vuelva a intentarlo.';
$string['returntoimport'] = 'Volver a la página de selección de cursos';
$string['timespent'] = 'Tiempo empleado';
$string['memoryusage'] = 'Memoria utilizada';
$string['initdate'] = 'Hora de inicio';
$string['enddate'] = 'Hora de fin';
$string['countcourses'] = 'Número de cursos creados';

// Traces Course
$string['startingcourse'] = 'Comenzando creación del curso <b>{$a}</b>';
$string['recoverycourse'] = 'Curso recuperado de Classroom correctamente';
$string['recoverycourseerror'] = 'ERROR al recuperar el curso de Classroom: <b>{$a}</b>';
$string['coursebasecreated'] = 'Curso Base creado con id: {$a}';
$string['coursebasecreatederror'] = 'ERROR al crear el curso: <b>{$a}</b>';
$string['teacherfoldercreated'] = 'Carpeta del profesor creada correctamente';
$string['teacherfoldererrorcreated'] = 'ERROR al crear la carpeta del profesor';
$string['recoverysections'] = 'Se han recuperado correctamente <b>{$a}</b> secciones del curso';
$string['sectioncreated'] = 'Se ha creado correctamente la sección {$a}';
$string['sectionerrorcreated'] = 'ERROR al crear la sección {$a}';
$string['recoverysectionserror'] = 'ERROR al recuperar las secciones del curso: <b>{$a}</b>';
$string['recoverymodules'] = 'Se han recuperado correctamente <b>{$a}</b> módulos del curso';
$string['modulecreated'] = 'Se ha creado correctamente el módulo del tipo {$a->type} con el título <b>{$a->title}</b>';
$string['moduleerrorcreated'] = 'ERROR al crear el módulo del tipo {$a->type} con el título <b>{$a->title}</b>';
$string['recoverymoduleserror'] = 'ERROR al recuperar los módulos del curso: <b>{$a}</b>';
$string['enrolteacher'] = 'El usuario ha sido matriculado en el curso como profesor';
$string['enrolteachererror'] = 'ERROR al matricular al usuario en el curso como profesor: <b>{$a}</b>';
$string['cleancourse'] = 'Sección inicial del curso normalizada';
$string['cleancourseerror'] = 'ERROR al normalizar la sección inicial del curso: <b>{$a}</b>';
$string['creationcoursecompleted'] = 'Proceso de creación del curso finalizado';
$string['creationcoursecompletederror'] = 'Proceso de creación del curso finalizado CON ERRORES';

// Traces errors
$string['user_can_not_view_category'] = 'No tienes acceso a la categoría "{$a->category}", el curso "{$a->course}" no se creará.';
$string['category_no_exist'] = 'La categoría con id {$a->categoryid} no existe, el curso "{$a->course}" no se creará.';
