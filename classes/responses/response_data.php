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
 * Generic provider response with untyped payload.
 *
 * @package    local_tresipuntimportgc
 * @copyright  2026 3iPunt (contacte@tresipunt.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_tresipuntimportgc\responses;

/**
 * Provider response whose payload is free-form (object, array or null).
 *
 * Used by the provider contract methods that do not have a dedicated DTO
 * (Drive metadata, calendar events, form summaries...).
 *
 * @package    local_tresipuntimportgc
 * @copyright  2026 3iPunt (contacte@tresipunt.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class response_data {

    /** @var bool Whether the call succeeded. */
    public $success;

    /** @var mixed Payload: object, array of objects or null. */
    public $data;

    /** @var error|null Error detail when success is false. */
    public $error;

    /**
     * Constructor.
     *
     * @param bool       $success Whether the call succeeded.
     * @param mixed      $data    Payload: object, array of objects or null.
     * @param error|null $error   Error detail when success is false.
     */
    public function __construct(bool $success, $data = null, ?error $error = null) {
        $this->success = $success;
        $this->data = $data;
        $this->error = $error;
    }
}
