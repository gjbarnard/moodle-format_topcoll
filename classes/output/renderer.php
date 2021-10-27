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
 * @copyright  &copy; 2012-onwards G J Barnard in respect to modifications of standard topics format.
 * @author     G J Barnard - {@link http://moodle.org/user/profile.php?id=442195}
 * @link       http://docs.moodle.org/en/Collapsed_Topics_course_format
 * @license    http://www.gnu.org/copyleft/gpl.html GNU Public License
 *
 */

namespace format_topcoll\output;

defined('MOODLE_INTERNAL') || die();

use context_course;
use html_writer;
use moodle_url;

require_once($CFG->dirroot.'/course/format/renderer.php'); // For format_section_renderer_base.
require_once($CFG->dirroot.'/course/format/lib.php'); // For course_get_format.

class renderer extends \format_section_renderer_base {

    protected $tccolumnwidth = 100; // Default width in percent of the column(s).
    protected $tccolumnpadding = 0; // Default padding in pixels of the column(s).
    protected $mobiletheme = false; // As not using a mobile theme we can react to the number of columns setting.
    protected $tablettheme = false; // As not using a tablet theme we can react to the number of columns setting.
    protected $courseformat = null; // Our course format object as defined in lib.php.
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

    /**
     * Constructor method, calls the parent constructor - MDL-21097.
     *
     * @param moodle_page $page.
     * @param string $target one of rendering target constants.
     */
    public function __construct(\moodle_page $page, $target) {
        parent::__construct($page, $target);
        $this->courserenderer = $this->page->get_renderer('format_topcoll', 'course');
        $this->togglelib = new \format_topcoll\togglelib();
        $this->courseformat = course_get_format($page->course); // Needed for collapsed topics settings retrieval.

        /* Since format_topcoll_renderer::section_edit_control_items() only displays the 'Set current section' control when editing
           mode is on we need to be sure that the link 'Turn editing mode on' is available for a user who does not have any
           other managing capability. */
        $page->set_other_editing_capability('moodle/course:setcurrentsection');

        $this->userisediting = $page->user_is_editing();
        $this->tctoggleiconsize = clean_param(get_config('format_topcoll', 'defaulttoggleiconsize'), PARAM_TEXT);
        $this->formatresponsive = get_config('format_topcoll', 'formatresponsive');

        $this->rtl = right_to_left();

        // Portable.
        $devicetype = \core_useragent::get_device_type(); // In /lib/classes/useragent.php.
        if ($devicetype == "mobile") {
            $this->mobiletheme = true;
        } else if ($devicetype == "tablet") {
            $this->tablettheme = true;
        } else {
            $this->mobiletheme = false;
            $this->tablettheme = false;
        }
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
     * Generate the starting container html for a list of sections.
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
        $attributes = array();
        if (($this->mobiletheme === true) || ($this->tablettheme === true)) {
            $classes .= ' ctportable';
        }
        if ($this->formatresponsive) {
            $style = '';
            if ($this->tcsettings['layoutcolumnorientation'] == 1) { // Vertical columns.
                $style .= 'width:'.$this->tccolumnwidth.'%;';
            } else {
                $style .= 'width: 100%;';  // Horizontal columns.
            }
            if ($this->mobiletheme === false) {
                $classes .= ' ctlayout';
            }
            $style .= ' padding-left: '.$this->tccolumnpadding.'px; padding-right: '.$this->tccolumnpadding.'px;';
            $attributes['style'] = $style;
        } else {
            if ($this->tcsettings['layoutcolumnorientation'] == 1) { // Vertical columns.
                $classes .= ' '.$this->get_column_class($this->tcsettings['layoutcolumns']);
            } else {
                $classes .= ' '.$this->get_row_class();
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
     * @param bool $sectionishidden true if section is hidden.
     *
     * @return string HTML to output.
     */
    protected function section_right_content($section, $course, $onsectionpage, $sectionishidden = false) {
        $o = '';

        if ($section->section != 0) {
            $controls = $this->section_edit_control_items($course, $section, $onsectionpage);
            if (!empty($controls)) {
                $o .= $this->section_edit_control_menu($controls, $course, $section);
            } else if ((!$onsectionpage) && (!$sectionishidden)) {
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
                if ($this->tcsettings['viewsinglesectionenabled'] == 2) {
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
                $highlightoff = get_string('highlightoff');
                $controls['highlight'] = array(
                    'url' => $url, "icon" => 'i/marked',
                    'name' => $highlightoff,
                    'pixattr' => array('class' => ''),
                    'attr' => array('class' => 'editing_highlight',
                    'data-action' => 'removemarker')
                );
            } else {
                $url->param('marker', $section->section);
                $highlight = get_string('highlight');
                $controls['highlight'] = array(
                    'url' => $url, "icon" => 'i/marker',
                    'name' => $highlight,
                    'pixattr' => array('class' => ''),
                    'attr' => array('class' => 'editing_highlight',
                    'data-action' => 'setmarker')
                );
            }
        }

        $parentcontrols = parent::section_edit_control_items($course, $section, $onsectionpage);

        // If the edit key exists, we are going to insert our controls after it.
        if (array_key_exists("edit", $parentcontrols)) {
            $merged = array();
            /* We can't use splice because we are using associative arrays.
               Step through the array and merge the arrays. */
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
     * Generate the edit control action menu
     *
     * @param array $controls The edit control items from section_edit_control_items
     * @param stdClass $course The course entry from DB
     * @param stdClass $section The course_section entry from DB
     * @return string HTML to output.
     */
    protected function section_edit_control_menu($controls, $course, $section) {
        $o = "";
        if (!empty($controls)) {
            $menu = new \action_menu();
            $menu->set_menu_trigger(get_string('edit'));
            $menu->attributes['class'] .= ' section-actions';
            foreach ($controls as $value) {
                $url = empty($value['url']) ? '' : $value['url'];
                $icon = empty($value['icon']) ? '' : $value['icon'];
                $name = empty($value['name']) ? '' : $value['name'];
                $attr = empty($value['attr']) ? array() : $value['attr'];
                $class = empty($value['pixattr']['class']) ? '' : $value['pixattr']['class'];
                $al = new \action_menu_link_secondary(
                    new moodle_url($url),
                    new \pix_icon($icon, '', null, array('class' => "smallicon " . $class)),
                    $name,
                    $attr
                );
                $menu->add($al);
            }

            $coursecontext = context_course::instance($course->id);
            if (has_capability('moodle/course:manageactivities', $coursecontext)) {
                $duplicatestr = get_string('duplicate', 'format_topcoll');
                $duplicateurl = new moodle_url('/course/format/topcoll/duplicate.php',
                    array('courseid' => $course->id, 'sectionno' => $section->section, 'sesskey' => sesskey()));
                $link = new \action_link($duplicateurl, ' '.$duplicatestr, null,
                    array('class' => 'menu-action', 'role' => 'menuitem'),
                    new \pix_icon('t/copy', $duplicatestr));
                $link->add_action(new \confirm_action(get_string('duplicateconfirm', 'format_topcoll'), null,
                    $duplicatestr));
                $menu->add_secondary_action($link);
            }

            $o .= html_writer::div($this->render($menu), 'section_action_menu',
                array('data-sectionid' => $section->id));
        }

        return $o;
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
        $title = $this->courseformat->get_topcoll_section_name($course, $section, false);
        $sectionsummarycontext = array(
            'formatsummarytext' => $this->format_summary_text($section),
            'rtl' => $this->rtl,
            'sectionactivitysummary' => $this->section_activity_summary($section, $course, null),
            'sectionavailability' => $this->section_availability($section),
            'sectionno' => $section->section,
            'title' => $title
        );

        $classattrextra = '';
        $linkclasses = '';
        // If section is hidden then display grey section link.
        if (!$section->visible) {
            $classattrextra = ' hidden';
            $linkclasses .= 'dimmed_text';
        } else if ($this->courseformat->is_section_current($section)) {
            $classattrextra = ' current';
        }
        $sectionsummarycontext['classattrextra'] = $classattrextra;

        if ($this->tcsettings['layoutcolumnorientation'] == 2) { // Horizontal column layout.
            if ($this->formatresponsive) {
                $sectionsummarycontext['horizontalwidth'] = $this->tccolumnwidth;
            } else {
                $sectionsummarycontext['horizontalclass'] = $this->get_column_class($this->tcsettings['layoutcolumns']);
            }
        }

        if ($section->uservisible) {
            $title = html_writer::tag('a', $title,
                array('href' => course_get_url($course, $section->section), 'class' => $linkclasses));
        }
        $sectionsummarycontext['heading'] = $this->output->heading($title, 3, 'section-title');

        return $this->render_from_template('format_topcoll/sectionsummary', $sectionsummarycontext);
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
     * Generate the section.
     *
     * @param stdClass $section The course_section entry from DB.
     * @param stdClass $course The course entry from DB.
     * @param bool $onsectionpage true if being printed on a section page.
     * @param int $sectionreturn The section to return to after an action.
     * @param array $displayoptions The display options.
     *
     * @return string HTML to output.
     */
    protected function topcoll_section($section, $course, $onsectionpage, $sectionreturn = null, $displayoptions = array()) {
        $context = context_course::instance($course->id);

        $sectioncontext = array(
            'rtl' => $this->rtl,
            'sectionid' => $section->id,
            'sectionno' => $section->section,
            'sectionreturn' => $sectionreturn
        );

        if ($section->section != 0) {
            // Only in the non-general sections.
            if (!$section->visible) {
                $sectioncontext['sectionstyle'] = 'hidden';
            }
            if ($section->section == $this->currentsection) {
                $sectioncontext['sectionstyle'] = 'current';
            }
        }

        if (empty($this->tcsettings)) {
            $this->tcsettings = $this->courseformat->get_settings();
        }

        if ($this->tcsettings['layoutcolumnorientation'] == 2) { // Horizontal column layout.
            if ($this->formatresponsive) {
                $sectioncontext['horizontalwidth'] = $this->tccolumnwidth;
            } else if ((!$onsectionpage) && ($section->section != 0)) {
                $sectioncontext['horizontalclass'] = $this->get_column_class($this->tcsettings['layoutcolumns']);
            }
        }

        if ((($this->mobiletheme === false) && ($this->tablettheme === false)) || ($this->userisediting)) {
            $sectioncontext['nomtore'] = true;
            $sectioncontext['leftcontent'] = $this->section_left_content($section, $course, $onsectionpage);
            $sectioncontext['rightcontent'] = '';
            if (($section->section != 0) && $this->userisediting && has_capability('moodle/course:update', $context)) {
                $url = new moodle_url('/course/editsection.php', array('id' => $section->id, 'sr' => $sectionreturn));
                $sectioncontext['rightcontent'] .= html_writer::link(
                    $url,
                    $this->output->pix_icon('t/edit', get_string('edit')),
                        array('title' => get_string('editsection', 'format_topcoll'), 'class' => 'tceditsection')
                );
            }
            $sectioncontext['rightcontent'] .= $this->section_right_content($section, $course, $onsectionpage);
        }
        if ($section->section != 0) {
            $sectioncontext['contentaria'] = true;
        }
        $sectioncontext['sectionavailability'] = $this->section_availability($section);

        if (($onsectionpage == false) && ($section->section != 0)) {
            $sectioncontext['sectionpage'] = false;
            $sectioncontext['toggleiconset'] = $this->tcsettings['toggleiconset'];
            $sectioncontext['toggleiconsize'] = $this->tctoggleiconsize;

            if ((!($section->toggle === null)) && ($section->toggle == true)) {
                $sectioncontext['toggleopen'] = true;
            } else {
                $sectioncontext['toggleopen'] = false;
            }

            if ($this->userisediting) {
                $title = $this->section_title($section, $course);
            } else {
                $title = $this->courseformat->get_topcoll_section_name($course, $section, true);
            }
            if ((($this->mobiletheme === false) && ($this->tablettheme === false)) || ($this->userisediting)) {
                $sectioncontext['heading'] = $this->output->heading($title, 3, 'sectionname', "sectionid-{$section->id}-title");
            } else {
                // Moodle H3's look bad on mobile / tablet with CT so use plain.
                $sectioncontext['heading'] = html_writer::tag('h3', $title, array('id' => "sectionid-{$section->id}-title"));
            }

            $sectioncontext['sectionsummary'] = $this->section_summary_container($section);
            $sectioncontext['sectionsummarywhencollapsed'] = ($this->tcsettings['showsectionsummary'] == 2);

            if ($this->userisediting) {
                // CONTRIB-7434.
                $sectioncontext['usereditingtitle'] = $this->courseformat->get_topcoll_section_name($course, $section, false);
            }
        } else {
            $sectioncontext['sectionpage'] = true;
            // When on a section page, we only display the general section title, if title is not the default one.
            $hasnamesecpg = ($section->section == 0 && (string) $section->name !== '');

            if ($hasnamesecpg) {
                $headingclass = 'section-title';
                $title = $this->section_title($section, $course);
            } else {
                $headingclass = 'accesshide';
                $title = $this->section_title_without_link($section, $course);
            }
            $sectioncontext['heading'] = $this->output->heading($title, 3, $headingclass, "sectionid-{$section->id}-title");
            $sectioncontext['summary'] = $this->format_summary_text($section);
        }
        if ($this->userisediting && has_capability('moodle/course:update', $context)) {
            $sectioncontext['usereditingicon'] = $this->output->pix_icon('t/edit', get_string('edit'));
            $sectioncontext['usereditingurl'] = new moodle_url('/course/editsection.php', array('id' => $section->id, 'sr' => $sectionreturn));
        }

        if ($section->uservisible) {
            $sectioncontext['cscml'] = $this->courserenderer->course_section_cm_list($course, $section, $sectionreturn, $displayoptions);
            $sectioncontext['cscml'] .= $this->courserenderer->course_section_add_cm_control($course, $section->section, $sectionreturn);
        }

        return $this->render_from_template('format_topcoll/section', $sectioncontext);
    }

    /**
     * Generate the stealth section.
     *
     * @param stdClass $section The course_section entry from DB.
     * @param stdClass $course The course entry from DB.
     * @return string HTML to output.
     */
    protected function stealth_section($section, $course) {
        $stealthsectioncontext = array(
            'cscml' => $this->courserenderer->course_section_cm_list($course, $section->section, 0),
            'heading' => $this->output->heading(get_string('orphanedactivitiesinsectionno', '', $section->section),
                3, 'sectionname', "sectionid-{$section->id}-title"),
            'rightcontent' => $this->section_right_content($section, $course, false),
            'rtl' => $this->rtl,
            'sectionid' => $section->id,
            'sectionno' => $section->section
        );

        if ($this->tcsettings['layoutcolumnorientation'] == 2) { // Horizontal column layout.
            if ($this->formatresponsive) {
                $stealthsectioncontext['horizontalwidth'] = $this->tccolumnwidth;
            } else {
                $stealthsectioncontext['horizontalclass'] = $this->get_column_class($this->tcsettings['layoutcolumns']);
            }
        }

        return $this->render_from_template('format_topcoll/stealthsection', $stealthsectioncontext);
    }

    /**
     * Generate the html for a hidden section.
     *
     * @param stdClass $section The section in the course which is being displayed.
     * @param int|stdClass $courseorid The course to get the section name for (object or just course id).
     * @return string HTML to output.
     */
    protected function section_hidden($section, $courseorid = null) {
        $sectionhiddencontext = array(
            'sectionavailability' => $this->section_availability($section),
            'sectionno' => $section->section,
            'sectionid' => $section->id
        );
        $course = $this->courseformat->get_course();

        if ($this->tcsettings['layoutcolumnorientation'] == 2) { // Horizontal column layout.
            if ($this->formatresponsive) {
                $sectionhiddencontext['horizontalwidth'] = $this->tccolumnwidth;
            } else {
                $sectionhiddencontext['horizontalclass'] = $this->get_column_class($this->tcsettings['layoutcolumns']);
            }
        }

        $title = $this->section_title_without_link($section, $course);
        if ((($this->mobiletheme === false) && ($this->tablettheme === false)) || ($this->userisediting)) {
            $sectionhiddencontext['nomtore'] = true;
            $sectionhiddencontext['rtl'] = $this->rtl;
            $sectionhiddencontext['leftcontent'] = $this->section_left_content($section, $course, false);
            $sectionhiddencontext['rightcontent'] = $this->section_right_content($section, $course, false, true);
            $sectionhiddencontext['heading'] = $this->output->heading($title, 3, 'section-title', "sectionid-{$section->id}-title");
        } else {
            $sectionhiddencontext['title'] = $title;
        }

        return $this->render_from_template('format_topcoll/sectionhidden', $sectionhiddencontext);
    }

    /**
     * Output the html for a single section page.
     *
     * @param stdClass $course The course from the format_topcoll class.
     * @param array $sections (argument not used)
     * @param array $mods (argument not used)
     * @param array $modnames (argument not used)
     * @param array $modnamesused (argument not used)
     * @param int $displaysection The section number in the course which is being displayed
     */
    public function print_single_section_page($course, $sections, $mods, $modnames, $modnamesused, $displaysection) {
        $modinfo = get_fast_modinfo($course);

        // Can we view the section in question?
        if (!($sectioninfo = $modinfo->get_section_info($displaysection)) || !$sectioninfo->uservisible) {
            /* This section doesn't exist or is not available for the user.
               We actually already check this in course/view.php but just in case exit from this function as well. */
            print_error('unknowncoursesection', 'error', course_get_url($course),
                format_string($course->fullname));
        }

        $maincoursepage = get_string('maincoursepage', 'format_topcoll');
        // The requested section page.
        $thissection = $modinfo->get_section_info($displaysection);

        $singlesectioncontext = array(
            'activityclipboard' => $this->course_activity_clipboard($course, $displaysection),
            'maincoursepageicon' => $this->output->pix_icon('t/less', $maincoursepage),
            'maincoursepagestr' => $maincoursepage,
            'maincoursepageurl' => new moodle_url('/course/view.php', array('id' => $course->id)),
            'sectionnavselection' => $this->section_nav_selection($course, null, $displaysection),
            'thissection' => $this->topcoll_section($thissection, $course, true, $displaysection, array('sr' => $displaysection))
        );

        $sectionzero = $modinfo->get_section_info(0);
        if ($sectionzero->summary or !empty($modinfo->sections[0]) or $this->page->user_is_editing()) {
            $singlesectioncontext['sectionzero'] = $this->topcoll_section($sectionzero, $course, true, $displaysection, array('sr' => $displaysection));
        }

        // The requested section page.
        $thissection = $modinfo->get_section_info($displaysection);

        // Title with section navigation links.
        $sectionnavlinks = $this->get_nav_links($course, $modinfo->get_section_info_all(), $displaysection);
        $singlesectioncontext['sectionnavlinksprevious'] = $sectionnavlinks['previous'];
        $singlesectioncontext['sectionnavlinksnext'] = $sectionnavlinks['next'];
        // Title attributes.
        $classes = 'sectionname';
        if (!$thissection->visible) {
            $classes .= ' dimmed_text';
        }
        $sectionname = $this->section_title_without_link($thissection, $course);
        $singlesectioncontext['sectiontitle'] = $this->output->heading($sectionname, 3, $classes);

        echo $this->render_from_template('format_topcoll/singlesection', $singlesectioncontext);
    }

    /**
     * Output the html for a multiple section page
     *
     * @param stdClass $course The course from the format_topcoll class.
     * @param array $sections (argument not used)
     * @param array $mods (argument not used)
     * @param array $modnames (argument not used)
     * @param array $modnamesused (argument not used)
     */
    public function print_multiple_section_page($course, $sections, $mods, $modnames, $modnamesused) {
        echo $this->course_styles();

        $modinfo = get_fast_modinfo($course);
        if (empty($this->tcsettings)) {
            $this->tcsettings = $this->courseformat->get_settings();
        }

        $context = context_course::instance($course->id);
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
            echo $this->topcoll_section($thissection, $course, false, 0);
        }

        $shownonetoggle = false;
        $coursenumsections = $this->courseformat->get_last_section_number();
        if ($coursenumsections > 0) {
            $sectiondisplayarray = array();
            $sectionoutput = '';
            $toggledsections = array();
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

            $sectionoutput .= $this->end_section_list();
            if ((!$this->formatresponsive) && ($this->tcsettings['layoutcolumnorientation'] == 1)) { // Vertical columns.
                $sectionoutput .= html_writer::start_tag('div', array('class' => $this->get_row_class()));
            }
            $sectionoutput .= $this->start_toggle_section_list();

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

            $numshownsections = count($sectiondisplayarray);
            foreach ($sectiondisplayarray as $thissection) {
                $shownsectioncount++;

                if (!empty($thissection->ishidden)) {
                    $sectionoutput .= $this->section_hidden($thissection);
                } else if (!empty($thissection->issummary)) {
                    $sectionoutput .= $this->section_summary($thissection, $course, null);
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
                    $sectionoutput .= $this->topcoll_section($thissection, $course, false, 0);
                    $toggledsections[] = $thissection->section;
                }

                // Only check for breaking up the structure with rows if more than one column and when we output all of the sections.
                if ($canbreak === true) {
                    // Only break in non-mobile themes or using a responsive theme.
                    if ((!$this->formatresponsive) || ($this->mobiletheme === false)) {
                        if ($this->tcsettings['layoutcolumnorientation'] == 1) {  // Vertical mode.
                            if ($breaking == false) {
                                $breaking = true;
                                // Divide the number of sections by the number of columns.
                                $breakpoint = $numshownsections / $this->tcsettings['layoutcolumns'];
                            }

                            if (($breaking == true) && ($shownsectioncount >= $breakpoint) &&
                                ($columncount < $this->tcsettings['layoutcolumns'])) {
                                $sectionoutput .= $this->end_section_list();
                                $sectionoutput .= $this->start_toggle_section_list();
                                $columncount++;
                                // Next breakpoint is...
                                $breakpoint += $numshownsections / $this->tcsettings['layoutcolumns'];
                            }
                        } else {  // Horizontal mode.
                            if ($breaking == false) {
                                $breaking = true;
                                // The lowest value here for layoutcolumns is 2 and the maximum for shownsectioncount is 2, so :).
                                $breakpoint = $this->tcsettings['layoutcolumns'];
                            }

                            if (($breaking == true) && ($shownsectioncount >= $breakpoint)) {
                                $sectionoutput .= $this->end_section_list();
                                $sectionoutput .= $this->start_toggle_section_list();
                                // Next breakpoint is...
                                $breakpoint += $this->tcsettings['layoutcolumns'];
                            }
                        }
                    }
                }

                unset($sections[$thissection->section]);
            }
            if ($coursenumsections > 1) {
                if ($this->tcsettings['toggleallenabled'] == 2) {
                    if (($this->userisediting) || ($this->tcsettings['onesection'] == 1)) {
                        // Collapsed Topics all toggles.
                        echo $this->toggle_all($toggledsections);
                    }
                }
                if ($this->tcsettings['displayinstructions'] == 2) {
                    // Collapsed Topics instructions.
                    echo $this->display_instructions();
                }
            }
            echo $sectionoutput;
        }

        $changenumsections = '';
        if ($this->userisediting and has_capability('moodle/course:update', $context)) {
            $changenumsections = $this->change_number_sections($course, 0);
            // Print stealth sections if present.
            foreach ($modinfo->get_section_info_all() as $thissection) {
                $sectionno = $thissection->section;
                if ($sectionno <= $coursenumsections or empty($modinfo->sections[$sectionno])) {
                    // This is not stealth section or it is empty.
                    continue;
                }
                echo $this->stealth_section($thissection, $course);
            }
        }
        echo $this->end_section_list();
        if ($coursenumsections > 0) {
            if ((!$this->formatresponsive) && ($this->tcsettings['layoutcolumnorientation'] == 1)) { // Vertical columns.
                echo html_writer::end_tag('div');
            }
        }

        echo $changenumsections;

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
        /* Make sure the database has the correct state of the toggles if changed by the code.
           This ensures that a no-change page reload is correct. */
        set_user_preference('topcoll_toggle_'.$course->id, $toggles);
    }

    /**
     * Displays the toggle all functionality.
     * @param array $toggledsections Array of section id's that are toggled.
     *
     * @return string HTML to output.
     */
    protected function toggle_all($toggledsections) {
        $sct = $this->courseformat->get_structure_collection_type();
        $toggleallcontext = array(
            'iconset' => $this->tcsettings['toggleiconset'],
            'rtl' => $this->rtl,
            'sctcloseall' => get_string('sctcloseall', 'format_topcoll', $sct),
            'sctopenall' => get_string('sctopenall', 'format_topcoll', $sct),
            'toggleallhover' => ($this->tcsettings['toggleallhover'] == 2),
            'tctoggleiconsize' => $this->tctoggleiconsize
        );

        if ((($this->mobiletheme === false) && ($this->tablettheme === false)) || ($this->userisediting)) {
            $toggleallcontext['spacer'] = $this->output->spacer();
        }

        $ariacontrolselements = array();
        foreach ($toggledsections as $toggledsection) {
            $ariacontrolselements[] = 'toggledsection-'.$toggledsection;
        }
        $toggleallcontext['ariacontrols'] = implode(' ', $ariacontrolselements);

        return $this->render_from_template('format_topcoll/toggleall', $toggleallcontext);
    }

    /**
     * Displays the instructions functionality.
     * @return string HTML to output.
     */
    protected function display_instructions() {
        $displayinstructionscontext = array(
            'rtl' => $this->rtl
        );

        if ((($this->mobiletheme === false) && ($this->tablettheme === false)) || ($this->userisediting)) {
            $displayinstructionscontext['spacer'] = $this->output->spacer();
        }

        return $this->render_from_template('format_topcoll/displayinstructions', $displayinstructionscontext);
    }

    /**
     * The course styles.
     * @return string HTML to output.
     */
    protected function course_styles() {
        if (empty($this->tcsettings)) {
            $this->tcsettings = $this->courseformat->get_settings();
        }

        $coursestylescontext = array();
        $coursestylescontext['togglebackground'] = \format_topcoll\toolbox::hex2rgba(
            $this->tcsettings['togglebackgroundcolour'], $this->tcsettings['togglebackgroundopacity']);
        $coursestylescontext['toggleforegroundcolour'] = \format_topcoll\toolbox::hex2rgba(
            $this->tcsettings['toggleforegroundcolour'], $this->tcsettings['toggleforegroundopacity']);
        switch ($this->tcsettings['togglealignment']) {
            case 1:
                $coursestylescontext['togglealignment'] = 'left';
                break;
            case 3:
                $coursestylescontext['togglealignment'] = 'right';
                break;
            default:
                $coursestylescontext['togglealignment'] = 'center';
        }
        switch ($this->tcsettings['toggleiconposition']) {
            case 2:
                $coursestylescontext['toggleiconposition'] = 'right';
                break;
            default:
                $coursestylescontext['toggleiconposition'] = 'left';
        }
        $coursestylescontext['toggleforegroundhovercolour'] = \format_topcoll\toolbox::hex2rgba(
            $this->tcsettings['toggleforegroundhovercolour'], $this->tcsettings['toggleforegroundhoveropacity']);
        $coursestylescontext['togglebackgroundhovercolour'] = \format_topcoll\toolbox::hex2rgba(
            $this->tcsettings['togglebackgroundhovercolour'], $this->tcsettings['togglebackgroundhoveropacity']);

        $topcollsidewidth = get_string('topcollsidewidthlang', 'format_topcoll');
        $topcollsidewidthdelim = strpos($topcollsidewidth, '-');
        $topcollsidewidthlang = strcmp(substr($topcollsidewidth, 0, $topcollsidewidthdelim), current_language());
        $topcollsidewidthval = substr($topcollsidewidth, $topcollsidewidthdelim + 1);
        // Dynamically changing widths with language.
        if ((!$this->userisediting) && (($this->mobiletheme == false) && ($this->tablettheme == false)) && ($topcollsidewidthlang == 0)) {
            $coursestylescontext['topcollsidewidthval'] = $topcollsidewidthval;
        } else if ($this->userisediting) {
            $coursestylescontext['topcollsidewidthval'] = '40px';
        }

        // Make room for editing icons.
        if ((!$this->userisediting) && ($topcollsidewidthlang == 0)) {
            $coursestylescontext['topcollsidewidthvalicons'] = $topcollsidewidthval;
        }

        // Establish horizontal unordered list for horizontal columns.
        if (($this->get_format_responsive()) && ($this->tcsettings['layoutcolumnorientation'] == 2)) {
            $coursestylescontext['hulhc'] = true;
        }

        // Site wide configuration Site Administration -> Plugins -> Course formats -> Collapsed Topics.
        $coursestylescontext['tcborderradiustl'] = clean_param(get_config('format_topcoll', 'defaulttoggleborderradiustl'), PARAM_TEXT);
        $coursestylescontext['tcborderradiustr'] = clean_param(get_config('format_topcoll', 'defaulttoggleborderradiustr'), PARAM_TEXT);
        $coursestylescontext['tcborderradiusbr'] = clean_param(get_config('format_topcoll', 'defaulttoggleborderradiusbr'), PARAM_TEXT);
        $coursestylescontext['tcborderradiusbl'] = clean_param(get_config('format_topcoll', 'defaulttoggleborderradiusbl'), PARAM_TEXT);

        return $this->render_from_template('format_topcoll/coursestyles', $coursestylescontext);
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
        return 'row';
    }

    protected function get_column_class($columns) {
        static $colclasses = array(
            1 => 'col-sm-12',
            2 => 'col-sm-6',
            3 => 'col-md-4',
            4 => 'col-lg-3');

        return $colclasses[$columns];
    }

    public function get_format_responsive() {
        return $this->formatresponsive;
    }
}
