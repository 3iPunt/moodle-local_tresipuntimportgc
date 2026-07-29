<p align="center">
  <img src="pix/tresipunt_logo.svg" alt="Tresipunt" width="160" height="40">
  &nbsp;&nbsp;&nbsp;
  <img src="pix/icon.svg" alt="" width="46" height="40">
</p>

<h1 align="center">Import courses from Google Classroom</h1>

<p align="center">
  <img src="https://img.shields.io/badge/version-2.0.0-informational" alt="Version">
  <a href="https://moodle.org"><img src="https://img.shields.io/badge/Moodle-4.5%20%7C%205.1-orange?logo=moodle" alt="Moodle"></a>
  <img src="https://img.shields.io/badge/PHP-8.1%2B-777BB4?logo=php&logoColor=white" alt="PHP">
  <img src="https://img.shields.io/badge/License-GPL--3.0-green" alt="License">
  <a href="https://tresipunt.com"><img src="https://img.shields.io/badge/made%20by-Tresipunt-F84015" alt="Made by Tresipunt"></a>
</p>

<p align="center"><b>Turn your Google Classroom classes into Moodle courses, with the content inside Moodle.</b></p>

<p align="center"><b>🇬🇧 English</b> · <a href="README.es.md">🇪🇸 Español</a></p>

Local plugin that imports a Google Classroom class as a Moodle course: sections,
materials and coursework, announcements, the teacher folder and the calendar.
Each course is imported by its own background task, with a live progress page and
a history panel. It does not modify Moodle core or the theme.

---

## ✨ What it does

- **Imports the class as a course** — name, section and room in the summary;
  Classroom *topics* become course sections, in their original order.
- **Brings the content into Moodle** — Drive files are downloaded into the course
  (or linked, per setting); links and videos become URL resources.
- **Maps coursework and materials to activities** — multiple-choice question →
  Choice; short question → Feedback; assignment with a rubric → Assignment with
  its rubric; Google Form → label with the form embedded.
- **Announcements → announcements forum**; **teacher folder →** hidden folder
  whose files live in Moodle; **calendar →** course events.
- **Scheduled publication** — the Classroom publication date is kept as an
  "available from" access restriction.
- **Tracking** — live progress page (with retry and discard) and a history panel
  with filters and detail.

## 🔄 What is imported (mapping)

| Google Classroom | Moodle |
|---|---|
| Class | Course (name, section and room in the summary) |
| Topics | Course sections (in order) |
| Attached Drive file | File brought into the course (or a link, per setting) |
| Link / YouTube video | URL resource |
| Multiple-choice question | Choice |
| Short question (ungraded) | Feedback |
| Assignment (with rubric) | Assignment (with its rubric) |
| Google Form | Label with the form embedded |
| Announcements | Discussions in the announcements forum |
| Teacher folder | Hidden folder with the files stored in Moodle |
| Calendar | Course events |
| Scheduled publication | "Available from" access restriction |

**Not imported yet** (planned for future releases):

- Student submissions, grades and comments.
- Enrolments: co-teachers and the students of the class (currently only the
  importing user is enrolled, as a teacher).
- The **questions** inside Google Forms (the form is embedded; it is not turned
  into a quiz or feedback activity with its questions).
- Google Meet links as a videoconference activity (currently kept as a link).

> The class cover image and downloading YouTube videos are not possible due to
> Google API limits.

## ⚙️ How it works

- Connects a Google account over OAuth 2.0 and reads the class with the
  Classroom, Drive, Calendar and Forms APIs.
- Each course is imported by its own **scheduled (adhoc) task**; status and
  traces are stored for the progress page and the panel.
- Content is **brought into Moodle** (not linked to Google) whenever possible, so
  the course does not depend on the Google account after the import.
- It never touches existing courses other than the one it creates; it is disabled
  by removing the capabilities or uninstalling the plugin.

## 📋 Requirements

