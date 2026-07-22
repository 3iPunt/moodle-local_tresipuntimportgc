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
 * Plugin strings are defined here 'ca'.
 *
 * @package     local_tresipuntimportgc
 * @category    string
 * @copyright   2021 Tresipunt
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['pluginname'] = 'Tresipunt Importar Google Classroom';
$string['pluginconfig'] = 'Paràmetres de Tresipunt Importar Google Classroom';
$string['import_page'] = 'Importa classes de Google Classroom';
$string['create_page'] = 'Important classes de Google Classroom';
$string['not_capability'] = 'No tens autorització per importar classes de Google Classroom';
$string['token'] = 'Access Token';
$string['gcheading'] = 'Connexió amb l\'API de Google';
$string['gcheading_desc'] = 'Credencials OAuth 2.0 del projecte de Google Cloud amb què s\'accedeix a les APIs de Classroom, Drive, Calendar i Forms. Creeu un client OAuth 2.0 de tipus web al <a href="https://console.cloud.google.com/apis/credentials" target="_blank">tauler de credencials de console.cloud.google.com</a> i afegiu-hi <code>{$a}/local/tresipuntimportgc/import.php</code> com a URI de redirecció autoritzada.';
$string['credentialsjson'] = 'Credencials (JSON)';
$string['credentialsjson_help'] = 'Contingut complet del fitxer credentials.json del client OAuth 2.0, tal com es descarrega del <a href="https://console.cloud.google.com/apis/credentials" target="_blank">tauler de credencials</a> del projecte de Google Cloud.';
$string['clientid'] = 'ID de client';
$string['clientid_help'] = 'ID de client OAuth 2.0 de l\'aplicació de Google Cloud. Ha de coincidir amb el camp <code>client_id</code> del JSON de credencials anterior.';
$string['secretkey'] = 'Secret de client';
$string['secretkey_help'] = 'Secret de client OAuth 2.0 de l\'aplicació de Google Cloud. Ha de coincidir amb el camp <code>client_secret</code> del JSON de credencials anterior.';

$string['configimportheading'] = 'Opcions d\'importació per defecte';
$string['configimportheading_help'] = 'Comportament que s\'aplica en importar els cursos. Si es permet la configuració per curs, els usuaris podran canviar les opcions de fitxers i calendari per a cada curs des de la pàgina d\'importació. L\'opció de formularis de Google s\'aplica sempre a tot el lloc.';
$string['allowconfig'] = 'Permet la configuració per curs';
$string['allowconfig_help'] = 'Si s\'activa, a la pàgina d\'importació cada usuari podrà triar, curs a curs, el tractament dels fitxers de Google Drive i del calendari. Si es desactiva, tots els cursos s\'importaran amb les opcions per defecte de més avall.';
$string['importfiles'] = 'Fitxers de Google Drive';
$string['importfiles_help'] = 'Què cal fer amb la carpeta de Google Drive de cada curs importat. Els fitxers no es copien dins del curs de Moodle: es poden enllaçar des del curs o copiar-se a l\'àrea de fitxers privats de l\'usuari que fa la importació.';
$string['generategdlink'] = 'Afegeix un enllaç a la carpeta de Google Drive del curs a la primera secció (ocult per als estudiants)';
$string['importtoprivatearea'] = 'Copia els fitxers de Drive del curs a l\'àrea de fitxers privats de l\'usuari que fa la importació';
$string['importtonextcloud'] = 'Importa els fitxers a NextCloud (encara no disponible: actualment equival a «No importar»)';
$string['notimport'] = 'No importar';
$string['teacherfolderimportfiles'] = 'Importing teacher files';
$string['teacherfolderimportfiles_help'] = 'Select how the files will be imported from the teacher\'s folder';
$string['teacherfoldergenerategdlink'] = 'Generate a direct link (hidden for students) to the GoogleDrive folder in the first section of the course';
$string['teacherfolderimporttoprivatefiles'] = 'Import all files to the user\'s private files';
$string['teacherfolderimporttonextcloud'] = 'Import all files associated with the user to NextCloud (coming soon)';
$string['googlecalendarimport'] = 'Calendari del curs';
$string['googlecalendarimport_help'] = 'Què cal fer amb el calendari de Google de cada curs importat. Només s\'importen al calendari de Moodle els esdeveniments sense relació amb continguts del curs.';
$string['calendargenerategdlink'] = 'Afegeix un enllaç al calendari de Google a la primera secció (sense implementar: actualment equival a «No importar»)';
$string['calendarimport'] = 'Importa els esdeveniments del curs al calendari de Moodle';
$string['formsiframegenerate'] = 'Incrusta el formulari de Google original al curs (etiqueta amb el formulari en un iframe)';
$string['formsimport'] = 'Crea una activitat de Moodle a partir del formulari (Qüestionari o Retroacció; en desenvolupament: l\'activitat es crea sense preguntes)';
$string['googleformsimport'] = 'Formularis de Google';
$string['googleformsimport_help'] = 'Què cal fer quan una tasca de Classroom consisteix en un únic formulari de Google. Aquesta opció s\'aplica a tot el lloc i no es pot canviar per curs.';

