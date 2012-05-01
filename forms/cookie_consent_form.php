<?php

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
 * @copyright  &copy; 2009-onwards G J Barnard in respect to modifications of standard topics format.
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
require_once("$CFG->libdir/formslib.php");

class set_cookie_consent_form extends moodleform {

    function definition() {
        global $CFG;

        $mform = $this->_form;
        $instance = $this->_customdata;

        $formcookieconsentoptions =
            array(2 => get_string('cookieconsentallowed', 'format_topcoll'),
                  3 => get_string('cookieconsentdenied', 'format_topcoll'));
        /* Note: Values for the field 'cookieconsent' are:
                 1 - Display message.
                 2 - Use cookie.
                 3 - Don't use cookie.
        */

        $mform->addElement('select', 'setcookieconsent', get_string('setcookieconsent', 'format_topcoll'), $formcookieconsentoptions);
        $mform->setDefault('setcookieconsent', 3);
        $mform->addHelpButton('setcookieconsent','setcookieconsent', 'format_topcoll','',true);

        // hidden params
        $mform->addElement('hidden', 'courseid', $instance['courseid']);
        $mform->setType('courseid', PARAM_INT);
        $mform->addElement('hidden', 'userid', $instance['userid']);
        $mform->setType('userid', PARAM_INT);
        // buttons
        $this->add_action_buttons(true, get_string('savechanges', 'admin'));
    }
}
?>