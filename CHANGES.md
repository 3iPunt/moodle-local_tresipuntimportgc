# Changelog

## 2.0.0 (2026-07-24)

Full refactor of the import flow plus an extended Classroom → Moodle mapping.
Compatible with Moodle 4.5 LTS and 5.1 (`requires` 4.5, `supported [405, 501]`).

### Added

- Background imports: one adhoc task per course, with persistent status and
  traces (`local_tresipuntimportgc_import`, `_course`, `_log` tables).
- Progress page with live polling (incremental traces), retry of failed
  courses and discard of pending ones.
- Imports panel: history, filters, pagination and detail (capability-gated).
- New course selection screen (search, filters, per-course configuration).
- Connection block in settings: status, copiable redirect URI and test link.
- Scheduled task that purges import history older than the configured
  retention.
- Events: `gc_course_imported` (rebuilt and now triggered), `gc_course_retried`
  and `gc_course_discarded`.
- Privacy provider (GDPR): declares the data sent to Google and the stored
  import history, with export and deletion support.
- Extended mapping: multiple-choice question → `mod_choice`; short question
  without grade → `mod_feedback`; announcements → `mod_forum`; teacher folder
  downloaded into a hidden `mod_folder` (files live in Moodle, no Drive link);
  assignment rubric → `mod_assign` + `gradingform_rubric`.
- Scheduled publication (`scheduledTime`) mapped to an "available from" access
  restriction.
- Per-course effective configuration (`run_config`) passed down to the maps
  (`formsimport`, `importindividual`).
- Category autocomplete backed by a searchable web service (scales to any
  number of categories); duplicate shortname/idnumber warning in the importer.
- Test suite: PHPUnit (maps, models, importer, external, google, helper,
  panel query, events, cleanup task, trace router), Behat and privacy.

### Changed

- Google integration rewritten on top of a vendored `google/apiclient` v2
  (Classroom, Drive v3, Calendar, Forms) behind an internal provider contract;
  the deprecated core `lib/google` v1 library is no longer used.
- Settings reorganised in three blocks; selects only offer implemented
  options and legacy values are remapped on upgrade.
- Client secret stored with `admin_setting_configpasswordunmask`; the
  credentials JSON setting was removed (client id + secret are enough).
- Global helpers moved into autoloaded classes; `lib.php` now only contains
  hook implementations.
- `import` and `viewreports` capabilities granted by default to managers and
  course creators (not only site admins).
- Classroom descriptions (plain text) are converted to safe HTML on import
  (escaped, line breaks preserved, URLs auto-linked).
- Responsive layout reworked (mobile/tablet), spacing via container `gap`.
- Vendored dependencies pruned to what Moodle core does not already ship and
  autoload (`.extlib` down from 6.0 MB to 2.6 MB). Versions are pinned to what
  Moodle 4.5 provides, the oldest supported release.
- Clearing the section summaries of an imported course now runs as a single
  statement and touches the course cache once, instead of rebuilding the whole
  course cache once per section.

### Fixed (packaging)

- `thirdpartylibs.xml` now declares one entry per vendored package with its real
  licence (Apache-2.0 for the Google libraries, MIT for Monolog, PSR-6 cache and
  the Composer autoloader), instead of a single Apache-2.0 entry covering
  packages that are actually MIT.

- The plugin no longer claims the legacy global `Google_*` class names. Those
  belong to the google-api-php-client v1 that Moodle core still ships: when this
  plugin was loaded first (typically in cron), core's `get_google_client()` got
  the v2 class and failed with a TypeError, breaking `repository_googledocs`,
  `portfolio_googledocs` and the googledrive file converter.

### Fixed

- Web services now declare and enforce the `local/tresipuntimportgc:import`
  capability (privilege escalation).
- Import traces escape the data they interpolate (file names, titles and Google
  API error messages), and the progress web service cleans the message it
  returns: a crafted Drive file name could inject markup into the progress page.
- Course creation requires `moodle/course:create` in the target category, both
  when queueing a run and in the web service; the import capability alone no
  longer allows creating courses anywhere on the site. The category selector
  only offers categories the user can create in.
- An import run can only be seen and acted on by the user who launched it,
  unless the viewer holds `local/tresipuntimportgc:viewreports`. Previously
  anyone able to import could read another user's progress page (including
  their Google account) and retry or discard their courses, which also replaced
  that run's refresh token with their own.
- The OAuth callback validates a `state` issued with the consent URL, so a
  third party cannot make the user's session exchange a code of their own.
- The Google Calendar description and location are no longer stored as raw HTML;
  they go through the same plain-text conversion as the rest of the plugin.
- `shortname` in the file import service is `PARAM_SAFEPATH` (it is used as a
  folder name), and the web services now use the values returned by
  `validate_parameters()`.
- The privacy provider declares the `usermodified` field of its three tables.
- Capabilities declare only `CAP_ALLOW`. The previous `CAP_PROHIBIT` entries for
  student, guest, teacher and editingteacher were irrevocable per context and
  stopped a site administrator from granting the capability to a role with those
  archetypes; anything not granted is denied by default anyway.

- Fatal error on successful imports (`error` dereferenced on null).
- TLS host verification is no longer disabled on Google API calls.
- Import start is protected with `sesskey`; browser cookies are no longer
  used to pass the selection.
- "Do not import" option for Google Forms now skips the module instead of
  creating an empty hidden label.
- Course creation no longer fails when no category is chosen (falls back to the
  site default category).
- Imported activities no longer show test placeholder text ("Test <mod> N")
  when the Classroom description is empty (intro passed via `introeditor`).
- Broken Google `webthumbnail` images removed from link/driveFile description
  cards (that endpoint requires a Google session).
- Google Form attachments report an informational notice instead of an error.
- Plugin pages no longer render a double card on themes that paint
  `#region-main` with a white background (the theme card is neutralised only on
  our pages).

### Removed

- Legacy import flow (`create.php`, `import_desc.php`) and its templates,
  outputs and AMD module.
- Orphan settings and language strings (NextCloud option, teacher folder
  setting, credentials JSON).
- The unused `pix/importgc_login.png` image and the empty `db/uninstall.php`
  (Moodle already drops the tables and settings on uninstall).
- The diagnostic dev tool (`devtools/`), which forced `display_errors` and
  printed stack traces; the settings page already reports connection status and
  can run a real test connection.

## 1.0

Initial release.
