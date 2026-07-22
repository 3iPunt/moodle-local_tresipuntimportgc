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
 * Legacy global helpers of the plugin.
 *
 * @package    local_tresipuntimportgc
 * @copyright  2026 3iPunt (contacte@tresipunt.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use local_tresipuntimportgc\providers\provider;

defined('MOODLE_INTERNAL') || die();

/**
 * @param $str
 * @return array|string|string[]
 */
function normalize_str($str) {
    return str_replace(
        ['á', 'à', 'ä', 'â', 'ª', 'Á', 'À', 'Â', 'Ä',
            'é', 'è', 'ë', 'ê', 'É', 'È', 'Ê', 'Ë',
            'í', 'ì', 'ï', 'î', 'Í', 'Ì', 'Ï', 'Î',
            'ó', 'ò', 'ö', 'ô', 'Ó', 'Ò', 'Ö', 'Ô',
            'ú', 'ù', 'ü', 'û', 'Ú', 'Ù', 'Û', 'Ü',
            'ñ', 'Ñ', 'ç', 'Ç'],
        ['a', 'a', 'a', 'a', 'a', 'A', 'A', 'A', 'A',
            'e', 'e', 'e', 'e', 'E', 'E', 'E', 'E',
            'i', 'i', 'i', 'i', 'I', 'I', 'I', 'I',
            'o', 'o', 'o', 'o', 'O', 'O', 'O', 'O',
            'u', 'u', 'u', 'u', 'U', 'U', 'U', 'U', 'n', 'N', 'c', 'C'],
        $str);
}

/**
 * @param array $arr
 * @return int|string|null
 */
function array_key_first_compatible(array $arr) {
    foreach($arr as $key => $unused) {
        return $key;
    }
    return null;
}

/**
 * @param string $traceid
 * @param string $type
 * @param null $param
 * @param null $time
 * @throws coding_exception
 */
function print_trace(string $traceid, string $type, $param = null, $time = null): void {
    // Bridge to the persistent log when an import course is running.
    $logger = \local_tresipuntimportgc\local\trace_router::get_logger();
    if ($logger !== null) {
        $message = get_string($traceid, 'local_tresipuntimportgc', $param);
        if ($type === 'danger' || $type === 'error') {
            $logger->error($message);
        } else if ($type === 'warning') {
            $logger->warning($message);
        } else {
            $logger->info($message);
        }
    }
    if (defined('CLI_SCRIPT') && CLI_SCRIPT) {
        // Cron/adhoc context: no output buffering, just the task log.
        mtrace('  ' . strip_tags(get_string($traceid, 'local_tresipuntimportgc', $param)));
        return;
    }
    ob_implicit_flush(true);
    /*if ($time !== null) {
        echo '<span class="badge badge-info">'
            . display_size(memory_get_usage()) . ' - '
            . round(microtime(true) - $time, 2) . 's' . ' - '
            . date('H:i:s') . '</span>';
    } else {
        echo '<span class="badge badge-info">'
            . display_size(memory_get_usage()) . ' - '
            . date('H:i:s') . '</span>';
    }*/
    switch ($type) {
        case 'light':
            //$content = html_writer::tag('h5', get_string($traceid, 'local_tresipuntimportgc', $param), ['class' => 'text-light', 'role' => 'alert']);
            $content = html_writer::div(get_string($traceid, 'local_tresipuntimportgc', $param), 'alert alert-light', ['role' => 'alert']);
            break;
        case 'danger':
            //$content = html_writer::tag('h5', get_string($traceid, 'local_tresipuntimportgc', $param), ['class' => 'text-danger', 'role' => 'alert']);
            $content = html_writer::div(get_string($traceid, 'local_tresipuntimportgc', $param), 'alert alert-danger', ['role' => 'alert']);
            break;
        case 'primary':
            //$content = html_writer::tag('h5', get_string($traceid, 'local_tresipuntimportgc', $param), ['class' => 'text-primary', 'role' => 'alert']);
            $content = html_writer::div(get_string($traceid, 'local_tresipuntimportgc', $param), 'alert alert-primary', ['role' => 'alert']);
            break;
        case 'secondary':
            //$content = html_writer::tag('h5', get_string($traceid, 'local_tresipuntimportgc', $param), ['class' => 'text-secondary', 'role' => 'alert']);
            $content = html_writer::div(get_string($traceid, 'local_tresipuntimportgc', $param), 'alert alert-secondary', ['role' => 'alert']);
            break;
        case 'success':
            //$content = html_writer::tag('h5', get_string($traceid, 'local_tresipuntimportgc', $param), ['class' => 'text-success', 'role' => 'alert']);
            $content = html_writer::div(get_string($traceid, 'local_tresipuntimportgc', $param), 'alert alert-success', ['role' => 'alert']);
            break;
        case 'warning':
            //$content = html_writer::tag('h5', get_string($traceid, 'local_tresipuntimportgc', $param), ['class' => 'text-warning', 'role' => 'alert']);
            $content = html_writer::div(get_string($traceid, 'local_tresipuntimportgc', $param), 'alert alert-warning', ['role' => 'alert']);
            break;
        case 'dark':
            //$content = html_writer::tag('h5', get_string($traceid, 'local_tresipuntimportgc', $param), ['class' => 'text-dark', 'role' => 'alert']);
            $content = html_writer::div(get_string($traceid, 'local_tresipuntimportgc', $param), 'alert alert-dark', ['role' => 'alert']);
            break;
        case 'info':
        default:
            //$content = html_writer::tag('h5', get_string($traceid, 'local_tresipuntimportgc', $param), ['class' => 'text-info', 'role' => 'alert']);
            $content = html_writer::div(get_string($traceid, 'local_tresipuntimportgc', $param), 'alert alert-info', ['role' => 'alert']);
            break;
    }
    echo $content;
    ob_flush();
    ob_implicit_flush(false);
    // For CLI
    /*mtrace(get_string($traceid . '_cli', 'local_nuxeocontroller', $param)
        . display_size(memory_get_usage()) . ' - ' . round(microtime(true) - self::$time, 2) . 's' . ' - ' . date('H:i:s') . PHP_EOL
    );*/
    // TODO save the trace or create log for revision
}

/**
 * @param $obj
 * @param $prop
 * @return mixed
 * @throws ReflectionException
 */
function accessProtected($obj, $prop) {
    $reflection = new ReflectionClass($obj);
    $property = $reflection->getProperty($prop);
    $property->setAccessible(true);
    return $property->getValue($obj);
}

/**
 * Stores one Google Drive file (by metadata) into a Moodle file area, tracing the result.
 *
 * Native Google documents are exported to an Office/SVG format by the provider;
 * Google Forms are not importable as files.
 *
 * @param provider $provider  Connected provider.
 * @param stdClass $filemeta  Drive metadata (id, name, mimetype, weblink, size).
 * @param int      $contextid Target context id.
 * @param int      $userid    Owner of the file.
 * @param string   $component File area component.
 * @param string   $filearea  File area name.
 * @param string   $filepath  File path inside the area.
 * @throws coding_exception
 */
function local_tresipuntimportgc_store_drive_file(
    provider $provider,
    stdClass $filemeta,
    int $contextid,
    int $userid,
    string $component,
    string $filearea,
    string $filepath
): void {
    if ($filemeta->mimetype === 'application/vnd.google-apps.form') {
        print_trace('importfileerrorcontent', 'error', $filemeta->name);
        return;
    }
    $exports = [
        'application/vnd.google-apps.document' => '.docx',
        'application/vnd.google-apps.presentation' => '.pptx',
        'application/vnd.google-apps.spreadsheet' => '.xlsx',
        'application/vnd.google-apps.drawing' => '.svg',
    ];
    if (isset($exports[$filemeta->mimetype])) {
        print_trace('convertdocumentto', 'info',
            ['title' => $filemeta->name, 'format' => $exports[$filemeta->mimetype]]);
    }
    $res = $provider->save_drive_file_to_storage($filemeta, [
        'contextid' => $contextid,
        'component' => $component,
        'filearea' => $filearea,
        'itemid' => 0,
        'filepath' => $filepath,
        'userid' => $userid,
    ]);
    if ($res->success && $res->data->status === 'imported') {
        print_trace('importfilesuccess', 'success', $filemeta->name);
    } else if ($res->success) {
        print_trace('importfilealreadyexist', 'warning', $filemeta->name);
    } else {
        print_trace('importfileerror', 'danger',
            ['name' => $filemeta->name, 'error' => $res->error->to_string()]);
    }
}

/**
 * Stores one Google Drive file (by id) into a Moodle file area, tracing the result.
 *
 * @param provider $provider  Connected provider.
 * @param string   $fileid    Drive file id.
 * @param int      $contextid Target context id.
 * @param int      $userid    Owner of the file.
 * @param string   $component File area component.
 * @param string   $filearea  File area name.
 * @param string   $filepath  File path inside the area.
 * @throws coding_exception
 */
function local_tresipuntimportgc_import_drive_file(
    provider $provider,
    string $fileid,
    int $contextid,
    int $userid,
    string $component,
    string $filearea,
    string $filepath
): void {
    $meta = $provider->get_drive_file($fileid);
    if (!$meta->success) {
        print_trace('importfileerror', 'danger',
            ['name' => $fileid, 'error' => $meta->error->to_string()]);
        return;
    }
    local_tresipuntimportgc_store_drive_file($provider, $meta->data, $contextid, $userid,
        $component, $filearea, $filepath);
}
