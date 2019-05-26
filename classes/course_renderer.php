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
 * @package    format_topcoll
 * @version    See the value of '$plugin->version' in version.php.
 * @copyright  &copy; 2018-onwards G J Barnard in respect to modifications of standard topics format.
 * @author     G J Barnard - {@link http://moodle.org/user/profile.php?id=442195}
 * @link       http://docs.moodle.org/en/Collapsed_Topics_course_format
 * @license    http://www.gnu.org/copyleft/gpl.html GNU Public License
 *
 */

defined('MOODLE_INTERNAL') || die();

class format_topcoll_course_renderer extends \core_course_renderer {

    /**
     * Renders html to display a name with the link to the course module on a course page
     *
     * If module is unavailable for user but still needs to be displayed
     * in the list, just the name is returned without a link
     *
     * Note, that for course modules that never have separate pages (i.e. labels)
     * this function return an empty string
     *
     * @param cm_info $mod
     * @param array $displayoptions
     * @return string
     */
    public function course_section_cm_name(cm_info $mod, $displayoptions = array()) {
        if (!$mod->is_visible_on_course_page() || !$mod->url) {
            // Nothing to be displayed to the user.
            return '';
        }

        list($linkclasses, $textclasses) = $this->course_section_cm_classes($mod);
        $groupinglabel = $mod->get_grouping_label($textclasses);

        /* Render element that allows to edit activity name inline. It calls {@link course_section_cm_name_title()}
           to get the display title of the activity. */
        $tmpl = new \format_topcoll\output\course_module_name($mod, $this->page->user_is_editing(), $displayoptions);
        return $this->output->render_from_template('core/inplace_editable', $tmpl->export_for_template($this->output)) .
            $groupinglabel;
    }

    /**
     * Renders html to display a name with the link to the course module on a course page
     *
     * If module is unavailable for user but still needs to be displayed
     * in the list, just the name is returned without a link
     *
     * Note, that for course modules that never have separate pages (i.e. labels)
     * this function return an empty string
     *
     * @param cm_info $mod
     * @param array $displayoptions
     * @return string
     */
    public function course_section_cm_name_title(cm_info $mod, $displayoptions = array()) {
        $output = '';
        $url = $mod->url;
        if (!$mod->is_visible_on_course_page() || !$url) {
            // Nothing to be displayed to the user.
            return $output;
        }
        // Accessibility: for files get description via icon, this is very ugly hack!
        $instancename = $mod->get_formatted_name();
        $altname = $mod->modfullname;
        /* Avoid unnecessary duplication: if e.g. a forum name already
           includes the word forum (or Forum, etc) then it is unhelpful
           to include that in the accessible description that is added. */
        if (false !== strpos(core_text::strtolower($instancename),
                core_text::strtolower($altname))) {
            $altname = '';
        }
        // File type after name, for alphabetic lists (screen reader).
        if ($altname) {
            $altname = get_accesshide(' '.$altname);
        }

        list($linkclasses, $textclasses) = $this->course_section_cm_classes($mod);

        /* Get on-click attribute value if specified and decode the onclick - it
           has already been encoded for display. */
        $onclick = htmlspecialchars_decode($mod->onclick, ENT_QUOTES);

        // Display link itself.
        if (array_key_exists('sr', $displayoptions)) {
            // Add in the section return if the module was displayed on a single section page.
            $url->param('section', $displayoptions['sr']);
        }
        $activitylink = html_writer::empty_tag('img', array('src' => $mod->get_icon_url(),
            'class' => 'iconlarge activityicon', 'alt' => ' ', 'role' => 'presentation')) .
            html_writer::tag('span', $instancename . $altname, array('class' => 'instancename'));
        if ($mod->uservisible) {
            $output .= html_writer::link($url, $activitylink, array('class' => $linkclasses, 'onclick' => $onclick));
        } else {
            /* We may be displaying this just in order to show information
               about visibility, without the actual link ($mod->is_visible_on_course_page()). */
            $output .= html_writer::tag('div', $activitylink, array('class' => $textclasses));
        }
        return $output;
    }
}
