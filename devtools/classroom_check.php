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
 * DEVTOOL (not part of the release): diagnoses the Google Classroom chain.
 *
 * Read-only: prints connection status, granted scopes and the raw course
 * listing result. Never prints credentials or tokens.
 *
 * @package    local_tresipuntimportgc
 * @copyright  2026 3iPunt (contacte@tresipunt.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../../config.php');

use local_tresipuntimportgc\providers\google;

require_login();
require_capability('local/tresipuntimportgc:import', context_system::instance());

header('Content-Type: text/plain; charset=utf-8');

// Diagnóstico: queremos ver cualquier error, venga de donde venga.
error_reporting(E_ALL);
ini_set('display_errors', '1');

$provider = new google();

echo "== Diagnóstico local_tresipuntimportgc ==\n\n";
echo "1) is_configured(): " . ($provider->is_configured() ? 'SÍ' : 'NO') . "\n";
echo "2) has_token():     " . ($provider->has_token() ? 'SÍ' : 'NO') . "\n";
echo "3) cuenta:          " . ($provider->get_account_email() ?? '(desconocida)') . "\n\n";

if (!$provider->has_token()) {
    echo "Sin token: entra antes por import.php para conectar la cuenta.\n";
    exit;
}

// Scopes realmente concedidos (el consentimiento granular puede recortarlos).
global $SESSION;
$token = $SESSION->local_tresipuntimportgc_token ?? null;
if (is_array($token) && isset($token['scope'])) {
    echo "4) scopes concedidos:\n";
    foreach (explode(' ', $token['scope']) as $scope) {
        echo "   - $scope\n";
    }
    echo "\n";
} else {
    echo "4) scopes concedidos: (no disponibles en el token)\n\n";
}

echo "5) get_courses():\n";
try {
    $res = $provider->get_courses();
} catch (Throwable $t) {
    echo "   FATAL: " . get_class($t) . ": " . $t->getMessage() . "\n";
    echo "   en " . $t->getFile() . ":" . $t->getLine() . "\n";
    echo $t->getTraceAsString() . "\n";
    exit;
}
echo "   success: " . ($res->success ? 'SÍ' : 'NO') . "\n";
if (!$res->success) {
    echo "   error:   " . $res->error->to_string() . "\n";
    exit;
}
echo "   nº cursos: " . count($res->data) . "\n";
foreach ($res->data as $i => $course) {
    $d = $course->providerdata;
    echo sprintf("   [%d] id=%s | name=%s | state=%s | owner=%s\n",
        $i,
        $d->id ?? '?',
        $d->name ?? '?',
        $d->courseState ?? '?',
        $d->ownerId ?? '?'
    );
}
if (count($res->data) === 0) {
    echo "\n   Lista vacía devuelta por la API: la cuenta conectada no es\n";
    echo "   miembro (profesor o alumno) de ninguna clase de Classroom, o\n";
    echo "   las clases existen bajo otra cuenta.\n";
}