$string['classroom_courses'] = 'Classes disponibles del teu compte de Google Classroom';
$string['editcourses'] = 'Podeu editar els valors predeterminats del curs fent clic al botó amb la icona de llapis';
$string['selectallcourses'] = 'Selecciona totes les classes';
$string['create'] = 'Create courses';
$string['createcourses'] = 'Create selected courses';
$string['createcourses_help'] = 'If you continue, the page will reload and the selected courses will start to be generated, showing the trace information. <br>THE OPERATION CANNOT BE STOPPED UNTIL IT HAS ENDED BY ITSELF.';
$string['changeaccount'] = 'Change account';

$string['error_client'] = 'Error when generating the client';
$string['view_more'] = 'Veure més';
$string['drivefile'] = 'Arxiu GoogleDrive';
$string['form'] = 'Formulario GoogleDrive';
$string['link'] = 'Enllaç extern';
$string['teacher_folder'] = "Carpeta de l'Professor";
$string['uniquename_course'] = 'Nom curt';
$string['select_category'] = 'Seleccionar categoria';
$string['course_visible'] = 'Visible per als alumnes';
$string['uniquename_course_help'] = "Aquest valor s’utilitzarà per al nom curt del curs. (Ha de ser únic, sense majúscules, caràcters especials ni accents. Si no es compleix, el valor es normalitzarà automàticament. Si es deixa buit, el nom del curs es normalitzarà).";
$string['select_category_help'] = "Seleccioneu la categoria on es crearà el curs importat";
$string['performancedata'] = 'Implementation data';
$string['courselinks'] = 'Links to the courses';

// Traces
$string['generatingcourses'] = '<h4 class="alert-heading">GENERATING COURSES</h4>';
$string['generatingcoursesfinish'] = '<h4 class="alert-heading">GENERATION OF COURSES COMPLETED</h4>';
$string['generatingcourses_help'] = '<p>The creation of courses has begun.<br>Even if you close this tab, courses will still be created.<br>You can see the information generated both from here and from the log.txt file of the plugin.</p><hr>';
$string['shortnamealreadyexist'] = 'The short name <b>{$a}</b> is already in use. Choose another short name for this course and try again';
$string['returntoimport'] = 'Back to course selection page';
$string['timespent'] = 'Time spent';
$string['memoryusage'] = 'Memory used';
$string['initdate'] = 'Start time';
$string['enddate'] = 'End time';
$string['countcourses'] = 'Number of courses created';

// Traces Course
$string['startingcourse'] = 'Beginning course creation <b>{$a}</b>';
$string['recoverycourse'] = 'Course retrieved from Classroom correctly';
$string['recoverycourseerror'] = 'ERROR when retrieving the Classroom course: <b>{$a}</b>';
$string['coursebasecreated'] = 'Base course created with id: {$a}';
$string['coursebasecreatederror'] = 'ERROR when creating the course: <b>{$a}</b>';
$string['teacherfoldercreated'] = 'Teacher\'s folder successfully created';
$string['teacherfoldererrorcreated'] = 'ERROR when creating the teacher\'s folder';
$string['recoverysections'] = 'They have been successfully recovered <b>{$a}</b> sections of the course';
$string['sectioncreated'] = 'The section has been successfully created {$a}';
$string['sectionerrorcreated'] = 'ERROR when creating the section {$a}';
$string['recoverysectionserror'] = 'ERROR when retrieving course sections: <b>{$a}</b>';
$string['recoverymodules'] = 'They have been successfully recovered <b>{$a}</b> course modules';
$string['modulecreated'] = 'The module with type {$a->type} and title <b>{$a->title}</b> has been successfully created';
$string['moduleerrorcreated'] = 'ERROR when creating the module of type {$a->type} and title <b>{$a->title}</b>';
$string['recoverymoduleserror'] = 'ERROR when retrieving course modules: <b>{$a}</b>';
$string['enrolteacher'] = 'The user has been enrolled in the course as a teacher';
$string['enrolteachererror'] = 'ERROR when enrolling the user in the course as a teacher: <b>{$a}</b>';
$string['cleancourse'] = 'Standardised initial course section';
$string['cleancourseerror'] = 'ERROR when normalising the initial section of the course: <b>{$a}</b>';
$string['creationcoursecompleted'] = 'Course creation process completed';
$string['creationcoursecompletederror'] = 'Course creation process completed WITH ERRORS';

// Traces importfiles
$string['importingfiles'] = 'Importing files from Google Drive to the user\'s Private Area';
$string['filesfound'] = '{$a} files have been found';
$string['importfilesuccess'] = 'The file <b>{$a}</b> has been successfully imported';
$string['importfilealreadyexist'] = 'A file with the name <b>{$a}</b> is already in this user\'s private area';
$string['importfileerror'] = 'The file <b>{$a->name}</b> could not be imported: {$a->error}';
$string['importfileerrorcontent'] = 'File <b>{$a}</b> has no content stored in Drive';
$string['emptyurl'] = 'Empty URL';
$string['convertdocumentto'] = 'Converting the file <b>{$a->title}</b> to format <b>{$a->format}</b>';

