# Changelog

## 2.0.0 (in development)

Full refactor of the import flow. Compatible with Moodle 4.5 LTS and 5.1.

### Added

- Background imports: one adhoc task per course, with persistent status and
  traces (`local_tresipuntimportgc_import`, `_course`, `_log` tables).
- Progress page with live polling (incremental traces), retry of failed
  courses and discard of pending ones.
- Imports panel for administrators: history, filters, pagination and detail.
- New course selection screen (search, filters, per-course configuration).
- Connection block in settings: status, copiable redirect URI and test link.
- Scheduled task that purges import history older than the configured
  retention.
- Events: `gc_course_imported` (rebuilt and now triggered), `gc_course_retried`
  and `gc_course_discarded`.
- Privacy provider (GDPR): declares the data sent to Google and the stored
  import history, with export and deletion support.

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

### Fixed

- Web services now declare and enforce the `local/tresipuntimportgc:import`
  capability (privilege escalation).
- Fatal error on successful imports (`error` dereferenced on null).
- TLS host verification is no longer disabled on Google API calls.
- Import start is protected with `sesskey`; browser cookies are no longer
  used to pass the selection.
- "Do not import" option for Google Forms now skips the module instead of
  creating an empty hidden label.

### Removed

- Legacy import flow (`create.php`, `import_desc.php`) and its templates,
  outputs and AMD module.
- Orphan settings and language strings (NextCloud option, teacher folder
  setting, credentials JSON).

## 1.0

Initial release.
