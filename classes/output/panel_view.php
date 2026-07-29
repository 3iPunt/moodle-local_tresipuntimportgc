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
 * Imports panel (history) view.
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
 * Pure renderable of the imports panel: receives the (already filtered and
 * paginated) rows and the filter state resolved by the page.
 *
 * @package    local_tresipuntimportgc
 * @copyright  2026 3iPunt (contacte@tresipunt.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 o later
 */
class panel_view implements renderable, templatable {

    /** @var array Table rows (see panel.php). */
    private $rows;

    /** @var array Status filter chips ({key, label, active}). */
    private $statuschips;

    /** @var string Active search text. */
    private $search;

    /** @var bool There are no imports at all on the site. */
    private $emptysite;

    /** @var bool Cron looks stalled and there are open runs. */
    private $cronstalled;

    /** @var string Paging summary ("1-25 de 132"). */
    private $pagingnote;

    /** @var string HTML of the paging bar (rendered by the page). */
    private $pagingbar;

    /**
     * Constructor.
     *
     * @param array  $rows        Table rows.
     * @param array  $statuschips Status filter chips.
     * @param string $search      Active search text.
     * @param bool   $emptysite   No imports at all.
     * @param bool   $cronstalled Cron stalled with open runs.
     * @param string $pagingnote  Paging summary.
     * @param string $pagingbar   Rendered paging bar HTML.
     */
    public function __construct(array $rows, array $statuschips, string $search,
            bool $emptysite, bool $cronstalled, string $pagingnote, string $pagingbar) {
        $this->rows = $rows;
        $this->statuschips = $statuschips;
        $this->search = $search;
        $this->emptysite = $emptysite;
        $this->cronstalled = $cronstalled;
        $this->pagingnote = $pagingnote;
        $this->pagingbar = $pagingbar;
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
            'title' => get_string('panel_title', 'local_tresipuntimportgc'),
            'description' => get_string('panel_desc', 'local_tresipuntimportgc'),
            'hasstatus' => false,
            'statuslabel' => '',
            'statusclass' => '',
        ];
        $data->rows = $this->rows;
        $data->hasrows = count($this->rows) > 0;
        $data->statuschips = $this->statuschips;
        $data->search = $this->search;
        $data->hasfilters = $this->search !== ''
            || count(array_filter($this->statuschips, static fn($c) => $c['active'])) > 0;
        $data->emptysite = $this->emptysite;
        $data->noresults = !$this->emptysite && count($this->rows) === 0;
        $data->cronstalled = $this->cronstalled;
        $data->pagingnote = $this->pagingnote;
        $data->pagingbar = $this->pagingbar;
        $data->selectionurl = (new moodle_url('/local/tresipuntimportgc/import.php'))->out(false);
        $data->panelurl = (new moodle_url('/local/tresipuntimportgc/panel.php'))->out(false);
        return $data;
    }
}
