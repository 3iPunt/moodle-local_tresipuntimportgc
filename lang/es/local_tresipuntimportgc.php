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
 * @copyright   2021 3iPunt (contacte@tresipunt.com)
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['pluginname'] = 'Tresipunt Importar Google Classroom';
$string['pluginconfig'] = 'Ajustes de Tresipunt Importar Google Classroom';
$string['import_page'] = 'Importar clases de Google Classroom';
$string['not_capability'] = 'No tiene permisos para importar clases de Google Classroom';
$string['gcheading'] = 'Conexión con la API de Google';
$string['gcheading_desc'] = 'Credenciales OAuth 2.0 del proyecto de Google Cloud con el que se accede a las APIs de Classroom, Drive, Calendar y Forms. Cree un cliente OAuth 2.0 de tipo web en el <a href="https://console.cloud.google.com/apis/credentials" target="_blank">panel de credenciales de console.cloud.google.com</a> y registre la URI de redirección que se muestra más abajo.';
$string['clientid'] = 'ID de cliente';
$string['clientid_help'] = 'ID de cliente OAuth 2.0 del cliente web creado en el proyecto de Google Cloud.';
$string['secretkey'] = 'Secreto de cliente';
$string['secretkey_help'] = 'Secreto de cliente OAuth 2.0 del cliente web creado en el proyecto de Google Cloud.';
$string['connectionstatus'] = 'Estado de la conexión';
$string['conn_configured'] = 'Credenciales configuradas';
$string['conn_incomplete'] = 'Configuración incompleta';
$string['redirecturi'] = 'URI de redirección autorizada';
$string['copyuri'] = 'Copiar';
$string['uricopied'] = 'URI copiada al portapapeles.';
$string['testconnection'] = 'Probar conexión';
$string['testconnection_note'] = 'Abre la página de importación: conectar allí con Google verifica las credenciales.';

$string['configimportheading'] = 'Opciones de importación por defecto';
$string['configimportheading_help'] = 'Comportamiento que se aplica al importar los cursos. Si se permite la configuración por curso, los usuarios podrán cambiar las opciones de archivos y calendario para cada curso desde la página de importación. La opción de formularios de Google se aplica siempre a todo el sitio.';
$string['allowconfig'] = 'Permitir configuración por curso';
$string['allowconfig_help'] = 'Si se activa, en la página de importación cada usuario podrá elegir, curso a curso, el tratamiento de los archivos de Google Drive y del calendario. Si se desactiva, todos los cursos se importarán con las opciones por defecto de más abajo.';
$string['importfiles'] = 'Carpeta del profesor';
$string['importfiles_help'] = 'Qué hacer con la carpeta de Google Drive del profesor de la clase. No afecta a los archivos adjuntos de las actividades, que siempre se importan dentro de su actividad: solo gobierna la carpeta de la clase, que puede enlazarse desde el curso o copiarse al área de archivos privados del usuario que realiza la importación.';
$string['generategdlink'] = 'Descargar la carpeta de Drive del profesor a una carpeta del curso, oculta para los estudiantes';
$string['importtoprivatearea'] = 'Copiar los archivos de Drive del curso al área de archivos privados del usuario que realiza la importación';
$string['notimport'] = 'No importar';
$string['googlecalendarimport'] = 'Calendario del curso';
$string['googlecalendarimport_help'] = 'Qué hacer con el calendario de Google de cada curso importado. Solo se importan al calendario de Moodle los eventos sin relación con contenidos del curso.';
$string['calendarimport'] = 'Importar los eventos del curso al calendario de Moodle';
$string['formsiframegenerate'] = 'Incrustar el formulario de Google original en el curso (etiqueta con el formulario en un iframe)';
$string['importindividual'] = 'Contenidos asignados a estudiantes concretos';
$string['importindividual_help'] = 'Qué hacer con las tareas, materiales y anuncios que en Classroom se asignaron a estudiantes concretos en vez de a toda la clase.';
$string['importindividualhidden'] = 'Importar ocultos para los estudiantes, con una nota para el profesor';
$string['individual_note'] = 'Nota para el profesor: en Google Classroom este contenido estaba asignado a estudiantes concretos. Se ha importado oculto para los estudiantes.';
$string['googleformsimport'] = 'Formularios de Google';
$string['googleformsimport_help'] = 'Qué hacer cuando una tarea de Classroom consiste en un único formulario de Google. Esta opción se aplica a todo el sitio y no puede cambiarse por curso. Actualmente el formulario se incrusta en una etiqueta; la conversión a un cuestionario o retroalimentación de Moodle con sus preguntas está prevista para una versión futura.';

