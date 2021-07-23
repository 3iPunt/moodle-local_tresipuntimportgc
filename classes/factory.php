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
 * Class factory
 *
 * @package     local_tresipuntimportgc
 * @copyright   2021 Tresipunt
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_tresipuntimportgc;

use local_tresipuntimportgc\models\course;
use local_tresipuntimportgc\providers\provider;
use moodle_exception;
use stdClass;

defined('MOODLE_INTERNAL') || die;

/**
 * Class factory
 *
 * @package     local_tresipuntimportgc
 * @copyright   2021 Tresipunt
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class factory  {

    /** @var provider Provider */
    protected $provider;

    /**
     * constructor.
     *
     * @param provider $provider
     */
    public function __construct(provider $provider) {
        $this->provider = $provider;
    }

    /**
     * Create Course.
     *
     * @param string $providerid
     * @param int $categoryid
     * @param string $fullname
     * @param string $shortname
     * @param bool $visible
     * @return object
     * @throws moodle_exception
     */
    function create_course(
        string $providerid, int $categoryid, string $fullname, string $shortname, bool $visible
    ): object {

        global $DB, $USER;

        $res = new stdClass();
        $res->success = true;
        $res->error = '';

        // Get Course.
        $provider_course = $this->provider->get_course($providerid);

        // Create Course.
        $course3ip = new course($providerid);
        $newcourse = $course3ip->create_course(
            $categoryid, $fullname, $shortname, $visible
        );

        // Create Modules.



        // Enrol teacher.
        $plugin_instance = $DB->get_record("enrol",
            array('courseid' => $newcourse->id, 'enrol'=>'manual'));
        $plugin = enrol_get_plugin('manual');
        $roleid = $DB->get_field('role', 'id', array('shortname' => 'editingteacher'));
        $plugin->enrol_user($plugin_instance, $USER->id, $roleid);


        return $res;
    }

}
