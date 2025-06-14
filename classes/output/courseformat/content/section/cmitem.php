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
 * Contains the default section controls output class.
 *
 * @package    format_topcoll
 * @copyright  &copy; 2022-onwards G J Barnard in respect to modifications of core code.
 * @author     G J Barnard - {@link https://moodle.org/user/profile.php?id=442195}
 * @copyright  2020 Ferran Recio <ferran@moodle.com>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace format_topcoll\output\courseformat\content\section;

use core\output\renderer_base;
use stdClass;

/**
 * Class to render a section activity item.
 *
 * @package   format_topcoll
 * @copyright  &copy; 2022-onwards G J Barnard.
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class cmitem extends \core_courseformat\output\local\content\section\cmitem {
    /**
     * Get the name of the template to use for this templatable.
     *
     * @param renderer_base $renderer The renderer requesting the template name
     * @return string
     */
    public function get_template_name(renderer_base $renderer): string {
        return 'format_topcoll/local/content/section/cmitem';
    }

    /**
     * Export this data so it can be used as the context for a mustache template.
     *
     * @param renderer_base $output typically, the renderer that's calling this function
     * @return stdClass data context for a mustache template
     */
    public function export_for_template(renderer_base $output): stdClass {
        $context = parent::export_for_template($output);

        $tcsettings = $this->format->get_settings();
        if ($tcsettings['flexiblemodules'] == 2) {
            // Turn off indentation.
            $context->indent = 0;
        }

        // --- What's New? Highlight Feature ---
        // $output should be an instance of format_topcoll\output\renderer
        $main_renderer = $output; // Assuming $output is the correct renderer instance.
                                  // If not, $this->format->get_renderer($this->format->get_page()) might be needed.

        $user_last_access = null;
        if (method_exists($main_renderer, 'get_user_last_course_access')) {
             $user_last_access = $main_renderer->get_user_last_course_access();
        } else {
            // Fallback if the main_renderer isn't what we expect or method isn't available.
            // This is less ideal due to potential for repeated calls if not fetched once per page load in main_renderer.
            if (property_exists($main_renderer, 'user_last_course_access_fetched') && $main_renderer->user_last_course_access_fetched) {
                 if (property_exists($main_renderer, 'user_last_course_access')) {
                    $user_last_access = $main_renderer->user_last_course_access;
                 }
            } else {
                 // Last resort, fetch directly.
                 $user_last_access = get_user_preference('format_topcoll_last_access_' . $this->format->get_course()->id, null);
            }
        }

        $cm_timemodified = 0;
        if (isset($this->cm->timemodified)) {
            $cm_timemodified = (int)$this->cm->timemodified;
        } else if (method_exists($this->cm, 'get_course_module_record')) {
            $cm_record = $this->cm->get_course_module_record();
            if ($cm_record && isset($cm_record->timemodified)) {
                $cm_timemodified = (int)$cm_record->timemodified;
            }
        }

        // Call the public is_content_new method from the main renderer.
        if (method_exists($main_renderer, 'is_content_new')) {
            $context->isnew = $main_renderer->is_content_new($cm_timemodified, $user_last_access);
        } else {
            // Fallback simple check if main_renderer's method is not callable as expected.
            // This duplicates logic and might not be ideal if is_content_new has more complex conditions.
            if ($user_last_access === null || $user_last_access === 0) {
                $context->isnew = false;
            } else {
                $context->isnew = $cm_timemodified > $user_last_access;
            }
        }
        // --- End What's New? Highlight Feature ---

        return $context;
    }
}
