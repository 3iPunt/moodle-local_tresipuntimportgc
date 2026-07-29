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
 * Response
 *
 * @package     local_tresipuntimportgc
 * @copyright   2021 3iPunt (contacte@tresipunt.com)
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_tresipuntimportgc\responses;

/**
 * Response
 *
 * @package     local_tresipuntimportgc
 * @copyright   2021 3iPunt (contacte@tresipunt.com)
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class response {

    /** @var bool Success */
    public $success;

    /** @var error Error object */
    public $error;

    /** @var string Data */
    public $data;

    /**
     * Response constructor.
     * @param bool $success
     * @param string $data
     * @param error|null $error $error
     */
    public function __construct(bool $success, string $data, error $error = null) {
        $this->success = $success;
        $this->data = $data;
        if (isset($error)) {
            $this->error = $error;
        } else {
            $this->error = new error('0', '');
        }

    }

    /**
     * To String.
     *
     * @return string
     */
    public function to_string(): string {
        if ($this->success) {
            return '';
        } else {
            return $this->error->to_string();
        }
    }
}
