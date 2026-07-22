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
 * Plugin strings are defined here 'en'.
 *
 * @package     local_tresipuntimportgc
 * @category    string
 * @copyright   2021 Tresipunt
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['pluginname'] = 'Tresipunt Import Google Classroom';
$string['pluginconfig'] = 'Tresipunt Import Google Classroom settings';
$string['import_page'] = 'Import classes from Google Classroom';
$string['create_page'] = 'Importing Google Classroom classes';
$string['not_capability'] = 'You do not have permissions to import classes from Google Classroom';
$string['token'] = 'Access Token';
$string['gcheading'] = 'Google API connection';
$string['gcheading_desc'] = 'OAuth 2.0 credentials of the Google Cloud project used to access the Classroom, Drive, Calendar and Forms APIs. Create an OAuth 2.0 web client in the <a href="https://console.cloud.google.com/apis/credentials" target="_blank">credentials panel of console.cloud.google.com</a> and add <code>{$a}/local/tresipuntimportgc/import.php</code> as an authorised redirect URI.';
$string['credentialsjson'] = 'Credentials JSON';
$string['credentialsjson_help'] = 'Full content of the credentials.json file of the OAuth 2.0 client, as downloaded from the <a href="https://console.cloud.google.com/apis/credentials" target="_blank">credentials panel</a> of your Google Cloud project.';
$string['clientid'] = 'Client ID';
$string['clientid_help'] = 'OAuth 2.0 client ID of the Google Cloud application. It must match the <code>client_id</code> field of the credentials JSON above.';
$string['secretkey'] = 'Client secret';
$string['secretkey_help'] = 'OAuth 2.0 client secret of the Google Cloud application. It must match the <code>client_secret</code> field of the credentials JSON above.';

$string['configimportheading'] = 'Default import options';
$string['configimportheading_help'] = 'Behaviour applied when importing courses. If per-course configuration is allowed, users can override the Drive files and calendar options for each course from the import page. The Google Forms option always applies to the whole site.';
$string['allowconfig'] = 'Allow per-course configuration';
$string['allowconfig_help'] = 'If enabled, on the import page each user can choose, course by course, how Google Drive files and the course calendar are handled. If disabled, all courses are imported with the default options below.';
$string['importfiles'] = 'Google Drive files';
$string['importfiles_help'] = 'What to do with the Google Drive folder of each imported course. The files are not copied into the Moodle course: they can be linked from the course or copied to the private files area of the user who runs the import.';
$string['generategdlink'] = 'Add a link to the Google Drive folder of the course in the first section (hidden from students)';
$string['importtoprivatearea'] = 'Copy the Drive files of the course to the private files area of the user who runs the import';
$string['importtonextcloud'] = 'Import the files into NextCloud (not available yet: currently equivalent to "Do not import")';
$string['notimport'] = 'Do not import';
$string['teacherfolderimportfiles'] = 'Importing teacher files';
$string['teacherfolderimportfiles_help'] = 'Select how the files will be imported from the teacher\'s folder';
$string['teacherfoldergenerategdlink'] = 'Generate a direct link (hidden for students) to the GoogleDrive folder in the first section of the course';
$string['teacherfolderimporttoprivatefiles'] = 'Import all files to the user\'s private files';
$string['teacherfolderimporttonextcloud'] = 'Import all files associated with the user to NextCloud (coming soon)';
$string['googlecalendarimport'] = 'Course calendar';
$string['googlecalendarimport_help'] = 'What to do with the Google Calendar of each imported course. Only events not linked to course content are imported into the Moodle calendar.';
$string['calendargenerategdlink'] = 'Add a link to the Google Calendar in the first section (not implemented yet: currently equivalent to "Do not import")';
$string['calendarimport'] = 'Import the course events into the Moodle calendar';
$string['formsiframegenerate'] = 'Embed the original Google Form in the course (label with the form in an iframe)';
$string['formsimport'] = 'Create a Moodle activity from the form (Quiz or Feedback; in development: the activity is created without questions)';
$string['googleformsimport'] = 'Google Forms';
$string['googleformsimport_help'] = 'What to do when a Classroom assignment consists of a single Google Form. This option applies to the whole site and cannot be changed per course.';

$string['classroom_courses'] = 'Classes available from your Google Classroom account';
$string['editcourses'] = 'You can edit the default values of the course by clicking on the button with the pencil icon';
$string['selectallcourses'] = 'Select all classes';
$string['create'] = 'Create courses';
$string['createcourses'] = 'Create selected courses';
$string['createcourses_help'] = 'If you continue, the page will reload and the selected courses will start to be generated, showing the trace information. <br>THE OPERATION CANNOT BE STOPPED UNTIL IT HAS ENDED BY ITSELF.';
$string['changeaccount'] = 'Change account';

