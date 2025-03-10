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
 * Collapsed Topics.
 *
 * @package   format_topcoll
 * @copyright 2021 Ferran Recio <ferran@moodle.com>
 * @copyright &copy; 2025-onwards G J Barnard.
 * @author    G J Barnard - gjbarnard at gmail dot com and {@link http://moodle.org/user/profile.php?id=442195}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace format_topcoll\output\courseformat\state;

use core_courseformat\output\local\state\course as course_base;
use core_courseformat\base as course_format;
use course_modinfo;
use core\url;
use stdClass;

/**
 * Contains the ajax update course structure.
 *
 * @package   core_course
 * @copyright 2021 Ferran Recio <ferran@moodle.com>
 * @copyright &copy; 2025-onwards G J Barnard.
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class course extends course_base {

    /**
     * Export this data so it can be used as state object in the course editor.
     *
     * @param renderer_base $output typically, the renderer that's calling this function
     * @return stdClass data context for a mustache template
     */
    public function export_for_template(\renderer_base $output): stdClass {
        global $CFG;

        $format = $this->format;
        $course = $format->get_course();
        $context = $format->get_context();
        // State must represent always the most updated version of the course.
        $modinfo = course_modinfo::instance($course);

        $url = new url('/course/view.php', ['id' => $course->id]);
        $maxbytes = get_user_max_upload_file_size($context, $CFG->maxbytes, $course->maxbytes);

        $data = (object)[
            'id' => $course->id,
            'numsections' => $format->get_last_section_number(),
            'numsectionswithoutdeligated' => $format->get_last_section_number_without_deligated(),
            'sectionlist' => [],
            'editmode' => $format->show_editor(),
            'highlighted' => $format->get_section_highlighted_name(),
            'maxsections' => $format->get_max_sections(),
            'maxsectionswithoutdeligated' => $format->get_max_sections_without_deligated(),
            'baseurl' => $url->out(),
            'statekey' => course_format::session_cache($course),
            'maxbytes' => $maxbytes,
            'maxbytestext' => display_size($maxbytes),
        ];

        $sections = $modinfo->get_section_info_all();
        unset($sections[0]); // Remove section zero.
        foreach ($sections as $section) {
            if ($format->is_section_visible($section)) {
                $data->sectionlist[] = $section->id;
            }
        }

        return $data;
    }
}
