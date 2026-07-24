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

### Fixed

- Web services now declare and enforce the `local/tresipuntimportgc:import`
  capability (privilege escalation).
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

### Removed

- Legacy import flow (`create.php`, `import_desc.php`) and its templates,
  outputs and AMD module.
- Orphan settings and language strings (NextCloud option, teacher folder
  setting, credentials JSON).

## 1.0

Initial release.
