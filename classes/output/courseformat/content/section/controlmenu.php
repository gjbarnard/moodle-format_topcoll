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
 * @copyright  &copy; 2021-onwards G J Barnard in respect to modifications of standard topics format.
 * @author     G J Barnard - {@link https://moodle.org/user/profile.php?id=442195}
 * @copyright  2020 Ferran Recio <ferran@moodle.com>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace format_topcoll\output\courseformat\content\section;

use format_topics\output\courseformat\content\section\controlmenu as controlmenu_base;

/**
 * Base class to render a course section menu.
 *
 * @package   format_topics
 * @copyright 2020 Ferran Recio <ferran@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class controlmenu extends controlmenu_base {
    /** @var course_format the course format class */
    protected $format;

    /** @var section_info the course section class */
    protected $section;

    /**
     * Generate the edit control items of a section.
     *
     * This method must remain public until the final deprecation of section_edit_control_items.
     *
     * @return array of edit control items
     */
    public function section_control_items() {
        $controls = parent::section_control_items();
        $format = $this->format;
        $section = $this->section;

        // Alter 'permalink' to our form of url.
        if (array_key_exists('permalink', $controls)) {
            $controls['permalink']->url = $format->get_view_url($section->section, ['singlenavigation' => true]);
        }

        if ($section->is_orphan() || !$section->sectionnum) {
            return $controls;
        }

        if (!has_capability('moodle/course:setcurrentsection', $this->coursecontext)) {
            return $controls;
        }

        $tcsettings = $format->get_settings();
        if (
            (($tcsettings['layoutstructure'] == 1) || ($tcsettings['layoutstructure'] == 4)) &&
            $section->section && has_capability('moodle/course:setcurrentsection', $this->coursecontext)
        ) {
            $controls = $this->add_control_after($controls, 'edit', 'highlight', $this->get_section_highlight_item());
        }

        return $controls;
    }
}
