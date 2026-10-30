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

    /** @var string Session key holding the pending OAuth state. */
    private const SESSION_STATE = 'local_tresipuntimportgc_oauthstate';

    /**
     * Loads the provider before each test.
     *
     * Loading it registers the vendored Google autoloader (done at file level
     * in providers/google.php), which the library objects built below need.
     */
    protected function setUp(): void {
        parent::setUp();
        class_exists(google::class);
    }

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

        $provider = new class ($classroom) extends google {
            /** @var \Google\Service\Classroom Fake service. */
            private $fake;
            /**
             * Takes the fake Classroom service in place of the real one.
             *
             * @param \Google\Service\Classroom $classroom Fake Classroom service.
             */
            public function __construct(\Google\Service\Classroom $classroom) {
                parent::__construct();
                $this->fake = $classroom;
            }
            /**
             * Returns the fake service, so no call ever reaches Google.
             *
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
             * Simulates a provider with no usable token.
             *
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

    /**
     * A successful OAuth callback stores the new token and the email of its account.
     */
    public function test_authenticate_with_code_stores_token_and_email(): void {
        global $SESSION;
        $this->resetAfterTest();

        $provider = $this->provider_with_responses([
            $this->token_response('new-access'),
            new \GuzzleHttp\Psr7\Response(200, ['Content-Type' => 'application/json'], '{"email": "new@example.com"}'),
        ]);
        $SESSION->{self::SESSION_STATE} = 'state-1';

        $result = $provider->authenticate_with_code('code-1', 'state-1');

        $this->assertTrue($result->success);
        $this->assertSame('new-access', $SESSION->{self::SESSION_TOKEN}['access_token']);
        $this->assertSame('new@example.com', $provider->get_account_email());
    }

    /**
     * When the profile of the new account cannot be read, the session keeps the
     * previous account whole: its token and its email stay paired.
     */
    public function test_authenticate_with_code_keeps_previous_account_on_profile_failure(): void {
        global $SESSION;
        $this->resetAfterTest();

        $provider = $this->provider_with_responses([
            $this->token_response('new-access'),
            new \GuzzleHttp\Psr7\Response(
                500,
                ['Content-Type' => 'application/json'],
                '{"error": {"code": 500, "message": "Backend error"}}'
            ),
        ]);
        $SESSION->{self::SESSION_TOKEN} = ['access_token' => 'old-access'];
        $SESSION->{self::SESSION_EMAIL} = 'old@example.com';
        $SESSION->{self::SESSION_STATE} = 'state-1';

        $result = $provider->authenticate_with_code('code-1', 'state-1');

        $this->assertFalse($result->success);
        $this->assertSame('old-access', $SESSION->{self::SESSION_TOKEN}['access_token']);
        $this->assertSame('old@example.com', $provider->get_account_email());
    }

    /**
     * Builds a configured provider whose Google client answers with canned HTTP
     * responses, in order, so no call ever reaches Google.
     *
     * @param array $responses Guzzle responses, one per HTTP call.
     * @return google
     */
    private function provider_with_responses(array $responses): google {
        set_config('clientid', 'abc.apps.googleusercontent.com', 'local_tresipuntimportgc');
        set_config('secretkey', 'shh', 'local_tresipuntimportgc');
        $provider = new google();
        $client = (new \ReflectionMethod($provider, 'get_client'))->invoke($provider);
        $stack = \GuzzleHttp\HandlerStack::create(new \GuzzleHttp\Handler\MockHandler($responses));
        $client->setHttpClient(new \GuzzleHttp\Client(['handler' => $stack]));
        return $provider;
    }

    /**
     * Response of the Google token endpoint to an authorisation code.
     *
     * @param string $accesstoken Access token to hand out.
     * @return \GuzzleHttp\Psr7\Response
     */
    private function token_response(string $accesstoken): \GuzzleHttp\Psr7\Response {
        return new \GuzzleHttp\Psr7\Response(200, ['Content-Type' => 'application/json'], json_encode([
            'access_token' => $accesstoken,
            'expires_in' => 3599,
            'refresh_token' => '1//refresh',
            'token_type' => 'Bearer',
        ]));
    }
}
