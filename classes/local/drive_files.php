<?php
// This file is part of Moodle - http://moodle.org/
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
 * Google Drive file import helpers.
 *
 * @package    local_tresipuntimportgc
 * @copyright  2026 3iPunt (contacte@tresipunt.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_tresipuntimportgc\local;

use local_tresipuntimportgc\providers\provider;
use stdClass;

/**
 * Stores Google Drive files into Moodle file areas, tracing each result.
 *
 * Native Google documents are exported to an Office/SVG format by the
 * provider; Google Forms are not importable as files.
 *
 * @package    local_tresipuntimportgc
 * @copyright  2026 3iPunt (contacte@tresipunt.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class drive_files {

    /**
     * Stores one Google Drive file (by metadata) into a Moodle file area.
     *
     * @param  provider $provider  Connected provider.
     * @param  stdClass $filemeta  Drive metadata (id, name, mimetype, weblink, size).
     * @param  int      $contextid Target context id.
     * @param  int      $userid    Owner of the file.
     * @param  string   $component File area component.
     * @param  string   $filearea  File area name.
     * @param  string   $filepath  File path inside the area.
     * @param  int      $itemid    File area item id (0 for content/private areas).
     * @return void
     * @throws \coding_exception
     */
    public static function store(provider $provider, stdClass $filemeta, int $contextid,
            int $userid, string $component, string $filearea, string $filepath, int $itemid = 0): void {
        if ($filemeta->mimetype === 'application/vnd.google-apps.form') {
            // Un Google Form no tiene binario descargable: se omite (no es un
            // error, los formularios se tratan aparte según formsimport).
            trace_router::trace('importfileerrorcontent', 'warning', $filemeta->name);
            return;
        }
        $exports = [
            'application/vnd.google-apps.document' => '.docx',
            'application/vnd.google-apps.presentation' => '.pptx',
            'application/vnd.google-apps.spreadsheet' => '.xlsx',
            'application/vnd.google-apps.drawing' => '.svg',
        ];
        if (isset($exports[$filemeta->mimetype])) {
            trace_router::trace('convertdocumentto', 'info',
                ['title' => $filemeta->name, 'format' => $exports[$filemeta->mimetype]]);
        }
        $res = $provider->save_drive_file_to_storage($filemeta, [
            'contextid' => $contextid,
            'component' => $component,
            'filearea' => $filearea,
            'itemid' => $itemid,
            'filepath' => $filepath,
            'userid' => $userid,
        ]);
        if ($res->success && $res->data->status === 'imported') {
            trace_router::trace('importfilesuccess', 'success', $filemeta->name);
        } else if ($res->success) {
            trace_router::trace('importfilealreadyexist', 'warning', $filemeta->name);
        } else {
            trace_router::trace('importfileerror', 'danger',
                ['name' => $filemeta->name, 'error' => $res->error->to_string()]);
        }
    }

    /**
     * Stores one Google Drive file (by id) into a Moodle file area.
     *
     * @param  provider $provider  Connected provider.
     * @param  string   $fileid    Drive file id.
     * @param  int      $contextid Target context id.
     * @param  int      $userid    Owner of the file.
     * @param  string   $component File area component.
     * @param  string   $filearea  File area name.
     * @param  string   $filepath  File path inside the area.
     * @param  int      $itemid    File area item id (0 for content/private areas).
     * @return void
     * @throws \coding_exception
     */
    public static function import(provider $provider, string $fileid, int $contextid,
            int $userid, string $component, string $filearea, string $filepath, int $itemid = 0): void {
        $meta = $provider->get_drive_file($fileid);
        if (!$meta->success) {
            trace_router::trace('importfileerror', 'danger',
                ['name' => $fileid, 'error' => $meta->error->to_string()]);
            return;
        }
        self::store($provider, $meta->data, $contextid, $userid, $component, $filearea, $filepath, $itemid);
    }
}
