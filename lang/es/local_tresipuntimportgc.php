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
$string['pluginconfig'] = 'Ajustes de Tresipunt Importar Google Classroom';
$string['import_page'] = 'Importar clases de Google Classroom';
$string['create_page'] = 'Importando clases de Google Classroom';
$string['not_capability'] = 'No tiene permisos para importar clases de Google Classroom';
$string['token'] = 'Access Token';
$string['gcheading'] = 'Conexión con la API de Google';
$string['gcheading_desc'] = 'Credenciales OAuth 2.0 del proyecto de Google Cloud con el que se accede a las APIs de Classroom, Drive, Calendar y Forms. Cree un cliente OAuth 2.0 de tipo web en el <a href="https://console.cloud.google.com/apis/credentials" target="_blank">panel de credenciales de console.cloud.google.com</a> y añada <code>{$a}/local/tresipuntimportgc/import.php</code> como URI de redirección autorizada.';
$string['credentialsjson'] = 'Credenciales (JSON)';
$string['credentialsjson_help'] = 'Contenido completo del archivo credentials.json del cliente OAuth 2.0, tal como se descarga del <a href="https://console.cloud.google.com/apis/credentials" target="_blank">panel de credenciales</a> del proyecto de Google Cloud.';
$string['clientid'] = 'ID de cliente';
$string['clientid_help'] = 'ID de cliente OAuth 2.0 de la aplicación de Google Cloud. Debe coincidir con el campo <code>client_id</code> del JSON de credenciales anterior.';
$string['secretkey'] = 'Secreto de cliente';
$string['secretkey_help'] = 'Secreto de cliente OAuth 2.0 de la aplicación de Google Cloud. Debe coincidir con el campo <code>client_secret</code> del JSON de credenciales anterior.';

$string['configimportheading'] = 'Opciones de importación por defecto';
$string['configimportheading_help'] = 'Comportamiento que se aplica al importar los cursos. Si se permite la configuración por curso, los usuarios podrán cambiar las opciones de archivos y calendario para cada curso desde la página de importación. La opción de formularios de Google se aplica siempre a todo el sitio.';
$string['allowconfig'] = 'Permitir configuración por curso';
$string['allowconfig_help'] = 'Si se activa, en la página de importación cada usuario podrá elegir, curso a curso, el tratamiento de los archivos de Google Drive y del calendario. Si se desactiva, todos los cursos se importarán con las opciones por defecto de más abajo.';
$string['importfiles'] = 'Archivos de Google Drive';
$string['importfiles_help'] = 'Qué hacer con la carpeta de Google Drive de cada curso importado. Los archivos no se copian dentro del curso de Moodle: pueden enlazarse desde el curso o copiarse al área de archivos privados del usuario que realiza la importación.';
$string['generategdlink'] = 'Añadir un enlace a la carpeta de Google Drive del curso en la primera sección (oculto para los estudiantes)';
$string['importtoprivatearea'] = 'Copiar los archivos de Drive del curso al área de archivos privados del usuario que realiza la importación';
$string['importtonextcloud'] = 'Importar los archivos a NextCloud (no disponible todavía: actualmente equivale a «No importar»)';
$string['notimport'] = 'No importar';
$string['teacherfolderimportfiles'] = 'Importación de archivos del profesor';
$string['teacherfolderimportfiles_help'] = 'Seleccione cómo se importarán los archivos de la carpeta del profesor';
$string['teacherfoldergenerategdlink'] = 'Generar un enlace (oculto para los estudiantes) directo a la carpeta GoogleDrive en la primera sección del curso';
$string['teacherfolderimporttoprivatefiles'] = 'Importar todos los archivos a los archivos privados del usuario';
$string['teacherfolderimporttonextcloud'] = 'Importar todos los archivos a NextCloud asociados al usuario (proximamente)';
$string['googlecalendarimport'] = 'Calendario del curso';
$string['googlecalendarimport_help'] = 'Qué hacer con el calendario de Google de cada curso importado. Solo se importan al calendario de Moodle los eventos sin relación con contenidos del curso.';
$string['calendargenerategdlink'] = 'Añadir un enlace al calendario de Google en la primera sección (sin implementar: actualmente equivale a «No importar»)';
$string['calendarimport'] = 'Importar los eventos del curso al calendario de Moodle';
$string['formsiframegenerate'] = 'Incrustar el formulario de Google original en el curso (etiqueta con el formulario en un iframe)';
$string['formsimport'] = 'Crear una actividad de Moodle a partir del formulario (Cuestionario o Retroalimentación; en desarrollo: la actividad se crea sin preguntas)';
$string['googleformsimport'] = 'Formularios de Google';
$string['googleformsimport_help'] = 'Qué hacer cuando una tarea de Classroom consiste en un único formulario de Google. Esta opción se aplica a todo el sitio y no puede cambiarse por curso.';

