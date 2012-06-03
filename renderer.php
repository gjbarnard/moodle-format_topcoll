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
 * Renderer for outputting the topics course format.
 *
 * @package format_topics
 * @copyright 2012 Dan Poltawski
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since Moodle 2.3
 */


defined('MOODLE_INTERNAL') || die();
require_once($CFG->dirroot.'/course/format/renderer.php');

/**
 * Basic renderer for collapsed topics format.
 *
 * @copyright 2012 Dan Poltawski
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class format_topcoll_renderer extends format_section_renderer_base {

    /**
     * Generate the starting container html for a list of sections
     * @return string HTML to output.
     */
    protected function start_section_list() {
        return html_writer::start_tag('ul', array('class' => 'sections'));
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
        return get_string('sectionname','format_topcoll');
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

        if (!has_capability('moodle/course:update', context_course::instance($course->id))) {
            return array();
        }

        if ($onsectionpage) {
            $url = course_get_url($course, $section->section);
        } else {
            $url = course_get_url($course);
        }
        $url->param('sesskey', sesskey());

        $controls = array();
        if ($course->marker == $section->section) {  // Show the "light globe" on/off.
            $url->param('marker', 0);
            $controls[] = html_writer::link($url,
                                html_writer::empty_tag('img', array('src' => $this->output->pix_url('i/marked'),
                                    'class' => 'icon ', 'alt' => get_string('markedthistopic'))),
                                array('title' => get_string('markedthistopic'), 'class' => 'editing_highlight'));
        } else {
            $url->param('marker', $section->section);
            $controls[] = html_writer::link($url,
                            html_writer::empty_tag('img', array('src' => $this->output->pix_url('i/marker'),
                                'class' => 'icon', 'alt' => get_string('markthistopic'))),
                            array('title' => get_string('markthistopic'), 'class' => 'editing_highlight'));
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
     * @return string HTML to output.
     */
    protected function section_header($section, $course, $onsectionpage) {
        global $PAGE;

        $o = '';
        $currenttext = '';
        $sectionstyle = '';
        $linktitle = false;

        if ($section->section != 0) {
            // Only in the non-general sections.
            if (!$section->visible) {
                $sectionstyle = ' hidden';
            } else if ($this->is_section_current($section, $course)) {
                $sectionstyle = ' current';
            }
            $linktitle = ($course->coursedisplay == COURSE_DISPLAY_MULTIPAGE);
        }

        $o.= html_writer::start_tag('li', array('id' => 'section-'.$section->section,
            'class' => 'section main clearfix'.$sectionstyle));

        $leftcontent = $this->section_left_content($section, $course, $onsectionpage);
        $o.= html_writer::tag('div', $leftcontent, array('class' => 'left side'));

        $rightcontent = $this->section_right_content($section, $course, $onsectionpage);
        $o.= html_writer::tag('div', $rightcontent, array('class' => 'right side'));
        $o.= html_writer::start_tag('div', array('class' => 'content'));

        if (!$onsectionpage) {
        $o.= html_writer::start_tag('div', array('class' => 'sectionhead toggle'));
        //$o.= '<a id="sectionatag-' . $section . '" class="cps_nosumm" href="#" onclick="toggle_topic(this,' . $section->section . '); return false;">';	
        $title = get_section_name($course, $section);
        $o.= html_writer::tag('a', $title, array('class' => 'cps_nosumm', 'href' => '#', 'onclick' => 'toggle_topic(this,' . $section->section . '); return false;'));
        $o.= html_writer::end_tag('div');
        $o.= html_writer::start_tag('div', array('class' => 'sectionbody toggledsection'));

            if ($linktitle) {
                $title = html_writer::link(course_get_url($course, $section->section), $title);
            }
            //$o.= $this->output->heading($title, 3, 'sectionname');
        } else {
		        $o.= html_writer::start_tag('div', array('class' => 'sectionbody'));
        }

        $o.= html_writer::start_tag('div', array('class' => 'summary'));
        $o.= $this->format_summary_text($section);

        $context = context_course::instance($course->id);
        if ($PAGE->user_is_editing() && has_capability('moodle/course:update', $context)) {
            $url = new moodle_url('/course/editsection.php', array('id'=>$section->id));

            if ($onsectionpage) {
                $url->param('sectionreturn', 1);
            }

            $o.= html_writer::link($url,
                html_writer::empty_tag('img', array('src' => $this->output->pix_url('t/edit'), 'class' => 'iconsmall edit')),
                array('title' => get_string('editsummary')));
        }
        $o.= html_writer::end_tag('div');

        $o .= $this->section_availability_message($section);

        return $o;
    }

    /**
     * Generate the display of the footer part of a section
     *
     * @return string HTML to output.
     */
    protected function section_footer() {
        $o = html_writer::end_tag('div');
        $o = html_writer::end_tag('div');
        $o.= html_writer::end_tag('li');

        return $o;
    }	
}