// Traces importcalendar
$string['importingcalendar'] = 'Importing Google Calendar from the course to the Moodle calendar';
$string['noteventsfound'] = 'No events found in course calendar';
$string['eventsfound'] = '{$a} events have been found in the course calendar unrelated to course content';
$string['conference'] = 'Hangouts meeting';

// Traces errors
$string['user_can_not_view_category'] = 'You do not have access to the category "{$a->category}", the course "{$a->course}" shall not be created.';
$string['category_no_exist'] = 'The category with id {$a->categoryid} does not exist, the course "{$a->course}" shall not be created.';

// Pantalla de selecció (2.0).
$string['selection_desc'] = "Tria les classes i la seva configuració; la importació s'executa en segon pla.";
$string['connect_title'] = 'Importa les teves classes de Google Classroom';
$string['connect_intro'] = 'Converteix les teves classes de Classroom en cursos de Moodle en tres passos.';
$string['connect_step1'] = 'Connecta el teu compte de Google i accepta els permisos de només lectura.';
$string['connect_step2'] = 'Tria les classes que vols importar i la seva configuració.';
$string['connect_step3'] = "La importació s'executa en segon pla i pots seguir-ne el progrés.";
$string['connect_button'] = 'Connecta amb Google';
$string['connect_revoke'] = 'Els permisos es poden revocar en qualsevol moment des del teu compte de Google.';
$string['noconfig_title'] = "L'administrador encara no ha configurat la connexió amb Google";
$string['noconfig_desc'] = "No és possible importar fins que es registri el client OAuth del lloc.";
$string['gotosettings'] = 'Vés als paràmetres del connector';
$string['searchplaceholder'] = 'Cerca la classe pel nom…';
$string['filter_all'] = 'Totes';
$string['filter_active'] = 'Actives';
$string['filter_archived'] = 'Arxivades';
$string['nselected'] = '{$a} seleccionades';
$string['clearselection'] = 'Neteja';
$string['importcourses_btn'] = 'Importa els cursos seleccionats';
$string['nofilterresults'] = 'Cap classe coincideix amb el filtre actual.';
$string['noclasses_title'] = 'No hi ha classes en aquest compte de Google Classroom';
$string['noclasses_desc'] = "Comprova que ets professor o propietari d'alguna classe, o prova amb un altre compte.";
$string['importmodal_title'] = 'Importa {$a} cursos';
$string['importmodal_body'] = "La importació es posarà a la cua i s'executarà en segon pla. Seràs redirigit a la pantalla de progrés; pots tancar el navegador i tornar més tard.";
$string['importmodal_confirm'] = 'Importa';
$string['importqueued'] = 'La importació s\'ha posat a la cua.';
$string['importqueue_novalid'] = 'No s\'ha rebut cap curs vàlid per importar.';
// Pantalla de progrés (2.0).
$string['progress_title'] = 'Importació del {$a}';
$string['progress_desc'] = 'Progrés i traces de cada curs importat.';
$string['launchedby'] = 'Llançada per {$a}';
$string['gotocourse'] = 'Vés al curs';
$string['traces'] = 'Traces';
$string['notraces'] = 'Encara no hi ha traces.';
$string['status_pending'] = 'Pendent';
$string['status_running'] = 'En curs';
$string['status_success'] = 'Completat';
$string['status_error'] = 'Error';
$string['status_discarded'] = 'Descartat';
$string['istatus_queued'] = 'A la cua';
$string['istatus_running'] = 'En curs';
$string['istatus_completed'] = 'Completada';
$string['istatus_partial'] = 'Amb incidències';
$string['istatus_error'] = 'Error';

$string['tresipuntimportgc:import'] = "Importa cursos d'un compte de Google Classroom";
$string['tresipuntimportgc:viewreports'] = "Veure el tauler d'importacions de Google Classroom (historial, detall i traces)";
$string['import_page_desc_01'] = 'Instruccions per a Importa cursos de Google Classroom';
$string['import_page_desc_02'] = "Des d'aquesta pàgina podrà importar els cursos d'un compte de Google Classroom.";
$string['import_page_desc_03'] = "Per realitzar aquesta operació, haurà de donar permisos del seu compte de Google a la nostra plataforma.";
$string['import_page_desc_04'] = "A l'fer clic al botó 'Següent', es redirigirà a un formulari d'autenticació del compte de Google.";
$string['import_page_desc_05'] = "Allà, haurà d'iniciar sessió amb les dades del seu compte de Google, i Google li mostrarà els permisos que necessita la nostra plataforma per realitzar la importació. En aquest formulari ha d'acceptar els permisos. Recordeu, aquests permisos vostè podrà revocar en qualsevol moment des del seu compte de Google.";
$string['import_page_desc_06'] = "Si l'autenticació amb Google és correcta, se li mostrarà un llistat dels cursos del seu compte de Google Classroom.";
$string['import_page_desc_07'] = "En cas d'error, poseu-vos en contacte amb l'administrador de la plataforma.";
$string['import_page_desc_08'] = "Següent";
