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

class set_settings_form extends moodleform {

    function definition() {
        global $CFG;
        MoodleQuickForm::registerElementType('tccolourpopup', "$CFG->dirroot/course/format/topcoll/js/tc_colourpopup.php", 'MoodleQuickForm_tccolourpopup');

        $mform = $this->_form;
        $instance = $this->_customdata;

        $mform->addElement('header','setlayout', get_string('setlayout', 'format_topcoll'));
        $mform->addHelpButton('setlayout','setlayout', 'format_topcoll','',true);
        $formcourselayoutelements =
            array(1 => get_string('setlayout_default', 'format_topcoll'),                                     // Default.
                  2 => get_string('setlayout_no_toggle_section_x', 'format_topcoll'),                         // No 'Topic x' / 'Week x'.
                  3 => get_string('setlayout_no_section_no', 'format_topcoll'),                               // No section number.
                  4 => get_string('setlayout_no_toggle_section_x_section_no', 'format_topcoll'),              // No 'Topic x' / 'Week x' and no section number.
                  5 => get_string('setlayout_no_toggle_word', 'format_topcoll'),                              // No 'Toggle' word.
                  6 => get_string('setlayout_no_toggle_word_toggle_section_x', 'format_topcoll'),             // No 'Toggle' word and no 'Topic x' / 'Week x'.
                  7 => get_string('setlayout_no_toggle_word_toggle_section_x_section_no', 'format_topcoll')); // No 'Toggle' word, no 'Topic x' / 'Week x'  and no section number.

        $mform->addElement('select', 'setelementnew', get_string('setlayoutelements', 'format_topcoll'), $formcourselayoutelements);
        $mform->setDefault('setelementnew', $instance['setelement']);
        $mform->addHelpButton('setelementnew','setlayoutelements', 'format_topcoll','',true);

        $formcourselayoutstrutures =
            array(1 => get_string('setlayoutstructuretopic', 'format_topcoll'),               // Topic
                  2 => get_string('setlayoutstructureweek', 'format_topcoll'),                // Week   
                  3 => get_string('setlayoutstructurelatweekfirst', 'format_topcoll'),        // Latest Week First 
                  4 => get_string('setlayoutstructurecurrenttopicfirst', 'format_topcoll'));  // Current Topic First

        $mform->addElement('select', 'setstructurenew', get_string('setlayoutstructure', 'format_topcoll'), $formcourselayoutstrutures); 
        $mform->setDefault('setstructurenew', $instance['setstructure']);
        $mform->addHelpButton('setstructurenew','setlayoutstructure', 'format_topcoll','',true);
        $mform->addElement('checkbox','resetlayout',get_string('resetlayout', 'format_topcoll'),false);
        $mform->addHelpButton('resetlayout','resetlayout', 'format_topcoll','',true);

        $mform->addElement('header','setcolour', get_string('setcolour', 'format_topcoll'));
        $mform->addHelpButton('setcolour','setcolour', 'format_topcoll','',true);

        $mform->addElement('tccolourpopup','tgfg',get_string('settoggleforegroundcolour', 'format_topcoll'),array('tabindex'=>-1,'value'=>'#'.$instance['tgfgcolour']));
        $mform->addHelpButton('tgfg','settoggleforegroundcolour', 'format_topcoll','',true);
        $mform->registerRule('colour','regex','/^#([a-fA-F0-9]{6})$/');
        $mform->addRule('tgfg',get_string('colourrule', 'format_topcoll'),'colour');

        $mform->addElement('tccolourpopup','tgbg',get_string('settogglebackgroundcolour', 'format_topcoll'),array('tabindex'=>-1,'value'=>'#'.$instance['tgbgcolour']));
        $mform->addHelpButton('tgbg','settogglebackgroundcolour', 'format_topcoll','',true);
        $mform->addRule('tgbg',get_string('colourrule', 'format_topcoll'),'colour');

        $mform->addElement('tccolourpopup','tgbghvr',get_string('settogglebackgroundhovercolour', 'format_topcoll'),array('tabindex'=>-1,'value'=>'#'.$instance['tgbghvrcolour']));
        $mform->addHelpButton('tgbghvr','settogglebackgroundhovercolour', 'format_topcoll','',true);
        $mform->addRule('tgbghvr',get_string('colourrule', 'format_topcoll'),'colour');

        $mform->addElement('checkbox','resetcolour',get_string('resetcolour', 'format_topcoll'),false);
        $mform->addHelpButton('resetcolour','resetcolour', 'format_topcoll','',true);
        // hidden params
        $mform->addElement('hidden', 'id', $instance['courseid']);
        $mform->setType('id', PARAM_INT);
        // buttons
        $this->add_action_buttons(true, get_string('savechanges', 'admin'));
    }
}

?>