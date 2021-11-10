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

    /* New / overridden methods added for activity styling below.
       Adapted from snap theme by Moodleroooms and Adaptable theme.*/
    /**
     * Renders HTML to display one course module in a course section
     *
     * This includes link, content, availability, completion info and additional information
     * that module type wants to display (i.e. number of unread forum posts)
     *
     * This function calls:
     * {@link core_course_renderer::course_section_cm_name()}
     * {@link core_course_renderer::course_section_cm_text()}
     * {@link core_course_renderer::course_section_cm_availability()}
     * {@link core_course_renderer::course_section_cm_completion()}
     * {@link course_get_cm_edit_actions()}
     * {@link core_course_renderer::course_section_cm_edit_actions()}
     *
     * @param stdClass $course
     * @param completion_info $completioninfo
     * @param cm_info $mod
     * @param int|null $sectionreturn
     * @param array $displayoptions
     * @return string
     */
    public function course_section_cm($course, &$completioninfo, cm_info $mod, $sectionreturn, $displayoptions = array()) {
        if ($this->page->user_is_editing()) { // Don't display the activity meta when editing so that drag and drop is not broken.
            return parent::course_section_cm($course, $completioninfo, $mod, $sectionreturn, $displayoptions);
        }

        /* We return empty string (because course module will not be displayed at all) if:
           1) The activity is not visible to users
              and
           2) The 'availableinfo' is empty, i.e. the activity was
              hidden in a way that leaves no info, such as using the
              eye icon. */
        if (!$mod->is_visible_on_course_page()) {
            return '';
        }

        global $USER;
        $sectioncmcontext = array(
            'availability' => $this->course_section_cm_availability($mod, $displayoptions),
            'contentpart' => $this->course_section_cm_text($mod, $displayoptions),
            'hasurl' => (empty($mod->url))
        );

        $sectioncmcontext['indent'] = 'mod-indent';
        if (!empty($mod->indent)) {
            $sectioncmcontext['indent'] .= ' mod-indent-'.$mod->indent;
            if ($mod->indent > 15) {
                $sectioncmcontext['indent'] .= ' mod-indent-huge';
            }
        }

        // Display the link to the module (or do nothing if module has no url).
        $cmname = $this->course_section_cm_name($mod, $displayoptions);
        if (!empty($cmname)) {
            $sectioncmcontext['cmname'] = $cmname;
            $sectioncmcontext['cmnameafterlink'] = $mod->afterlink;
        }

        // Fetch completion details.
        $showcompletionconditions = $course->showcompletionconditions == COMPLETION_SHOW_CONDITIONS;
        $completiondetails = \core_completion\cm_completion_details::get_instance($mod, $USER->id, $showcompletionconditions);
        $ismanualcompletion = $completiondetails->has_completion() && !$completiondetails->is_automatic();

        // Fetch activity dates.
        $activitydates = [];
        if ($course->showactivitydates) {
            $activitydates = \core\activity_dates::get_dates_for_module($mod, $USER->id);
        }

        /* Show the activity information if:
           - The course's showcompletionconditions setting is enabled; or
           - The activity tracks completion manually; or
           - There are activity dates to be shown. */
        if ($showcompletionconditions || $ismanualcompletion || $activitydates) {
            $sectioncmcontext['activityinformation'] = $this->output->activity_information($mod, $completiondetails, $activitydates);
        }

        // Get further information.
        if (\format_topcoll\activity::activitymetaenabled()) {
            $courseformat = course_get_format($course);
            if (\format_topcoll\activity::activitymetaused($courseformat)) {
                $courseid = $mod->course;
                if (\format_topcoll\activity::maxstudentsnotexceeded($courseid)) {
                    $settingname = 'coursesectionactivityfurtherinformation'.$mod->modname;
                    $setting = get_config('format_topcoll', $settingname);
                    if ((!empty($setting)) && ($setting == 2)) {
                        $sectioncmcontext['cmmeta'] = $this->course_section_cm_get_meta($mod);
                    }
                }
            }
        }

        return $this->output->render_from_template('format_topcoll/sectioncm', $sectioncmcontext);
    }

    /**
     * Get the module meta data for a specific module.
     *
     * @param cm_info $mod The module.
     * @return string The markup.
     */
    protected function course_section_cm_get_meta(cm_info $mod) {
        $courseid = $mod->course;
        if (is_guest(context_course::instance($courseid))) {
            return '';
        }

        // If module is not visible to the user then don't bother getting meta data.
        if (!$mod->uservisible) {
            return '';
        }

        // Do we have an activity function for this module for returning meta data?
        $meta = \format_topcoll\activity::module_meta($mod);
        if ($meta == null) {
            // Can't get meta data for this module.
            return '';
        }
        $content = '';

        if ($meta->isteacher) {
            // Teacher - useful teacher meta data.
            $engagementmeta = array();

            if (!$meta->submissionnotrequired) {
                /* Below, != 0 means we would get x out of 0 submissions, so at least show something as
                   the module could now be hidden, but there is still useful information. */
                if ($meta->numparticipants != 0) {
                    $engagementmeta[] = get_string('xofy'.$meta->submitstrkey, 'format_topcoll',
                        (object) array(
                            'completed' => $meta->numsubmissions,
                            'participants' => $meta->numparticipants
                        )
                    );
                } else {
                    $engagementmeta[] = get_string('x'.$meta->submitstrkey, 'format_topcoll',
                        (object) array(
                            'completed' => $meta->numsubmissions
                        )
                    );
                }
            }

            if ($meta->numrequiregrading) {
                $engagementmeta[] = get_string('xungraded', 'format_topcoll', $meta->numrequiregrading);
            }
            if (!empty($engagementmeta)) {
                $params = array(
                    'action' => 'grading',
                    'id' => $mod->id,
                    'tsort' => 'timesubmitted',
                    'filter' => 'require_grading'
                );

                $sectioncmmetacontext = array(
                    'linkclass' => 'ct-activity-action',
                    'linkicon' => $this->output->pix_icon('docs', get_string('info')),
                    'linktext' => implode(', ', $engagementmeta),
                    'linkurl' => new moodle_url("/mod/{$mod->modname}/view.php", $params),
                    'type' => 'engagement'
                );
                $content = $this->output->render_from_template('format_topcoll/sectioncmmeta', $sectioncmmetacontext);
            }
        } else {
            // Feedback meta.
            if (!empty($meta->grade)) {
                if (in_array($mod->modname, ['quiz', 'assign'])) {
                    $url = new \moodle_url('/mod/'.$mod->modname.'/view.php?id='.$mod->id);
                } else {
                    $url = new \moodle_url('/grade/report/user/index.php', ['id' => $courseid]);
                }

                $sectioncmmetacontext = array(
                    'linkicon' => $this->output->pix_icon('t/message', get_string('feedback')),
                    'linktext' => get_string('feedbackavailable', 'format_topcoll'),
                    'linkurl' => $url,
                    'type' => 'feedback'
                );
                $content = $this->output->render_from_template('format_topcoll/sectioncmmeta', $sectioncmmetacontext);
            }
        }

        return $content;
    }
}
