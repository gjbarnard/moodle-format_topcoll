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
 * @copyright  &copy; 2012-onwards G J Barnard in respect to modifications of standard topics format.
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

class set_settings_form extends moodleform {

    function definition() {
        global $CFG, $USER; //, $DB;
        //MoodleQuickForm::registerElementType('tccolourpopup', "$CFG->dirroot/course/format/topcoll/js/tc_colourpopup.php", 'MoodleQuickForm_tccolourpopup');

        $mform = $this->_form;
        $instance = $this->_customdata;

        $mform->addElement('header', 'ctreset', get_string('ctreset', 'format_topcoll'));
        $mform->addHelpButton('ctreset', 'ctreset', 'format_topcoll', '', true);

        $mform->addElement('checkbox', 'resetlayout', get_string('resetlayout', 'format_topcoll'), false);
        $mform->addHelpButton('resetlayout', 'resetlayout', 'format_topcoll', '', true);

        if (is_siteadmin($USER)) {
            $mform->addElement('checkbox', 'resetalllayout', get_string('resetalllayout', 'format_topcoll'), false);
            $mform->addHelpButton('resetalllayout', 'resetalllayout', 'format_topcoll', '', true);
        }

        $mform->addElement('checkbox', 'resetcolour', get_string('resetcolour', 'format_topcoll'), false);
        $mform->addHelpButton('resetcolour', 'resetcolour', 'format_topcoll', '', true);

        if (is_siteadmin($USER)) {
            $mform->addElement('checkbox', 'resetallcolour', get_string('resetallcolour', 'format_topcoll'), false);
            $mform->addHelpButton('resetallcolour', 'resetallcolour', 'format_topcoll', '', true);
        }

        // hidden params
        $mform->addElement('hidden', 'id', $instance['courseid']);
        $mform->setType('id', PARAM_INT);
        // buttons
        $this->add_action_buttons(true, get_string('savechanges', 'admin'));
    }
}
?>