| Requirement | Version |
|---|---|
| Moodle | 4.5 LTS or 5.1 |
| PHP | 8.1 or later |
| Other plugins | None |
| Moodle cron | Required — imports run as background tasks |
| Google services | A Google Cloud project with the Classroom, Drive, Calendar and Forms APIs enabled, and an OAuth 2.0 *web application* client |

> Every site uses **its own OAuth client**; the steps are below, under *Settings*.

## 🚀 Installation

1. Copy the code into `<MOODLE_ROOT>/local/tresipuntimportgc/`.
2. Complete the installation from **Site administration › Notifications**
   (or on the CLI: `php admin/cli/upgrade.php --non-interactive`).
3. Purge the caches (**Site administration › Development › Purge caches**
   or `php admin/cli/purge_caches.php`).

## 🔧 Settings

### Giving access to Google (once per site)

The plugin needs **an OAuth client of the site itself**: the Drive scope is
*restricted*, and an application shared between organisations would have to pass
a paid annual security assessment. With your own project and an *Internal*
audience, no verification from Google is needed.

1. At [console.cloud.google.com](https://console.cloud.google.com), signed in
   with a **Google Workspace** account, create a **project**.
2. **APIs & Services › Library**: enable **Classroom**, **Drive**, **Calendar**
   and **Forms**. Only those four.
3. **Google Auth Platform**: fill in the application name and support emails, and
   set the **audience to "Internal"**.
4. **Clients › Create client › Web application** tab. Under *Authorised redirect
   URIs*, paste **exactly** the one shown in the connection block of the plugin
   settings (Google requires a literal match: `https`, no trailing slash).
   ⚠️ The **client secret is only shown when the client is created**.
5. Paste the **client ID** and **secret** into the settings and use **test
   connection** before letting anyone import.

| If something fails | It is usually |
|---|---|
| `redirect_uri_mismatch` | The URI does not match the one in the connection block: `http` instead of `https`, a trailing slash, or a host other than the `wwwroot` |
| "This app is not verified" | The audience was left *External* and unpublished: switch it to **Internal**, or add the account as a test user |
| "API … is disabled" | One of the four APIs is not enabled (step 2); the message says which one |
| Classes missing from the list | The connected account is not a teacher in them, or they belong to another Workspace. Archived ones do show up |
| Imports never start | Moodle cron is not running |

### Options

In **Site administration › Plugins › Local plugins › Import courses from Google
Classroom**:

| Setting | Effect |
|---|---|
| **Client ID / secret** | Credentials of the Google OAuth client. The redirect URI to register in Google is shown, ready to copy, in the connection block. |
| **Test connection** | Runs the real OAuth flow to validate the credentials. |
| **Drive files** | What to do with attached files: bring them into Moodle, link them, or skip them. |
| **Calendar / Forms / Individual content** | Default behaviour of those elements during the import. |
| **Log retention and panel page size** | Days the history is kept and rows per page. |

To import: **Site administration › Courses › Import courses from Google
Classroom** (also under the plugin category in *Plugins*). Requires the
`local/tresipuntimportgc:import` capability (by default, managers and course
creators).

## 🗑️ Uninstalling

Uninstalling removes the plugin tables (import history and traces) and its
settings. Courses that were already imported **remain**, like any other Moodle
course.

## 🛠️ Development (optional)

```bash
# Unit tests
vendor/bin/phpunit --testsuite local_tresipuntimportgc_testsuite

# Acceptance tests
vendor/bin/behat --tags @local_tresipuntimportgc
```

After changing `amd/src/*.js`, rebuild with `grunt amd`. The `google/apiclient`
v2 library is vendored under `.extlib/`.

## 📄 Licence

[GNU GPL v3 or later](https://www.gnu.org/copyleft/gpl.html) — 2026 [Tresipunt](https://tresipunt.com) (contacte@tresipunt.com)

---

<p align="center">
  <a href="https://tresipunt.com"><img src="pix/tresipunt_logo.svg" alt="Tresipunt" width="120" height="30"></a>
</p>
