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
$string['not_capability'] = 'You do not have permissions to import classes from Google Classroom';
$string['gcheading'] = 'Google API connection';
$string['gcheading_desc'] = 'OAuth 2.0 credentials of the Google Cloud project used to access the Classroom, Drive, Calendar and Forms APIs. Create an OAuth 2.0 web client in the <a href="https://console.cloud.google.com/apis/credentials" target="_blank">credentials panel of console.cloud.google.com</a> and register the redirect URI shown below.';
$string['clientid'] = 'Client ID';
$string['clientid_help'] = 'OAuth 2.0 client ID of the web client created in the Google Cloud project.';
$string['secretkey'] = 'Client secret';
$string['secretkey_help'] = 'OAuth 2.0 client secret of the web client created in the Google Cloud project.';
$string['connectionstatus'] = 'Connection status';
$string['conn_configured'] = 'Credentials configured';
$string['conn_incomplete'] = 'Incomplete configuration';
$string['redirecturi'] = 'Authorised redirect URI';
$string['copyuri'] = 'Copy';
$string['uricopied'] = 'URI copied to the clipboard.';
$string['testconnection'] = 'Test connection';
$string['testconnection_note'] = 'Opens the import page: connecting with Google from there verifies the credentials.';

$string['configimportheading'] = 'Default import options';
$string['configimportheading_help'] = 'Behaviour applied when importing courses. If per-course configuration is allowed, users can override the Drive files and calendar options for each course from the import page. The Google Forms option always applies to the whole site.';
$string['allowconfig'] = 'Allow per-course configuration';
$string['allowconfig_help'] = 'If enabled, on the import page each user can choose, course by course, how Google Drive files and the course calendar are handled. If disabled, all courses are imported with the default options below.';
$string['importfiles'] = 'Google Drive files';
$string['importfiles_help'] = 'What to do with the Google Drive folder of each imported course. The files are not copied into the Moodle course: they can be linked from the course or copied to the private files area of the user who runs the import.';
$string['generategdlink'] = 'Add a link to the Google Drive folder of the course in the first section (hidden from students)';
$string['importtoprivatearea'] = 'Copy the Drive files of the course to the private files area of the user who runs the import';
$string['notimport'] = 'Do not import';
$string['googlecalendarimport'] = 'Course calendar';
$string['googlecalendarimport_help'] = 'What to do with the Google Calendar of each imported course. Only events not linked to course content are imported into the Moodle calendar.';
$string['calendarimport'] = 'Import the course events into the Moodle calendar';
$string['formsiframegenerate'] = 'Embed the original Google Form in the course (label with the form in an iframe)';
$string['googleformsimport'] = 'Google Forms';
$string['googleformsimport_help'] = 'What to do when a Classroom assignment consists of a single Google Form. This option applies to the whole site and cannot be changed per course.';

$string['logheading'] = 'Import log';
$string['logheading_desc'] = 'Each import stores its per-course traces so they can be reviewed later from the imports panel.';
$string['logretention'] = 'Import history retention (days)';
$string['logretention_help'] = 'Import runs older than this number of days are deleted daily, together with their traces. The Moodle courses already created are never touched. Set 0 to keep the history forever.';
$string['cleanuptask'] = 'Purge old Google Classroom import history';

$string['event_courseimported'] = 'Google Classroom class imported';
$string['event_courseretried'] = 'Google Classroom class import retried';
$string['event_coursediscarded'] = 'Google Classroom class import discarded';

