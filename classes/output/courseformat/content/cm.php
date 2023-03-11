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
 * Contains the default activity list from a section.
 *
 * @package    format_topcoll
 * @version    See the value of '$plugin->version' in version.php.
 * @copyright  &copy; 2022-onwards G J Barnard in respect to modifications of standard topics format.
 * @author     G J Barnard - {@link http://moodle.org/user/profile.php?id=442195}
 * @copyright 2020 Ferran Recio <ferran@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace format_topcoll\output\courseformat\content;

use core_courseformat\output\local\content\cm as cm_base;

/**
 * Base class to render a course module inside a course format.
 *
 * @package   core_courseformat
 * @copyright 2020 Ferran Recio <ferran@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class cm extends cm_base {

    /**
     * Export this data so it can be used as the context for a mustache template.
     *
     * @param \renderer_base $output typically, the renderer that's calling this function
     * @return stdClass data context for a mustache template
     */
    public function export_for_template(\renderer_base $output): \stdClass {
        $data = parent::export_for_template($output);

        // Get further information.
        if (\format_topcoll\activity::activitymetaenabled()) {
            $courseformat = $this->format;

            if (\format_topcoll\activity::activitymetaused($courseformat)) {
                $courseid = $this->mod->course;
                if (\format_topcoll\activity::maxstudentsnotexceeded($courseid)) {
                    $settingname = 'coursesectionactivityfurtherinformation'.$this->mod->modname;
                    $setting = get_config('format_topcoll', $settingname);
                    if ((!empty($setting)) && ($setting == 2)) {
                        $data->cmmeta = $this->course_section_cm_get_meta($this->mod);
                    }
                }
            }
        }

        return $data;
    }

    /**
     * Get the module meta data for a specific module.
     *
     * @param cm_info $mod The module.
     * @return string The markup.
     */
    protected function course_section_cm_get_meta(\cm_info $mod) {
        $courseid = $mod->course;
        if (is_guest(\context_course::instance($courseid))) {
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

        global $OUTPUT;
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
                    'id' => $mod->id
                );
                $file = 'view';

                switch ($mod->modname) {
                    case 'assign':
                        $params['action'] = 'grading';
                    break;
                    case 'quiz':
                        $file = 'report';
                        $params['mode'] = 'overview';
                    break;
                }

                $sectioncmmetacontext = array(
                    'linkclass' => 'ct-activity-action',
                    'linkicon' => $OUTPUT->pix_icon('docs', get_string('info')),
                    'linktext' => implode(', ', $engagementmeta),
                    'linkurl' => new \moodle_url("/mod/{$mod->modname}/{$file}.php", $params),
                    'type' => 'engagement'
                );
                $content = $OUTPUT->render_from_template('format_topcoll/sectioncmmeta', $sectioncmmetacontext);
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
                    'linkicon' => $OUTPUT->pix_icon('t/message', get_string('feedback')),
                    'linktext' => get_string('feedbackavailable', 'format_topcoll'),
                    'linkurl' => $url,
                    'type' => 'feedback'
                );
                $content = $OUTPUT->render_from_template('format_topcoll/sectioncmmeta', $sectioncmmetacontext);
            }
        }

        return $content;
    }
}
