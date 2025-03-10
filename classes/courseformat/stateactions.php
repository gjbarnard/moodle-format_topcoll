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

namespace format_topcoll\courseformat;

use core_courseformat\stateactions as stateactions_base;
use core_courseformat\stateupdates;
use context_course;
use moodle_exception;
use section_info;
use stdClass;

/**
 * Contains the core course state actions specific to Collapsed Topics.
 *
 * @package    format_topcoll
 * @copyright  &copy; 2025-onwards G J Barnard in respect to modifications of standard topics format.
 * @author     G J Barnard - {@link https://moodle.org/user/profile.php?id=442195}
 * @copyright  2020 Ferran Recio <ferran@moodle.com>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class stateactions extends stateactions_base {
    /**
     * Create a course section.
     *
     * This method follows the same logic as changenumsections.php.
     *
     * @param stateupdates $updates the affected course elements track
     * @param stdClass $course the course object
     * @param int[] $ids not used
     * @param int $targetsectionid optional target section id (if not passed section will be appended)
     * @param int $targetcmid not used
     */
    public function section_add(
        stateupdates $updates,
        stdClass $course,
        array $ids = [],
        ?int $targetsectionid = null,
        ?int $targetcmid = null
    ): void {

        $coursecontext = context_course::instance($course->id);
        require_capability('moodle/course:update', $coursecontext);

        // Get course format settings.
        $format = course_get_format($course->id);
        $lastsectionnumber = $format->get_last_section_number_without_deligated(); // Difference from core.
        $maxsections = $format->get_max_sections();

        if ($lastsectionnumber >= $maxsections) {
            throw new moodle_exception('maxsectionslimit', 'moodle', '', $maxsections);
        }

        $modinfo = get_fast_modinfo($course);

        // Get target section.
        if ($targetsectionid) {
            $this->validate_sections($course, [$targetsectionid], __FUNCTION__);
            $targetsection = $modinfo->get_section_info_by_id($targetsectionid, MUST_EXIST);
            // Inserting sections at any position except in the very end requires capability to move sections.
            require_capability('moodle/course:movesections', $coursecontext);
            $insertposition = $targetsection->section + 1;
        } else {
            // Get last section.
            $insertposition = 0;
        }

        course_create_section($course, $insertposition);

        // Adding a section affects the full course structure.
        $this->course_state($updates, $course);
    }
}
