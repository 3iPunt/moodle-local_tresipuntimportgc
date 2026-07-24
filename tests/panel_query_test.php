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

use local_tresipuntimportgc\local\panel_query;
use local_tresipuntimportgc\models\import;

/**
 * Tests of the imports panel query (aggregation, filter, order, paging).
 *
 * @package    local_tresipuntimportgc
 * @copyright  2026 3iPunt (contacte@tresipunt.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers     \local_tresipuntimportgc\local\panel_query
 */
final class panel_query_test extends \advanced_testcase {

    /** @var \component_generator_base Plugin generator. */
    private $generator;

    /**
     * Sets up the plugin generator.
     */
    protected function setUp(): void {
        parent::setUp();
        $this->resetAfterTest();
        $this->generator = $this->getDataGenerator()->get_plugin_generator('local_tresipuntimportgc');
    }

    /**
     * Seeds an import with one course of the given status, aged $daysago days.
     *
     * @param  int    $userid  Launcher.
     * @param  string $status  Course status (drives the derived run status).
     * @param  int    $daysago Age of the run.
     * @return import
     */
    private function seed(int $userid, string $status, int $daysago = 0): import {
        global $DB;
        $import = $this->generator->create_import(['userid' => $userid]);
        $this->generator->create_import_course(
            ['importid' => $import->get('id'), 'status' => $status]);
        if ($daysago > 0) {
            $DB->set_field('local_tresipuntimportgc_import', 'timecreated',
                time() - $daysago * DAYSECS, ['id' => $import->get('id')]);
        }
        return $import;
    }

    /**
     * On an empty site the query reports it and returns nothing.
     */
    public function test_empty_site(): void {
        $result = panel_query::fetch('', '', 0, 25);
        $this->assertTrue($result->emptysite);
        $this->assertSame(0, $result->total);
        $this->assertSame([], $result->records);
    }

    /**
     * Runs are returned newest first, with the derived status resolved.
     */
    public function test_order_newest_first_and_derived_status(): void {
        $user = $this->getDataGenerator()->create_user();
        $old = $this->seed((int) $user->id, 'success', 5);
        $recent = $this->seed((int) $user->id, 'error', 1);

        $result = panel_query::fetch('', '', 0, 25);
        $this->assertFalse($result->emptysite);
        $this->assertSame(2, $result->total);
        $this->assertSame((int) $recent->get('id'), (int) $result->records[0]->id);
        $this->assertSame(import::STATUS_ERROR, $result->records[0]->derivedstatus);
        $this->assertSame((int) $old->get('id'), (int) $result->records[1]->id);
        $this->assertSame(import::STATUS_COMPLETED, $result->records[1]->derivedstatus);
    }

    /**
     * The status filter keeps only runs whose derived status matches.
     */
    public function test_status_filter(): void {
        $user = $this->getDataGenerator()->create_user();
        $this->seed((int) $user->id, 'success');
        $errorrun = $this->seed((int) $user->id, 'error');

        $result = panel_query::fetch(import::STATUS_ERROR, '', 0, 25);
        $this->assertSame(1, $result->total);
        $this->assertSame((int) $errorrun->get('id'), (int) $result->records[0]->id);

        $this->assertSame(0, panel_query::fetch(import::STATUS_PARTIAL, '', 0, 25)->total);
    }

    /**
     * hasopenruns is true only when some run is queued or running.
     */
    public function test_hasopenruns_flag(): void {
        $user = $this->getDataGenerator()->create_user();
        $this->seed((int) $user->id, 'success');
        $this->assertFalse(panel_query::fetch('', '', 0, 25)->hasopenruns);

        $this->seed((int) $user->id, 'pending');
        $this->assertTrue(panel_query::fetch('', '', 0, 25)->hasopenruns);
    }

    /**
     * Search matches the launcher full name and the Google account.
     */
    public function test_search_by_name_and_account(): void {
        global $DB;
        $alice = $this->getDataGenerator()->create_user(
            ['firstname' => 'Alice', 'lastname' => 'Adams']);
        $bob = $this->getDataGenerator()->create_user(
            ['firstname' => 'Bob', 'lastname' => 'Brown']);
        $ia = $this->seed((int) $alice->id, 'success');
        $DB->set_field('local_tresipuntimportgc_import', 'googleaccount',
            'alice@school.org', ['id' => $ia->get('id')]);
        $this->seed((int) $bob->id, 'success');

        $this->assertSame(1, panel_query::fetch('', 'Alice', 0, 25)->total);
        $this->assertSame(1, panel_query::fetch('', 'Brown', 0, 25)->total);
        $this->assertSame(1, panel_query::fetch('', 'school.org', 0, 25)->total);
        $this->assertSame(0, panel_query::fetch('', 'nobody', 0, 25)->total);
    }

    /**
     * Paging slices the filtered set while total stays the full count.
     */
    public function test_pagination(): void {
        $user = $this->getDataGenerator()->create_user();
        for ($i = 0; $i < 7; $i++) {
            $this->seed((int) $user->id, 'success', $i + 1);
        }

        $page0 = panel_query::fetch('', '', 0, 3);
        $this->assertSame(7, $page0->total);
        $this->assertCount(3, $page0->records);

        $page2 = panel_query::fetch('', '', 2, 3);
        $this->assertSame(7, $page2->total);
        $this->assertCount(1, $page2->records);

        // A page past the end is empty but the total is still reported.
        $page9 = panel_query::fetch('', '', 9, 3);
        $this->assertSame(7, $page9->total);
        $this->assertCount(0, $page9->records);
    }

    /**
     * A non-positive perpage never divides by zero: it is clamped to 1.
     */
    public function test_perpage_is_clamped(): void {
        $user = $this->getDataGenerator()->create_user();
        $this->seed((int) $user->id, 'success');
        $this->seed((int) $user->id, 'success');

        $result = panel_query::fetch('', '', 0, 0);
        $this->assertCount(1, $result->records);
        $this->assertSame(2, $result->total);
    }
}
