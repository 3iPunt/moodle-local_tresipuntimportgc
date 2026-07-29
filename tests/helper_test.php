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

use local_tresipuntimportgc\local\helper;

/**
 * Tests of the text helpers.
 *
 * @package    local_tresipuntimportgc
 * @copyright  2026 3iPunt (contacte@tresipunt.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers     \local_tresipuntimportgc\local\helper
 */
final class helper_test extends \advanced_testcase {

    /**
     * Shortname slug cases.
     *
     * @return array[] class name, expected slug.
     */
    public static function shortname_slug_provider(): array {
        return [
            'plain' => ['Biology', 'biology'],
            'spaces' => ['Biology 101', 'biology_101'],
            'accents' => ['Educación Física', 'educacion_fisica'],
            'enye and cedilla' => ['Año Barça', 'ano_barca'],
            'catalan' => ['Física i Química', 'fisica_i_quimica'],
            'uppercase' => ['MATES', 'mates'],
            'empty' => ['', ''],
        ];
    }

    /**
     * shortname_slug() transliterates, lowercases and replaces spaces.
     *
     * @dataProvider shortname_slug_provider
     * @param string $name     Class name.
     * @param string $expected Expected slug.
     */
    public function test_shortname_slug(string $name, string $expected): void {
        $this->assertSame($expected, helper::shortname_slug($name));
    }
}
