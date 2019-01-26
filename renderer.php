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
 * @version    See the value of '$plugin->version' in version.php.
 * @copyright  &copy; 2012-onwards G J Barnard in respect to modifications of standard topics format.
 * @author     G J Barnard - {@link http://moodle.org/user/profile.php?id=442195}
 * @link       http://docs.moodle.org/en/Collapsed_Topics_course_format
 * @license    http://www.gnu.org/copyleft/gpl.html GNU Public License
 *
 */
defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/course/format/renderer.php');
require_once($CFG->dirroot . '/course/format/topcoll/lib.php');

class format_topcoll_renderer extends format_section_renderer_base {

    protected $tccolumnwidth = 100; // Default width in percent of the column(s).
    protected $tccolumnpadding = 0; // Default padding in pixels of the column(s).
    protected $mobiletheme = false; // As not using a mobile theme we can react to the number of columns setting.
    protected $tablettheme = false; // As not using a tablet theme we can react to the number of columns setting.
    protected $courseformat = null; // Our course format object as defined in lib.php;
    protected $tcsettings; // Settings for the format - array.
    protected $defaulttogglepersistence; // Default toggle persistence.
    protected $defaultuserpreference; // Default user preference when none set - bool - true all open, false all closed.
    protected $togglelib;
    protected $currentsection = false; // If not false then will be the current section number.
    protected $isoldtogglepreference = false;
    protected $userisediting = false;
    protected $tctoggleiconsize;
    protected $formatresponsive;
    protected $rtl = false;
    protected $bsnewgrid = false;

    /**
     * Constructor method, calls the parent constructor - MDL-21097.
     *
     * @param moodle_page $page.
     * @param string $target one of rendering target constants.
     */
    public function __construct(moodle_page $page, $target) {
        parent::__construct($page, $target);
        $this->courserenderer = $this->page->get_renderer('format_topcoll', 'course');
        $this->togglelib = new \format_topcoll\togglelib;
        $this->courseformat = course_get_format($page->course); // Needed for collapsed topics settings retrieval.

        /* Since format_topcoll_renderer::section_edit_control_items() only displays the 'Set current section' control when editing
          mode is on we need to be sure that the link 'Turn editing mode on' is available for a user who does not have any
          other managing capability. */
        $page->set_other_editing_capability('moodle/course:setcurrentsection');

        $this->userisediting = $page->user_is_editing();
        $this->tctoggleiconsize = clean_param(get_config('format_topcoll', 'defaulttoggleiconsize'), PARAM_TEXT);
        $this->formatresponsive = get_config('format_topcoll', 'formatresponsive');

        $this->rtl = right_to_left();

        if (strcmp($page->theme->name, 'boost') === 0) {
            $this->bsnewgrid = true;
        } else if (!empty($page->theme->parents)) {
            if (in_array('boost', $page->theme->parents) === true) {
                $this->bsnewgrid = true;
            }
        }
    }

    /**
     * Generate the starting container html for a list of sections.
     * @return string HTML to output.
     */
    protected function start_section_list() {
        if ($this->bsnewgrid) {
            return html_writer::start_tag('ul', array('class' => 'ctopics bsnewgrid'));
        } else {
            return html_writer::start_tag('ul', array('class' => 'ctopics'));
        }
    }

    /**
     * Generate the starting container html for a list of sections when showing a toggle.
     * @return string HTML to output.
     */
    protected function start_toggle_section_list() {
        $classes = 'ctopics topics';
        if ($this->bsnewgrid) {
            $classes .= ' bsnewgrid';
        }
        $attributes = array();
        if (($this->mobiletheme === true) || ($this->tablettheme === true)) {
            $classes .= ' ctportable';
        }
        if ($this->formatresponsive) {
            $style = '';
            if ($this->tcsettings['layoutcolumnorientation'] == 1) { // Vertical columns.
                $style .= 'width:' . $this->tccolumnwidth . '%;';
            } else {
                $style .= 'width: 100%;';  // Horizontal columns.
            }
            if ($this->mobiletheme === false) {
                $classes .= ' ctlayout';
            }
            $style .= ' padding-left: ' . $this->tccolumnpadding . 'px; padding-right: ' . $this->tccolumnpadding . 'px;';
            $attributes['style'] = $style;
        } else {
            if ($this->tcsettings['layoutcolumnorientation'] == 1) { // Vertical columns.
                $classes .= ' ' . $this->get_column_class($this->tcsettings['layoutcolumns']);
            } else {
                $classes .= ' ' . $this->get_row_class();
            }
        }
        $attributes['class'] = $classes;

        return html_writer::start_tag('ul', $attributes);
    }

    /**
     * Generate the closing container html for a list of sections.
     * @return string HTML to output.
     */
    protected function end_section_list() {
        return html_writer::end_tag('ul');
    }

    /**
     * Generate the title for this section page.
     * @return string the page title.
     */
    protected function page_title() {
        return get_string('sectionname', 'format_topcoll');
    }

    /**
     * Generate the content to displayed on the right part of a section
     * before course modules are included.
     *
     * @param stdClass $section The course_section entry from DB.
     * @param stdClass $course The course entry from DB.
     * @param bool $onsectionpage true if being printed on a section page.
     * @return string HTML to output.
     */
    protected function section_right_content($section, $course, $onsectionpage) {
        $o = '';

        if ($section->section != 0) {
            $controls = $this->section_edit_control_items($course, $section, $onsectionpage);
            if (!empty($controls)) {
                $o .= $this->section_edit_control_menu($controls, $course, $section);
            } else if (!$onsectionpage) {
                if (empty($this->tcsettings)) {
                    $this->tcsettings = $this->courseformat->get_settings();
                }
                $url = new moodle_url('/course/view.php', array('id' => $course->id, 'section' => $section->section));
                // Get the specific words from the language files.
                $topictext = null;
                if (($this->tcsettings['layoutstructure'] == 1) || ($this->tcsettings['layoutstructure'] == 4)) {
                    $topictext = get_string('setlayoutstructuretopic', 'format_topcoll');
                } else if (($this->tcsettings['layoutstructure'] == 2) || ($this->tcsettings['layoutstructure'] == 3)) {
                    $topictext = get_string('setlayoutstructureweek', 'format_topcoll');
                } else {
                    $topictext = get_string('setlayoutstructureday', 'format_topcoll');
                }
                $title = get_string('viewonly', 'format_topcoll', array('sectionname' => $topictext.' '.$section->section));
                switch ($this->tcsettings['layoutelement']) { // Toggle section x.
                    case 1:
                    case 3:
                    case 5:
                    case 8:
                        $o .= html_writer::link($url,
                            $topictext.html_writer::empty_tag('br').
                            $section->section, array('title' => $title, 'class' => 'cps_centre'));
                        break;
                    default:
                        $o .= html_writer::link($url,
                            $this->output->pix_icon('one_section', $title, 'format_topcoll'),
                            array('title' => $title, 'class' => 'cps_centre'));
                        break;
                }
            }
        }

        return $o;
    }

    /**
     * Generate the content to displayed on the left part of a section
     * before course modules are included.
     *
     * @param stdClass $section The course_section entry from DB.
     * @param stdClass $course The course entry from DB.
     * @param bool $onsectionpage true if being printed on a section page.
     * @return string HTML to output.
     */
    protected function section_left_content($section, $course, $onsectionpage) {
        $o = '';

        if (($section->section != 0) && (!$onsectionpage)) {
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
     * Generate the section title, wraps it in a link to the section page if page is to be displayed on a separate page
     *
     * @param stdClass $section The course_section entry from DB
     * @param stdClass $course The course entry from DB
     * @return string HTML to output.
     */
    public function section_title($section, $course) {
        return $this->render($this->courseformat->inplace_editable_render_section_name($section));
    }

    /**
     * Generate the section title to be displayed on the section page, without a link
     *
     * @param stdClass $section The course_section entry from DB
     * @param stdClass $course The course entry from DB
     * @return string HTML to output.
     */
    public function section_title_without_link($section, $course) {
        return $this->render($this->courseformat->inplace_editable_render_section_name($section, false));
    }

    /**
     * Generate the edit controls of a section.
     *
     * @param stdClass $course The course entry from DB.
     * @param stdClass $section The course_section entry from DB.
     * @param bool $onsectionpage true if being printed on a section page.
     * @return array of links with edit controls.
     */
    protected function section_edit_control_items($course, $section, $onsectionpage = false) {

        if (!$this->userisediting) {
            return array();
        }

        $coursecontext = context_course::instance($course->id);
        $sectionreturn = $onsectionpage ? $section->section : null;

        $url = course_get_url($course, $sectionreturn);
        $url->param('sesskey', sesskey());

        if (empty($this->tcsettings)) {
            $this->tcsettings = $this->courseformat->get_settings();
        }
        $controls = array();
        if ((($this->tcsettings['layoutstructure'] == 1) || ($this->tcsettings['layoutstructure'] == 4)) &&
                $section->section && has_capability('moodle/course:setcurrentsection', $coursecontext)) {
            if ($course->marker == $section->section) {  // Show the "light globe" on/off.
                $url->param('marker', 0);
                $markedthissection = get_string('markedthissection', 'format_topcoll');
                $highlightoff = get_string('highlightoff');
                $controls['highlight'] = array('url' => $url, "icon" => 'i/marked',
                                               'name' => $highlightoff,
                                               'pixattr' => array('class' => '', 'alt' => $markedthissection),
                                               'attr' => array('class' => 'editing_highlight', 'title' => $markedthissection,
                                               'data-action' => 'removemarker'));
            } else {
                $url->param('marker', $section->section);
                $markthissection = get_string('markthissection', 'format_topcoll');
                $highlight = get_string('highlight');
                $controls['highlight'] = array('url' => $url, "icon" => 'i/marker',
                                               'name' => $highlight,
                                               'pixattr' => array('class' => '', 'alt' => $markthissection),
                                               'attr' => array('class' => 'editing_highlight', 'title' => $markthissection,
                                               'data-action' => 'setmarker'));
            }
        }

        $parentcontrols = parent::section_edit_control_items($course, $section, $onsectionpage);

        // If the edit key exists, we are going to insert our controls after it.
        if (array_key_exists("edit", $parentcontrols)) {
            $merged = array();
            // We can't use splice because we are using associative arrays.
            // Step through the array and merge the arrays.
            foreach ($parentcontrols as $key => $action) {
                $merged[$key] = $action;
                if ($key == "edit") {
                    // If we have come to the edit key, merge these controls here.
                    $merged = array_merge($merged, $controls);
                }
            }

            return $merged;
        } else {
            return array_merge($controls, $parentcontrols);
        }
    }

    /**
     * Generate a summary of a section for display on the 'course index page'.
     *
     * @param stdClass $section The course_section entry from DB.
     * @param stdClass $course The course entry from DB.
     * @param array    $mods (argument not used).
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
            'id' => 'section-' . $section->section,
            'class' => $classattr,
            'role' => 'region',
            'aria-label' => $title
        );
        if (($this->formatresponsive) && ($this->tcsettings['layoutcolumnorientation'] == 2)) { // Horizontal column layout.
            $liattributes['style'] = 'width: ' . $this->tccolumnwidth . '%;';
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

        $o .= html_writer::start_tag('div', array('class' => 'summarytext'));
        $o .= $this->format_summary_text($section);
        $o .= html_writer::end_tag('div');
        $o .= $this->section_activity_summary($section, $course, null);

        $o .= $this->section_availability($section);

        $o .= html_writer::end_tag('div');
        $o .= html_writer::end_tag('li');

        return $o;
    }

    /**
     * Generate the display of the header part of a section before
     * course modules are included.
     *
     * @param stdClass $section The course_section entry from DB.
     * @param stdClass $course The course entry from DB.
     * @param bool $onsectionpage true if being printed on a section page.
     * @param int $sectionreturn The section to return to after an action.
     * @return string HTML to output.
     */
    protected function section_header($section, $course, $onsectionpage, $sectionreturn = null) {
        $o = '';

        $sectionstyle = '';
        $rightcurrent = '';
        $context = context_course::instance($course->id);

        if ($section->section != 0) {
            // Only in the non-general sections.
            if (!$section->visible) {
                $sectionstyle = ' hidden';
            }
            if ($section->section == $this->currentsection) {
                $sectionstyle = ' current';
                $rightcurrent = ' left';
            }
        }

        if ((!$this->formatresponsive) && ($section->section != 0) &&
            ($this->tcsettings['layoutcolumnorientation'] == 2)) { // Horizontal column layout.
            $sectionstyle .= ' ' . $this->get_column_class($this->tcsettings['layoutcolumns']);
        }
        $liattributes = array(
            'id' => 'section-' . $section->section,
            'class' => 'section main clearfix' . $sectionstyle,
            'role' => 'region',
            'aria-label' => $this->courseformat->get_topcoll_section_name($course, $section, false)
        );
        if (($this->formatresponsive) && ($this->tcsettings['layoutcolumnorientation'] == 2)) { // Horizontal column layout.
            $liattributes['style'] = 'width: ' . $this->tccolumnwidth . '%;';
        }
        $o .= html_writer::start_tag('li', $liattributes);

        if ((($this->mobiletheme === false) && ($this->tablettheme === false)) || ($this->userisediting)) {
            $leftcontent = $this->section_left_content($section, $course, $onsectionpage);
            $rightcontent = '';
            if (($section->section != 0) && $this->userisediting && has_capability('moodle/course:update', $context)) {
                $url = new moodle_url('/course/editsection.php', array('id' => $section->id, 'sr' => $sectionreturn));

                $rightcontent .= html_writer::link($url,
                    $this->output->pix_icon('t/edit', get_string('edit')),
                        array('title' => get_string('editsection', 'format_topcoll'), 'class' => 'tceditsection'));
            }
            $rightcontent .= $this->section_right_content($section, $course, $onsectionpage);

            if ($this->rtl) {
                // Swap content.
                $o .= html_writer::tag('div', $rightcontent, array('class' => 'right side'));
                $o .= html_writer::tag('div', $leftcontent, array('class' => 'left side'));
            } else {
                $o .= html_writer::tag('div', $leftcontent, array('class' => 'left side'));
                $o .= html_writer::tag('div', $rightcontent, array('class' => 'right side'));
            }
        }
        $o .= html_writer::start_tag('div', array('class' => 'content'));

        if (($onsectionpage == false) && ($section->section != 0)) {
            $o .= html_writer::start_tag('div',
                array('class' => 'sectionhead toggle toggle-'.$this->tcsettings['toggleiconset'],
                'id' => 'toggle-'.$section->section)
            );

            if ((!($section->toggle === null)) && ($section->toggle == true)) {
                $toggleclass = 'toggle_open';
                $ariapressed = 'true';
                $sectionclass = ' sectionopen';
            } else {
                $toggleclass = 'toggle_closed';
                $ariapressed = 'false';
                $sectionclass = '';
            }
            $toggleclass .= ' the_toggle ' . $this->tctoggleiconsize;
            $o .= html_writer::start_tag('span',
                array('class' => $toggleclass, 'role' => 'button', 'aria-pressed' => $ariapressed)
            );

            if (empty($this->tcsettings)) {
                $this->tcsettings = $this->courseformat->get_settings();
            }

            if ($this->userisediting) {
                $title = $this->section_title($section, $course);
            } else {
                $title = $this->courseformat->get_topcoll_section_name($course, $section, true);
            }
            if ((($this->mobiletheme === false) && ($this->tablettheme === false)) || ($this->userisediting)) {
                $o .= $this->output->heading($title, 3, 'sectionname');
            } else {
                $o .= html_writer::tag('h3', $title); // Moodle H3's look bad on mobile / tablet with CT so use plain.
            }

            $o .= $this->section_availability($section);

            $o .= html_writer::end_tag('span');
            $o .= html_writer::end_tag('div');

            if ($this->tcsettings['showsectionsummary'] == 2) {
                $o .= $this->section_summary_container($section);
            }

            $o .= html_writer::start_tag('div',
                array('class' => 'sectionbody toggledsection' . $sectionclass,
                'id' => 'toggledsection-' . $section->section)
            );

            if ($this->userisediting) {
                // CONTRIB-7434.
                $o .= html_writer::tag('span',
                    $this->courseformat->get_topcoll_section_name($course, $section, false),
                    array('class' => 'hidden', 'aria-hidden' => 'true'));
            }

            if ($this->userisediting && has_capability('moodle/course:update', $context)) {
                $url = new moodle_url('/course/editsection.php', array('id' => $section->id, 'sr' => $sectionreturn));
                $o .= html_writer::link($url,
                    $this->output->pix_icon('t/edit', get_string('edit')),
                    array('title' => get_string('editsection', 'format_topcoll'))
                );
            }

            if ($this->tcsettings['showsectionsummary'] == 1) {
                $o .= $this->section_summary_container($section);
            }
        } else {
            // When on a section page, we only display the general section title, if title is not the default one.
            $hasnamesecpg = ($section->section == 0 && (string) $section->name !== '');

            if ($hasnamesecpg) {
                $o .= $this->output->heading($this->section_title($section, $course), 3, 'section-title');
            }
            $o .= $this->section_availability($section);
            $o .= html_writer::start_tag('div', array('class' => 'summary'));
            $o .= $this->format_summary_text($section);

            if ($this->userisediting && has_capability('moodle/course:update', $context)) {
                $url = new moodle_url('/course/editsection.php', array('id' => $section->id, 'sr' => $sectionreturn));
                $o .= html_writer::link($url,
                    $this->output->pix_icon('t/edit', get_string('edit')),
                    array('title' => get_string('editsection', 'format_topcoll'))
                );
            }
            $o .= html_writer::end_tag('div');
        }
        return $o;
    }

    protected function section_summary_container($section) {
        $summarytext = $this->format_summary_text($section);
        if ($summarytext) {
            $classextra = ($this->tcsettings['showsectionsummary'] == 1) ? '' : ' summaryalwaysshown';
            $o = html_writer::start_tag('div', array('class' => 'summary' . $classextra));
            $o .= $this->format_summary_text($section);
            $o .= html_writer::end_tag('div');
        } else {
            $o = '';
        }
        return $o;
    }

    /**
     * Generate the display of the footer part of a section.
     *
     * @return string HTML to output.
     */
    protected function section_footer() {
        $o = html_writer::end_tag('div');
        $o .= html_writer::end_tag('li');

        return $o;
    }

    /**
     * Generate the header html of a stealth section.
     *
     * @param int $sectionno The section number in the coruse which is being dsiplayed.
     * @return string HTML to output.
     */
    protected function stealth_section_header($sectionno) {
        $o = '';
        $sectionstyle = '';
        $course = $this->courseformat->get_course();
        // Horizontal column layout.
        if ((!$this->formatresponsive) && ($sectionno != 0) && ($this->tcsettings['layoutcolumnorientation'] == 2)) {
            $sectionstyle .= ' ' . $this->get_column_class($this->tcsettings['layoutcolumns']);
        }
        $liattributes = array(
            'id' => 'section-' . $sectionno,
            'class' => 'section main clearfix orphaned hidden' . $sectionstyle,
            'role' => 'region',
            'aria-label' => $this->courseformat->get_topcoll_section_name($course, $sectionno, false)
        );
        if (($this->formatresponsive) && ($this->tcsettings['layoutcolumnorientation'] == 2)) { // Horizontal column layout.
            $liattributes['style'] = 'width: ' . $this->tccolumnwidth . '%;';
        }
        $o .= html_writer::start_tag('li', $liattributes);
        $o .= html_writer::tag('div', '', array('class' => 'left side'));
        $section = $this->courseformat->get_section($sectionno);
        $rightcontent = $this->section_right_content($section, $course, false);
        $o .= html_writer::tag('div', $rightcontent, array('class' => 'right side'));
        $o .= html_writer::start_tag('div', array('class' => 'content'));
        $o .= $this->output->heading(get_string('orphanedactivitiesinsectionno', '', $sectionno), 3, 'sectionname');
        return $o;
    }

    /**
     * Generate the html for a hidden section.
     *
     * @param stdClass $section The section in the course which is being displayed.
     * @param int|stdClass $courseorid The course to get the section name for (object or just course id).
     * @return string HTML to output.
     */
    protected function section_hidden($section, $courseorid = null) {
        $o = '';
        $course = $this->courseformat->get_course();
        $sectionstyle = 'section main clearfix hidden';
        if ((!$this->formatresponsive) && ($this->tcsettings['layoutcolumnorientation'] == 2)) { // Horizontal column layout.
            $sectionstyle .= ' ' . $this->get_column_class($this->tcsettings['layoutcolumns']);
        }
        $liattributes = array(
            'id' => 'section-' . $section->section,
            'class' => $sectionstyle,
            'role' => 'region',
            'aria-label' => $this->courseformat->get_topcoll_section_name($course, $section, false)
        );
        if (($this->formatresponsive) && ($this->tcsettings['layoutcolumnorientation'] == 2)) { // Horizontal column layout.
            $liattributes['style'] = 'width: ' . $this->tccolumnwidth . '%;';
        }

        $o .= html_writer::start_tag('li', $liattributes);
        if ((($this->mobiletheme === false) && ($this->tablettheme === false)) || ($this->userisediting)) {
            $leftcontent = $this->section_left_content($section, $course, false);
            $rightcontent = $this->section_right_content($section, $course, false);

            if ($this->rtl) {
                // Swap content.
                $o .= html_writer::tag('div', $leftcontent, array('class' => 'right side'));
                $o .= html_writer::tag('div', $rightcontent, array('class' => 'left side'));
            } else {
                $o .= html_writer::tag('div', $leftcontent, array('class' => 'left side'));
                $o .= html_writer::tag('div', $rightcontent, array('class' => 'right side'));
            }

        }

        $o .= html_writer::start_tag('div', array('class' => 'content sectionhidden'));

        $title = get_string('notavailable');
        if ((($this->mobiletheme === false) && ($this->tablettheme === false)) || ($this->userisediting)) {
            $o .= $this->output->heading($title, 3, 'section-title');
        } else {
            $o .= html_writer::tag('h3', $title); // Moodle H3's look bad on mobile / tablet with CT so use plain.
        }
        $o .= html_writer::end_tag('div');
        $o .= html_writer::end_tag('li');
        return $o;
    }

    /**
     * Output the html for a single section page .
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
        $course = $this->courseformat->get_course();

        // Can we view the section in question?
        if (!($sectioninfo = $modinfo->get_section_info($displaysection)) || !$sectioninfo->uservisible) {
            /* This section doesn't exist or is not available for the user.
               We actually already check this in course/view.php but just in case exit from this function as well. */
            print_error('unknowncoursesection', 'error', course_get_url($course),
                format_string($course->fullname));
        }

        // Copy activity clipboard.
        echo $this->course_activity_clipboard($course, $displaysection);
        $thissection = $modinfo->get_section_info(0);
        if ($thissection->summary or !empty($modinfo->sections[0]) or $PAGE->user_is_editing()) {
            echo $this->start_section_list();
            echo $this->section_header($thissection, $course, true, $displaysection);
            echo $this->courserenderer->course_section_cm_list($course, $thissection, $displaysection, array('sr' => $displaysection));
            echo $this->courserenderer->course_section_add_cm_control($course, 0, $displaysection);
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
        $sectiontitle .= html_writer::start_tag('div', array('class' => 'section-navigation navigationtitle'));
        $sectiontitle .= html_writer::tag('span', $sectionnavlinks['previous'], array('class' => 'mdl-left'));
        $sectiontitle .= html_writer::tag('span', $sectionnavlinks['next'], array('class' => 'mdl-right'));
        // Title attributes.
        $classes = 'sectionname';
        if (!$thissection->visible) {
            $classes .= ' dimmed_text';
        }
        $sectionname = html_writer::tag('span', $this->section_title_without_link($thissection, $course));
        $sectiontitle .= $this->output->heading($sectionname, 3, $classes);

        $sectiontitle .= html_writer::end_tag('div');
        echo $sectiontitle;

        // Now the list of sections.
        echo $this->start_section_list();

        echo $this->section_header($thissection, $course, true, $displaysection);
        // Show completion help icon.
        $completioninfo = new completion_info($course);
        echo $completioninfo->display_help_icon();

        echo $this->courserenderer->course_section_cm_list($course, $thissection, $displaysection, array('sr' => $displaysection));
        echo $this->courserenderer->course_section_add_cm_control($course, $displaysection, $displaysection);
        echo $this->section_footer();
        echo $this->end_section_list();

        // Display section bottom navigation.
        $sectionbottomnav = '';
        $sectionbottomnav .= html_writer::start_tag('div', array('class' => 'section-navigation mdl-bottom'));
        $sectionbottomnav .= html_writer::tag('span', $sectionnavlinks['previous'], array('class' => 'mdl-left'));
        $sectionbottomnav .= html_writer::tag('span', $sectionnavlinks['next'], array('class' => 'mdl-right'));
        $sectionbottomnav .= html_writer::tag('div', $this->section_nav_selection($course, $sections, $displaysection),
            array('class' => 'mdl-align'));
        $sectionbottomnav .= html_writer::end_tag('div');
        echo $sectionbottomnav;

        // Close single-section div.
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
        if ($this->formatresponsive) {
            $this->tccolumnwidth = 100; // Reset to default.
        }
        echo $this->start_section_list();

        $sections = $modinfo->get_section_info_all();
        // General section if non-empty.
        $thissection = $sections[0];
        unset($sections[0]);
        if ($thissection->summary or ! empty($modinfo->sections[0]) or $this->userisediting) {
            echo $this->section_header($thissection, $course, false, 0);
            echo $this->courserenderer->course_section_cm_list($course, $thissection, 0);
            echo $this->courserenderer->course_section_add_cm_control($course, $thissection->section, 0);
            echo $this->section_footer();
        }

        $shownonetoggle = false;
        $coursenumsections = $this->courseformat->get_last_section_number();
        if ($coursenumsections > 0) {
            $sectiondisplayarray = array();
            if ($coursenumsections > 1) {
                if (($this->userisediting) || ($this->tcsettings['onesection'] == 1)) {
                    // Collapsed Topics all toggles.
                    echo $this->toggle_all();
                }
                if ($this->tcsettings['displayinstructions'] == 2) {
                    // Collapsed Topics instructions.
                    echo $this->display_instructions();
                }
            }
            $currentsectionfirst = false;
            if (($this->tcsettings['layoutstructure'] == 4) && (!$this->userisediting)) {
                $currentsectionfirst = true;
            }

            if (($this->tcsettings['layoutstructure'] != 3) || ($this->userisediting)) {
                $section = 1;
            } else {
                $timenow = time();
                $weekofseconds = 604800;
                $course->enddate = $course->startdate + ($weekofseconds * $coursenumsections);
                $section = $coursenumsections;
                $weekdate = $course->enddate;      // This should be 0:00 Monday of that week.
                $weekdate -= 7200;                 // Subtract two hours to avoid possible DST problems.
            }

            $numsections = $coursenumsections; // Because we want to manipulate this for column breakpoints.
            if (($this->tcsettings['layoutstructure'] == 3) && ($this->userisediting == false)) {
                $loopsection = 1;
                $numsections = 0;
                while ($loopsection <= $coursenumsections) {
                    $nextweekdate = $weekdate - ($weekofseconds);
                    if ((($thissection->uservisible ||
                            ($thissection->visible && !$thissection->available && !empty($thissection->availableinfo))) &&
                            ($nextweekdate <= $timenow)) == true) {
                        $numsections++; // Section not shown so do not count in columns calculation.
                    }
                    $weekdate = $nextweekdate;
                    $section--;
                    $loopsection++;
                }
                // Reset.
                $section = $coursenumsections;
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

                if ($this->formatresponsive) {
                    $this->tccolumnwidth = 100 / $this->tcsettings['layoutcolumns'];
                    if ($this->tcsettings['layoutcolumnorientation'] == 2) { // Horizontal column layout.
                        $this->tccolumnwidth -= 0.5;
                        $this->tccolumnpadding = 0; // In 'px'.
                    } else {
                        $this->tccolumnwidth -= 0.2;
                        $this->tccolumnpadding = 0; // In 'px'.
                    }
                }
            } else if ($this->tcsettings['layoutcolumns'] < 1) {
                // Distributed default in plugin settings (and reset in database) or database has been changed incorrectly.
                $this->tcsettings['layoutcolumns'] = 1;

                // Update....
                $this->courseformat->update_topcoll_columns_setting($this->tcsettings['layoutcolumns']);
            }

            echo $this->end_section_list();
            if ((!$this->formatresponsive) && ($this->tcsettings['layoutcolumnorientation'] == 1)) { // Vertical columns.
                echo html_writer::start_tag('div', array('class' => $this->get_row_class()));
            }
            echo $this->start_toggle_section_list();

            $loopsection = 1;
            $breaking = false; // Once the first section is shown we can decide if we break on another column.

            while ($loopsection <= $coursenumsections) {
                if (($this->tcsettings['layoutstructure'] == 3) && ($this->userisediting == false)) {
                    $nextweekdate = $weekdate - ($weekofseconds);
                }
                $thissection = $modinfo->get_section_info($section);

                /* Show the section if the user is permitted to access it, OR if it's not available
                  but there is some available info text which explains the reason & should display. */
                if (($this->tcsettings['layoutstructure'] != 3) || ($this->userisediting)) {
                    $showsection = $thissection->uservisible ||
                        ($thissection->visible && !$thissection->available && !empty($thissection->availableinfo));
                } else {
                    $showsection = ($thissection->uservisible ||
                        ($thissection->visible && !$thissection->available && !empty($thissection->availableinfo))) &&
                        ($nextweekdate <= $timenow);
                }
                if (($currentsectionfirst == true) && ($showsection == true)) {
                    // Show the section if we were meant to and it is the current section:....
                    $showsection = ($course->marker == $section);
                } else if (($this->tcsettings['layoutstructure'] == 4) &&
                    ($course->marker == $section) && (!$this->userisediting)) {
                    $showsection = false; // Do not reshow current section.
                }
                if (!$showsection) {
                    // Hidden section message is overridden by 'unavailable' control.
                    $testhidden = false;
                    if ($this->tcsettings['layoutstructure'] != 4) {
                        if (($this->tcsettings['layoutstructure'] != 3) || ($this->userisediting)) {
                            $testhidden = true;
                        } else if ($nextweekdate <= $timenow) {
                            $testhidden = true;
                        }
                    } else {
                        if (($currentsectionfirst == true) && ($course->marker == $section)) {
                            $testhidden = true;
                        } else if (($currentsectionfirst == false) && ($course->marker != $section)) {
                            $testhidden = true;
                        }
                    }
                    if ($testhidden) {
                        if (!$course->hiddensections && $thissection->available) {
                            $thissection->ishidden = true;
                            $sectiondisplayarray[] = $thissection;
                        }
                    }
                } else {
                    if ($this->isoldtogglepreference == true) {
                        $togglestate = substr($this->togglelib->get_toggles(), $section, 1);
                        if ($togglestate == '1') {
                            $thissection->toggle = true;
                        } else {
                            $thissection->toggle = false;
                        }
                    } else {
                        $thissection->toggle = $this->togglelib->get_toggle_state($thissection->section);
                    }

                    if ($this->courseformat->is_section_current($thissection)) {
                        $this->currentsection = $thissection->section;
                        $thissection->toggle = true; // Open current section regardless of toggle state.
                        $this->togglelib->set_toggle_state($thissection->section, true);
                    }

                    $thissection->isshown = true;
                    $sectiondisplayarray[] = $thissection;
                }

                if (($this->tcsettings['layoutstructure'] != 3) || ($this->userisediting)) {
                    $section++;
                } else {
                    $section--;
                    if (($this->tcsettings['layoutstructure'] == 3) && ($this->userisediting == false)) {
                        $weekdate = $nextweekdate;
                    }
                }

                $loopsection++;
                if (($currentsectionfirst == true) && ($loopsection > $coursenumsections)) {
                    // Now show the rest.
                    $currentsectionfirst = false;
                    $loopsection = 1;
                    $section = 1;
                }
                if ($section > $coursenumsections) {
                    // Activities inside this section are 'orphaned', this section will be printed as 'stealth' below.
                    break;
                }
            }

            $canbreak = ($this->tcsettings['layoutcolumns'] > 1);
            $columncount = 1;
            $breakpoint = 0;
            $shownsectioncount = 0;
            if ((!$this->userisediting) && ($this->tcsettings['onesection'] == 2) && (!empty($this->currentsection))) {
                $shownonetoggle = $this->currentsection; // One toggle open only, so as we have a current section it will be it.
            }
            foreach ($sectiondisplayarray as $thissection) {
                $shownsectioncount++;

                if (!empty($thissection->ishidden)) {
                    echo $this->section_hidden($thissection);
                } else if (!empty($thissection->issummary)) {
                    echo $this->section_summary($thissection, $course, null);
                } else if (!empty($thissection->isshown)) {
                    if ((!$this->userisediting) && ($this->tcsettings['onesection'] == 2)) {
                        if ($thissection->toggle) {
                            if (!empty($shownonetoggle)) {
                                // Make sure the current section is not closed if set above.
                                if ($shownonetoggle != $thissection->section) {
                                    // There is already a toggle open so others need to be closed.
                                    $thissection->toggle = false;
                                    $this->togglelib->set_toggle_state($thissection->section, false);
                                }
                            } else {
                                // No open toggle, so as this is the first, it can be the one.
                                $shownonetoggle = $thissection->section;
                            }
                        }
                    }
                    echo $this->section_header($thissection, $course, false, 0);
                    if ($thissection->uservisible) {
                        echo $this->courserenderer->course_section_cm_list($course, $thissection, 0);
                        echo $this->courserenderer->course_section_add_cm_control($course, $thissection->section, 0);
                    }
                    echo html_writer::end_tag('div');
                    echo $this->section_footer();
                }

                // Only check for breaking up the structure with rows if more than one column and when we output all of the sections.
                if ($canbreak === true) {
                    // Only break in non-mobile themes or using a responsive theme.
                    if ((!$this->formatresponsive) || ($this->mobiletheme === false)) {
                        if ($this->tcsettings['layoutcolumnorientation'] == 1) {  // Vertical mode.
                            // This is not perfect yet as does not tally the shown sections and divide by columns.
                            if (($breaking == false) && ($showsection == true)) {
                                $breaking = true;
                                // Divide the number of sections by the number of columns.
                                $breakpoint = $numsections / $this->tcsettings['layoutcolumns'];
                            }

                            if (($breaking == true) && ($shownsectioncount >= $breakpoint) &&
                                ($columncount < $this->tcsettings['layoutcolumns'])) {
                                echo $this->end_section_list();
                                echo $this->start_toggle_section_list();
                                $columncount++;
                                // Next breakpoint is...
                                $breakpoint += $numsections / $this->tcsettings['layoutcolumns'];
                            }
                        } else {  // Horizontal mode.
                            if (($breaking == false) && ($showsection == true)) {
                                $breaking = true;
                                // The lowest value here for layoutcolumns is 2 and the maximum for shownsectioncount is 2, so :).
                                $breakpoint = $this->tcsettings['layoutcolumns'];
                            }

                            if (($breaking == true) && ($shownsectioncount >= $breakpoint)) {
                                echo $this->end_section_list();
                                echo $this->start_toggle_section_list();
                                // Next breakpoint is...
                                $breakpoint += $this->tcsettings['layoutcolumns'];
                            }
                        }
                    }
                }

                unset($sections[$thissection->section]);
            }
        }

        $changenumsection = '';
        if ($this->userisediting and has_capability('moodle/course:update', $context)) {
            // Print stealth sections if present.
            foreach ($modinfo->get_section_info_all() as $section => $thissection) {
                if ($section <= $coursenumsections or empty($modinfo->sections[$section])) {
                    // This is not stealth section or it is empty.
                    continue;
                }
                echo $this->stealth_section_header($section);
                echo $this->courserenderer->course_section_cm_list($course, $thissection->section, 0);
                echo $this->stealth_section_footer();
            }

            $changenumsection = $this->change_number_sections($course, 0);
        }

        if (!empty($sections)) {
            echo $this->end_section_list();
            if ((!$this->formatresponsive) && ($this->tcsettings['layoutcolumnorientation'] == 1)) { // Vertical columns.
                echo html_writer::end_tag('div');
            }
        }

        echo $changenumsection;

        // Now initialise the JavaScript.
        $toggles = $this->togglelib->get_toggles();
        $this->page->requires->js_init_call('M.format_topcoll.init', array(
            $course->id,
            $toggles,
            $coursenumsections,
            $this->defaulttogglepersistence,
            $this->defaultuserpreference,
            ((!$this->userisediting) && ($this->tcsettings['onesection'] == 2)),
            $shownonetoggle,
            $this->userisediting));
        // Make sure the database has the correct state of the toggles if changed by the code.
        // This ensures that a no-change page reload is correct.
        set_user_preference('topcoll_toggle_'.$course->id, $toggles);
    }

    /**
     * Displays the toggle all functionality.
     * @return string HTML to output.
     */
    protected function toggle_all() {
        $o = html_writer::start_tag('li', array('class' => 'tcsection main clearfix', 'id' => 'toggle-all'));

        if ((($this->mobiletheme === false) && ($this->tablettheme === false)) || ($this->userisediting)) {
            $o .= html_writer::tag('div', $this->output->spacer(), array('class' => 'left side'));
            $o .= html_writer::tag('div', $this->output->spacer(), array('class' => 'right side'));
        }

        $o .= html_writer::start_tag('div', array('class' => 'content'));
        $iconsetclass = ' toggle-' . $this->tcsettings['toggleiconset'];
        if ($this->tcsettings['toggleallhover'] == 2) {
            $iconsetclass .= '-hover' . $iconsetclass;
        }
        $o .= html_writer::start_tag('div', array('class' => 'sectionbody' . $iconsetclass));
        $o .= html_writer::start_tag('h4', null);
        $o .= html_writer::tag('span', get_string('topcollopened', 'format_topcoll'),
            array('class' => 'on ' . $this->tctoggleiconsize, 'id' => 'toggles-all-opened',
            'role' => 'button')
        );
        $o .= html_writer::tag('span', get_string('topcollclosed', 'format_topcoll'),
            array('class' => 'off ' . $this->tctoggleiconsize, 'id' => 'toggles-all-closed',
            'role' => 'button')
        );
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
        $o = html_writer::start_tag('li',
            array('class' => 'tcsection main clearfix', 'id' => 'topcoll-display-instructions'));

        if ((($this->mobiletheme === false) && ($this->tablettheme === false)) || ($this->userisediting)) {
            $o .= html_writer::tag('div', $this->output->spacer(), array('class' => 'left side'));
            $o .= html_writer::tag('div', $this->output->spacer(), array('class' => 'right side'));
        }

        $o .= html_writer::start_tag('div', array('class' => 'content'));
        $o .= html_writer::start_tag('div', array('class' => 'sectionbody'));
        $o .= html_writer::tag('p', get_string('instructions', 'format_topcoll'),
            array('class' => 'topcoll-display-instructions')
        );
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

    public function set_user_preference($userpreference, $defaultuserpreference, $defaulttogglepersistence) {
        $this->defaultuserpreference = $defaultuserpreference;
        $this->defaulttogglepersistence = $defaulttogglepersistence;
        $coursenumsections = $this->courseformat->get_last_section_number();
        if ($userpreference != null) {
            $this->isoldtogglepreference = $this->togglelib->is_old_preference($userpreference);
            if ($this->isoldtogglepreference == true) {
                $ts1 = base_convert(substr($userpreference, 0, 6), 36, 2);
                $ts2 = base_convert(substr($userpreference, 6, 12), 36, 2);
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
                $this->togglelib->set_toggles($tb);
            } else {
                // Check we have enough digits for the number of toggles in case this has increased.
                $numdigits = $this->togglelib->get_required_digits($coursenumsections);
                $totdigits = strlen($userpreference);
                if ($numdigits > $totdigits) {
                    if ($this->defaultuserpreference == 0) {
                        $dchar = $this->togglelib->get_min_digit();
                    } else {
                        $dchar = $this->togglelib->get_max_digit();
                    }
                    for ($i = $totdigits; $i < $numdigits; $i++) {
                        $userpreference .= $dchar;
                    }
                } else if ($numdigits < $totdigits) {
                    // Shorten to save space.
                    $userpreference = substr($userpreference, 0, $numdigits);
                }
                $this->togglelib->set_toggles($userpreference);
            }
        } else {
            $numdigits = $this->togglelib->get_required_digits($coursenumsections);
            if ($this->defaultuserpreference == 0) {
                $dchar = $this->togglelib->get_min_digit();
            } else {
                $dchar = $this->togglelib->get_max_digit();
            }
            $userpreference = '';
            for ($i = 0; $i < $numdigits; $i++) {
                $userpreference .= $dchar;
            }
            $this->togglelib->set_toggles($userpreference);
        }
    }

    protected function get_row_class() {
        if ($this->bsnewgrid) {
            return 'row';
        } else {
            return 'row-fluid';
        }
    }

    protected function get_column_class($columns) {
        if ($this->bsnewgrid) {
            $colclasses = array(
                1 => 'col-sm-12 col-md-12 col-lg-12',
                2 => 'col-sm-6 col-md-6 col-lg-6',
                3 => 'col-md-4 col-lg-4',
                4 => 'col-lg-3');
        } else {
            $colclasses = array(1 => 'span12', 2 => 'span6', 3 => 'span4', 4 => 'span3');
        }

        return $colclasses[$columns];
    }

    public function get_format_responsive() {
        return $this->formatresponsive;
    }
}
