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
 * Class to render a course add section buttons.
 *
 * @package    format_topcoll
 * @copyright  &copy; 2024-onwards G J Barnard in respect to modifications of standard topics format.
 * @author     G J Barnard - {@link https://moodle.org/user/profile.php?id=442195}
 * @copyright  2020 Ferran Recio <ferran@moodle.com>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace format_topcoll\output\courseformat\content;
use stdClass;

/**
 * Collapsed Topics
 *
 * Class to render a course add section buttons.
 *
 * @package    format_topcoll
 * @copyright  &copy; 2024-onwards G J Barnard in respect to modifications of standard topics format.
 * @author     G J Barnard - {@link https://moodle.org/user/profile.php?id=442195}
 * @copyright  2020 Ferran Recio <ferran@moodle.com>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class addsection extends \core_courseformat\output\local\content\addsection {
    /**
     * Export this data so it can be used as the context for a mustache template.
     *
     * @param \renderer_base $output typically, the renderer that's calling this function
     * @return stdClass data context for a mustache template
     */
    public function export_for_template(\renderer_base $output): stdClass {

        // If no editor must be displayed, just return an empty structure.
        if (!$this->format->show_editor(['moodle/course:update'])) {
            return new stdClass();
        }

        $format = $this->format;
        $course = $format->get_course();
        $options = $format->get_format_options();

        $lastsection = $format->get_last_section_number_without_deligated(); // Difference from core.
        $maxsections = $format->get_max_sections();

        // Component based formats handle add section button in the frontend.
        $show = ($lastsection < $maxsections) || $format->supports_components();

        $supportsnumsections = array_key_exists('numsections', $options);
        if ($supportsnumsections) {
            $data = $this->get_num_sections_data($output, $lastsection, $maxsections);
        } else if (course_get_format($course)->uses_sections() && $show) {
            $data = $this->get_add_section_data($output, $lastsection, $maxsections);
        }

        if (count((array)$data)) {
            $data->showaddsection = true;
        }

        return $data;
    }

    /**
     * Get the name of the template to use for this templatable.
     *
     * @param renderer_base $renderer The renderer requesting the template name.
     * @return string.
     */
    public function get_template_name(\renderer_base $renderer): string {
        return 'format_topcoll/local/content/addsection';
    }
}
