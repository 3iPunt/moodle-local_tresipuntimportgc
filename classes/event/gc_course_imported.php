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
 * The local_tresipuntimportgc import Google Classroom Course.
 *
 * @package     local_tresipuntimportgc
 * @copyright   2021 Tresipunt
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_tresipuntimportgc\event;

use core\event\base;
use stdClass;

defined('MOODLE_INTERNAL') || die();

/**
 * gc_course_imported
 *
 * @package    local_tresipuntimportgc
 * @copyright  2021 Tresipunt
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class gc_course_imported extends base {

    /**
     * Init method.
     *
     * @return void
     */
    protected function init() {
        $this->data['crud'] = 'c';
        $this->data['objecttable'] = 'course';
        $this->data['edulevel'] = self::LEVEL_OTHER;
    }

    /**
     * Return localised event name.
     *
     * @return string
     */
    public static function get_name(): string {
        return 'Tresipunt Import Course Google Classroom';
    }

    /**
     * Returns description of what happened.
     *
     * @return string
     */
    public function get_description(): string {

        $res = new stdClass();
        $res->success = false;
        $courseid = isset($this->other["courseid"]) ? $this->other["courseid"] : '';
        $providerid = isset($this->other["providerid"]) ? $this->other["providerid"] : '';
        if (isset($this->other["response"])) {
            $response = $this->other["response"];
            if (!is_array($response)) {
                $res = json_decode($response);
            } else {
                $response = json_encode($response);
            }
        } else {
            $response = '';
        }

        if (!empty($res->success)) {
            $msg = "The userid ('$this->relateduserid') create Desktop ID '$desktopid' with Isard Id '$isardid' and template '$templateid'";
        } else {
            $msg = "ERROR: The userid ('$this->relateduserid') COULD NOT CREATE Desktop, with Isard Id '$isardid' and template '$templateid'." .
                " Response: " . $response;
        }
        return $msg;
    }

    /**
     * Custom validation.
     *
     * @throws \coding_exception
     * @return void
     */
    protected function validate_data() {
        parent::validate_data();

        if (!isset($this->relateduserid)) {
            throw new \coding_exception('The \'relateduserid\' must be set.');
        }

        if (!isset($this->other['response'])) {
            throw new \coding_exception('The \'response\' value must be set in other.');
        }

        if (!isset($this->other['courseid'])) {
            throw new \coding_exception('The \'courseid\' value must be set in other.');
        }

        if (!isset($this->other['courseid'])) {
            throw new \coding_exception('The \'providerid\' value must be set in other.');
        }
    }
}
