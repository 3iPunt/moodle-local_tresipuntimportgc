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
 * Course selection view.
 *
 * @package    local_tresipuntimportgc
 * @copyright  2026 3iPunt (contacte@tresipunt.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_tresipuntimportgc\output;

use moodle_url;
use renderable;
use renderer_base;
use stdClass;
use templatable;

/**
 * Pure renderable of the selection screen: every piece of data arrives
 * through the constructor (no DB, no config reads, no network calls here).
 *
 * States: 'noconfig' (site OAuth client missing), 'connect' (user without
 * token) and 'list' (connected; courses may be empty or carry an error).
 *
 * @package    local_tresipuntimportgc
 * @copyright  2026 3iPunt (contacte@tresipunt.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class import_view implements renderable, templatable {

    /** @var string One of: noconfig | connect | list. */
    private $state;

    /** @var array Course rows (id, name, section, archived, link, defaultshortname). */
    private $courses;

    /** @var array Categories the user can use ({id, name}). */
    private $categories;

    /** @var bool Whether per-course advanced options are allowed. */
    private $allowconfig;

    /** @var array Drive files select options ({value, text, selected}). */
    private $importfilesoptions;

    /** @var array Calendar select options ({value, text, selected}). */
    private $calendaroptions;

    /** @var string|null Connected account email. */
    private $accountemail;

    /** @var string OAuth consent URL. */
    private $authurl;

    /** @var bool Whether the current user can configure the site connection. */
    private $isadmin;

    /** @var string Error message from the provider, if any. */
    private $errormsg;

    /** @var bool Whether the current user can open the imports panel. */
    private $canviewpanel;

    /** @var array Default category preselected in the importer ({id, name}). */
    private $defaultcategory;

    /**
     * Constructor.
     *
     * @param string      $state              noconfig | connect | list.
     * @param array       $courses            Course rows.
     * @param array       $categories         Usable categories.
     * @param bool        $allowconfig        Per-course advanced options allowed.
     * @param array       $importfilesoptions Drive files select options.
     * @param array       $calendaroptions    Calendar select options.
     * @param string|null $accountemail       Connected account email.
     * @param string      $authurl            OAuth consent URL.
     * @param bool        $isadmin            User can reach site settings.
     * @param string      $errormsg           Provider error message, if any.
     * @param bool        $canviewpanel       User can open the imports panel.
     * @param array       $defaultcategory    Preselected category ({id, name}).
     */
    public function __construct(
        string $state,
        array $courses = [],
        array $categories = [],
        bool $allowconfig = false,
        array $importfilesoptions = [],
        array $calendaroptions = [],
        ?string $accountemail = null,
        string $authurl = '',
        bool $isadmin = false,
        string $errormsg = '',
        bool $canviewpanel = false,
        array $defaultcategory = []
    ) {
        $this->state = $state;
        $this->courses = $courses;
        $this->categories = $categories;
        $this->allowconfig = $allowconfig;
        $this->importfilesoptions = $importfilesoptions;
        $this->calendaroptions = $calendaroptions;
        $this->accountemail = $accountemail;
        $this->authurl = $authurl;
        $this->isadmin = $isadmin;
        $this->errormsg = $errormsg;
        $this->canviewpanel = $canviewpanel;
        $this->defaultcategory = $defaultcategory;
    }

    /**
     * Export for template.
     *
     * @param  renderer_base $output Renderer.
     * @return stdClass
     */
    public function export_for_template(renderer_base $output): stdClass {
        $data = new stdClass();
        $data->header = [
            'logotresipunt' => $output->image_url('tresipunt_logo', 'local_tresipuntimportgc')->out(false),
            'logotresipunticon' => $output->image_url('tresipunt_icon', 'local_tresipuntimportgc')->out(false),
            'logoclassroom' => $output->image_url('icon', 'local_tresipuntimportgc')->out(false),
            'title' => get_string('import_page', 'local_tresipuntimportgc'),
            'description' => ($this->state === 'list')
                ? get_string('selection_desc', 'local_tresipuntimportgc')
                : get_string('connect_intro', 'local_tresipuntimportgc'),
            'hasstatus' => false,
            'statuslabel' => '',
            'statusclass' => '',
        ];
        $data->isconnect = $this->state === 'connect' || $this->state === 'noconfig';
        $data->noconfig = $this->state === 'noconfig';
        $data->islist = $this->state === 'list';
        $data->isempty = $data->islist && $this->errormsg === '' && count($this->courses) === 0;
        $data->haserror = $this->errormsg !== '';
        $data->errormsg = $this->errormsg;
        $data->authurl = $this->authurl;
        $data->settingsurl = (new moodle_url('/admin/settings.php',
            ['section' => 'local_tresipuntimportgc_config']))->out(false);
        $data->isadmin = $this->isadmin;
        $data->accountemail = (string) $this->accountemail;
        $data->accountinitial = mb_strtoupper(mb_substr((string) $this->accountemail, 0, 1));
        $data->courses = $this->courses;
        $data->categories = $this->categories;
        $data->allowconfig = $this->allowconfig;
        $data->importfilesoptions = $this->importfilesoptions;
        $data->calendaroptions = $this->calendaroptions;
        $data->sesskey = sesskey();
        $data->actionurl = (new moodle_url('/local/tresipuntimportgc/import.php'))->out(false);
        $data->canviewpanel = $this->canviewpanel;
        $data->panelurl = (new moodle_url('/local/tresipuntimportgc/panel.php'))->out(false);
        $data->defaultcategoryid = (int) ($this->defaultcategory['id'] ?? 0);
        $data->defaultcategoryname = (string) ($this->defaultcategory['name'] ?? '');
        return $data;
    }
}
