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
 * @copyright  &copy; 2012-onwards G J Barnard in respect to modifications of standard topics format.
 * @author     G J Barnard - gjbarnard at gmail dot com and {@link http://moodle.org/user/profile.php?id=442195}
 * @link       http://docs.moodle.org/en/Collapsed_Topics_course_format
 * @license    http://www.gnu.org/copyleft/gpl.html GNU Public License
 *
 */

defined('MOODLE_INTERNAL') || die();
require_once($CFG->dirroot . '/course/format/renderer.php');
require_once($CFG->dirroot . '/course/format/topcoll/lib.php');
require_once($CFG->dirroot . '/course/format/topcoll/togglelib.php');

class format_topcoll_renderer extends format_section_renderer_base {

    private $tccolumnwidth = 100; // Default width in percent of the column(s).
    private $tccolumnpadding = 0; // Default padding in pixels of the column(s).
    private $mobiletheme = false; // As not using a mobile theme we can react to the number of columns setting.
    private $tablettheme = false; // As not using a tablet theme we can react to the number of columns setting.
    private $courseformat = null; // Our course format object as defined in lib.php;
    private $tcsettings; // Settings for the format - array.
    private $userpreference; // User toggle state preference - string.
    private $defaultuserpreference; // Default user preference when none set - bool - true all open, false all closed.
    private $togglelib;
    private $isoldtogglepreference = false;

    /**
     * Constructor method, calls the parent constructor - MDL-21097
     *
     * @param moodle_page $page
     * @param string $target one of rendering target constants
     */
    public function __construct(moodle_page $page, $target) {
        parent::__construct($page, $target);
        $this->togglelib = new topcoll_togglelib;
        $this->courseformat = course_get_format($page->course); // Needed for collapsed topics settings retrieval.

        /* Since format_topcoll_renderer::section_edit_controls() only displays the 'Set current section' control when editing
           mode is on we need to be sure that the link 'Turn editing mode on' is available for a user who does not have any
           other managing capability. */
        $page->set_other_editing_capability('moodle/course:setcurrentsection');
    }

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
                switch ($this->tcsettings['layoutelement']) { // Toggle section x.
                    case 1:
                    case 3:
                    case 5:
                    case 8:
                        // Get the specific words from the language files.
                        $topictext = null;
                        if (($this->tcsettings['layoutstructure'] == 1) || ($this->tcsettings['layoutstructure'] == 4)) {
                            $topictext = get_string('setlayoutstructuretopic', 'format_topcoll');
                        } else if (($this->tcsettings['layoutstructure'] == 2) || ($this->tcsettings['layoutstructure'] == 3)) {
                            $topictext = get_string('setlayoutstructureweek', 'format_topcoll');
                        } else {
                            $topictext = get_string('setlayoutstructureday', 'format_topcoll');
                        }

                        $o .= html_writer::tag('span', $topictext.html_writer::empty_tag('br').
                                               $section->section, array('class' => 'cps_centre'));
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
            if ($this->courseformat->is_section_current($section)) {
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
        if ((($this->tcsettings['layoutstructure'] == 1) || ($this->tcsettings['layoutstructure'] == 4)) &&
              has_capability('moodle/course:setcurrentsection', $coursecontext)) {
            if ($course->marker == $section->section) {  // Show the "light globe" on/off.
                $strmarkedthissection = get_string('markedthissection', 'format_topcoll');
                $url->param('marker', 0);
                $controls[] = html_writer::link($url, html_writer::empty_tag('img',
                                    array('src' => $this->output->pix_url('i/marked'),
                                          'class' => 'icon ', 'alt' => $strmarkedthissection)),
                                    array('title' => $strmarkedthissection,
                                          'class' => 'editing_highlight'));
            } else {
                $strmarkthissection = get_string('markthissection', 'format_topcoll');
                $url->param('marker', $section->section);
                $controls[] = html_writer::link($url, html_writer::empty_tag('img',
                                    array('src' => $this->output->pix_url('i/marker'),
                                          'class' => 'icon', 'alt' => $strmarkthissection)),
                                    array('title' => $strmarkthissection,
                                          'class' => 'editing_highlight'));
            }
        }

        return array_merge($controls, parent::section_edit_controls($course, $section, $onsectionpage));
    }

    /**
     * Generate a summary of a section for display on the 'course index page'
     *
     * @param stdClass $section The course_section entry from DB
     * @param stdClass $course The course entry from DB
     * @param array    $mods (argument not used)
     * @return string HTML to output.
     */
    protected function section_summary($section, $course, $mods) {
        $classattr = 'section main section-summary clearfix';
        $linkclasses = '';

        // If section is hidden then display grey section link.
        if (!$section->visible) {
            $classattr .= ' hidden';
            $linkclasses .= ' dimmed_text';
        } else if ($this->courseformat->is_section_current($section)) {
            $classattr .= ' current';
        }

        $o = '';
        $title = $this->courseformat->get_topcoll_section_name($course, $section, false);
        $liattributes = array(
            'id' => 'section-'.$section->section,
            'class' => $classattr,
            'role' => 'region',
            'aria-label'=> $title
        );
        if ($this->tcsettings['layoutcolumnorientation'] == 2) { // Horizontal column layout.
            $liattributes['style'] = 'width:' . $this->tccolumnwidth . '%;';
        }
        $o .= html_writer::start_tag('li', $liattributes);

        $o .= html_writer::tag('div', '', array('class' => 'left side'));
        $o .= html_writer::tag('div', '', array('class' => 'right side'));
        $o .= html_writer::start_tag('div', array('class' => 'content'));

        if ($section->uservisible) {
            $title = html_writer::tag('a', $title,
                    array('href' => course_get_url($course, $section->section), 'class' => $linkclasses));
        }
        $o .= $this->output->heading($title, 3, 'section-title');

        $o.= html_writer::start_tag('div', array('class' => 'summarytext'));
        $o.= $this->format_summary_text($section);
        $o.= html_writer::end_tag('div');
        $o.= $this->section_activity_summary($section, $course, null);

        $context = context_course::instance($course->id);
        $o .= $this->section_availability_message($section,
                has_capability('moodle/course:viewhiddensections', $context));

        $o .= html_writer::end_tag('div');
        $o .= html_writer::end_tag('li');

        return $o;
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
            } else if ($this->courseformat->is_section_current($section)) {
                $section->toggle = true; // Open current section regardless of toggle state.
                $sectionstyle = ' current';
                $rightcurrent = ' left';
            }
        }

        $liattributes = array(
            'id' => 'section-' . $section->section,
            'class' => 'section main clearfix' . $sectionstyle,
            'role' => 'region',
            'aria-label' => $this->courseformat->get_topcoll_section_name($course, $section, false)
        );
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

                $rightcontent .= html_writer::link($url, html_writer::empty_tag('img',
                                    array('src' => $this->output->pix_url('t/edit'),
                                          'class' => 'iconsmall edit tceditsection', 'alt' => get_string('edit'))),
                                    array('title' => get_string('editsummary'), 'class' => 'tceditsection'));
                $rightcontent .= html_writer::empty_tag('br');
            }
            $rightcontent .= $this->section_right_content($section, $course, $onsectionpage);
            $o .= html_writer::tag('div', $rightcontent, array('class' => 'right side'));
        }
        $o .= html_writer::start_tag('div', array('class' => 'content'));

        if (($onsectionpage == false) && ($section->section != 0)) {
            $o .= html_writer::start_tag('div',
                    array('class' => 'sectionhead toggle toggle-'.$this->tcsettings['toggleiconset'],
                    'id' => 'toggle-' . $section->section));

            if ((!($section->toggle === null)) && ($section->toggle == true)) {
                $toggleclass = 'toggle_open';
                $sectionclass = ' sectionopen';
            } else {
                $toggleclass = 'toggle_closed';
                $sectionclass = '';
            }
            $toggleclass .= ' the_toggle';
            $toggleurl = new moodle_url('/course/view.php', array('id' => $course->id));
            $o .= html_writer::start_tag('a', array('class' => $toggleclass, 'href' => $toggleurl));

            if (empty($this->tcsettings)) {
                $this->tcsettings = $this->courseformat->get_settings();
            }

            $title = $this->courseformat->get_topcoll_section_name($course, $section, true);
            if (($this->mobiletheme === false) && ($this->tablettheme === false)) {
                $o .= $this->output->heading($title, 3, 'section-title');
            } else {
                $o .= html_writer::tag('h3', $title); // Moodle H3's look bad on mobile / tablet with CT so use plain.
            }

            $o .= html_writer::end_tag('a');
            $o .= html_writer::end_tag('div');
            $o .= html_writer::start_tag('div', array('class' => 'sectionbody toggledsection'.$sectionclass,
                                                      'id' => 'toggledsection-' . $section->section));

            if ($PAGE->user_is_editing() && has_capability('moodle/course:update', $context)) {
                $url = new moodle_url('/course/editsection.php', array('id' => $section->id, 'sr' => $sectionreturn));
                $o.= html_writer::link($url, html_writer::empty_tag('img', array('src' => $this->output->pix_url('t/edit'),
                                    'class' => 'iconsmall edit', 'alt' => get_string('edit'))),
                                    array('title' => get_string('editsummary')));
            }

            $o .= html_writer::start_tag('div', array('class' => 'summary'));
            $o .= $this->format_summary_text($section);

            $o .= html_writer::end_tag('div');

            $o .= $this->section_availability_message($section, has_capability('moodle/course:viewhiddensections', $context));
        } else {
            // When on a section page, we only display the general section title, if title is not the default one.
            $hasnamesecpg = ($section->section == 0 && (string) $section->name !== '');

            if ($hasnamesecpg) {
                $o .= $this->output->heading($this->section_title($section, $course), 3, 'section-title');
            }
            $o .= html_writer::start_tag('div', array('class' => 'summary'));
            $o .= $this->format_summary_text($section);

            if ($PAGE->user_is_editing() && has_capability('moodle/course:update', $context)) {
                $url = new moodle_url('/course/editsection.php', array('id' => $section->id, 'sr' => $sectionreturn));
                $o.= html_writer::link($url, html_writer::empty_tag('img', array('src' => $this->output->pix_url('t/edit'),
                                    'class' => 'iconsmall edit', 'alt' => get_string('edit'))),
                                    array('title' => get_string('editsummary')));
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
     * Generate the html for a hidden section
     *
     * @param int $sectionno The section number in the course which is being dsiplayed
     * @return string HTML to output.
     */
    protected function section_hidden($section) {
        $o = '';
        $course = $this->courseformat->get_course();
        $liattributes = array(
            'id' => 'section-' . $section->section,
            'class' => 'section main clearfix hidden',
            'role' => 'region',
            'aria-label' => $this->courseformat->get_topcoll_section_name($course, $section, false)
        );
        if ($this->tcsettings['layoutcolumnorientation'] == 2) { // Horizontal column layout.
            $liattributes['style'] = 'width:' . $this->tccolumnwidth . '%;';
        }

        $o .= html_writer::start_tag('li', $liattributes);
        if (($this->mobiletheme === false) && ($this->tablettheme === false)) {
            $leftcontent = $this->section_left_content($section, $course, false);
            $o .= html_writer::tag('div', $leftcontent, array('class' => 'left side'));
        }

        if (($this->mobiletheme === false) && ($this->tablettheme === false)) {
            $rightcontent = $this->section_right_content($section, $course, false);
            $o .= html_writer::tag('div', $rightcontent, array('class' => 'right side'));
        }

        $o .= html_writer::start_tag('div', array('class' => 'content sectionhidden'));

        $title = get_string('notavailable');
        if (($this->mobiletheme === false) && ($this->tablettheme === false)) {
            $o .= $this->output->heading($title, 3, 'section-title');
        } else {
            $o .= html_writer::tag('h3', $title); // Moodle H3's look bad on mobile / tablet with CT so use plain.
        }
        $o .= html_writer::end_tag('div');
        $o .= html_writer::end_tag('li');
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
        parent::print_single_section_page($course, $sections, $mods, $modnames, $modnamesused, $displaysection);
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
            echo $this->courserenderer->course_section_cm_list($course, $thissection, 0);
            echo $this->courserenderer->course_section_add_cm_control($course, $thissection->section, 0, 0);
            echo $this->section_footer();
        }

        if ($course->numsections > 0) {
            if ($course->numsections > 1) {
                if ($PAGE->user_is_editing() || $course->coursedisplay != COURSE_DISPLAY_MULTIPAGE) {
                    // Collapsed Topics all toggles.
                    echo $this->toggle_all();
                    if ($this->tcsettings['displayinstructions'] == 2) {
                        // Collapsed Topics instructions.
                        echo $this->display_instructions();
                    }
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
                $weekdate = $course->enddate;      // This should be 0:00 Monday of that week.
                $weekdate -= 7200;                 // Subtract two hours to avoid possible DST problems.
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
                // Reset.
                $section = $course->numsections;
                $weekdate = $course->enddate;      // This should be 0:00 Monday of that week.
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
                if ($this->tcsettings['layoutcolumnorientation'] == 2) { // Horizontal column layout.
                    $this->tccolumnwidth -= 1;
                } else {
                    $this->tccolumnwidth -= 0.2;
                }
                $this->tccolumnpadding = 0; // 'px'.
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
                $this->isoldtogglepreference = $this->togglelib->is_old_preference($this->userpreference);
                if ($this->isoldtogglepreference == true) {
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
                    // Check we have enough digits for the number of toggles in case this has increased.
                    $numdigits = $this->togglelib->get_required_digits($course->numsections);
                    if ($numdigits > strlen($this->userpreference)) {
                        if ($this->defaultuserpreference == 0) {
                            $dchar = $this->togglelib->get_min_digit();
                        } else {
                            $dchar = $this->togglelib->get_max_digit();
                        }
                        for ($i = strlen($this->userpreference); $i < $numdigits; $i++) {
                            $this->userpreference .= $dchar;
                        }
                    }
                    $this->togglelib->set_toggles($this->userpreference);
                }
            } else {
                $numdigits = $this->togglelib->get_required_digits($course->numsections);
                if ($this->defaultuserpreference == 0) {
                    $dchar = $this->togglelib->get_min_digit();
                } else {
                    $dchar = $this->togglelib->get_max_digit();
                }
                $this->userpreference = '';
                for ($i = 0; $i < $numdigits; $i++) {
                    $this->userpreference .= $dchar;
                }
                $this->togglelib->set_toggles($this->userpreference);
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
                    // Show  the section if we were meant to and it is the current section:....
                    $showsection = ($course->marker == $section);
                } else if (($this->tcsettings['layoutstructure'] == 4) && ($course->marker == $section)) {
                    $showsection = false; // Do not reshow current section.
                }
                if (!$showsection) {
                    // Hidden section message is overridden by 'unavailable' control (showavailability option).
                    if ($this->tcsettings['layoutstructure'] != 4) {
                        if (($this->tcsettings['layoutstructure'] != 3) || ($userisediting)) {
                            if (!$course->hiddensections && $thissection->available) {
                                $shownsectioncount++;
                                echo $this->section_hidden($thissection);
                            }
                        }
                    }
                } else {
                    $shownsectioncount++;
                    if (!$PAGE->user_is_editing() && $course->coursedisplay == COURSE_DISPLAY_MULTIPAGE) {
                        // Display section summary only.
                        echo $this->section_summary($thissection, $course, null);
                    } else {
                        if ($this->isoldtogglepreference == true) {
                            $togglestate = substr($tb, $section, 1);
                            if ($togglestate == '1') {
                                $thissection->toggle = true;
                            } else {
                                $thissection->toggle = false;
                            }
                        } else {
                            $thissection->toggle = $this->togglelib->get_toggle_state($thissection->section);
                        }
                        echo $this->section_header($thissection, $course, false, 0);
                        if ($thissection->uservisible) {
                            echo $this->courserenderer->course_section_cm_list($course, $thissection, 0);
                            echo $this->courserenderer->course_section_add_cm_control($course, $thissection->section, 0);
                        }
                        echo html_writer::end_tag('div');
                        echo $this->section_footer();
                    }
                }

                if ($currentsectionfirst == false) {
                    /* Only need to do this on the iteration when $currentsectionfirst is not true as this iteration will always
                       happen.  Otherwise you get duplicate entries in course_sections in the DB. */
                    unset($sections[$section]);
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
                    if ($this->tcsettings['layoutcolumnorientation'] == 1) {  // Only break columns in vertical mode.
                        if (($canbreak == false) && ($currentsectionfirst == false) && ($showsection == true)) {
                            $canbreak = true;
                            $columnbreakpoint = ($shownsectioncount + ($numsections / $this->tcsettings['layoutcolumns'])) - 1;
                            if ($this->tcsettings['layoutstructure'] == 4) {
                                $columnbreakpoint -= 1;
                            }
                        }

                        if (($currentsectionfirst == false) && ($canbreak == true) && ($shownsectioncount >= $columnbreakpoint) &&
                            ($columncount < $this->tcsettings['layoutcolumns'])) {
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
                    // Activities inside this section are 'orphaned', this section will be printed as 'stealth' below.
                    break;
                }
            }
        }

        if ($PAGE->user_is_editing() and has_capability('moodle/course:update', $context)) {
            // Print stealth sections if present.
            foreach ($modinfo->get_section_info_all() as $section => $thissection) {
                if ($section <= $course->numsections or empty($modinfo->sections[$section])) {
                    // This is not stealth section or it is empty.
                    continue;
                }
                echo $this->stealth_section_header($section);
                echo $this->courserenderer->course_section_cm_list($course, $thissection->section, 0);
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
     * Displays the toggle all functionality.
     * @return string HTML to output.
     */
    protected function toggle_all() {
        $o = html_writer::start_tag('li', array('class' => 'tcsection main clearfix', 'id' => 'toggle-all'));

        if (($this->mobiletheme === false) && ($this->tablettheme === false)) {
            $o .= html_writer::tag('div', $this->output->spacer(), array('class' => 'left side'));
            $o .= html_writer::tag('div', $this->output->spacer(), array('class' => 'right side'));
        }

        $o .= html_writer::start_tag('div', array('class' => 'content'));
        $iconsetclass = ' toggle-'.$this->tcsettings['toggleiconset'];
        if ($this->tcsettings['toggleallhover'] == 2) {
            $iconsetclass .= '-hover'.$iconsetclass;
        }
        $o .= html_writer::start_tag('div', array('class' => 'sectionbody'.$iconsetclass));
        $o .= html_writer::start_tag('h4', null);
        $o .= html_writer::tag('a', get_string('topcollopened', 'format_topcoll'),
                               array('class' => 'on', 'href' => '#', 'id' => 'toggles-all-opened'));
        $o .= html_writer::tag('a', get_string('topcollclosed', 'format_topcoll'),
                               array('class' => 'off', 'href' => '#', 'id' => 'toggles-all-closed'));
        $o .= html_writer::end_tag('h4');
        $o .= html_writer::end_tag('div');
        $o .= html_writer::end_tag('div');
        $o .= html_writer::end_tag('li');

        return $o;
    }

    /**
     * Displays the instructions functionality.
     * @return string HTML to output.
     */
    protected function display_instructions() {
        $o = html_writer::start_tag('li', array('class' => 'tcsection main clearfix', 'id' => 'topcoll-display-instructions'));

        if (($this->mobiletheme === false) && ($this->tablettheme === false)) {
            $o .= html_writer::tag('div', $this->output->spacer(), array('class' => 'left side'));
            $o .= html_writer::tag('div', $this->output->spacer(), array('class' => 'right side'));
        }

        $o .= html_writer::start_tag('div', array('class' => 'content'));
        $o .= html_writer::start_tag('div', array('class' => 'sectionbody'));
        $o .= html_writer::tag('p', get_string('instructions', 'format_topcoll'), array('class' => 'topcoll-display-instructions'));
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
