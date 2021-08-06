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
