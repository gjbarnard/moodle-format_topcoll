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
 * @copyright  &copy; 2012-onwards G J Barnard in respect to modifications of standard topics format.
 * @author     G J Barnard - {@link https://moodle.org/user/profile.php?id=442195}
 * @link       https://docs.moodle.org/en/Collapsed_Topics_course_format
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace format_topcoll\output;

defined('MOODLE_INTERNAL') || die();

use core_courseformat\base as course_format;
use context_course;
use core\output\html_writer;
use core\url;
use core_courseformat\output\section_renderer;
use core_useragent;
use format_topcoll\togglelib;
use format_topcoll\toolbox;
use moodle_exception;
use moodle_page;
use section_info;
use stdClass;

require_once($CFG->dirroot . '/course/format/lib.php'); // For course_get_format.

/**
 * The renderer.
 */
class renderer extends section_renderer {
    use format_renderer_migration_toolbox;

    /** @var int $tccolumnwidth Default width in percent of the column(s).*/
    protected $tccolumnwidth = 100;
    /** @var int $tccolumnpadding Default padding in pixels of the column(s).*/
    protected $tccolumnpadding = 0;
    /** @var bool $mobiletheme As not using a mobile theme we can react to the number of columns setting.*/
    protected $mobiletheme = false;
    /** @var bool $tablettheme As not using a tablet theme we can react to the number of columns setting.*/
    protected $tablettheme = false;
    /** @var class $courseformat Our course format object as defined in lib.php.*/
    protected $courseformat = null;
    /** @var class $course Our course object.*/
    protected $course = null;
    /** @var array $tcsettings Settings for the format.*/
    protected $tcsettings;
    /** @var string $defaulttogglepersistence Default toggle persistence.*/
    protected $defaulttogglepersistence;
    /** @var string $defaultuserpreference Default user preference when none set - bool - true all open, false all closed.*/
    protected $defaultuserpreference;
    /** @var class $togglelib Toggle lib object.*/
    protected $togglelib;
    /** @var int $currentsection If not false then will be the current section number.*/
    protected $currentsection = false;
    /** @var bool $userisediting */
    protected $userisediting = false;
    /** @var string $tconesectioniconfont */
    protected $tconesectioniconfont;
    /** @var string $tctoggleiconsize */
    protected $tctoggleiconsize;
    /** @var bool $formatresponsive */
    protected $formatresponsive;
    /** @var bool $rtl */
    protected $rtl = false;
    /** @var optional visibility output class */
    protected $visibilityclass;

    /**
     * Constructor method, calls the parent constructor - MDL-21097.
     *
     * @param moodle_page $page The page.
     * @param string $target One of rendering target constants.
     */
    public function __construct(moodle_page $page, $target) {
        parent::__construct($page, $target);
        $this->togglelib = new togglelib();
        $this->courseformat = course_get_format($page->course); // Needed for collapsed topics settings retrieval.
        $this->course = $this->courseformat->get_course();

        /* Since format_topcoll_renderer::section_edit_control_items() only displays the 'Set current section' control when editing
           mode is on we need to be sure that the link 'Turn editing mode on' is available for a user who does not have any
           other managing capability. */
        $page->set_other_editing_capability('moodle/course:setcurrentsection');

        $this->userisediting = $this->courseformat->show_editor();
        $this->tconesectioniconfont = get_config('format_topcoll', 'defaultonesectioniconfont');
        $this->tctoggleiconsize = get_config('format_topcoll', 'defaulttoggleiconsize');
        $this->formatresponsive = get_config('format_topcoll', 'formatresponsive');

        $this->rtl = right_to_left();

        // Portable.
        $devicetype = core_useragent::get_device_type(); // In /lib/classes/useragent.php.
        if ($devicetype == "mobile") {
            $this->mobiletheme = true;
        } else if ($devicetype == "tablet") {
            $this->tablettheme = true;
        } else {
            $this->mobiletheme = false;
            $this->tablettheme = false;
        }

        $this->visibilityclass = $this->courseformat->get_output_classname('content\\section\\visibility');
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
     * Get the updated rendered version of a section.
     *
     * NOTE: Left here, but I'm currently not sure if used - Shows how could happen as CT does not have 'Section' class.
     *
     * This method will only be used when the course editor requires to get an updated cm item HTML
     * to perform partial page refresh. It will be used for supporting the course editor webservices.
     *
     * By default, the template used for update a section is the same as when it renders initially,
     * but format plugins are free to override this method to provide extra effects or so.
     *
     * @param course_format $format the course format.
     * @param section_info $section the section info.
     * @return string the rendered element.
     */
    public function course_section_updated(
        course_format $format,
        section_info $section
    ): string {
        if ($section->section == 0) {
            return '';
        }
        if (empty($this->tcsettings)) {
            $this->tcsettings = $format->get_settings();
        }
        $this->set_user_preferences();

        if ($this->courseformat->is_section_current($section)) {
            $togglestate = true;
        } else {
            $togglestate = $this->togglelib->get_toggle_state($section->section);
        }
        $course = $format->get_course();
        $output = $this->topcoll_section($section, $course, false, null, $togglestate);

        return $output;
    }

    /**
     * Generate the starting container html for a list of sections.
     * @return string HTML to output.
     */
    protected function start_section_list() {
        return html_writer::start_tag('ul', ['class' => 'ctopics']);
    }

    /**
     * Generate the starting container html for a list of sections when showing a toggle.
     * @return string HTML to output.
     */
    protected function start_toggle_section_list() {
        $classes = 'ctopics ctoggled topics';
        $attributes = [];
        if (($this->mobiletheme === true) || ($this->tablettheme === true)) {
            $classes .= ' ctportable';
        }
        if ($this->tcsettings['layoutcolumnorientation'] == 3) { // Dynamic columns.
            $classes .= ' ' . $this->get_row_class();
        } else if (!$this->userisediting) {
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
        }
        $attributes['class'] = $classes;
        if ($this->userisediting) {
            $attributes['data-for'] = 'course_sectionlist';
        }

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
            if ($this->userisediting) {
                $widgetclass = $this->courseformat->get_output_classname('content\\section\\controlmenu');
                $widget = new $widgetclass($this->courseformat, $section);
                $o .= $this->render($widget);
            } else if (!$onsectionpage) {
                if (empty($this->tcsettings)) {
                    $this->tcsettings = $this->courseformat->get_settings();
                }
                $url = new url('/course/view.php', ['id' => $course->id, 'section' => $section->section]);
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
                    if ($sectionishidden) {
                        switch ($this->tcsettings['layoutelement']) { // Toggle section x.
                            case 1:
                            case 3:
                            case 5:
                            case 8:
                                $o .= html_writer::tag(
                                    'span',
                                    $topictext . html_writer::empty_tag('br') .
                                    $section->section,
                                    ['class' => 'cps_centre']
                                );
                                break;
                        }
                    } else {
                        $title = get_string('viewonly', 'format_topcoll', ['sectionname' => $topictext . ' ' . $section->section]);
                        switch ($this->tcsettings['layoutelement']) { // Toggle section x.
                            case 1:
                            case 3:
                            case 5:
                            case 8:
                                $o .= html_writer::link(
                                    $url,
                                    $topictext . html_writer::empty_tag('br') .
                                    $section->section,
                                    ['title' => $title, 'class' => 'cps_centre']
                                );
                                break;
                            default:
                                $o .= html_writer::link(
                                    $url,
                                    $this->one_section_icon($title),
                                    ['title' => $title, 'class' => 'cps_centre']
                                );
                                break;
                        }
                    }
                }
            }
        }

        if (!empty($o) || ($this->userisediting)) {
            $o = html_writer::tag('div', $o, ['class' => 'right side']);
        }

        return $o;
    }

    /**
     * Generate the one section icon.
     *
     * @param string $title The title.
     * @return string HTML to output.
     */
    protected function one_section_icon($title) {
        if (empty($this->tconesectioniconfont)) {
            return $this->output->pix_icon('one_section', $title, 'format_topcoll');
        } else {
            $osicontext = [
                'osifc' => $this->tconesectioniconfont,
                'osift' => $title,
            ];
            return $this->render_from_template('format_topcoll/onesectioniconfont', $osicontext);
        }
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
                    $attr = ['class' => 'cps_centre'];
                    if ($this->userisediting) {
                        $attr['id'] = 'tcnoid-' . $section->id;
                    }
                    $o .= html_writer::tag('span', $section->section, $attr);
                    break;
            }
        }

        if (!empty($o) || ($this->userisediting)) {
            $o = html_writer::tag('div', $o, ['class' => 'left side']);
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
        $sectionsummarycontext = [
            'formatsummarytext' => $this->format_summary_text($section),
            'rtl' => $this->rtl,
            'sectionactivitysummary' => $this->section_activity_summary($section, $course, null),
            'sectionavailability' => $this->section_availability($section),
            'sectionno' => $section->section,
            'title' => $title,
        ];

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

        if ($this->tcsettings['layoutcolumnorientation'] == 3) { // Dynamic column layout.
            $sectionsummarycontext['columnclass'] = $this->get_column_class('D');
        } else if ($this->tcsettings['layoutcolumnorientation'] == 2) { // Horizontal column layout.
            if ($this->formatresponsive) {
                $sectionsummarycontext['columnwidth'] = $this->tccolumnwidth;
            } else {
                $sectionsummarycontext['colummnclass'] = $this->get_column_class($this->tcsettings['layoutcolumns']);
            }
        }

        if ($section->uservisible) {
            $title = html_writer::tag(
                'a',
                $title,
                [
                    'href' => $this->courseformat->get_view_url($section->section, ['navigation' => 'true'])->out(false),
                    'class' => $linkclasses,
                ]
            );
        }
        $sectionsummarycontext['heading'] = $this->section_heading($section, $title, 'section-title');

        return $this->render_from_template('format_topcoll/sectionsummary', $sectionsummarycontext);
    }

    /**
     * Generate section summary container.
     *
     * @param stdClass $section The course_section entry from DB.
     *
     * @return string HTML to output.
     */
    protected function section_summary_container($section) {
        $summarytext = $this->format_summary_text($section);
        if ($summarytext) {
            $classextra = ($this->tcsettings['showsectionsummary'] == 1) ? '' : ' summaryalwaysshown';
            $o = html_writer::start_tag('div', ['class' => 'summary' . $classextra]);
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
     * @param section_info $section The section.
     * @param stdClass $course The course entry from DB.
     * @param bool $onsectionpage true if being printed on a section page.
     * @param int $sectionreturn The section to return to after an action.
     * @param ?bool $toggle true if toggle is enabled, false otherwise, null if not set
     *
     * @return string HTML to output.
     */
    protected function topcoll_section($section, $course, $onsectionpage, $sectionreturn = null, $toggle = null) {
        $context = context_course::instance($course->id);

        $sectioncontext = [
            'rtl' => $this->rtl,
            'sectionid' => $section->id,
            'sectionno' => $section->section,
            'sectionreturn' => $sectionreturn,
            'editing' => $this->userisediting,
        ];

        if ($section->section != 0) {
            // Only in the non-general sections.
            if (!$section->visible) {
                $sectioncontext['sectionstyle'] = 'hidden';
            } else if ($section->section == $this->currentsection) {
                $sectioncontext['sectionstyle'] = 'current';
            }
        }

        if (empty($this->tcsettings)) {
            $this->tcsettings = $this->courseformat->get_settings();
        }

        if (($section->section != 0) && (!$onsectionpage)) {
            if ($this->tcsettings['layoutcolumnorientation'] == 3) { // Dynamic column layout.
                $sectioncontext['columnclass'] = $this->get_column_class('D');
            } else if ((!$this->userisediting) && ($this->tcsettings['layoutcolumnorientation'] == 2)) {
                 // User is not editing and horizontal column layout.
                if ($this->formatresponsive) {
                    $sectioncontext['columnwidth'] = $this->tccolumnwidth;
                } else {
                    $sectioncontext['columnclass'] = $this->get_column_class($this->tcsettings['layoutcolumns']);
                }
            }
        }

        if ((($this->mobiletheme === false) && ($this->tablettheme === false)) || ($this->userisediting)) {
            $sectioncontext['nomtore'] = true;
            $sectioncontext['leftcontent'] = $this->section_left_content($section, $course, $onsectionpage);
            $sectioncontext['rightcontent'] = $this->section_right_content($section, $course, $onsectionpage);
        }
        if ($section->section != 0) {
            $sectioncontext['contentaria'] = true;
        }
        $sectioncontext['sectionavailability'] = $this->section_availability($section);
        $sectioncontext['sectionvisibility'] = $this->add_section_visibility_data($sectioncontext, $section, $context, false);

        if (($onsectionpage == false) && ($section->section != 0)) {
            $sectioncontext['sectionpage'] = false;
            $this->toggle_icon_set($sectioncontext);
            $sectioncontext['toggleiconsize'] = $this->tctoggleiconsize;

            $sectioncontext['toggleopen'] = !empty($toggle);

            if ($this->userisediting) {
                $title = $this->section_title($section, $course);
            } else {
                $title = $this->courseformat->get_topcoll_section_name($course, $section, true);
            }
            $sectioncontext['heading'] = $this->section_heading($section, $title, 'sectionname');
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
            $sectioncontext['heading'] = $this->section_heading($section, $title, $headingclass);
            $sectioncontext['summary'] = $this->format_summary_text($section);
        }

        if ($this->userisediting && has_capability('moodle/course:update', $context)) {
            $sectioncontext['usereditingicon'] = $this->output->pix_icon('t/edit', get_string('edit'));
            $sectioncontext['usereditingurl'] = new url(
                '/course/editsection.php',
                ['id' => $section->id, 'sr' => $sectionreturn]
            );
        }

        $sectioncontext['cscml'] = $this->course_section_cmlist($section);
        if ($this->courseformat->show_editor()) {
            $sectioncontext['cscml'] .= $this->course_section_add_cm_control($course, $section->section, $sectionreturn);
        }

        return $this->render_from_template('format_topcoll/section', $sectioncontext);
    }

    /**
     * Add the section visibility information to the data structure.
     *
     * @param array $data context for the template
     * @param section_info $section The section.
     * @param context_course $coursecontext The course context.
     * @param bool $isstealth If stealth section.
     * @return bool If data
     */
    protected function add_section_visibility_data(array &$data, $section, $coursecontext, $isstealth): bool {
        global $USER;
        $result = false;
        // Check if it is a stealth section (orphaned).
        if ($isstealth) {
            $data['isstealth'] = true;
            $data['ishidden'] = true;
            $result = true;
        }
        if (!$section->visible) {
            $data['ishidden'] = true;
            $data['notavailable'] = true;
            if (has_capability('moodle/course:viewhiddensections', $coursecontext, $USER)) {
                $data['hiddenfromstudents'] = true;
                $data['notavailable'] = false;
                $result = true;
            }
        }

        /* @var \core_courseformat\output\local\content\section\visibility $visibility By default the visibility class used
         * here but can be overriden by any course format */
        $visibility = new $this->visibilityclass($this->courseformat, $section);
        $data['visibility'] = $visibility->export_for_template($this);

        return $result;
    }

    /**
     * Section heading.
     */
    protected function section_heading($section, $title, $classes = '') {
        $attributes = [
            'data-for' => 'section_title',
            'data-id' => $section->id,
            'data-number' => $section->section,
            'id' => "sectionid-{$section->id}-title",
        ];
        if (!empty($classes)) {
            $attributes['class'] = $classes;
        }

        return html_writer::tag('h3', $title, $attributes);
    }

    /**
     * Calculate the icon to use.
     *
     * @param array $sectioncontext The section context for the template.
     */
    protected function toggle_icon_set(&$sectioncontext) {
        if ($this->tcsettings['toggleiconset'] == 'tif') {
            $tifcontext = [
                "tifcc" => $this->tcsettings['toggleiconfontclosed'],
                "tifoc" => $this->tcsettings['toggleiconfontopen'],
            ];
            switch ($this->tcsettings['toggleiconposition']) {
                case 2:
                    $sectioncontext['tifpleft'] = false;
                    break;
                default:
                    $sectioncontext['tifpleft'] = true;
            }
            $sectioncontext['tif'] = $this->render_from_template('format_topcoll/tif', $tifcontext);
        } else {
            switch ($this->tcsettings['toggleiconposition']) {
                case 2:
                    $sectioncontext['toggleiconposition'] = 'end';
                    break;
                default:
                    $sectioncontext['toggleiconposition'] = 'start';
            }
            $sectioncontext['toggleiconset'] = $this->tcsettings['toggleiconset'];
        }
    }

    /**
     * Generate the stealth section.
     *
     * @param stdClass $section The course_section entry from DB.
     * @param stdClass $course The course entry from DB.
     * @return string HTML to output.
     */
    protected function stealth_section($section, $course) {
        $stealthsectioncontext = [
            'cscml' => $this->course_section_cmlist($section),
            'heading' => $this->section_heading(
                $section,
                get_string('orphanedactivitiesinsectionno', '', $section->section),
                'section-title'
            ),
            'rightcontent' => $this->section_right_content($section, $course, false),
            'rtl' => $this->rtl,
            'sectionid' => $section->id,
            'sectionno' => $section->section,
        ];

        if ($this->tcsettings['layoutcolumnorientation'] == 3) { // Dynamic column layout.
            $stealthsectioncontext['columnclass'] = $this->get_column_class('D');
        } else if ($this->tcsettings['layoutcolumnorientation'] == 2) { // Horizontal column layout.
            if ($this->formatresponsive) {
                $stealthsectioncontext['columnwidth'] = $this->tccolumnwidth;
            } else {
                $stealthsectioncontext['columnclass'] = $this->get_column_class($this->tcsettings['layoutcolumns']);
            }
        }

        $context = context_course::instance($course->id);
        $stealthsectioncontext['sectionvisibility'] = $this->add_section_visibility_data(
            $stealthsectioncontext,
            $section,
            $context,
            true
        );

        if ($this->courseformat->show_editor()) {
            $stealthsectioncontext['cmcontrols'] =
                $this->course_section_add_cm_control($course, $section->section, $section->section);
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
        $sectionhiddencontext = [
            'sectionavailability' => $this->section_availability($section),
            'sectionno' => $section->section,
            'sectionid' => $section->id,
        ];
        $course = $this->courseformat->get_course();

        if ($this->tcsettings['layoutcolumnorientation'] == 3) { // Dynamic column layout.
            $sectionhiddencontext['columnclass'] = $this->get_column_class('D');
        } else if ($this->tcsettings['layoutcolumnorientation'] == 2) { // Horizontal column layout.
            if ($this->formatresponsive) {
                $sectionhiddencontext['columnwidth'] = $this->tccolumnwidth;
            } else {
                $sectionhiddencontext['columnclass'] = $this->get_column_class($this->tcsettings['layoutcolumns']);
            }
        }

        $title = $this->section_title_without_link($section, $course);
        if ((($this->mobiletheme === false) && ($this->tablettheme === false)) || ($this->userisediting)) {
            $sectionhiddencontext['nomtore'] = true;
            $sectionhiddencontext['rtl'] = $this->rtl;
            $sectionhiddencontext['leftcontent'] = $this->section_left_content($section, $course, false);
            $sectionhiddencontext['rightcontent'] = $this->section_right_content($section, $course, false, true);
            $sectionhiddencontext['heading'] = $this->section_heading($section, $title, 'section-title');
            $sectionhiddencontext['toggleiconsize'] = $this->tctoggleiconsize;
        } else {
            $sectionhiddencontext['title'] = $title;
        }

        return $this->render_from_template('format_topcoll/sectionhidden', $sectionhiddencontext);
    }

    /**
     * Output the html for a single section page.
     *
     * @param stdClass $course The course from the format_topcoll class.
     * @param int $displaysection The section number in the course which is being displayed.
     */
    public function single_section_page($displaysection) {
        $course = $this->course;
        $modinfo = get_fast_modinfo($course);

        // Can we view the section in question?
        if (!($thissection = $modinfo->get_section_info($displaysection))) {
            /* This section doesn't exist or is not available for the user.
               We actually already check this in course/view.php but just in case exit from this function as well. */
            throw new moodle_exception(
                'unknowncoursesection',
                'error',
                course_get_url($course),
                format_string($course->fullname. ' - id='.$course->id)
            );
        }

        if (!$thissection->uservisible) {
            // Can't view this section.
            return;
        }

        $maincoursepage = get_string('maincoursepage', 'format_topcoll');

        $singlesectioncontext = [
            'maincoursepageicon' => $this->output->pix_icon('t/less', $maincoursepage),
            'maincoursepagestr' => $maincoursepage,
            'maincoursepageurl' => new url('/course/view.php', ['id' => $course->id]),
            'sectionnavlinks' => $this->section_nav_links(),
            // Title with section navigation links and jump to menu.
            'sectionnavselection' => $this->section_nav_selection($course, null, $displaysection),
            'thissection' => $this->topcoll_section($thissection, $course, true, $displaysection),
        ];

        $sectionzero = $modinfo->get_section_info(0);
        if ($this->courseformat->is_section_visible($sectionzero)) {
            $singlesectioncontext['sectionzero'] = $this->topcoll_section($sectionzero, $course, true, $displaysection);
        }

        // Title attributes.
        $classes = 'sectionname';
        if (!$thissection->visible) {
            $classes .= ' dimmed_text';
        }
        $sectionname = $this->section_title_without_link($thissection, $course);
        $singlesectioncontext['sectiontitle'] = $this->section_heading($thissection, $sectionname, $classes);
        $singlesectioncontext['bulkedittools'] = $this->bulkedittools();

        return $this->single_section_styles().$this->render_from_template('format_topcoll/singlesection', $singlesectioncontext);
    }

    /**
     * Output the html for a multiple section page.
     *
     * @param stdClass $course The course from the format_topcoll class.
     */
    public function multiple_section_page() {
        $course = $this->course;
        $this->set_user_preferences();
        $content = $this->course_styles();

        $modinfo = get_fast_modinfo($course);
        if (empty($this->tcsettings)) {
            $this->tcsettings = $this->courseformat->get_settings();
        }

        $context = context_course::instance($course->id);

        // Now the list of sections..
        if ($this->formatresponsive) {
            $this->tccolumnwidth = 100; // Reset to default.
        }
        $content .= $this->start_section_list();

        $shownsectionsinfo = $this->courseformat->get_shown_sections();

        // General section if non-empty.
        if (!empty($shownsectionsinfo['sectionzero'])) {
            $content .= $this->topcoll_section($shownsectionsinfo['sectionzero'], $course, false);
        }

        $shownonetoggle = false;
        if ($shownsectionsinfo['coursenumsections'] > 0) {
            $sectionoutput = '';
            $toggledsections = [];

            $numshownsections = count($shownsectionsinfo['sectionsdisplayed']);
            if ($numshownsections < $this->tcsettings['layoutcolumns']) {
                $this->tcsettings['layoutcolumns'] = $numshownsections;  // Help to ensure a reasonable display.
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
                $sectionoutput .= html_writer::start_tag('div', ['class' => $this->get_row_class()]);
            }

            $sectionoutput .= $this->start_toggle_section_list();

            $extrasectioninfo = [];
            foreach ($shownsectionsinfo['sectionsdisplayed'] as $displayedsection) {
                $extrasectioninfo[$displayedsection->id] = new stdClass();

                if (in_array($displayedsection->id, $shownsectionsinfo['hiddensectionids'])) {
                    // Shown hidden section - to state that it is hidden.
                    $extrasectioninfo[$displayedsection->id]->ishidden = true;
                } else {
                    if ((!empty($shownsectionsinfo['currentsectionno'])) &&
                        ($shownsectionsinfo['currentsectionno'] == $displayedsection->section)) {
                        $this->currentsection = $shownsectionsinfo['currentsectionno'];
                        $extrasectioninfo[$displayedsection->id]->toggle = true; // Open current section regardless of toggle state.
                        $this->togglelib->set_toggle_state($displayedsection->section, true);
                    } else {
                        $extrasectioninfo[$displayedsection->id]->toggle =
                            $this->togglelib->get_toggle_state($displayedsection->section);
                    }
                    $extrasectioninfo[$displayedsection->id]->isshown = true;
                }
            }

            $breaking = false; // Once the first section is shown we can decide if we break on another column.
            $canbreak = (
                (!$this->userisediting) &&
                ($this->tcsettings['layoutcolumns'] > 1) &&
                ($this->tcsettings['layoutcolumnorientation'] != 3) // Dynamic columns.
            );
            $columncount = 1;
            $breakpoint = 0;
            $shownsectioncount = 0;
            if (($this->tcsettings['onesection'] == 2) && (!empty($this->currentsection))) {
                $shownonetoggle = $this->currentsection; // One toggle open only, so as we have a current section it will be it.
            }

            foreach ($shownsectionsinfo['sectionsdisplayed'] as $displayedsection) {
                $shownsectioncount++;

                if (!empty($extrasectioninfo[$displayedsection->id]->ishidden)) {
                    $sectionoutput .= $this->section_hidden($displayedsection);
                } else if (!empty($displayedsection->issummary)) {
                    $sectionoutput .= $this->section_summary($displayedsection, $course, null);
                } else if (!empty($extrasectioninfo[$displayedsection->id]->isshown)) {
                    if ($this->tcsettings['onesection'] == 2) {
                        if ($extrasectioninfo[$displayedsection->id]->toggle) {
                            if (!empty($shownonetoggle)) {
                                // Make sure the current section is not closed if set above.
                                if ($shownonetoggle != $displayedsection->section) {
                                    // There is already a toggle open so others need to be closed.
                                    $displayedsection->toggle = false;
                                    $this->togglelib->set_toggle_state($displayedsection->section, false);
                                }
                            } else {
                                // No open toggle, so as this is the first, it can be the one.
                                $shownonetoggle = $displayedsection->section;
                            }
                        }
                    }
                    $sectionoutput .= $this->topcoll_section($displayedsection, $course, false,
                            null, $extrasectioninfo[$displayedsection->id]->toggle);
                    $toggledsections[] = $displayedsection->section;
                }

                // Check for breaking up the structure with rows if more than one column and when we output all of the sections.
                if ($canbreak === true) {
                    // Only break in non-mobile themes or using a responsive theme.
                    if ((!$this->formatresponsive) || ($this->mobiletheme === false)) {
                        if ($this->tcsettings['layoutcolumnorientation'] == 1) {  // Vertical mode.
                            if ($breaking == false) {
                                $breaking = true;
                                // Divide the number of sections by the number of columns.
                                $breakpoint = $numshownsections / $this->tcsettings['layoutcolumns'];
                            }

                            if (
                                ($breaking == true) && ($shownsectioncount >= $breakpoint) &&
                                ($columncount < $this->tcsettings['layoutcolumns'])
                            ) {
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
            }

            if ($shownsectionsinfo['coursenumsections'] > 1) {
                if ($this->tcsettings['toggleallenabled'] == 2) {
                    if ($this->tcsettings['onesection'] == 1) {
                        // Collapsed Topics all toggles.
                        $content .= $this->toggle_all($toggledsections);
                    }
                }
                if ($this->tcsettings['displayinstructions'] == 2) {
                    // Collapsed Topics instructions.
                    $content .= $this->display_instructions();
                }
            }
            $content .= $sectionoutput;
        }

        $changenumsections = '';
        if ($this->userisediting && has_capability('moodle/course:update', $context)) {
            $changenumsections = $this->change_number_sections($course, 0);
            // Print stealth sections if present.
            foreach ($modinfo->get_section_info_all() as $thissection) {
                if (!empty($thissection->component)) {
                    // Delegated section.
                    continue;
                }
                $sectionno = $thissection->section;
                if ($sectionno <= $shownsectionsinfo['coursenumsections'] || empty($modinfo->sections[$sectionno])) {
                    // This is not stealth section or it is empty.
                    continue;
                }
                $content .= $this->stealth_section($thissection, $course);
            }
        }
        $content .= $this->end_section_list();
        if ($shownsectionsinfo['coursenumsections'] > 0) {
            if ((!$this->formatresponsive) && ($this->tcsettings['layoutcolumnorientation'] == 1)) { // Vertical columns.
                $content .= html_writer::end_tag('div');
            }
        }

        $content .= $changenumsections;
        $content .= $this->bulkedittools();

        // Now initialise the JavaScript.
        $toggles = $this->togglelib->get_toggles();
        $onetopic = ($this->tcsettings['onesection'] == 2) ? 'true' : 'false';
        $onetopictoggle = (empty($shownonetoggle)) ? 'false' : $shownonetoggle;
        $defaulttogglepersistence = ($this->defaulttogglepersistence == 1) ? 'true' : 'false';
        $content .= '<span id="tcdata" class="d-none"'.
            ' data-onetopic="'.$onetopic.'"'.
            ' data-onetopictoggle="'.$onetopictoggle.'"'.
            ' data-defaulttogglepersistence="'.$defaulttogglepersistence.'"'.
            '></span>';

        /* Make sure the database has the correct state of the toggles if changed by the code.
           This ensures that a no-change page reload is correct. */
        set_user_preference(togglelib::TOPCOLL_TOGGLE.'_' . $course->id, $toggles);

        return $content;
    }

    /**
     * Displays the toggle all functionality.
     * @param array $toggledsections Array of section id's that are toggled.
     *
     * @return string HTML to output.
     */
    protected function toggle_all($toggledsections) {
        $sct = $this->courseformat->get_structure_collection_type();
        $toggleallcontext = [
            'rtl' => $this->rtl,
            'sctcloseall' => get_string('sctcloseall', 'format_topcoll', $sct),
            'sctopenall' => get_string('sctopenall', 'format_topcoll', $sct),
            'toggleallhover' => ($this->tcsettings['toggleallhover'] == 2),
            'tctoggleiconsize' => $this->tctoggleiconsize,
        ];
        $this->toggle_icon_set($toggleallcontext);

        if ((($this->mobiletheme === false) && ($this->tablettheme === false)) || ($this->userisediting)) {
            $toggleallcontext['spacer'] = $this->output->spacer();
        }

        $ariacontrolselements = [];
        foreach ($toggledsections as $toggledsection) {
            $ariacontrolselements[] = 'toggledsection-' . $toggledsection;
        }
        $toggleallcontext['ariacontrols'] = implode(' ', $ariacontrolselements);

        return $this->render_from_template('format_topcoll/toggleall', $toggleallcontext);
    }

    /**
     * Displays the instructions functionality.
     * @return string HTML to output.
     */
    protected function display_instructions() {
        $displayinstructionscontext = [
            'rtl' => $this->rtl,
        ];

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

        $coursestylescontext = [];
        $coursestylescontext['togglebackground'] = toolbox::hex2rgba(
            $this->tcsettings['togglebackgroundcolour'],
            $this->tcsettings['togglebackgroundopacity']
        );
        $coursestylescontext['toggleforegroundcolour'] = toolbox::hex2rgba(
            $this->tcsettings['toggleforegroundcolour'],
            $this->tcsettings['toggleforegroundopacity']
        );
        $coursestylescontext['tif'] = ($this->tcsettings['toggleiconset'] == 'tif');
        if ($coursestylescontext['tif']) {
            switch ($this->tcsettings['togglealignment']) {
                case 1:
                    $coursestylescontext['tiftogglealignment'] = 'start';
                    break;
                case 3:
                    $coursestylescontext['tiftogglealignment'] = 'end';
                    break;
                default:
                    $coursestylescontext['tiftogglealignment'] = 'center';
            }
        }
        switch ($this->tcsettings['togglealignment']) {
            case 1:
                $coursestylescontext['togglealignment'] = ($this->rtl) ? 'right' : 'left';
                break;
            case 3:
                $coursestylescontext['togglealignment'] = ($this->rtl) ? 'left' : 'right';
                break;
            default:
                $coursestylescontext['togglealignment'] = 'center';
        }
        if (!$coursestylescontext['tif']) {
            switch ($this->tcsettings['toggleiconposition']) {
                case 2:
                    $coursestylescontext['toggleiconposition'] = ($this->rtl) ? 'left' : 'right';
                    break;
                default:
                    $coursestylescontext['toggleiconposition'] = ($this->rtl) ? 'right' : 'left';
            }
        }
        $coursestylescontext['toggleforegroundhovercolour'] = toolbox::hex2rgba(
            $this->tcsettings['toggleforegroundhovercolour'],
            $this->tcsettings['toggleforegroundhoveropacity']
        );
        $coursestylescontext['togglebackgroundhovercolour'] = toolbox::hex2rgba(
            $this->tcsettings['togglebackgroundhovercolour'],
            $this->tcsettings['togglebackgroundhoveropacity']
        );

        $coursestylescontext['topcollsidewidthval'] = $this->calc_topcollsidewidth();

        // Make room for single section icon.
        if (!$this->userisediting) {
            $coursestylescontext['topcollsidewidthvalicons'] = $coursestylescontext['topcollsidewidthval'];
        }

        // Establish horizontal unordered list for horizontal columns.
        if (($this->get_format_responsive()) && ($this->tcsettings['layoutcolumnorientation'] == 2)) {
            $coursestylescontext['hulhc'] = true;
        }

        // Site wide configuration Site Administration -> Plugins -> Course formats -> Collapsed Topics.
        $coursestylescontext['tcborderradiustl'] = clean_param(
            get_config('format_topcoll', 'defaulttoggleborderradiustl'),
            PARAM_TEXT
        );
        $coursestylescontext['tcborderradiustr'] = clean_param(
            get_config('format_topcoll', 'defaulttoggleborderradiustr'),
            PARAM_TEXT
        );
        $coursestylescontext['tcborderradiusbr'] = clean_param(
            get_config('format_topcoll', 'defaulttoggleborderradiusbr'),
            PARAM_TEXT
        );
        $coursestylescontext['tcborderradiusbl'] = clean_param(
            get_config('format_topcoll', 'defaulttoggleborderradiusbl'),
            PARAM_TEXT
        );

        return $this->render_from_template('format_topcoll/coursestyles', $coursestylescontext);
    }

    /**
     * The single section styles.
     * @return string HTML to output.
     */
    protected function single_section_styles() {
        if (empty($this->tcsettings)) {
            $this->tcsettings = $this->courseformat->get_settings();
        }

        $singlesectionstylescontext = [];
        $singlesectionstylescontext['topcollsidewidthval'] = $this->calc_topcollsidewidth();

        return $this->render_from_template('format_topcoll/singlesectionstyles', $singlesectionstylescontext);
    }

    /**
     * Calculate the side width.
     * @return string Side width.
     */
    protected function calc_topcollsidewidth() {
        if (empty($this->tcsettings)) {
            $this->tcsettings = $this->courseformat->get_settings();
        }
        if (!$this->userisediting) {
            if (($this->tcsettings['layoutelement'] != 4) && ($this->tcsettings['layoutelement'] != 7)) {
                $topcollsidewidth = get_string('topcollsidewidthlang', 'format_topcoll');
                $topcollsidewidthdelim = strpos($topcollsidewidth, '-');
                $topcollsidewidthlang = strcmp(substr($topcollsidewidth, 0, $topcollsidewidthdelim), current_language());
                if ($topcollsidewidthlang != 0) {
                    // Could have defaulted to 'en', so check.
                    $topcollsidewidthlang = strcmp(substr($topcollsidewidth, 0, $topcollsidewidthdelim), 'en');
                    if ($topcollsidewidthlang == 0) {
                        // We have defaulted to 'en', so have a default.
                        $topcollsidewidthval = '42px';
                    }
                }
                if (empty($topcollsidewidthval)) {
                    // Dynamically changing widths with language.
                    if ((($this->mobiletheme == false) &&
                        ($this->tablettheme == false)) &&
                        ($topcollsidewidthlang == 0)
                    ) {
                        $topcollsidewidthval = substr($topcollsidewidth, $topcollsidewidthdelim + 1);
                    } else {
                        // Default.
                        $topcollsidewidthval = '28px';
                    }
                }
            } else {
                $topcollsidewidthval = '28px';
            }
        } else {
            // Default.
            $topcollsidewidthval = '40px';
        }
        return $topcollsidewidthval;
    }

    /**
     * Set the user preferences.
     */
    protected function set_user_preferences() {
        $this->defaultuserpreference = clean_param(get_config('format_topcoll', 'defaultuserpreference'), PARAM_INT);
        $this->defaulttogglepersistence = clean_param(get_config('format_topcoll', 'defaulttogglepersistence'), PARAM_INT);

        if ($this->defaulttogglepersistence == 1) {
            global $USER;
            $USER->topcoll_user_pref[togglelib::TOPCOLL_TOGGLE.'_' . $this->course->id] = PARAM_RAW;
            $userpreference = get_user_preferences(togglelib::TOPCOLL_TOGGLE.'_' . $this->course->id);
        } else {
            $userpreference = null;
        }

        $coursenumsections = $this->courseformat->get_last_section_number_without_delegated();
        if ($userpreference != null) {
            // Check we have enough digits for the number of toggles in case this has increased.
            $numdigits = togglelib::get_required_digits($coursenumsections);
            $totdigits = strlen($userpreference);
            if ($numdigits > $totdigits) {
                if ($this->defaultuserpreference == 0) {
                    $dchar = togglelib::get_min_digit();
                } else {
                    $dchar = togglelib::get_max_digit();
                }
                for ($i = $totdigits; $i < $numdigits; $i++) {
                    $userpreference .= $dchar;
                }
            } else if ($numdigits < $totdigits) {
                // Shorten to save space.
                $userpreference = substr($userpreference, 0, $numdigits);
            }
            $this->togglelib->set_toggles($userpreference);
        } else {
            $numdigits = togglelib::get_required_digits($coursenumsections);
            if ($this->defaultuserpreference == 0) {
                $dchar = togglelib::get_min_digit();
            } else {
                $dchar = togglelib::get_max_digit();
            }
            $userpreference = '';
            for ($i = 0; $i < $numdigits; $i++) {
                $userpreference .= $dchar;
            }
            $this->togglelib->set_toggles($userpreference);
        }
    }

    /**
     * Get row class.
     *
     * @return string.
     */
    protected function get_row_class() {
        return 'row';
    }

    /**
     * Get column class.
     *
     * @return string.
     */
    protected function get_column_class($columns) {
        static $colclasses = [
            1 => 'col-sm-12',
            2 => 'col-sm-6',
            3 => 'col-md-4',
            4 => 'col-lg-3',
            'D' => 'col-sm-12 col-md-12 col-lg-12 col-xl-6', ];

        return $colclasses[$columns];
    }

    /**
     * Is the format responsive?
     *
     * @return bool.
     */
    public function get_format_responsive() {
        return $this->formatresponsive;
    }
}
