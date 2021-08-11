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
$string['pluginconfig'] = 'Configuració Tresipunt Importació Google Classroom';
$string['import_page'] = 'Importa Cursos de Google Classroom';
$string['create_page'] = 'Importing Google Classroom Courses';
$string['not_capability'] = 'No tens autorització per importar cursos de Google Classroom';
$string['token'] = 'Access Token';
$string['gcheading'] = 'Google API configuration';
$string['credentialsjson'] = 'Credentials Json';
$string['credentialsjson_help'] = 'Add the content of the file credentials.json.<br>Credentials is a JSON file that you get when you configure the connection API in Google, in the credentials panel, at <a href="https://console.cloud.google.com/apis/credentials" target="_blank">console.cloud.google.com</a>';
$string['clientid'] = 'Client ID';
$string['clientid_help'] = 'This value is obtained by setting the Google Api to <a href="https://console.cloud.google.com/apis/credentials" target="_blank">console.cloud.google.com</a>';
$string['secretkey'] = 'Secret Key';
$string['secretkey_help'] = 'This value is obtained by setting the Google Api to <a href="https://console.cloud.google.com/apis/credentials" target="_blank">console.cloud.google.com</a>';

$string['configimportheading'] = 'General settings for importing courses';
$string['configimportheading_help'] = 'Configuration to be set by default when importing courses. If allowed, users will be able to change this setting for each course individually in the course import panel.';
$string['allowconfig'] = 'Allow user configuration for each course';
$string['allowconfig_help'] = 'If allowed, users will be able to choose the type of settings for each course from the course import panel. If not allowed, all courses will be imported with the general settings.';
$string['importfiles'] = 'Importing files from Google Drive';
$string['importfiles_help'] = 'Select how files will be imported from the GoogleDrive folder for each course';
$string['generategdlink'] = 'Generate a direct link to the GoogleDrive folder in the first section of the course (hidden for students).';
$string['importtoprivatearea'] = 'Import all files from GoogleDrive to the user\'s "Private Files" area in Moodle';
$string['importtonextcloud'] = 'Import all files to NextCloud associated to each course (coming soon)';
$string['notimport'] = 'Do not import';
$string['teacherfolderimportfiles'] = 'Importing teacher files';
$string['teacherfolderimportfiles_help'] = 'Select how the files will be imported from the teacher\'s folder';
$string['teacherfoldergenerategdlink'] = 'Generate a direct link (hidden for students) to the GoogleDrive folder in the first section of the course';
$string['teacherfolderimporttoprivatefiles'] = 'Import all files to the user\'s private files';
$string['teacherfolderimporttonextcloud'] = 'Import all files associated with the user to NextCloud (coming soon)';
$string['googlecalendarimport'] = 'Importing the course calendar';
$string['googlecalendarimport_help'] = 'Select how the course calendar will be imported';
$string['calendargenerategdlink'] = 'Generate a direct link to the calendar in the first section of the course.';
$string['calendarimport'] = 'Import the course calendar into Moodle calendar';

$string['classroom_courses'] = 'Courses available from your Google Classroom account';
$string['selectallcourses'] = 'Select all courses';
$string['create'] = 'Create courses';
$string['createcourses'] = 'Create selected courses';
$string['createcourses_help'] = 'If you continue, the page will reload and the selected courses will start to be generated, showing the trace information. <br>THE OPERATION CANNOT BE STOPPED UNTIL IT HAS ENDED BY ITSELF.';

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
$string['convertdocumentto'] = 'Converting the file <b>{$a->title}</b> to format <b>{$a->format}</b>';

// Traces errors
$string['user_can_not_view_category'] = 'You do not have access to the category "{$a->category}", the course "{$a->course}" shall not be created.';
$string['category_no_exist'] = 'The category with id {$a->categoryid} does not exist, the course "{$a->course}" shall not be created.';