$string['classroom_courses'] = 'Clases disponibles de tu cuenta de Google Classroom';
$string['editcourses'] = 'Puede editar los valores por defecto del curso, haciendo clic en el botón con el ícono de lápiz';
$string['selectallcourses'] = 'Seleccionar todas las clases';
$string['create'] = 'Crear cursos';
$string['createcourses'] = 'Crear cursos seleccionados';
$string['createcourses_help'] = 'Si continúa, se recargará la página y empezarán a generarse los cursos seleccionados, mostrando las información de la traza. <br>NO SE PODRÁ DETENER LA OPERACIÓN HASTA QUE TERMINE POR SÍ MISMA.<br>¿Esta seguro?';
$string['changeaccount'] = 'Cambiar cuenta';

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
$string['performancedata'] = 'Datos de ejecución';
$string['courselinks'] = 'Enlaces a los cursos';

// Traces
$string['generatingcourses'] = '<h4 class="alert-heading">GENERANDO CURSOS</h4>';
$string['generatingcoursesfinish'] = '<h4 class="alert-heading">GENERACIÓN DE CURSOS FINALIZADA</h4>';
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

// Traces importfiles
$string['importingfiles'] = 'Importando archivos de Google Drive al Área Privada del usuario';
$string['filesfound'] = 'Se han encontrado {$a} archivos';
$string['importfilesuccess'] = 'El archivo <b>{$a}</b> se ha importado correctamente';
$string['importfilealreadyexist'] = 'Ya se encuentra un archivo con el nombre <b>{$a}</b> en el área privada de este usuario';
$string['importfileerror'] = 'El archivo <b>{$a->name}</b> no se ha podido importar: {$a->error}';
$string['importfileerrorcontent'] = 'El archivo <b>{$a}</b> es un formulario de Google (aún por implementar) o no tiene ningún contenido almacenado en Drive';
$string['emptyurl'] = 'URL vacía';
$string['convertdocumentto'] = 'Convirtiendo el archivo <b>{$a->title}</b> a formato <b>{$a->format}</b>';

// Traces importcalendar
$string['importingcalendar'] = 'Importando Google Calendar del curso al calendario de Moodle';
$string['noteventsfound'] = 'No se han encontrado eventos en el calendario del curso';
$string['eventsfound'] = 'Se han encontrado {$a} eventos en el calendario del curso sin relación con contenidos del curso';
$string['conference'] = 'Reunión en Hangouts';

// Traces errors
$string['user_can_not_view_category'] = 'No tienes acceso a la categoría "{$a->category}", el curso "{$a->course}" no se creará.';
$string['category_no_exist'] = 'La categoría con id {$a->categoryid} no existe, el curso "{$a->course}" no se creará.';