$string['privacy:exportpath'] = 'Google Classroom imports';
$string['privacy:metadata:import'] = 'Import runs launched by each user.';
$string['privacy:metadata:import:userid'] = 'The user who launched the import.';
$string['privacy:metadata:import:googleaccount'] = 'Email of the Google account connected for the import.';
$string['privacy:metadata:import:refreshtoken'] = 'Encrypted OAuth refresh token of the Google account, stored only while the import runs and wiped when it finishes.';
$string['privacy:metadata:import:timecreated'] = 'When the import was launched.';
$string['privacy:metadata:course'] = 'Classes included in each import run and their result.';
$string['privacy:metadata:course:fullname'] = 'Name of the imported Google Classroom class.';
$string['privacy:metadata:course:status'] = 'Final status of the import of the class.';
$string['privacy:metadata:course:timestarted'] = 'When the import of the class started.';
$string['privacy:metadata:course:timefinished'] = 'When the import of the class finished.';
$string['privacy:metadata:log'] = 'Trace lines of each imported class.';
$string['privacy:metadata:log:level'] = 'Level of the trace (info, warning or error).';
$string['privacy:metadata:log:message'] = 'Text of the trace, which can mention class and file names.';
$string['privacy:metadata:log:timecreated'] = 'When the trace was written.';
$string['privacy:metadata:google'] = 'To import the classes, the plugin connects to the Google APIs (Classroom, Drive, Calendar and Forms) on behalf of the user.';
$string['privacy:metadata:google:oauthtoken'] = 'OAuth token of the connected Google account, sent to Google to authorise each API call.';
$string['privacy:metadata:google:account'] = 'Identity of the Google account chosen by the user in the consent screen.';

$string['editcourses'] = 'You can edit the default values of the course by clicking on the button with the pencil icon';
$string['selectallcourses'] = 'Select all classes';
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

// Traces
$string['shortnamealreadyexist'] = 'The short name <b>{$a}</b> is already in use. Choose another short name for this course and try again';

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
$string['retry_notfailed'] = 'Only failed courses can be retried.';
$string['retry_needsconnection'] = 'Connect your Google account again before retrying.';
$string['discard_notpending'] = 'Only pending courses can be discarded.';
$string['newimport'] = 'New import';
$string['bar_success'] = 'completed';
$string['bar_error'] = 'with errors';
$string['bar_running'] = 'in progress';
$string['bar_pending'] = 'pending';
$string['bar_total'] = 'of {$a} courses';
$string['updatednote'] = 'Updated {$a} s ago · polling every few seconds';
$string['run_finished'] = 'Import finished.';
$string['cron_title'] = 'The import is queued but scheduled tasks are not running';
$string['cron_desc'] = 'It will not move forward until the Moodle cron runs again.';
$string['retry'] = 'Retry';
$string['discard'] = 'Discard';
$string['retrymodal_title'] = 'Retry course';
$string['retrymodal_body'] = 'Only «{$a}» will be queued again. It runs in the background with the cron.';
$string['discardmodal_title'] = 'Discard course';
$string['discardmodal_body'] = '«{$a}» will be removed from this import. No Moodle course will be created. You can import it again later.';
$string['summary_title'] = 'Import summary';
$string['createdcourses'] = 'Created courses';
$string['log_all'] = 'All';
$string['log_warnings'] = 'Warnings';
$string['log_errors'] = 'Errors';
$string['witherrors'] = 'errors';
$string['progress_header'] = 'Import progress';
$string['panel_title'] = 'Google Classroom imports';
$string['panel_desc'] = 'History and status of every import on this site.';
$string['panel_searchplaceholder'] = 'Search by user or account…';
$string['panel_empty_title'] = 'No import has been run yet';
$string['panel_empty_desc'] = 'When someone imports Google Classroom classes, the history will show here with its status, who launched it and what failed.';
$string['panel_noresults_title'] = 'No import matches the filters';
$string['panel_noresults_desc'] = 'Try removing the status filter or the search text.';
$string['cronpanel_title'] = 'Scheduled tasks are not running';
$string['col_date'] = 'Date';
$string['col_user'] = 'User';
$string['col_courses'] = 'Courses';
$string['col_status'] = 'Status';
$string['viewdetail'] = 'View detail';
$string['viewpanel'] = 'Import history';
$string['panelpagesize'] = 'Imports per page in the panel';
$string['panelpagesize_help'] = 'Number of import runs shown per page in the imports panel.';
$string['pagingnote'] = '{$a->from}–{$a->to} of {$a->total} imports';

$string['tresipuntimportgc:import'] = 'Import classes from a Google Classroom account';
$string['tresipuntimportgc:viewreports'] = 'View the Google Classroom imports panel (history, detail and traces)';