$string['error_client'] = 'Error when generating the client';
$string['view_more'] = 'View more';
$string['drivefile'] = 'GoogleDrive File';
$string['form'] = 'GoogleDrive Form';
$string['link'] = 'External Link';
$string['teacher_folder'] = 'Teacher Folder';
$string['uniquename_course'] = 'Short name';
$string['select_category'] = 'Select category';
$string['course_visible'] = 'Visible to students';
$string['uniquename_course_help'] = "This value shall be used for the short name of the course. (It must be unique, without capital letters, special characters or accents. If this is not met, the value will be normalised automatically. If left empty, the course name will be normalised).";
$string['select_category_help'] = "Select the category where the imported course will be created";
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

// Selection screen (2.0).
$string['selection_desc'] = 'Choose your classes and their configuration; the import runs in the background.';
$string['connect_title'] = 'Import your Google Classroom classes';
$string['connect_intro'] = 'Turn your Classroom classes into Moodle courses in three steps.';
$string['connect_step1'] = 'Connect your Google account and accept the read-only permissions.';
$string['connect_step2'] = 'Choose the classes you want to import and their configuration.';
$string['connect_step3'] = 'The import runs in the background and you can follow its progress.';
$string['connect_button'] = 'Connect with Google';
$string['connect_revoke'] = 'You can revoke the permissions at any time from your Google account.';
$string['noconfig_title'] = 'The administrator has not configured the Google connection yet';
$string['noconfig_desc'] = 'Importing is not possible until the site OAuth client is registered.';
$string['gotosettings'] = 'Go to the plugin settings';
$string['searchplaceholder'] = 'Search class by name…';
$string['filter_all'] = 'All';
$string['filter_active'] = 'Active';
$string['filter_archived'] = 'Archived';
$string['nselected'] = '{$a} selected';
$string['clearselection'] = 'Clear';
$string['importcourses_btn'] = 'Import selected courses';
$string['nofilterresults'] = 'No class matches the current filter.';
$string['noclasses_title'] = 'There are no classes in this Google Classroom account';
$string['noclasses_desc'] = 'Check that you are a teacher or owner of a class, or try another account.';
$string['importmodal_title'] = 'Import {$a} courses';
$string['importmodal_body'] = 'The import will be queued and executed in the background. You will be redirected to the progress page; you can close the browser and come back later.';
$string['importmodal_confirm'] = 'Import';
$string['importqueued'] = 'The import has been queued.';
$string['importqueue_novalid'] = 'No valid course was received to import.';
// Progress screen (2.0).
$string['progress_title'] = 'Import of {$a}';
$string['progress_desc'] = 'Progress and traces of each imported course.';
$string['launchedby'] = 'Launched by {$a}';
$string['gotocourse'] = 'Go to the course';
$string['traces'] = 'Traces';
$string['notraces'] = 'No traces yet.';
$string['status_pending'] = 'Pending';
$string['status_running'] = 'In progress';
$string['status_success'] = 'Completed';
$string['status_error'] = 'Error';
$string['status_discarded'] = 'Discarded';
$string['istatus_queued'] = 'Queued';
$string['istatus_running'] = 'In progress';
$string['istatus_completed'] = 'Completed';
$string['istatus_partial'] = 'With issues';
$string['istatus_error'] = 'Error';

$string['tresipuntimportgc:import'] = 'Import classes from a Google Classroom account';
$string['tresipuntimportgc:viewreports'] = 'View the Google Classroom imports panel (history, detail and traces)';
$string['import_page_desc_01'] = 'Instructions for Importing courses from Google Classroom';
$string['import_page_desc_02'] = 'From this page you can import courses from a Google Classroom account.';
$string['import_page_desc_03'] = 'To perform this operation, you must give your Google account permissions to our platform.';
$string['import_page_desc_04'] = "By clicking the 'Next' button, you will be redirected to a Google account authentication form.";
$string['import_page_desc_05'] = "There, you will need to log in with your Google account data, and Google will show you the permissions that our platform needs to carry out the import. In that form you must accept the permissions. Remember, these permissions you can revoke at any time from your Google account.";
$string['import_page_desc_06'] = "If the authentication with Google is successful, you will be shown a list of the courses in your Google Classroom account.";
$string['import_page_desc_07'] = "In case of error, contact the platform administrator.";
$string['import_page_desc_08'] = "Next";
