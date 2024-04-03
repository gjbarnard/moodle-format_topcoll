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
 * A trait to contain all of the deprecated methods, but implemented using the new components.  Thus easing migration.
 * Note: 'courseformat' property is set in the using class constructor.
 *
 * @package    format_topcoll
 * @copyright  &copy; 2021-onwards G J Barnard in respect to modifications of core code.
 * @author     G J Barnard - {@link https://moodle.org/user/profile.php?id=442195}
 * @link       https://docs.moodle.org/en/Collapsed_Topics_course_format
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace format_topcoll\output;

/**
 * Migration trait.
 */
trait format_renderer_migration_toolbox {
    /**
     * Generate a summary of the activities in a section
     *
     * @deprecated since 4.0 MDL-72656 - use core_course output components instead.
     *
     * This element is now a section_format output component and it is displayed using
     * mustache templates instead of a renderer method.
     *
     * @param stdClass $section The course_section entry from DB
     * @param stdClass $course the course record from DB
     * @param array    $mods (argument not used)
     * @return string HTML to output.
     */
    protected function section_activity_summary($section, $course, $mods) {
        $widgetclass = $this->courseformat->get_output_classname('content\\section\\cmsummary');
        $widget = new $widgetclass($this->courseformat, $section);
        return $this->render($widget);
    }

    /**
     * Displays availability information for the section (hidden, not available unless, etc.)
     *
     * @deprecated since 4.0 MDL-72656 - use core_course output components instead.
     *
     * This element is now a section_format output component and it is displayed using
     * mustache templates instead of a renderer method.
     *
     * @param section_info $section
     * @return string
     */
    public function section_availability($section) {
        // Todo: Update to a template!
        $widgetclass = $this->courseformat->get_output_classname('content\\section\\availability');
        $widget = new $widgetclass($this->courseformat, $section);
        return $this->render($widget);
    }

    /**
     * Generate html for a section summary text
     *
     * @deprecated since 4.0 MDL-72656 - use core_course output components instead.
     *
     * @param stdClass $section The course_section entry from DB
     * @return string HTML to output.
     */
    protected function format_summary_text($section) {
        if (!($section instanceof section_info)) {
            $modinfo = $this->courseformat->get_modinfo();
            $section = $modinfo->get_section_info($section->section);
        }
        $summaryclass = $this->courseformat->get_output_classname('content\\section\\summary');
        $summary = new $summaryclass($this->courseformat, $section);
        return $summary->format_summary_text();
    }

    /**
     * Section course module list.
     *
     * @param stdClass $section The course_section entry from DB
     * @return string HTML to output.
     */
    protected function course_section_cmlist($section) {
        $cmlistclass = $this->courseformat->get_output_classname('content\\section\\cmlist');
        return $this->render(new $cmlistclass($this->courseformat, $section));
    }

    /**
     * Section navigation links.
     *
     * @return string HTML to output.
     */
    protected function section_nav_links() {
        $sectionnavigationclass = $this->courseformat->get_output_classname('content\\sectionnavigation');
        $sectionnavigation = new $sectionnavigationclass($this->courseformat, $this->courseformat->get_section_number());
        return $this->render($sectionnavigation);
    }

    /**
     * Generate the html for the 'Jump to' menu on a single section page.
     *
     * This element is now a core_courseformat\output\content\section output component and it is displayed using
     * mustache templates instead of a renderer method.
     *
     * @param stdClass $course The course entry from DB
     * @param array $sections The course_sections entries from the DB
     * @param int $displaysection the current displayed section number.
     *
     * @return string HTML to output.
     */
    protected function section_nav_selection($course, $sections, $displaysection) {
        $sectionnavigationclass = $this->courseformat->get_output_classname('content\\sectionnavigation');
        $sectionnavigation = new $sectionnavigationclass($this->courseformat, $displaysection);

        $sectionselectorclass = $this->courseformat->get_output_classname('content\\sectionselector');
        $sectionselector = new $sectionselectorclass($this->courseformat, $sectionnavigation);
        return $this->render($sectionselector);
    }

    /**
     * Returns controls in the bottom of the page to increase/decrease number of sections
     *
     * @deprecated since 4.0 MDL-72656 - use core_course output components instead.
     *
     * @param stdClass $course
     * @param int|null $sectionreturn
     *
     * @return string HTML to output.
     */
    protected function change_number_sections($course, $sectionreturn = null) {
        if ($sectionreturn) {
            $this->courseformat->set_section_number($sectionreturn);
        }
        $outputclass = $this->courseformat->get_output_classname('content\\addsection');
        $widget = new $outputclass($this->courseformat);
        return $this->render($widget);
    }

    /**
     * Returns bulk edit tools if any.
     *
     * @return string HTML to output.
     */
    protected function bulkedittools() {
        if ($this->courseformat->show_editor()) {
            $outputclass = $this->courseformat->get_output_classname('content\\bulkedittools');
            $widget = new $outputclass($this->courseformat);
            return $this->render($widget);
        }
        return '';
    }
}
