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
use core\url;
use stdClass;

/**
 * Class to render a section activity list.
 *
 * @package   format_topcoll
 * @copyright &copy; 2022-onwards G J Barnard.
 * @license   https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class cmlist extends \core_courseformat\output\local\content\section\cmlist {
    /**
     * Get the name of the template to use for this templatable.
     *
     * @param renderer_base $renderer The renderer requesting the template name
     * @return string
     */
    public function get_template_name(renderer_base $renderer): string {
        return 'format_topcoll/local/content/section/cmlist';
    }

    /**
     * Export this data so it can be used as the context for a mustache template.
     *
     * @param renderer_base $output typically, the renderer that's calling this function
     * @return array data context for a mustache template
     */
    public function export_for_template(renderer_base $output): stdClass {
        global $PAGE;
        $data = parent::export_for_template($output);
        $data->editing = $PAGE->user_is_editing();

        // Section information for the 'activitychooserbuttonactivity'.
        $data->num = $this->section->section ?? '0';
        $data->sectionreturn = $this->format->get_sectionnum();

        $tcsettings = $this->format->get_settings();
        $data->flexiblemodules = ($tcsettings['flexiblemodules'] == 2);

        return $data;
    }
}
