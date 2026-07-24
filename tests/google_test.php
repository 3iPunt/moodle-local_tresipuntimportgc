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

use local_tresipuntimportgc\providers\google;

/**
 * Tests of the Google provider that need no network: connection state,
 * session handling and the Classroom -> internal transformation of
 * get_courses() (fed with real library objects, no HTTP).
 *
 * @package    local_tresipuntimportgc
 * @copyright  2026 3iPunt (contacte@tresipunt.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers     \local_tresipuntimportgc\providers\google
 */
final class google_test extends \advanced_testcase {

    /** @var string Session key holding the OAuth token (mirror of the provider const). */
    private const SESSION_TOKEN = 'local_tresipuntimportgc_token';

    /** @var string Session key holding the account email. */
    private const SESSION_EMAIL = 'local_tresipuntimportgc_email';

    /**
     * is_configured() is true only when both client id and secret are set.
     */
    public function test_is_configured(): void {
        $this->resetAfterTest();

        $this->assertFalse((new google())->is_configured());

        set_config('clientid', 'abc.apps.googleusercontent.com', 'local_tresipuntimportgc');
        $this->assertFalse((new google())->is_configured());

        set_config('secretkey', 'shh', 'local_tresipuntimportgc');
        $this->assertTrue((new google())->is_configured());
    }

    /**
     * Account email and refresh token are read from the session.
     */
    public function test_session_accessors(): void {
        global $SESSION;
        $this->resetAfterTest();

        $provider = new google();
        $this->assertNull($provider->get_account_email());
        $this->assertNull($provider->get_refresh_token());
        $this->assertFalse($provider->has_token());

        $SESSION->{self::SESSION_EMAIL} = 'prof@example.com';
        $SESSION->{self::SESSION_TOKEN} = ['access_token' => 'a', 'refresh_token' => '1//r'];
        $this->assertSame('prof@example.com', $provider->get_account_email());
        $this->assertSame('1//r', $provider->get_refresh_token());
    }

    /**
     * A token without refresh_token yields a null refresh token.
     */
    public function test_refresh_token_absent(): void {
        global $SESSION;
        $this->resetAfterTest();

        $SESSION->{self::SESSION_TOKEN} = ['access_token' => 'a'];
        $this->assertNull((new google())->get_refresh_token());
    }

    /**
     * logout() clears the session token and account.
     */
    public function test_logout_clears_session(): void {
        global $SESSION;
        $this->resetAfterTest();

        $SESSION->{self::SESSION_EMAIL} = 'prof@example.com';
        $SESSION->{self::SESSION_TOKEN} = ['access_token' => 'a', 'refresh_token' => '1//r'];

        $provider = new google();
        $provider->logout();
        $this->assertObjectNotHasProperty(self::SESSION_TOKEN, $SESSION);
        $this->assertObjectNotHasProperty(self::SESSION_EMAIL, $SESSION);
        $this->assertNull($provider->get_account_email());
    }

    /**
     * get_courses() transforms the Classroom response into internal courses.
     *
     * The Classroom service is overridden with real library objects, so the
     * parsing and the gc_map transformation run for real without any HTTP.
     */
    public function test_get_courses_transforms_classroom_response(): void {
        $this->resetAfterTest();

        $active = new \Google\Service\Classroom\Course();
        $active->setId('gc-1');
        $active->setName('Biology 101');
        $active->setSection('Group A');
        $active->setCourseState('ACTIVE');
        $active->setAlternateLink('https://classroom.google.com/c/gc-1');

        $archived = new \Google\Service\Classroom\Course();
        $archived->setId('gc-2');
        $archived->setName('History 2');
        $archived->setCourseState('ARCHIVED');

        $response = new \Google\Service\Classroom\ListCoursesResponse();
        $response->setCourses([$active, $archived]);

        $coursesres = $this->createMock(\Google\Service\Classroom\Resource\Courses::class);
        $coursesres->method('listCourses')->willReturn($response);
        $classroom = $this->createMock(\Google\Service\Classroom::class);
        $classroom->courses = $coursesres;

        $provider = new class($classroom) extends google {
            /** @var \Google\Service\Classroom Fake service. */
            private $fake;
            /**
             * @param \Google\Service\Classroom $classroom Fake Classroom service.
             */
            public function __construct(\Google\Service\Classroom $classroom) {
                parent::__construct();
                $this->fake = $classroom;
            }
            /**
             * @return \Google\Service\Classroom Fake service.
             */
            protected function get_classroom(): \Google\Service\Classroom {
                return $this->fake;
            }
        };

        $result = $provider->get_courses();
        $this->assertTrue($result->success);
        $this->assertCount(2, $result->data);
        $this->assertSame('gc-1', $result->data[0]->providerdata->id);
        $this->assertSame('Biology 101', $result->data[0]->providerdata->name);
        $this->assertSame('ARCHIVED', $result->data[1]->providerdata->courseState);
    }

    /**
     * A failure of the Classroom service is caught and reported, not thrown.
     */
    public function test_get_courses_handles_failure(): void {
        $this->resetAfterTest();

        $provider = new class extends google {
            /**
             * @return \Google\Service\Classroom Never: always throws.
             */
            protected function get_classroom(): \Google\Service\Classroom {
                throw new \Exception('no token');
            }
        };

        $result = $provider->get_courses();
        $this->assertFalse($result->success);
        $this->assertSame([], $result->data);
        $this->assertNotNull($result->error);
    }
}
