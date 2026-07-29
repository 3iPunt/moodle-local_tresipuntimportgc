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
 * Errors Response
 *
 * @package     local_tresipuntimportgc
 * @copyright   2021 Tresipunt
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_tresipuntimportgc\responses;

defined('MOODLE_INTERNAL') || die();


/**
 * Errors Response
 *
 * @package     local_tresipuntimportgc
 * @copyright   2021 Tresipunt
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class errors extends error {

    /** @var error[] Errors */
    public $errors;

    /**
     * Error constructor.
     * @param string $code
     * @param string $message
     * @param error[] $errors
     */
    public function __construct(string $code, string $message, array $errors) {
        parent::__construct($code, $message);
        $this->errors = $errors;
    }

    /**
     * To String.
     *
     * @return string
     */
    public function to_string(): string {
        $res = '';
        if ($this->code !== '0') {
            $res = $this->code . ': ' . $this->message;
            foreach ($this->errors as $error) {
                $res .= PHP_EOL;
                $res .= $error->to_string();
            }
        }
        return $res;
    }

}
