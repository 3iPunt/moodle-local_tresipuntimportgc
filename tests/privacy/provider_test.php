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

namespace local_tresipuntimportgc\privacy;

use core_privacy\local\metadata\collection;
use core_privacy\local\request\approved_contextlist;
use core_privacy\local\request\approved_userlist;
use core_privacy\local\request\userlist;
use core_privacy\local\request\writer;
use core_privacy\tests\provider_testcase;

/**
 * Tests of the privacy provider.
 *
 * @package    local_tresipuntimportgc
 * @copyright  2026 3iPunt (contacte@tresipunt.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers     \local_tresipuntimportgc\privacy\provider
 */
final class provider_test extends provider_testcase {

    /**
     * Seeds an import with one course and one trace for a user.
     *
     * @param  int $userid User id.
     * @return void
     */
    private function seed_history(int $userid): void {
        $generator = $this->getDataGenerator()->get_plugin_generator('local_tresipuntimportgc');
        $import = $generator->create_import(
            ['userid' => $userid, 'googleaccount' => 'user' . $userid . '@example.com']);
        $course = $generator->create_import_course(
            ['importid' => $import->get('id'), 'fullname' => 'Class of ' . $userid,
                'status' => 'success']);
        $generator->create_log(['importcourseid' => $course->get('id'), 'message' => 'done']);
    }

    /**
     * The metadata declares the three tables and the Google link.
     */
    public function test_get_metadata(): void {
        $collection = provider::get_metadata(new collection('local_tresipuntimportgc'));
        $this->assertCount(4, $collection->get_collection());
    }

    /**
     * Only users with imports report the system context.
     */
    public function test_get_contexts_for_userid(): void {
        $this->resetAfterTest();
        $user = $this->getDataGenerator()->create_user();
        $other = $this->getDataGenerator()->create_user();
        $this->seed_history((int) $user->id);

        $contextlist = provider::get_contexts_for_userid((int) $user->id);
        $this->assertCount(1, $contextlist->get_contextids());
        $this->assertInstanceOf(\context_system::class, $contextlist->current());

        $this->assertCount(0,
            provider::get_contexts_for_userid((int) $other->id)->get_contextids());
    }

    /**
     * The userlist of the system context holds only users with history.
     */
    public function test_get_users_in_context(): void {
        $this->resetAfterTest();
        $user = $this->getDataGenerator()->create_user();
        $this->getDataGenerator()->create_user();
        $this->seed_history((int) $user->id);

        $userlist = new userlist(\context_system::instance(), 'local_tresipuntimportgc');
        provider::get_users_in_context($userlist);
        $this->assertSame([(int) $user->id], array_map('intval', $userlist->get_userids()));
    }

    /**
     * The export contains account, classes and traces of the user.
     */
    public function test_export_user_data(): void {
        $this->resetAfterTest();
        $user = $this->getDataGenerator()->create_user();
        $this->seed_history((int) $user->id);

        $context = \context_system::instance();
        $contextlist = new approved_contextlist($user, 'local_tresipuntimportgc',
            [$context->id]);
        provider::export_user_data($contextlist);

        $writer = writer::with_context($context);
        $this->assertTrue($writer->has_any_data());
        $subcontext = [get_string('privacy:exportpath', 'local_tresipuntimportgc')];
        $imports = \local_tresipuntimportgc\models\import::get_records(
            ['userid' => (int) $user->id]);
        $import = reset($imports);
        $data = $writer->get_data(array_merge($subcontext, ['import-' . $import->get('id')]));
        $this->assertSame('user' . $user->id . '@example.com', $data->googleaccount);
        $this->assertCount(1, $data->courses);
        $this->assertSame('Class of ' . $user->id, $data->courses[0]->classname);
        $this->assertSame('done', $data->courses[0]->traces[0]->message);
    }

    /**
     * Deleting one user's data keeps the history of the others.
     */
    public function test_delete_data_for_user(): void {
        global $DB;
        $this->resetAfterTest();
        $user = $this->getDataGenerator()->create_user();
        $other = $this->getDataGenerator()->create_user();
        $this->seed_history((int) $user->id);
        $this->seed_history((int) $other->id);

        $contextlist = new approved_contextlist($user, 'local_tresipuntimportgc',
            [\context_system::instance()->id]);
        provider::delete_data_for_user($contextlist);

        $this->assertSame(0, $DB->count_records('local_tresipuntimportgc_import',
            ['userid' => $user->id]));
        $this->assertSame(1, $DB->count_records('local_tresipuntimportgc_import',
            ['userid' => $other->id]));
        $this->assertSame(1, $DB->count_records('local_tresipuntimportgc_course'));
        $this->assertSame(1, $DB->count_records('local_tresipuntimportgc_log'));
    }

    /**
     * Deleting by userlist and by context works in cascade.
     */
    public function test_delete_data_for_users_and_context(): void {
        global $DB;
        $this->resetAfterTest();
        $user = $this->getDataGenerator()->create_user();
        $other = $this->getDataGenerator()->create_user();
        $this->seed_history((int) $user->id);
        $this->seed_history((int) $other->id);

        $userlist = new approved_userlist(\context_system::instance(),
            'local_tresipuntimportgc', [(int) $user->id]);
        provider::delete_data_for_users($userlist);
        $this->assertSame(1, $DB->count_records('local_tresipuntimportgc_import'));

        provider::delete_data_for_all_users_in_context(\context_system::instance());
        $this->assertSame(0, $DB->count_records('local_tresipuntimportgc_import'));
        $this->assertSame(0, $DB->count_records('local_tresipuntimportgc_course'));
        $this->assertSame(0, $DB->count_records('local_tresipuntimportgc_log'));
    }
}
