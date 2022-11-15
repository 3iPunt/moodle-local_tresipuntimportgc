<?php

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
 * @param file_storage $fs
 * @param Google_Service_Drive_DriveFile $file
 * @param Google_Service_Drive $service
 * @param int $contextid
 * @param int $userid
 * @param string $token
 * @param string $component
 * @param string $filearea
 * @param string $filepath
 * @throws coding_exception
 * @throws file_exception
 * @throws stored_file_creation_exception
 */
function import_file(
    file_storage $fs,
    Google_Service_Drive_DriveFile $file,
    Google_Service_Drive $service,
    int $contextid,
    int $userid,
    string $token,
    string $component,
    string $filearea,
    string $filepath
): void {
    $downloadUrl = $file->getDownloadUrl();
    $datafile = [
        'contextid' => $contextid,
        'component' => $component,
        'filearea' => $filearea,
        'itemid' => 0,
        'filepath' => $filepath,
        'filename' => $file->getTitle(),
        'userid' => $userid
    ];
    if ($downloadUrl) {
        $request = new Google_Http_Request($downloadUrl, 'GET', null, null);
        $httpRequest = $service->getClient()->getAuth()->authenticatedRequest($request);
        $response = $httpRequest->getResponseHttpCode();
        if ($response === 200) {
            if ($fs->get_file($contextid, $component, $filearea, 0, $filepath, $file->getTitle()) === false) {
                $fs->create_file_from_string($datafile, $httpRequest->getResponseBody());
                print_trace('importfilesuccess', 'success', $file->getTitle());
            } else {
                print_trace('importfilealreadyexist', 'warning', $file->getTitle());
            }
        } else {
            print_trace('importfileerror', 'error', ['title' => $file->getTitle(), 'error' => $response]);
        }
    } else {
        // Files created from Google Apps
        $mimetype = $file->getMimeType();
        $exportlinks = $file->getExportLinks();
        $url = '';
        $format = '';
        // TODO https://developers.google.com/drive/api/guides/mime-types
        switch ($mimetype) {
            case 'application/vnd.google-apps.document':
                $format = '.docx';
                print_trace('convertdocumentto', 'info', ['title' => $file->getTitle(), 'format' => $format]);
                $datafile['filename'] = $file->getTitle() . $format; // .odt ??
                $url = $exportlinks['application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
                $url .= '&access_token=' . $token;
                break;
            case 'application/vnd.google-apps.presentation':
                $format = '.pptx';
                print_trace('convertdocumentto', 'info', ['title' => $file->getTitle(), 'format' => $format]);
                $datafile['filename'] = $file->getTitle() . $format; // .odp ??
                $url = $exportlinks['application/vnd.openxmlformats-officedocument.presentationml.presentation'];
                $url .= '&access_token=' . $token;
                break;
            case 'application/vnd.google-apps.spreadsheet':
                $format = '.xlsx';
                print_trace('convertdocumentto', 'info', ['title' => $file->getTitle(), 'format' => $format]);
                $datafile['filename'] = $file->getTitle() . $format; // .ods ??
                $url = $exportlinks['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'];
                $url .= '&access_token=' . $token;
                break;
            case 'application/vnd.google-apps.drawing':
                $format = '.svg';
                print_trace('convertdocumentto', 'info', ['title' => $file->getTitle(), 'format' => $format]);
                $datafile['filename'] = $file->getTitle() . $format;
                $url = $exportlinks['image/svg+xml'];
                $url .= '&access_token=' . $token;
                break;
            case 'application/vnd.google-apps.form':
                // TODO QUIZ or feedback
                print_trace('importfileerrorcontent', 'error', $file->getTitle());
                break;
            case 'application/vnd.google-apps.folder':
                // TODO create a folder in the Moodle repository and upload these files
                $format = '.zip';
                print_trace('convertdocumentto', 'info', ['title' => $file->getTitle(), 'format' => $format]);
                $datafile['filename'] = $file->getTitle() . $format;
                break;
            default:
                break;
                $format = '.pdf';
                print_trace('convertdocumentto', 'info', ['title' => $file->getTitle(), 'format' => $format]);
                $datafile['filename'] = $file->getTitle() . $format;
                $url = $exportlinks['application/pdf'];
                $url .= '&access_token=' . $token;
                break;
        }
        // TODO forms in drive?? how to convert?? template feedback?
        if ($mimetype !== 'application/vnd.google-apps.form') {
            if ($fs->get_file($contextid, $component, $filearea, 0, $filepath, $file->getTitle() . $format) === false) {
                if ($url !== '') {
                    try {
                        $fs->create_file_from_url($datafile, $url);
                        print_trace('importfilesuccess', 'success', $file->getTitle());
                    } catch (Exception $e) {
                        print_trace('importfileerror', 'danger', ['name' => $file->getTitle(), 'error' => $e->getMessage()]);
                    }
                } else {
                    print_trace('importfileerror', 'danger', ['name' => $file->getTitle(), 'error' => get_string('emptyurl', 'local_tresipuntimportgc')]);
                }
            } else {
                print_trace('importfilealreadyexist', 'warning', $file->getTitle());
            }
        }
    }
}
