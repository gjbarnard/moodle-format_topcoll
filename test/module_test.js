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
 * @version    See the value of '$plugin->version' in version.php.
 * @copyright  &copy; 2014-onwards G J Barnard in respect to modifications of standard topics format.
 * @author     G J Barnard - gjbarnard at gmail dot com and {@link http://moodle.org/user/profile.php?id=442195}
 * @link       http://docs.moodle.org/en/Collapsed_Topics_course_format
 * @license    http://www.gnu.org/copyleft/gpl.html GNU Public License
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.

 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.

 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */
 
M.format_topcoll.test_all_states = function() {
    "use strict";
    // Reset for this course.
    console.log('test_all_states: togglestate:' + this.togglestate);
    console.log('test_all_states: reset course.');
    M.format_topcoll.resetState(M.format_topcoll.get_min_digit());
    M.format_topcoll.save_toggles();
    console.log('test_all_states: togglestate:' + this.togglestate);

    // Loop through all possible states, this involves the first twelve toggles.
    console.log('test_all_states: start loop.');
    var state = 0;

    for (state = 0; state < 64; state++) { 
        var newchar = this.encode_value_to_character(state);
        console.log('test_all_states: newchar: ' + newchar + ' - togglestate:' + this.togglestate);

        //M.util.set_user_preference('topcoll_toggle_' + state + '_' + this.courseid, this.togglestate);
        M.format_topcoll.set_user_preference('topcoll_toggle_a' + state + '_' + this.courseid, newchar);
        M.format_topcoll.set_user_preference('topcoll_toggle_b' + state + '_' + this.courseid, newchar + ':');
        M.format_topcoll.set_user_preference('topcoll_toggle_c' + state + '_' + this.courseid, ':' + newchar);
    }
    console.log('test_all_states: end loop.');
    console.log('test_all_states: start invalid data.');
    M.format_topcoll.set_user_preference('topcoll_toggle_bf_' + this.courseid, '9');
    M.format_topcoll.set_user_preference('topcoll_toggle_af_' + this.courseid, 'z');
    M.format_topcoll.set_user_preference('topcoll_toggle_bf2_' + this.courseid, '9:');
    M.format_topcoll.set_user_preference('topcoll_toggle_af2_' + this.courseid, 'z:');
    M.format_topcoll.set_user_preference('topcoll_toggle_bf3_' + this.courseid, ':9');
    M.format_topcoll.set_user_preference('topcoll_toggle_af3_' + this.courseid, ':z');
    console.log('test_all_states: end invalid data.');
};