$string['logheading'] = 'Registro de importaciones';
$string['logheading_desc'] = 'Cada importación guarda sus trazas por curso para poder revisarlas más tarde desde el panel de importaciones.';
$string['logretention'] = 'Retención del historial de importaciones (días)';
$string['logretention_help'] = 'Las importaciones con más días que este valor se eliminan a diario, junto con sus trazas. Los cursos de Moodle ya creados no se tocan nunca. Con 0 el historial se conserva para siempre.';
$string['cleanuptask'] = 'Purgar el historial antiguo de importaciones de Google Classroom';

$string['course_room'] = 'Aula: {$a}';
$string['announcement_title'] = 'Anuncio';
$string['rubric_name'] = 'Rúbrica (importada de Google Classroom)';
$string['event_courseimported'] = 'Clase de Google Classroom importada';
$string['event_courseretried'] = 'Importación de clase de Google Classroom reintentada';
$string['event_coursediscarded'] = 'Importación de clase de Google Classroom descartada';

$string['privacy:exportpath'] = 'Importaciones de Google Classroom';
$string['privacy:metadata:import'] = 'Importaciones lanzadas por cada usuario.';
$string['privacy:metadata:import:userid'] = 'El usuario que lanzó la importación.';
$string['privacy:metadata:import:googleaccount'] = 'Correo de la cuenta de Google conectada para la importación.';
$string['privacy:metadata:import:refreshtoken'] = 'Refresh token OAuth cifrado de la cuenta de Google, almacenado solo mientras la importación se ejecuta y destruido al terminar.';
$string['privacy:metadata:import:timecreated'] = 'Cuándo se lanzó la importación.';
$string['privacy:metadata:course'] = 'Clases incluidas en cada importación y su resultado.';
$string['privacy:metadata:course:fullname'] = 'Nombre de la clase de Google Classroom importada.';
$string['privacy:metadata:course:status'] = 'Estado final de la importación de la clase.';
$string['privacy:metadata:course:timestarted'] = 'Cuándo empezó la importación de la clase.';
$string['privacy:metadata:course:timefinished'] = 'Cuándo terminó la importación de la clase.';
$string['privacy:metadata:log'] = 'Trazas de cada clase importada.';
$string['privacy:metadata:log:level'] = 'Nivel de la traza (información, aviso o error).';
$string['privacy:metadata:log:message'] = 'Texto de la traza, que puede mencionar nombres de clases y de ficheros.';
$string['privacy:metadata:log:timecreated'] = 'Cuándo se escribió la traza.';
$string['privacy:metadata:usermodified'] = 'El usuario que modificó el registro por última vez.';
$string['privacy:metadata:google'] = 'Para importar las clases, el plugin se conecta a las APIs de Google (Classroom, Drive, Calendar y Forms) en nombre del usuario.';
$string['privacy:metadata:google:oauthtoken'] = 'Token OAuth de la cuenta de Google conectada, enviado a Google para autorizar cada llamada a la API.';
$string['privacy:metadata:google:account'] = 'Identidad de la cuenta de Google elegida por el usuario en la pantalla de consentimiento.';

$string['editcourses'] = 'Puede editar los valores por defecto del curso, haciendo clic en el botón con el ícono de lápiz';
$string['selectallcourses'] = 'Seleccionar todas las clases';
$string['changeaccount'] = 'Cambiar cuenta';

$string['error_client'] = 'Error al generar el cliente';
$string['error_oauthstate'] = 'No se ha podido verificar la respuesta de Google (estado no válido o caducado). Vuelve a conectar.';
$string['view_more'] = 'Ver más';
$string['drivefile'] = 'Archivo GoogleDrive';
$string['form'] = 'Formulario GoogleDrive';
$string['link'] = 'Enlace externo';
$string['teacher_folder'] = 'Carpeta del Profesor';
$string['teacher_folder_intro'] = 'Archivos de la carpeta del profesor importados desde Google Classroom.';
$string['uniquename_course'] = 'Nombre corto';
$string['shortname_in_use'] = 'Nombre corto en uso';
$string['shortname_in_use_help'] = 'Este nombre corto ya lo usa otro curso; cámbialo antes de importar o el curso fallará.';
$string['select_category'] = 'Seleccionar categoría';
$string['course_visible'] = 'Visible para los alumnos';

// Traces.
$string['shortnamealreadyexist'] = 'El nombre corto <b>{$a}</b> ya está siendo utilizado. Escoja otro nombre corto para este curso y vuelva a intentarlo.';

// Traces of the course factory.
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

