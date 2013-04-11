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
 * @version    See the value of '$plugin->version' in version.php.
 * @copyright  &copy; 2012-onwards G J Barnard in respect to modifications of standard topics format.
 * @author     G J Barnard - gjbarnard at gmail dot com and {@link http://moodle.org/user/profile.php?id=442195}
 * @author     Based on code originally written by Dan Poltawski.
 * @link       http://docs.moodle.org/en/Collapsed_Topics_course_format
 * @license    http://www.gnu.org/copyleft/gpl.html GNU Public License
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

defined('MOODLE_INTERNAL') || die();
require_once($CFG->dirroot . '/course/format/renderer.php');
require_once($CFG->dirroot . '/course/format/topcoll/lib.php');

class format_topcoll_renderer extends format_section_renderer_base {

    private $tccolumnwidth = 100; /* Default width in percent of the column(s). */
    private $tccolumnpadding = 0; /* Defailt padding in pixels of the column(s). */
    private $mobiletheme = false; /* As not using a mobile theme we can react to the number of columns setting. */
    private $tablettheme = false; /* As not using a tablet theme we can react to the number of columns setting. */
    private $courseformat; // Our course format object as defined in lib.php;
    private $tcsettings; // Settings for the format - array.
    private $userpreference; // User toggle state preference - string.
    private $defaultuserpreference; // Default user preference when none set - bool - true all open, false all closed.

    /**
     * Generate the starting container html for a list of sections
     * @return string HTML to output.
     */

    protected function start_section_list() {
        return html_writer::start_tag('ul', array('class' => 'ctopics'));
    }

    /**
     * Generate the starting container html for a list of sections when showing a toggle.
     * @return string HTML to output.
     */
    protected function start_toggle_section_list() {
        $classes = 'ctopics topics';
        $style = '';
        if ($this->tcsettings['layoutcolumnorientation'] == 1) {
            $style .= 'width:' . $this->tccolumnwidth . '%;';  // Vertical columns.
        } else {
            $style .= 'width:100%;';  // Horizontal columns.
        }
        if ($this->mobiletheme === false) {
            $classes .= ' ctlayout';
        }
        $style .= ' padding:' . $this->tccolumnpadding . 'px;';
        $attributes = array('class' => $classes);
        $attributes['style'] = $style;
        return html_writer::start_tag('ul', $attributes);
    }

    /**
     * Generate the closing container html for a list of sections
     * @return string HTML to output.
     */
    protected function end_section_list() {
        return html_writer::end_tag('ul');
    }

    /**
     * Generate the title for this section page
     * @return string the page title
     */
    protected function page_title() {
        return get_string('sectionname', 'format_topcoll');
    }

    /**
     * Generate the content to displayed on the right part of a section
     * before course modules are included
     *
     * @param stdClass $section The course_section entry from DB
     * @param stdClass $course The course entry from DB
     * @param bool $onsectionpage true if being printed on a section page
     * @return string HTML to output.
     */
    protected function section_right_content($section, $course, $onsectionpage) {
        $o = $this->output->spacer();

        if ($section->section != 0) {
            $controls = $this->section_edit_controls($course, $section, $onsectionpage);
            if (!empty($controls)) {
                $o .= implode('<br />', $controls);
            } else {
                if (empty($this->tcsettings)) {
                    $this->tcsettings = $this->courseformat->get_settings();
                }
                switch ($this->tcsettings['layoutelement']) {
                    case 1:
                    case 3:
                    case 5:
                        // Get the specific words from the language files.
                        $topictext = null;
                        if (($this->tcsettings['layoutstructure'] == 1) || ($this->tcsettings['layoutstructure'] == 4)) {
                            $topictext = get_string('setlayoutstructuretopic', 'format_topcoll');
                        } else if (($this->tcsettings['layoutstructure'] == 2) || ($this->tcsettings['layoutstructure'] == 3)) {
                            $topictext = get_string('setlayoutstructureweek', 'format_topcoll');
                        } else {
                            $topictext = get_string('setlayoutstructureday', 'format_topcoll');
                        }

                        $o .= html_writer::tag('span', $topictext . html_writer::empty_tag('br') . $section->section, array('class' => 'cps_centre'));
                        break;
                }
            }
        }

        return $o;
    }

    /**
     * Generate the content to displayed on the left part of a section
     * before course modules are included
     *
     * @param stdClass $section The course_section entry from DB
     * @param stdClass $course The course entry from DB
     * @param bool $onsectionpage true if being printed on a section page
     * @return string HTML to output.
     */
    protected function section_left_content($section, $course, $onsectionpage) {
        $o = $this->output->spacer();

        if ($section->section != 0) {
            // Only in the non-general sections.
            if (course_get_format($course)->is_section_current($section)) {
                $o .= get_accesshide(get_string('currentsection', 'format_' . $course->format));
            }
            if (empty($this->tcsettings)) {
                $this->tcsettings = $this->courseformat->get_settings();
            }
            switch ($this->tcsettings['layoutelement']) {
                case 1:
                case 2:
                case 5:
                case 6:
                    $o .= html_writer::tag('span', $section->section, array('class' => 'cps_centre'));
                    break;
            }
        }
        return $o;
    }

    /**
     * Generate the edit controls of a section
     *
     * @param stdClass $course The course entry from DB
     * @param stdClass $section The course_section entry from DB
     * @param bool $onsectionpage true if being printed on a section page
     * @return array of links with edit controls
     */
    protected function section_edit_controls($course, $section, $onsectionpage = false) {
        global $PAGE;

        if (!$PAGE->user_is_editing()) {
            return array();
        }

        $coursecontext = context_course::instance($course->id);

        if ($onsectionpage) {
            $url = course_get_url($course, $section->section);
        } else {
            $url = course_get_url($course);
        }
        $url->param('sesskey', sesskey());

        if (empty($this->tcsettings)) {
            $this->tcsettings = $this->courseformat->get_settings();
        }
        $controls = array();
        if ((($this->tcsettings['layoutstructure'] == 1) || ($this->tcsettings['layoutstructure'] == 4)) && has_capability('moodle/course:setcurrentsection', $coursecontext)) {
            if ($course->marker == $section->section) {  // Show the "light globe" on/off.
                $url->param('marker', 0);
                $controls[] = html_writer::link($url, html_writer::empty_tag('img', array('src' => $this->output->pix_url('i/marked'),
                                    'class' => 'icon ', 'alt' => get_string('markedthissection', 'format_topcoll'))), array('title' => get_string('markedthissection', 'format_topcoll'), 'class' => 'editing_highlight'));
            } else {
                $url->param('marker', $section->section);
                $controls[] = html_writer::link($url, html_writer::empty_tag('img', array('src' => $this->output->pix_url('i/marker'),
                                    'class' => 'icon', 'alt' => get_string('markthissection', 'format_topcoll'))), array('title' => get_string('markthissection', 'format_topcoll'), 'class' => 'editing_highlight'));
            }
        }

        return array_merge($controls, parent::section_edit_controls($course, $section, $onsectionpage));
    }

    /**
     * Generate the display of the header part of a section before
     * course modules are included
     *
     * @param stdClass $section The course_section entry from DB
     * @param stdClass $course The course entry from DB
     * @param bool $onsectionpage true if being printed on a section page
     * @param int $sectionreturn The section to return to after an action
     * @return string HTML to output.
     */
    protected function section_header($section, $course, $onsectionpage, $sectionreturn = null) {
        $o = '';
        global $PAGE;

        $sectionstyle = '';
        $rightcurrent = '';
        $context = context_course::instance($course->id);

        if ($section->section != 0) {
            // Only in the non-general sections.
            if (!$section->visible) {
                $sectionstyle = ' hidden';
            } else if (course_get_format($course)->is_section_current($section)) {
                $section->toggle = '1'; // Open current section regardless of toggle state.
                $sectionstyle = ' current';
                $rightcurrent = ' left';
            }
        }

        $liattributes = array('id' => 'section-' . $section->section,
            'class' => 'section main clearfix' . $sectionstyle);
        if ($this->tcsettings['layoutcolumnorientation'] == 2) { // Horizontal column layout.
            $liattributes['style'] = 'width:' . $this->tccolumnwidth . '%;';
        }
        $o .= html_writer::start_tag('li', $liattributes);

        if (($this->mobiletheme === false) && ($this->tablettheme === false)) {
            $leftcontent = $this->section_left_content($section, $course, $onsectionpage);
            $o .= html_writer::tag('div', $leftcontent, array('class' => 'left side'));
        }

        if (($this->mobiletheme === false) && ($this->tablettheme === false)) {
            $rightcontent = '';
            if (($section->section != 0) && $PAGE->user_is_editing() && has_capability('moodle/course:update', $context)) {
                $url = new moodle_url('/course/editsection.php', array('id' => $section->id, 'sr' => $sectionreturn));

                $rightcontent .= html_writer::link($url, html_writer::empty_tag('img', array('src' => $this->output->pix_url('t/edit'),
                                    'class' => 'iconsmall edit tceditsection', 'alt' => get_string('edit'))), array('title' => get_string('editsummary'), 'class' => 'tceditsection'));
                $rightcontent .= html_writer::empty_tag('br');
            }
            $rightcontent .= $this->section_right_content($section, $course, $onsectionpage);
            $o .= html_writer::tag('div', $rightcontent, array('class' => 'right side'));
        }
        $o .= html_writer::start_tag('div', array('class' => 'content'));

        if (($onsectionpage == false) && ($section->section != 0)) {
            $o .= html_writer::start_tag('div', array('class' => 'sectionhead toggle toggle-'.$this->tcsettings['toggleiconset'], 'id' => 'toggle-' . $section->section));

            $title = get_section_name($course, $section);
            if ((!($section->toggle === null)) && ($section->toggle == '1')) {
                $toggleclass = 'toggle_open';
                $sectionstyle = 'display: block;';
            } else {
                $toggleclass = 'toggle_closed';
                $sectionstyle = '';
            }
            $toggleclass .= ' the_toggle';
            $toggleurl = new moodle_url('/course/view.php', array('id' => $course->id));
            $o .= html_writer::start_tag('a', array('class' => $toggleclass, 'href' => $toggleurl));

            if (empty($this->tcsettings)) {
                $this->tcsettings = $this->courseformat->get_settings();
            }

            $otitle = $title;
            if ((string) $section->name !== '') {
                if (($this->tcsettings['layoutstructure'] == 2) || ($this->tcsettings['layoutstructure'] == 3) || ($this->tcsettings['layoutstructure'] == 5)) {
                    $otitle .= ' '.html_writer::empty_tag('br');
                    $otitle .= $this->courseformat->get_section_dates($section, $course, $this->tcsettings);
                }
            }
            // Add in the word toggle when we are displaying them for one section per page layout, see 'get_section_name()' in 'lib.php' for more information.
            if ($course->coursedisplay == COURSE_DISPLAY_MULTIPAGE) {
                switch ($this->tcsettings['layoutelement']) {
                    case 1:
                    case 2:
                    case 3:
                    case 4:
                        $otitle .= ' - ' . get_string('topcolltoggle', 'format_topcoll'); // The word 'Toggle'.
                        break;
                }
            }
            if (($this->mobiletheme === false) && ($this->tablettheme === false)) {
                $o .= $this->output->heading($otitle, 3, 'sectionname');
            } else {
                $o .= html_writer::tag('h3', $otitle); // Moodle H3's look bad on mobile / tablet with CT so use plain.
            }

            $o .= html_writer::end_tag('a');
            $o .= html_writer::end_tag('div');
            $o .= html_writer::start_tag('div', array('class' => 'sectionbody toggledsection', 'id' => 'toggledsection-' . $section->section, 'style' => $sectionstyle));
            if ($section->section != 0 && $course->coursedisplay == COURSE_DISPLAY_MULTIPAGE) {
                $o .= html_writer::link(course_get_url($course, $section->section), $title);
            }

            if ($PAGE->user_is_editing() && has_capability('moodle/course:update', $context)) {
                $url = new moodle_url('/course/editsection.php', array('id' => $section->id, 'sr' => $sectionreturn));
                $o.= html_writer::link($url, html_writer::empty_tag('img', array('src' => $this->output->pix_url('t/edit'),
                                    'class' => 'iconsmall edit', 'alt' => get_string('edit'))), array('title' => get_string('editsummary')));
            }

            $o .= html_writer::start_tag('div', array('class' => 'summary'));
            $o .= $this->format_summary_text($section);

            $o .= html_writer::end_tag('div');

            $o .= $this->section_availability_message($section, has_capability('moodle/course:viewhiddensections', $context));
        } else {
            // When on a section page, we only display the general section title, if title is not the default one.
            $hasnamesecpg = ($section->section == 0 && (string) $section->name !== '');

            if ($hasnamesecpg) {
                $o .= $this->output->heading($this->section_title($section, $course), 3, 'sectionname');
            }
            //$o .= parent::section_header($section, $course, $onsectionpage);
            $o .= html_writer::start_tag('div', array('class' => 'summary'));
            $o .= $this->format_summary_text($section);

            if ($PAGE->user_is_editing() && has_capability('moodle/course:update', $context)) {
                $url = new moodle_url('/course/editsection.php', array('id' => $section->id, 'sr' => $sectionreturn));
                $o.= html_writer::link($url, html_writer::empty_tag('img', array('src' => $this->output->pix_url('t/edit'),
                                    'class' => 'iconsmall edit', 'alt' => get_string('edit'))), array('title' => get_string('editsummary')));
            }
            $o .= html_writer::end_tag('div');

            $o .= $this->section_availability_message($section, has_capability('moodle/course:viewhiddensections', $context));
        }
        return $o;
    }

    /**
     * Generate the display of the footer part of a section
     *
     * @return string HTML to output.
     */
    protected function section_footer() {
        $o = html_writer::end_tag('div');
        $o .= html_writer::end_tag('li');

        return $o;
    }

    /**
     * Generate the html for the 'Jump to' menu on a single section page.
     * Temporary until MDL-34917 in core.
     * @param stdClass $course The course entry from DB
     * @param $displaysection the current displayed section number.
     *
     * @return string HTML to output.
     */
    protected function section_nav_selection($course, $displaysection) {
        global $CFG;
        $o = '';
        $sectionmenu = array();
        $url = course_get_url($course);
        $url = str_replace($CFG->wwwroot, '', $url);
        $url = str_replace('&amp;', '&', $url);
        $sectionmenu[$url] = get_string('maincoursepage', 'format_topcoll');
        $modinfo = get_fast_modinfo($course);
        $section = 1;
        while ($section <= $course->numsections) {
            $thissection = $modinfo->get_section_info($section);
            $showsection = $thissection->uservisible or !$course->hiddensections;
            if (($showsection) && ($section != $displaysection)) {
                $url = course_get_url($course, $section);
                $url = str_replace($CFG->wwwroot, '', $url);
                $url = str_replace('&amp;', '&', $url);
                $sectionmenu[$url] = get_section_name($course, $section);
            }
            $section++;
        }

        $select = new url_select($sectionmenu);
        $select->class = 'jumpmenu';
        $select->formid = 'sectionmenu';
        $o .= $this->output->render($select);

        return $o;
    }

    /**
     * Output the html for a single section page.
     *
     * @param stdClass $course The course entry from DB
     * @param array $sections (argument not used)
     * @param array $mods (argument not used)
     * @param array $modnames (argument not used)
     * @param array $modnamesused (argument not used)
     * @param int $displaysection The section number in the course which is being displayed
     */
    public function print_single_section_page($course, $sections, $mods, $modnames, $modnamesused, $displaysection) {
        global $PAGE;

        $modinfo = get_fast_modinfo($course);
        $this->courseformat = course_get_format($course); // Needed for collapsed topics settings retrieval.
        // Can we view the section in question?
        if (!($sectioninfo = $modinfo->get_section_info($displaysection))) {
            // This section doesn't exist.
            print_error('unknowncoursesection', 'error', null, $course->fullname);
            return;
        }

        if (!$sectioninfo->uservisible) {
            if (!$course->hiddensections) {
                echo $this->start_section_list();
                echo $this->section_hidden($displaysection);
                echo $this->end_section_list();
            }
            // Can't view this section.
            return;
        }

        // Copy activity clipboard..
        echo $this->course_activity_clipboard($course, $displaysection);

        $thissection = $modinfo->get_section_info(0);
        if ($thissection->summary or !empty($modinfo->sections[0]) or $PAGE->user_is_editing()) {
            echo $this->start_section_list();
            echo $this->section_header($thissection, $course, true, $displaysection);
            print_section($course, $thissection, null, null, true, "100%", false, $displaysection);
            if ($PAGE->user_is_editing()) {
                print_section_add_menus($course, 0, null, false, false, $displaysection);
            }
            echo $this->section_footer();
            echo $this->end_section_list();
        }

        // Start single-section div.
        echo html_writer::start_tag('div', array('class' => 'single-section'));

        // The requested section page.
        $thissection = $modinfo->get_section_info($displaysection);

        // Title with section navigation links.
        $sectionnavlinks = $this->get_nav_links($course, $modinfo->get_section_info_all(), $displaysection);
        $sectiontitle = '';
        $sectiontitle .= html_writer::start_tag('div', array('class' => 'section-navigation header headingblock'));
        $sectiontitle .= html_writer::tag('span', $sectionnavlinks['previous'], array('class' => 'mdl-left'));
        $sectiontitle .= html_writer::tag('span', $sectionnavlinks['next'], array('class' => 'mdl-right'));
        // Title attributes
        $titleattr = 'mdl-align title';
        if (!$thissection->visible) {
            $titleattr .= ' dimmed_text';
        }
        $sectiontitle .= html_writer::tag('div', get_section_name($course, $thissection), array('class' => $titleattr));
        $sectiontitle .= html_writer::end_tag('div');
        echo $sectiontitle;

        // Now the list of sections..
        echo $this->start_section_list();

        // The requested section page.
        $thissection = $modinfo->get_section_info($displaysection);
        echo $this->section_header($thissection, $course, true, $displaysection);
        // Show completion help icon.
        $completioninfo = new completion_info($course);
        echo $completioninfo->display_help_icon();

        print_section($course, $thissection, null, null, true, '100%', false, $displaysection);
        if ($PAGE->user_is_editing()) {
            print_section_add_menus($course, $displaysection, null, false, false, $displaysection);
        }
        echo $this->section_footer();
        echo $this->end_section_list();

        // Display section bottom navigation.
        $sectionbottomnav = '';
        $sectionbottomnav .= html_writer::start_tag('div', array('class' => 'section-navigation mdl-bottom'));
        $sectionbottomnav .= html_writer::tag('span', $sectionnavlinks['previous'], array('class' => 'mdl-left'));
        $sectionbottomnav .= html_writer::tag('span', $sectionnavlinks['next'], array('class' => 'mdl-right'));
        $sectionbottomnav .= html_writer::tag('div', $this->section_nav_selection($course, $displaysection), array('class' => 'mdl-align'));
        $sectionbottomnav .= html_writer::end_tag('div');
        echo $sectionbottomnav;

        // close single-section div.
        echo html_writer::end_tag('div');
    }

    /**
     * Output the html for a multiple section page
     *
     * @param stdClass $course The course entry from DB
     * @param array $sections (argument not used)
     * @param array $mods (argument not used)
     * @param array $modnames (argument not used)
     * @param array $modnamesused (argument not used)
     */
    public function print_multiple_section_page($course, $sections, $mods, $modnames, $modnamesused) {
        global $PAGE;

        $userisediting = $PAGE->user_is_editing();

        $modinfo = get_fast_modinfo($course);
        $this->courseformat = course_get_format($course);
        $course = $this->courseformat->get_course();
        if (empty($this->tcsettings)) {
            $this->tcsettings = $this->courseformat->get_settings();
        }

        $context = context_course::instance($course->id);
        // Title with completion help icon.
        $completioninfo = new completion_info($course);
        echo $completioninfo->display_help_icon();
        echo $this->output->heading($this->page_title(), 2, 'accesshide');

        // Copy activity clipboard..
        echo $this->course_activity_clipboard($course, 0);

        // Now the list of sections..
        $this->tccolumnwidth = 100; // Reset to default.
        echo $this->start_section_list();

        $sections = $modinfo->get_section_info_all();
        // General section if non-empty.
        $thissection = $sections[0];
        unset($sections[0]);
        if ($thissection->summary or !empty($modinfo->sections[0]) or $PAGE->user_is_editing()) {
            echo $this->section_header($thissection, $course, false, 0);
            print_section($course, $thissection, null, null, true, "100%", false, 0);
            if ($PAGE->user_is_editing()) {
                print_section_add_menus($course, 0, null, false, false, 0);
            }
            echo $this->section_footer();
        }

        if ($course->numsections > 0) {
            if ($course->numsections > 1) {
                if ($PAGE->user_is_editing() || $course->coursedisplay != COURSE_DISPLAY_MULTIPAGE) {
                    // Collapsed Topics all toggles.
                    echo $this->toggle_all();
                }
            }
            $currentsectionfirst = false;
            if ($this->tcsettings['layoutstructure'] == 4) {
                $currentsectionfirst = true;
            }

            if (($this->tcsettings['layoutstructure'] != 3) || ($userisediting)) {
                $section = 1;
            } else {
                $timenow = time();
                $weekofseconds = 604800;
                $course->enddate = $course->startdate + ($weekofseconds * $course->numsections);
                $section = $course->numsections;
                $weekdate = $course->enddate;      // this should be 0:00 Monday of that week
                $weekdate -= 7200;                 // Subtract two hours to avoid possible DST problems
            }

            $numsections = $course->numsections; // Because we want to manipulate this for column breakpoints.
            if (($this->tcsettings['layoutstructure'] == 3) && ($userisediting == false)) {
                $loopsection = 1;
                $numsections = 0;
                while ($loopsection <= $course->numsections) {
                    $nextweekdate = $weekdate - ($weekofseconds);
                    if ((($thissection->uservisible ||
                            ($thissection->visible && !$thissection->available && $thissection->showavailability))
                            && ($nextweekdate <= $timenow)) == true) {
                        $numsections++; // Section not shown so do not count in columns calculation.
                    }
                    $weekdate = $nextweekdate;
                    $section--;
                    $loopsection++;
                }
                // Reset
                $section = $course->numsections;
                $weekdate = $course->enddate;      // this should be 0:00 Monday of that week.
                $weekdate -= 7200;                 // Subtract two hours to avoid possible DST problems.
            }

            if ($numsections < $this->tcsettings['layoutcolumns']) {
                $this->tcsettings['layoutcolumns'] = $numsections;  // Help to ensure a reasonable display.
            }
            if (($this->tcsettings['layoutcolumns'] > 1) && ($this->mobiletheme === false)) {
                if ($this->tcsettings['layoutcolumns'] > 4) {
                    // Default in config.php (and reset in database) or database has been changed incorrectly.
                    $this->tcsettings['layoutcolumns'] = 4;

                    // Update....
                    $this->courseformat->update_topcoll_columns_setting($this->tcsettings['layoutcolumns']);
                }

                if (($this->tablettheme === true) && ($this->tcsettings['layoutcolumns'] > 2)) {
                    // Use a maximum of 2 for tablets.
                    $this->tcsettings['layoutcolumns'] = 2;
                }

                $this->tccolumnwidth = 100 / $this->tcsettings['layoutcolumns'];
                $this->tccolumnwidth -= 1; // Allow for the padding in %.
                $this->tccolumnpadding = 2; // px
            } else if ($this->tcsettings['layoutcolumns'] < 1) {
                // Distributed default in plugin settings (and reset in database) or database has been changed incorrectly.
                $this->tcsettings['layoutcolumns'] = 1;

                // Update....
                $this->courseformat->update_topcoll_columns_setting($this->tcsettings['layoutcolumns']);
            }

            echo $this->end_section_list();
            echo $this->start_toggle_section_list();

            $loopsection = 1;
            $canbreak = false; // Once the first section is shown we can decide if we break on another column.
            $columncount = 1;
            $columnbreakpoint = 0;
            $shownsectioncount = 0;

            if ($this->userpreference != null) {
                $ts1 = base_convert(substr($this->userpreference, 0, 6), 36, 2);
                $ts2 = base_convert(substr($this->userpreference, 6, 12), 36, 2);
                $thesparezeros = "00000000000000000000000000";
                if (strlen($ts1) < 26) {
                    // Need to PAD.
                    $ts1 = substr($thesparezeros, 0, (26 - strlen($ts1))) . $ts1;
                }
                if (strlen($ts2) < 27) {
                    // Need to PAD.
                    $ts2 = substr($thesparezeros, 0, (27 - strlen($ts2))) . $ts2;
                }
                $tb = $ts1 . $ts2;
            } else {
                if ($this->defaultuserpreference == 0) {
                    $tb = '10000000000000000000000000000000000000000000000000000';
                } else {
                    $tb = '11111111111111111111111111111111111111111111111111111';
                }
            }

            while ($loopsection <= $course->numsections) {
                if (($this->tcsettings['layoutstructure'] == 3) && ($userisediting == false)) {
                    $nextweekdate = $weekdate - ($weekofseconds);
                }
                $thissection = $modinfo->get_section_info($section);

                /* Show the section if the user is permitted to access it, OR if it's not available
                   but showavailability is turned on. */
                if (($this->tcsettings['layoutstructure'] != 3) || ($userisediting)) {
                    $showsection = $thissection->uservisible ||
                            ($thissection->visible && !$thissection->available && $thissection->showavailability);
                } else {
                    $showsection = ($thissection->uservisible ||
                            ($thissection->visible && !$thissection->available && $thissection->showavailability))
                            && ($nextweekdate <= $timenow);
                }
                if (($currentsectionfirst == true) && ($showsection == true)) {
                    $showsection = ($course->marker == $section);  // Show  the section if we were meant to and it is the current section.
                } else if (($this->tcsettings['layoutstructure'] == 4) && ($course->marker == $section)) {
                    $showsection = false; // Do not reshow current section.
                }
                if (!$showsection) {
                    // Hidden section message is overridden by 'unavailable' control (showavailability option).
                    if ($this->tcsettings['layoutstructure'] != 4) {
                        if (($this->tcsettings['layoutstructure'] != 3) || ($userisediting)) {
                            if (!$course->hiddensections && $thissection->available) {
                                echo $this->section_hidden($section);
                            }
                        }
                    }
                } else {
                    $shownsectioncount++;
                    if (!$PAGE->user_is_editing() && $course->coursedisplay == COURSE_DISPLAY_MULTIPAGE) {
                        // Display section summary only.
                        echo $this->section_summary($thissection, $course, null);
                    } else {
                        $thissection->toggle = substr($tb, $section, 1);
                        echo $this->section_header($thissection, $course, false, 0);
                        if ($thissection->uservisible) {
                            print_section($course, $thissection, null, null, true, "100%", false, 0);
                            if ($PAGE->user_is_editing()) {
                                print_section_add_menus($course, $section, null, false, false, 0);
                            }
                        }
                        echo html_writer::end_tag('div');
                        echo $this->section_footer();
                    }
                }

                if ($currentsectionfirst == false) {
                    unset($sections[$section]); // Only need to do this on the iteration when $currentsectionfirst is not true as this iteration will always happen.  Otherwise you get duplicate entries in course_sections in the DB.
                }
                if (($this->tcsettings['layoutstructure'] != 3) || ($userisediting)) {
                    $section++;
                } else {
                    $section--;
                    if (($this->tcsettings['layoutstructure'] == 3) && ($userisediting == false)) {
                        $weekdate = $nextweekdate;
                    }
                }

                if ($this->mobiletheme === false) { // Only break in non-mobile themes.
                    if ($this->tcsettings['layoutcolumnorientation'] == 1) {  // Only break columns in horizontal mode.
                        if (($canbreak == false) && ($currentsectionfirst == false) && ($showsection == true)) {
                            $canbreak = true;
                            $columnbreakpoint = ($shownsectioncount + ($numsections / $this->tcsettings['layoutcolumns'])) - 1;
                            if ($this->tcsettings['layoutstructure'] == 4) {
                                $columnbreakpoint -= 1;
                            }
                        }

                        if (($currentsectionfirst == false) && ($canbreak == true) && ($shownsectioncount >= $columnbreakpoint) && ($columncount < $this->tcsettings['layoutcolumns'])) {
                        echo $this->end_section_list();
                            echo $this->start_toggle_section_list();
                            $columncount++;
                            // Next breakpoint is...
                            $columnbreakpoint += $numsections / $this->tcsettings['layoutcolumns'];
                        }
                    }
                }

                $loopsection++;
                if (($currentsectionfirst == true) && ($loopsection > $course->numsections)) {
                    // Now show the rest.
                    $currentsectionfirst = false;
                    $loopsection = 1;
                    $section = 1;
                }
                if ($section > $course->numsections) {
                    // activities inside this section are 'orphaned', this section will be printed as 'stealth' below.
                    continue;
                }
            }
        }

        if ($PAGE->user_is_editing() and has_capability('moodle/course:update', $context)) {
            // Print stealth sections if present.
            foreach ($modinfo->get_section_info_all() as $section => $thissection) {
                if ($section <= $course->numsections or empty($modinfo->sections[$section])) {
                    // this is not stealth section or it is empty
                    continue;
                }
                echo $this->stealth_section_header($section);
                print_section($course, $thissection, null, null, true, "100%", false, 0);
                echo $this->stealth_section_footer();
            }

            echo $this->end_section_list();

            echo html_writer::start_tag('div', array('id' => 'changenumsections', 'class' => 'mdl-right'));

            // Increase number of sections.
            $straddsection = get_string('increasesections', 'moodle');
            $url = new moodle_url('/course/changenumsections.php',
                            array('courseid' => $course->id,
                                'increase' => true,
                                'sesskey' => sesskey()));
            $icon = $this->output->pix_icon('t/switch_plus', $straddsection);
            echo html_writer::link($url, $icon . get_accesshide($straddsection), array('class' => 'increase-sections'));

            if ($course->numsections > 0) {
                // Reduce number of sections sections.
                $strremovesection = get_string('reducesections', 'moodle');
                $url = new moodle_url('/course/changenumsections.php',
                                array('courseid' => $course->id,
                                    'increase' => false,
                                    'sesskey' => sesskey()));
                $icon = $this->output->pix_icon('t/switch_minus', $strremovesection);
                echo html_writer::link($url, $icon . get_accesshide($strremovesection), array('class' => 'reduce-sections'));
            }

            echo html_writer::end_tag('div');
        } else {
            echo $this->end_section_list();
        }
    }

    /**
     * Displays the toggle all fuctionality.
     * @return string HTML to output.
     */
    public function toggle_all() {
        $o = '';

        // Toggle all.
        $o .= html_writer::start_tag('li', array('class' => 'tcsection main clearfix', 'id' => 'toggle-all'));

        if (($this->mobiletheme === false) || ($this->tablettheme === false)) {
            $o.= html_writer::tag('div', $this->output->spacer(), array('class' => 'left side'));
        }
        $o .= html_writer::tag('div', $this->output->spacer(), array('class' => 'right side'));

        $o .= html_writer::start_tag('div', array('class' => 'content'));
        $iconsetclass = ' toggle-'.$this->tcsettings['toggleiconset'];
        if ($this->tcsettings['toggleallhover'] == 2) {
            $iconsetclass .= '-hover'.$iconsetclass;
        }
        $o .= html_writer::start_tag('div', array('class' => 'sectionbody'.$iconsetclass));
        $o .= html_writer::start_tag('h4', null);
        $o .= html_writer::tag('a', get_string('topcollopened', 'format_topcoll'), array('class' => 'on', 'href' => '#', 'id' => 'toggles-all-opened'));
        $o .= html_writer::tag('a', get_string('topcollclosed', 'format_topcoll'), array('class' => 'off', 'href' => '#', 'id' => 'toggles-all-closed'));
        $o .= html_writer::end_tag('h4');
        $o .= html_writer::end_tag('div');
        $o .= html_writer::end_tag('div');
        $o .= html_writer::end_tag('li');

        return $o;
    }

    public function set_portable($portable) {
        switch ($portable) {
            case 1:
                $this->mobiletheme = true;
            break;
            case 2:
                $this->tablettheme = true;
            break;
            default:
                $this->mobiletheme = false;
                $this->tablettheme = false;
            break;
        }
    }

    public function set_user_preference($preference) {
        $this->userpreference = $preference;
    }

    public function set_default_user_preference($defaultpreference) {
        $this->defaultuserpreference = $defaultpreference;
    }
}