// Pantalla de selección (2.0).
$string['selection_desc'] = 'Elige las clases y su configuración; la importación se ejecuta en segundo plano.';
$string['connect_title'] = 'Importa tus clases de Google Classroom';
$string['connect_intro'] = 'Convierte tus clases de Classroom en cursos de Moodle en tres pasos.';
$string['connect_step1'] = 'Conecta tu cuenta de Google y acepta los permisos de solo lectura.';
$string['connect_step2'] = 'Elige las clases que quieres importar y su configuración.';
$string['connect_step3'] = 'La importación se ejecuta en segundo plano y puedes seguir su progreso.';
$string['connect_button'] = 'Conectar con Google';
$string['connect_revoke'] = 'Los permisos pueden revocarse en cualquier momento desde tu cuenta de Google.';
$string['noconfig_title'] = 'El administrador aún no ha configurado la conexión con Google';
$string['noconfig_desc'] = 'No es posible importar hasta que se registre el cliente OAuth del sitio.';
$string['gotosettings'] = 'Ir a los ajustes del plugin';
$string['searchplaceholder'] = 'Buscar clase por nombre…';
$string['filter_all'] = 'Todas';
$string['filter_active'] = 'Activas';
$string['filter_archived'] = 'Archivadas';
$string['nselected'] = '{$a} seleccionadas';
$string['clearselection'] = 'Limpiar';
$string['importcourses_btn'] = 'Importar cursos seleccionados';
$string['nofilterresults'] = 'Ninguna clase coincide con el filtro actual.';
$string['noclasses_title'] = 'No hay clases en esta cuenta de Google Classroom';
$string['noclasses_desc'] = 'Comprueba que eres profesor o propietario de alguna clase, o prueba con otra cuenta.';
$string['importmodal_title'] = 'Importar {$a} cursos';
$string['importmodal_body'] = 'La importación se pondrá en cola y se ejecutará en segundo plano. Serás redirigido a la pantalla de progreso; puedes cerrar el navegador y volver más tarde.';
$string['importmodal_confirm'] = 'Importar';
$string['importqueued'] = 'La importación se ha puesto en cola.';
$string['importqueue_novalid'] = 'No se ha recibido ningún curso válido para importar.';
// Pantalla de progreso (2.0).
$string['progress_title'] = 'Importación del {$a}';
$string['progress_desc'] = 'Progreso y trazas de cada curso importado.';
$string['launchedby'] = 'Lanzada por {$a}';
$string['gotocourse'] = 'Ir al curso';
$string['traces'] = 'Trazas';
$string['notraces'] = 'Sin trazas todavía.';
$string['status_pending'] = 'Pendiente';
$string['status_running'] = 'En curso';
$string['status_success'] = 'Completado';
$string['status_error'] = 'Error';
$string['status_discarded'] = 'Descartado';
$string['istatus_queued'] = 'En cola';
$string['istatus_running'] = 'En curso';
$string['istatus_completed'] = 'Completada';
$string['istatus_partial'] = 'Con incidencias';
$string['istatus_error'] = 'Error';

$string['tresipuntimportgc:import'] = 'Importar clases de una cuenta de Google Classroom';
$string['tresipuntimportgc:viewreports'] = 'Ver el panel de importaciones de Google Classroom (historial, detalle y trazas)';
$string['import_page_desc_01'] = 'Instrucciones para Importar cursos de Google Classroom';
$string['import_page_desc_02'] = 'Desde esta página podrá importar los cursos de una cuenta de Google Classroom.';
$string['import_page_desc_03'] = 'Para realizar esta operación, deberá dar permisos de su cuenta de Google a nuestra plataforma.';
$string['import_page_desc_04'] = "Al hacer clic en el botón 'Siguiente', se redirigirá a un formulario de autenticación de la cuenta de Google.";
$string['import_page_desc_05'] = "Allí, deberá iniciar sesión con los datos de su cuenta de Google, y Google le mostrará los permisos que necesita nuestra plataforma para realizar la importación. En ese formulario debe aceptar los permisos. Recuerde, estos permisos usted podrá revocarlos en cualquier momento desde su cuenta de Google.";
$string['import_page_desc_06'] = "Si la autenticación con Google es correcta, se le mostrará un listado de los cursos de su cuenta de Google Classroom.";
$string['import_page_desc_07'] = "En caso de error, póngase en contacto con el administrador de la plataforma.";
$string['import_page_desc_08'] = "Siguiente";