// Traces of the Drive file import.
$string['importingfiles'] = 'Importando archivos de Google Drive al Área Privada del usuario';
$string['filesfound'] = 'Se han encontrado {$a} archivos';
$string['importfilesuccess'] = 'El archivo <b>{$a}</b> se ha importado correctamente';
$string['importfilealreadyexist'] = 'Ya se encuentra un archivo con el nombre <b>{$a}</b> en el área privada de este usuario';
$string['importfileerror'] = 'El archivo <b>{$a->name}</b> no se ha podido importar: {$a->error}';
$string['importfileerrorcontent'] = '<b>{$a}</b> es un formulario de Google: no se descarga como archivo (los formularios se incrustan o enlazan según la configuración de formularios).';
$string['convertdocumentto'] = 'Convirtiendo el archivo <b>{$a->title}</b> a formato <b>{$a->format}</b>';

// Traces of the calendar import.
$string['importingcalendar'] = 'Importando Google Calendar del curso al calendario de Moodle';
$string['noteventsfound'] = 'No se han encontrado eventos en el calendario del curso';
$string['eventsfound'] = 'Se han encontrado {$a} eventos en el calendario del curso sin relación con contenidos del curso';
$string['conference'] = 'Reunión en Hangouts';

// Traces of errors.
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
$string['retry_notfailed'] = 'Solo se pueden reintentar cursos con error.';
$string['retry_needsconnection'] = 'Vuelve a conectar tu cuenta de Google antes de reintentar.';
$string['discard_notpending'] = 'Solo se pueden descartar cursos pendientes.';
$string['newimport'] = 'Nueva importación';
$string['viewimports'] = 'Ver importaciones';
$string['bar_success'] = 'completados';
$string['bar_error'] = 'con error';
$string['bar_running'] = 'en curso';
$string['bar_pending'] = 'pendientes';
$string['bar_total'] = 'de {$a} cursos';
$string['updatednote'] = 'Actualizado hace {$a} s · sondeo cada pocos segundos';
$string['run_finished'] = 'Importación finalizada.';
$string['cron_title'] = 'La importación está en cola pero las tareas programadas no se están ejecutando';
$string['cron_desc'] = 'No avanzará hasta que el cron de Moodle vuelva a funcionar.';
$string['retry'] = 'Reintentar';
$string['discard'] = 'Descartar';
$string['retrymodal_title'] = 'Reintentar curso';
$string['retrymodal_body'] = 'Se volverá a encolar solo «{$a}». Se ejecuta en segundo plano con el cron.';
$string['discardmodal_title'] = 'Descartar curso';
$string['discardmodal_body'] = 'Se quitará «{$a}» de esta importación. No se creará el curso en Moodle. Podrás importarlo más tarde.';
$string['summary_title'] = 'Resumen de la importación';
$string['createdcourses'] = 'Cursos creados';
$string['log_all'] = 'Todo';
$string['log_warnings'] = 'Avisos';
$string['log_errors'] = 'Errores';
$string['witherrors'] = 'errores';
$string['progress_header'] = 'Progreso de la importación';
$string['panel_title'] = 'Importaciones de Google Classroom';
$string['panel_desc'] = 'Historial y estado de todas las importaciones del sitio.';
$string['panel_searchplaceholder'] = 'Buscar por usuario o cuenta…';
$string['panel_empty_title'] = 'Todavía no se ha realizado ninguna importación';
$string['panel_empty_desc'] = 'Cuando alguien importe clases de Google Classroom, aquí verás el histórico con su estado, quién la lanzó y qué falló.';
$string['panel_noresults_title'] = 'Ninguna importación coincide con los filtros';
$string['panel_noresults_desc'] = 'Prueba a quitar el filtro de estado o el texto de búsqueda.';
$string['cronpanel_title'] = 'Las tareas programadas no se están ejecutando';
$string['col_date'] = 'Fecha';
$string['col_user'] = 'Usuario';
$string['col_courses'] = 'Cursos';
$string['col_status'] = 'Estado';
$string['viewdetail'] = 'Ver detalle';
$string['panelpagesize'] = 'Importaciones por página en el panel';
$string['panelpagesize_help'] = 'Número de importaciones que se muestran por página en el panel de importaciones.';
$string['pagingnote'] = '{$a->from}–{$a->to} de {$a->total} importaciones';

$string['tresipuntimportgc:import'] = 'Importar clases de una cuenta de Google Classroom';
$string['tresipuntimportgc:viewreports'] = 'Ver el panel de importaciones de Google Classroom (historial, detalle y trazas)';
