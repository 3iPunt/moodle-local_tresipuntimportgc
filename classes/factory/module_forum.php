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
 * Class module_forum
 *
 * @package     local_tresipuntimportgc
 * @copyright   2026 3iPunt (contacte@tresipunt.com)
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_tresipuntimportgc\factory;

use coding_exception;
use context_module;
use dml_exception;
use local_tresipuntimportgc\local\drive_files;
use local_tresipuntimportgc\providers\provider;
use local_tresipuntimportgc\responses\error;
use local_tresipuntimportgc\responses\response_module;
use stdClass;

defined('MOODLE_INTERNAL') || die;

/**
 * Maps a Classroom announcement to a discussion in the course Announcements
 * forum (E10.7), replacing the external tresipuntshare module. The Drive files
 * of the announcement become attachments of the post.
 *
 * @package     local_tresipuntimportgc
 * @copyright   2026 3iPunt (contacte@tresipunt.com)
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class module_forum extends module {

    /** @var string Mod Name */
    protected $modname = 'forum';

    /** @var array The Classroom announcement. */
    protected $module;

    /** @var provider Connected provider. */
    protected $provider;

    /**
     * constructor.
     *
     * @param array    $module   The Classroom announcement (text, materials...).
     * @param string   $intro    Rendered materials for the message body.
     * @param bool     $visible  Ignored (announcements always post to the forum).
     * @param provider $provider Connected provider.
     * @throws coding_exception
     */
    public function __construct(array $module, string $intro, bool $visible, provider $provider) {
        parent::__construct('mod_forum', '', '', $intro, $visible);
        $this->module = $module;
        $this->provider = $provider;
    }

    /**
     * Create.
     *
     * @param  int $courseid
     * @return response_module
     * @throws coding_exception|dml_exception
     */
    public function create(int $courseid): response_module {
        global $CFG, $USER, $DB;
        require_once($CFG->dirroot . '/mod/forum/lib.php');

        $forum = forum_get_course_forum($courseid, 'news');
        if (empty($forum)) {
            return new response_module(false, null, new error('21000', 'ANNOUNCEMENTS_FORUM_NOT_FOUND'));
        }
        $text = (string) ($this->module['text'] ?? '');
        $name = shorten_text(trim(strip_tags($text)), 100);
        if ($name === '') {
            $name = get_string('announcement_title', 'local_tresipuntimportgc');
        }
        $discussion = new stdClass();
        $discussion->course = $courseid;
        $discussion->forum = $forum->id;
        $discussion->name = $name;
        $discussion->message = $text . $this->intro;
        $discussion->messageformat = FORMAT_HTML;
        $discussion->messagetrust = 0;
        $discussion->mailnow = 0;
        $discussion->groupid = -1;
        $did = forum_add_discussion($discussion, null, null, $USER->id);
        if (!$did) {
            return new response_module(false, null, new error('21001', 'DISCUSSION_NOT_CREATED'));
        }

        // Drive files of the announcement → attachments of the first post.
        $firstpost = $DB->get_field('forum_discussions', 'firstpost', ['id' => $did]);
        $cm = get_coursemodule_from_instance('forum', $forum->id, $courseid);
        if ($firstpost && $cm) {
            $context = context_module::instance($cm->id);
            foreach ($this->module['materials'] ?? [] as $material) {
                if (array_key_first($material) === 'driveFile') {
                    drive_files::import($this->provider, $material['driveFile']['driveFile']['id'],
                        $context->id, (int) $USER->id, 'mod_forum', 'attachment', '/', (int) $firstpost);
                }
            }
        }
        return new response_module(true, $this, null);
    }
}
