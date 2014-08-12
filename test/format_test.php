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
 * Collapsed Topics Information
 *
 * A topic based format that solves the issue of the 'Scroll of Death' when a course has many topics. All topics
 * except zero have a toggle that displays that topic. One or more topics can be displayed at any given time.
 * Toggles are persistent on a per browser session per course basis but can be made to persist longer by a small
 * code change. Full installation instructions, code adaptions and credits are included in the 'Readme.txt' file.
 *
 * @package    course/format
 * @subpackage topcoll
 * @version    See the value of '$plugin->version' in below.
 * @copyright  &copy; 2014-onwards G J Barnard in respect to modifications of standard topics format.
 * @author     G J Barnard - gjbarnard at gmail dot com and {@link http://moodle.org/user/profile.php?id=442195}
 * @link       http://docs.moodle.org/en/Collapsed_Topics_course_format
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 */
defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/course/format/topcoll/togglelib.php');

$run = false;
if ($run) {
    // TEST CODE.
    for ($i = 0; $i < 64; $i++) {
        user_preference_allow_ajax_update('topcoll_toggle_a'.$i.'_' . $course->id, PARAM_TOPCOLL);
        user_preference_allow_ajax_update('topcoll_toggle_b'.$i.'_' . $course->id, PARAM_TOPCOLL);
        user_preference_allow_ajax_update('topcoll_toggle_c'.$i.'_' . $course->id, PARAM_TOPCOLL);
    }
    user_preference_allow_ajax_update('topcoll_toggle_bf_' . $course->id, PARAM_TOPCOLL);
    user_preference_allow_ajax_update('topcoll_toggle_bf2_' . $course->id, PARAM_TOPCOLL);
    user_preference_allow_ajax_update('topcoll_toggle_bf3_' . $course->id, PARAM_TOPCOLL);
    user_preference_allow_ajax_update('topcoll_toggle_af_' . $course->id, PARAM_TOPCOLL);
    user_preference_allow_ajax_update('topcoll_toggle_af2_' . $course->id, PARAM_TOPCOLL);
    user_preference_allow_ajax_update('topcoll_toggle_af3_' . $course->id, PARAM_TOPCOLL);
    // Test clean_param to see if it accepts '<' and '>' for PARAM_TEXT as stated in moodlelib.php.
    echo '<h3>PARAM_TEXT < : '.clean_param('<',PARAM_TEXT).'</h3>';
    echo '<h3>PARAM_TEXT > : '.clean_param('>',PARAM_TEXT).'</h3>';
    echo '<h3>PARAM_RAW  < : '.clean_param('<',PARAM_RAW).'</h3>';
    echo '<h3>PARAM_RAW  > : '.clean_param('>',PARAM_RAW).'</h3>';
}