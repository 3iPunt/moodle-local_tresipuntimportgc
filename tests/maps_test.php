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

namespace local_tresipuntimportgc;

use local_tresipuntimportgc\factory\module_assign;
use local_tresipuntimportgc\factory\module_label;
use local_tresipuntimportgc\maps\modules\gc_mod_coursework_assignment_map;
use local_tresipuntimportgc\maps\modules\gc_mod_map;
use local_tresipuntimportgc\providers\google;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/local/tresipuntimportgc/classes/maps/modules/gc_mod_map.php');

/**
 * Tests of the Google -> Moodle module maps (no network).
 *
 * @package    local_tresipuntimportgc
 * @copyright  2026 3iPunt (contacte@tresipunt.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers     \local_tresipuntimportgc\maps\modules\gc_mod_coursework_assignment_map
 * @covers     \local_tresipuntimportgc\maps\modules\gc_mod_map
 */
final class maps_test extends \advanced_testcase {

    /**
     * Classroom courseWork fixture: an assignment whose only material is a form.
     *
     * @return array
     */
    private function form_assignment_fixture(): array {
        return [
            'state' => 'PUBLISHED',
            'topicId' => 'topic-1',
            'title' => 'Weekly quiz',
            'description' => 'Answer the form',
            'materials' => [
                ['form' => [
                    'formUrl' => 'https://docs.google.com/forms/d/example/viewform',
                    'title' => 'Weekly quiz form',
                    'thumbnailUrl' => 'https://example.com/thumb.png',
                ]],
            ],
        ];
    }

    /**
     * With "do not import", a form-only assignment is skipped entirely.
     */
    public function test_form_assignment_not_imported_when_disabled(): void {
        $this->resetAfterTest();
        set_config('formsimport', 2, 'local_tresipuntimportgc');

        $map = new gc_mod_coursework_assignment_map();
        $provider = $this->createMock(google::class);
        $this->assertNull($map->get_mod($this->form_assignment_fixture(), $provider));
    }

    /**
     * With "embed", a form-only assignment becomes a label with the form.
     */
    public function test_form_assignment_embedded_by_default(): void {
        $this->resetAfterTest();
        set_config('formsimport', 0, 'local_tresipuntimportgc');

        $map = new gc_mod_coursework_assignment_map();
        $provider = $this->createMock(google::class);
        $mod = $map->get_mod($this->form_assignment_fixture(), $provider);
        $this->assertInstanceOf(module_label::class, $mod);
    }

    /**
     * An assignment without form materials maps to a Moodle assignment.
     */
    public function test_regular_assignment_maps_to_assign(): void {
        $this->resetAfterTest();

        $module = [
            'state' => 'PUBLISHED',
            'topicId' => 'topic-1',
            'title' => 'Essay',
            'description' => 'Write an essay',
            'materials' => [
                ['link' => ['url' => 'https://example.com/reading', 'title' => 'Reading']],
            ],
        ];
        $map = new gc_mod_coursework_assignment_map();
        $provider = $this->createMock(google::class);
        $mod = $map->get_mod($module, $provider);
        $this->assertInstanceOf(module_assign::class, $mod);

        $draft = [
            'state' => 'DRAFT',
            'title' => 'Hidden essay',
            'materials' => [],
        ];
        $this->assertInstanceOf(module_assign::class, $map->get_mod($draft, $provider));
    }

    /**
     * get_desc_rich() renders the known materials into the description.
     */
    public function test_get_desc_rich_renders_materials(): void {
        $this->resetAfterTest();

        $html = gc_mod_map::get_desc_rich("line one\nline two", [
            ['link' => ['url' => 'https://example.com/doc', 'title' => 'A doc']],
        ]);
        $this->assertStringContainsString('line one<br />', $html);
        $this->assertStringContainsString('https://example.com/doc', $html);

        // Unknown material types are ignored, not fatal.
        $html = gc_mod_map::get_desc_rich('desc', [['unknown' => ['id' => 'x']]]);
        $this->assertSame('desc', $html);
    }
}
