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
 * Connection status admin setting (read-only block).
 *
 * @package    local_tresipuntimportgc
 * @copyright  2026 3iPunt (contacte@tresipunt.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_tresipuntimportgc\adminsetting;

use admin_setting;
use moodle_url;

defined('MOODLE_INTERNAL') || die();

/**
 * Read-only settings block: connection status, copiable redirect URI and a
 * "test connection" link (the OAuth flow itself is the real test).
 *
 * @package    local_tresipuntimportgc
 * @copyright  2026 3iPunt (contacte@tresipunt.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class connection extends admin_setting {

    /**
     * Constructor.
     */
    public function __construct() {
        $this->nosave = true;
        parent::__construct('local_tresipuntimportgc/connectionstatus',
            get_string('connectionstatus', 'local_tresipuntimportgc'), '', '');
    }

    /**
     * Always returns true: nothing stored.
     *
     * @return bool
     */
    public function get_setting(): bool {
        return true;
    }

    /**
     * Never writes anything.
     *
     * @param  mixed $data Ignored.
     * @return string
     */
    public function write_setting($data): string {
        return '';
    }

    /**
     * Renders the block.
     *
     * @param  mixed  $data  Ignored.
     * @param  string $query Admin search query.
     * @return string
     */
    public function output_html($data, $query = ''): string {
        global $OUTPUT;

        $clientid = (string) get_config('local_tresipuntimportgc', 'clientid');
        $secretkey = (string) get_config('local_tresipuntimportgc', 'secretkey');
        $configured = $clientid !== '' && $secretkey !== '';
        $context = [
            'configured' => $configured,
            'redirecturi' => (new moodle_url('/local/tresipuntimportgc/import.php'))->out(false),
            'testurl' => (new moodle_url('/local/tresipuntimportgc/import.php'))->out(false),
        ];
        return $OUTPUT->render_from_template('local_tresipuntimportgc/setting_connection', $context);
    }
}
