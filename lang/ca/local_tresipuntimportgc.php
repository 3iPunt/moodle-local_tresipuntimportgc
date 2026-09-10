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
 * @copyright   2021 3iPunt (contacte@tresipunt.com)
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['allowconfig'] = 'Permet la configuració per curs';
$string['allowconfig_help'] = 'Si s\'activa, a la pàgina d\'importació cada usuari podrà triar, curs a curs, el tractament dels fitxers de Google Drive i del calendari. Si es desactiva, tots els cursos s\'importaran amb les opcions per defecte de més avall.';
$string['announcement_title'] = 'Anunci';
$string['bar_error'] = 'amb error';
$string['bar_pending'] = 'pendents';
$string['bar_running'] = 'en curs';
$string['bar_success'] = 'completats';
$string['bar_total'] = 'de {$a} cursos';
$string['calendarimport'] = 'Importa els esdeveniments del curs al calendari de Moodle';
$string['category_no_exist'] = 'The category with id {$a->categoryid} does not exist, the course "{$a->course}" shall not be created.';
$string['changeaccount'] = 'Change account';
$string['cleancourse'] = 'Standardised initial course section';
$string['cleancourseerror'] = 'ERROR when normalising the initial section of the course: <b>{$a}</b>';
$string['cleanuptask'] = 'Purga l\'historial antic d\'importacions de Google Classroom';
$string['clearselection'] = 'Neteja';
$string['clientid'] = 'ID de client';
$string['clientid_help'] = 'ID de client OAuth 2.0 del client web creat al projecte de Google Cloud.';
$string['col_courses'] = 'Cursos';
$string['col_date'] = 'Data';
$string['col_status'] = 'Estat';
$string['col_user'] = 'Usuari';
$string['conference'] = 'Hangouts meeting';
$string['configimportheading'] = 'Opcions d\'importació per defecte';
$string['configimportheading_help'] = 'Comportament que s\'aplica en importar els cursos. Si es permet la configuració per curs, els usuaris podran canviar les opcions de fitxers i calendari per a cada curs des de la pàgina d\'importació. L\'opció de formularis de Google s\'aplica sempre a tot el lloc.';
$string['conn_configured'] = 'Credencials configurades';
$string['conn_incomplete'] = 'Configuració incompleta';
$string['connect_button'] = 'Connecta amb Google';
$string['connect_intro'] = 'Converteix les teves classes de Classroom en cursos de Moodle en tres passos.';
$string['connect_revoke'] = 'Els permisos es poden revocar en qualsevol moment des del teu compte de Google.';
$string['connect_step1'] = 'Connecta el teu compte de Google i accepta els permisos de només lectura.';
$string['connect_step2'] = 'Tria les classes que vols importar i la seva configuració.';
$string['connect_step3'] = "La importació s'executa en segon pla i pots seguir-ne el progrés.";
$string['connectionstatus'] = 'Estat de la connexió';
$string['convertdocumentto'] = 'Converting the file <b>{$a->title}</b> to format <b>{$a->format}</b>';
$string['copyuri'] = 'Copia';
$string['course_room'] = 'Aula: {$a}';
$string['course_visible'] = 'Visible per als alumnes';
$string['coursebasecreated'] = 'Base course created with id: {$a}';
$string['coursebasecreatederror'] = 'ERROR when creating the course: <b>{$a}</b>';
$string['createdcourses'] = 'Cursos creats';
$string['creationcoursecompleted'] = 'Course creation process completed';
$string['creationcoursecompletederror'] = 'Course creation process completed WITH ERRORS';
$string['cron_desc'] = 'No avançarà fins que el cron de Moodle torni a funcionar.';
$string['cron_title'] = 'La importació és a la cua però les tasques programades no s’executen';
$string['cronpanel_title'] = 'Les tasques programades no s’executen';
$string['discard'] = 'Descarta';
$string['discard_notpending'] = 'Només es poden descartar cursos pendents.';
$string['discardmodal_body'] = 'Es traurà «{$a}» d’aquesta importació. No es crearà el curs a Moodle. Podràs importar-lo més tard.';
$string['discardmodal_title'] = 'Descarta el curs';
$string['drivefile'] = 'Arxiu GoogleDrive';
$string['editcourses'] = 'Podeu editar els valors predeterminats del curs fent clic al botó amb la icona de llapis';
$string['enrolteacher'] = 'The user has been enrolled in the course as a teacher';
$string['enrolteachererror'] = 'ERROR when enrolling the user in the course as a teacher: <b>{$a}</b>';
$string['error_client'] = 'Error when generating the client';
$string['error_oauthstate'] = 'No s\'ha pogut verificar la resposta de Google (estat no vàlid o caducat). Torna a connectar.';
$string['event_coursediscarded'] = 'Importació de classe de Google Classroom descartada';
$string['event_courseimported'] = 'Classe de Google Classroom importada';
$string['event_courseretried'] = 'Importació de classe de Google Classroom reintentada';
$string['eventsfound'] = '{$a} events have been found in the course calendar unrelated to course content';
$string['filesfound'] = '{$a} files have been found';
$string['filter_active'] = 'Actives';
$string['filter_all'] = 'Totes';
$string['filter_archived'] = 'Arxivades';
$string['form'] = 'Formulario GoogleDrive';
$string['formsiframegenerate'] = 'Incrusta el formulari de Google original al curs (etiqueta amb el formulari en un iframe)';
$string['gcheading'] = 'Connexió amb l\'API de Google';
$string['gcheading_desc'] = 'Credencials OAuth 2.0 del projecte de Google Cloud amb què s\'accedeix a les APIs de Classroom, Drive, Calendar i Forms. Creeu un client OAuth 2.0 de tipus web al <a href="https://console.cloud.google.com/apis/credentials" target="_blank">tauler de credencials de console.cloud.google.com</a> i registreu-hi la URI de redirecció que es mostra més avall.';
$string['generategdlink'] = 'Baixa la carpeta de Drive del professor a una carpeta del curs, oculta per als estudiants';
$string['googlecalendarimport'] = 'Calendari del curs';
$string['googlecalendarimport_help'] = 'Què cal fer amb el calendari de Google de cada curs importat. Només s\'importen al calendari de Moodle els esdeveniments sense relació amb continguts del curs.';
$string['googleformsimport'] = 'Formularis de Google';
$string['googleformsimport_help'] = 'Què cal fer quan una tasca de Classroom consisteix en un únic formulari de Google. Aquesta opció s\'aplica a tot el lloc i no es pot canviar per curs. Actualment el formulari s\'incrusta en una etiqueta; la conversió a un qüestionari o retroacció de Moodle amb les seves preguntes està prevista per a una versió futura.';
$string['gotocourse'] = 'Vés al curs';
$string['gotosettings'] = 'Vés als paràmetres del connector';
$string['import_page'] = 'Importa classes de Google Classroom';
$string['importcourses_btn'] = 'Importa els cursos seleccionats';
$string['importfilealreadyexist'] = 'A file with the name <b>{$a}</b> is already in this user\'s private area';
$string['importfileerror'] = 'The file <b>{$a->name}</b> could not be imported: {$a->error}';
$string['importfileerrorcontent'] = '<b>{$a}</b> és un formulari de Google: no es descarrega com a fitxer (els formularis s\'incrusten o s\'enllacen segons la configuració de formularis).';
$string['importfiles'] = 'Carpeta del professor';
$string['importfiles_help'] = 'Què cal fer amb la carpeta de Google Drive del professor de la classe. No afecta els fitxers adjunts de les activitats, que sempre s\'importen dins la seva activitat: només governa la carpeta de la classe, que es pot enllaçar des del curs o copiar-se a l\'àrea de fitxers privats de l\'usuari que fa la importació.';
$string['importfilesuccess'] = 'The file <b>{$a}</b> has been successfully imported';
$string['importindividual'] = 'Continguts assignats a estudiants concrets';
$string['importindividual_help'] = 'Què cal fer amb les tasques, materials i anuncis que a Classroom es van assignar a estudiants concrets en lloc de a tota la classe.';
$string['importindividualhidden'] = 'Importar ocults per als estudiants, amb una nota per al professor';
$string['importingcalendar'] = 'Importing Google Calendar from the course to the Moodle calendar';
$string['importingfiles'] = 'Importing files from Google Drive to the user\'s Private Area';
$string['importmodal_body'] = "La importació es posarà a la cua i s'executarà en segon pla. Seràs redirigit a la pantalla de progrés; pots tancar el navegador i tornar més tard.";
$string['importmodal_confirm'] = 'Importa';
$string['importmodal_title'] = 'Importa {$a} cursos';
$string['importqueue_novalid'] = 'No s\'ha rebut cap curs vàlid per importar.';
$string['importqueued'] = 'La importació s\'ha posat a la cua.';
$string['importtoprivatearea'] = 'Copia els fitxers de Drive del curs a l\'àrea de fitxers privats de l\'usuari que fa la importació';
$string['individual_note'] = 'Nota per al professor: a Google Classroom aquest contingut estava assignat a estudiants concrets. S\'ha importat ocult per als estudiants.';
$string['istatus_completed'] = 'Completada';
$string['istatus_error'] = 'Error';
$string['istatus_partial'] = 'Amb incidències';
$string['istatus_queued'] = 'A la cua';
$string['istatus_running'] = 'En curs';
$string['launchedby'] = 'Llançada per {$a}';
$string['link'] = 'Enllaç extern';
$string['log_all'] = 'Tot';
$string['log_errors'] = 'Errors';
$string['log_warnings'] = 'Avisos';
$string['logheading'] = 'Registre d\'importacions';
$string['logheading_desc'] = 'Cada importació desa les seves traces per curs per poder revisar-les més tard des del tauler d\'importacions.';
$string['logretention'] = 'Retenció de l\'historial d\'importacions (dies)';
$string['logretention_help'] = 'Les importacions amb més dies que aquest valor s\'eliminen diàriament, juntament amb les seves traces. Els cursos de Moodle ja creats no es toquen mai. Amb 0 l\'historial es conserva per sempre.';
$string['modulecreated'] = 'The module with type {$a->type} and title <b>{$a->title}</b> has been successfully created';
$string['moduleerrorcreated'] = 'ERROR when creating the module of type {$a->type} and title <b>{$a->title}</b>';
$string['newimport'] = 'Nova importació';
$string['noclasses_desc'] = "Comprova que ets professor o propietari d'alguna classe, o prova amb un altre compte.";
$string['noclasses_title'] = 'No hi ha classes en aquest compte de Google Classroom';
$string['noconfig_desc'] = "No és possible importar fins que es registri el client OAuth del lloc.";
$string['noconfig_title'] = "L'administrador encara no ha configurat la connexió amb Google";
$string['nofilterresults'] = 'Cap classe coincideix amb el filtre actual.';
$string['noteventsfound'] = 'No events found in course calendar';
$string['notimport'] = 'No importar';
$string['nselected'] = '{$a} seleccionades';
$string['pagingnote'] = '{$a->from}–{$a->to} de {$a->total} importacions';
$string['panel_desc'] = 'Historial i estat de totes les importacions del lloc.';
$string['panel_empty_desc'] = 'Quan algú importi classes de Google Classroom, aquí veuràs l’històric amb el seu estat, qui la va llançar i què va fallar.';
$string['panel_empty_title'] = 'Encara no s’ha fet cap importació';
$string['panel_noresults_desc'] = 'Prova de treure el filtre d’estat o el text de cerca.';
$string['panel_noresults_title'] = 'Cap importació coincideix amb els filtres';
$string['panel_searchplaceholder'] = 'Cerca per usuari o compte…';
$string['panel_title'] = 'Importacions de Google Classroom';
$string['panelpagesize'] = 'Importacions per pàgina al tauler';
$string['panelpagesize_help'] = 'Nombre d’importacions que es mostren per pàgina al tauler d’importacions.';
$string['pluginconfig'] = 'Paràmetres de Tresipunt Importar Google Classroom';
$string['pluginname'] = 'Tresipunt Importar Google Classroom';
$string['privacy:exportpath'] = 'Importacions de Google Classroom';
$string['privacy:metadata:course'] = 'Classes incloses en cada importació i el seu resultat.';
$string['privacy:metadata:course:fullname'] = 'Nom de la classe de Google Classroom importada.';
$string['privacy:metadata:course:status'] = 'Estat final de la importació de la classe.';
$string['privacy:metadata:course:timefinished'] = 'Quan va acabar la importació de la classe.';
$string['privacy:metadata:course:timestarted'] = 'Quan va començar la importació de la classe.';
$string['privacy:metadata:google'] = 'Per importar les classes, el plugin es connecta a les APIs de Google (Classroom, Drive, Calendar i Forms) en nom de l\'usuari.';
$string['privacy:metadata:google:account'] = 'Identitat del compte de Google triat per l\'usuari a la pantalla de consentiment.';
$string['privacy:metadata:google:oauthtoken'] = 'Token OAuth del compte de Google connectat, enviat a Google per autoritzar cada crida a l\'API.';
$string['privacy:metadata:import'] = 'Importacions llançades per cada usuari.';
$string['privacy:metadata:import:googleaccount'] = 'Correu del compte de Google connectat per a la importació.';
$string['privacy:metadata:import:refreshtoken'] = 'Refresh token OAuth xifrat del compte de Google, emmagatzemat només mentre la importació s\'executa i destruït en acabar.';
$string['privacy:metadata:import:timecreated'] = 'Quan es va llançar la importació.';
$string['privacy:metadata:import:userid'] = 'L\'usuari que va llançar la importació.';
$string['privacy:metadata:log'] = 'Traces de cada classe importada.';
$string['privacy:metadata:log:level'] = 'Nivell de la traça (informació, avís o error).';
$string['privacy:metadata:log:message'] = 'Text de la traça, que pot esmentar noms de classes i de fitxers.';
$string['privacy:metadata:log:timecreated'] = 'Quan es va escriure la traça.';
$string['privacy:metadata:usermodified'] = 'L\'usuari que va modificar el registre per última vegada.';
$string['progress_desc'] = 'Progrés i traces de cada curs importat.';
$string['progress_header'] = 'Progrés de la importació';
$string['progress_title'] = 'Importació del {$a}';
$string['recoverycourse'] = 'Course retrieved from Classroom correctly';
$string['recoverycourseerror'] = 'ERROR when retrieving the Classroom course: <b>{$a}</b>';
$string['recoverymodules'] = 'They have been successfully recovered <b>{$a}</b> course modules';
$string['recoverymoduleserror'] = 'ERROR when retrieving course modules: <b>{$a}</b>';
$string['recoverysections'] = 'They have been successfully recovered <b>{$a}</b> sections of the course';
$string['recoverysectionserror'] = 'ERROR when retrieving course sections: <b>{$a}</b>';
$string['redirecturi'] = 'URI de redirecció autoritzada';
$string['retry'] = 'Reintenta';
$string['retry_needsconnection'] = 'Torna a connectar el teu compte de Google abans de reintentar.';
$string['retry_notfailed'] = 'Només es poden reintentar cursos amb error.';
$string['retrymodal_body'] = 'Només es tornarà a encuar «{$a}». S’executa en segon pla amb el cron.';
$string['retrymodal_title'] = 'Reintenta el curs';
$string['rubric_name'] = 'Rúbrica (importada de Google Classroom)';
$string['run_finished'] = 'Importació finalitzada.';
$string['searchplaceholder'] = 'Cerca la classe pel nom…';
$string['secretkey'] = 'Secret de client';
$string['secretkey_help'] = 'Secret de client OAuth 2.0 del client web creat al projecte de Google Cloud.';
$string['sectioncreated'] = 'The section has been successfully created {$a}';
$string['sectionerrorcreated'] = 'ERROR when creating the section {$a}';
$string['select_category'] = 'Seleccionar categoria';
$string['selectallcourses'] = 'Selecciona totes les classes';
$string['selection_desc'] = "Tria les classes i la seva configuració; la importació s'executa en segon pla.";
$string['shortname_in_use'] = 'Nom curt en ús';
$string['shortname_in_use_help'] = 'Aquest nom curt ja l\'utilitza un altre curs; canvia\'l abans d\'importar o el curs fallarà.';
$string['shortnamealreadyexist'] = 'The short name <b>{$a}</b> is already in use. Choose another short name for this course and try again';
$string['startingcourse'] = 'Beginning course creation <b>{$a}</b>';
$string['status_discarded'] = 'Descartat';
$string['status_error'] = 'Error';
$string['status_pending'] = 'Pendent';
$string['status_running'] = 'En curs';
$string['status_success'] = 'Completat';
$string['summary_title'] = 'Resum de la importació';
$string['teacher_folder'] = 'Carpeta del Professor';
$string['teacher_folder_intro'] = 'Fitxers de la carpeta del professor importats des de Google Classroom.';
$string['teacherfoldercreated'] = 'Teacher\'s folder successfully created';
$string['teacherfoldererrorcreated'] = 'ERROR when creating the teacher\'s folder';
$string['testconnection'] = 'Prova la connexió';
$string['testconnection_note'] = 'Obre la pàgina d\'importació: connectar-hi amb Google verifica les credencials.';
$string['traces'] = 'Traces';
$string['tresipuntimportgc:import'] = "Importa cursos d'un compte de Google Classroom";
$string['tresipuntimportgc:viewreports'] = "Veure el tauler d'importacions de Google Classroom (historial, detall i traces)";
$string['uniquename_course'] = 'Nom curt';
$string['updatednote'] = 'Actualitzat fa {$a} s · sondeig cada pocs segons';
$string['uricopied'] = 'URI copiada al porta-retalls.';






$string['user_can_not_view_category'] = 'You do not have access to the category "{$a->category}", the course "{$a->course}" shall not be created.';
$string['view_more'] = 'Veure més';






$string['viewdetail'] = 'Mostra el detall';
$string['viewimports'] = 'Veure importacions';
$string['witherrors'] = 'errors';
