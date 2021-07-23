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
 * Class course
 *
 * @package     local_tresipuntimportgc
 * @copyright   2021 Tresipunt
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_tresipuntimportgc\models;

use moodle_exception;
use stdClass;


defined('MOODLE_INTERNAL') || die;

/**
 * Class course
 *
 * @package     local_tresipuntimportgc
 * @copyright   2021 Tresipunt
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class course  {

    /** @var int ID course Provider */
    protected $providerid;

    /**
     * constructor.
     *
     * @param string $providerid
     */
    public function __construct(string $providerid) {
        $this->providerid = $providerid;
    }

    /**
     * Create Course.
     *
     * @param int $categoryid
     * @param string $fullname
     * @param string $shortname
     * @param bool $visible
     * @return object
     * @throws moodle_exception
     */
    public function create_course(int $categoryid, string $fullname, string $shortname, bool $visible): object {
        $data = new stdClass();
        $data->category = $categoryid;
        $data->idnumber = $this->providerid . '_' .uniqid();
        $data->shortname = $shortname;
        $data->fullname = $fullname;
        $data->visible = $visible;
        return create_course($data);
    }

}
