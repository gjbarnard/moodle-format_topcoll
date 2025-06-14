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
 * External functions for Collapsed Topics format.
 *
 * @package    format_topcoll
 * @copyright  &copy; 2024 Your Name/Current Year
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once(\$CFG->libdir . '/externallib.php');
require_once(__DIR__ . '/classes/notes_manager.php'); // Adjust path if notes_manager is elsewhere

use core_external\external_api;
use core_external\external_function_parameters;
use core_external\external_value;
use core_external\external_single_structure;
use core_external\external_multiple_structure; // If returning multiple notes, not used here yet
use format_topcoll\notes_manager;

class format_topcoll_external extends external_api {

    /**
     * Describes the parameters for the get_personal_note method.
     * @return external_function_parameters
     */
    public static function get_personal_note_parameters() {
        return new external_function_parameters([
            'courseid' => new external_value(PARAM_INT, 'Course ID'),
            'sectionid' => new external_value(PARAM_INT, 'Section ID (course_sections.id)'),
        ]);
    }

    /**
     * Retrieves a personal note.
     *
     * @param int \$courseid Course ID.
     * @param int \$sectionid Section ID.
     * @return array Note data.
     */
    public static function get_personal_note(\$courseid, \$sectionid) {
        global \$USER;

        self::validate_parameters(self::get_personal_note_parameters(), ['courseid' => \$courseid, 'sectionid' => \$sectionid]);
        \$coursecontext = context_course::instance(\$courseid);
        self::validate_context(\$coursecontext);

        // Check if feature is enabled
        \$course_format = course_get_format(\$courseid);
        \$topcoll_settings = \$course_format->get_settings();

        \$course_setting = \$topcoll_settings['enablepersonalnotes_course'] ?? 0;
        \$site_enabled = (bool)get_config('format_topcoll', 'enablepersonalnotes');
        \$feature_enabled = false;
        if (\$course_setting == 0) {
            \$feature_enabled = \$site_enabled;
        } else if (\$course_setting == 1) {
            \$feature_enabled = true;
        }

        if (!\$feature_enabled) {
            throw new \moodle_exception('featuredisabled', 'format_topcoll');
        }

        \$note = notes_manager::get_note_for_section(\$courseid, \$sectionid, \$USER->id);

        if (\$note) {
            return [
                'notescontent' => \$note->notescontent,
                'timemodified' => \$note->timemodified,
            ];
        } else {
            return ['notescontent' => '', 'timemodified' => 0]; // Return empty if no note exists
        }
    }

    /**
     * Describes the return values of the get_personal_note method.
     * @return external_single_structure
     */
    public static function get_personal_note_returns() {
        return new external_single_structure([
            'notescontent' => new external_value(PARAM_RAW, 'Content of the note'),
            'timemodified' => new external_value(PARAM_INT, 'Last modified timestamp'),
        ]);
    }

    /**
     * Describes the parameters for the save_personal_note method.
     * @return external_function_parameters
     */
    public static function save_personal_note_parameters() {
        return new external_function_parameters([
            'courseid' => new external_value(PARAM_INT, 'Course ID'),
            'sectionid' => new external_value(PARAM_INT, 'Section ID (course_sections.id)'),
            'notescontent' => new external_value(PARAM_RAW, 'Content of the note'),
        ]);
    }

    /**
     * Saves a personal note.
     *
     * @param int \$courseid Course ID.
     * @param int \$sectionid Section ID.
     * @param string \$notescontent Content of the note.
     * @return array Status of the save operation.
     */
    public static function save_personal_note(\$courseid, \$sectionid, \$notescontent) {
        global \$USER;

        self::validate_parameters(self::save_personal_note_parameters(), ['courseid' => \$courseid, 'sectionid' => \$sectionid, 'notescontent' => \$notescontent]);
        \$coursecontext = context_course::instance(\$courseid);
        self::validate_context(\$coursecontext);

        // Check if feature is enabled
        \$course_format = course_get_format(\$courseid);
        \$topcoll_settings = \$course_format->get_settings();

        \$course_setting = \$topcoll_settings['enablepersonalnotes_course'] ?? 0;
        \$site_enabled = (bool)get_config('format_topcoll', 'enablepersonalnotes');
        \$feature_enabled = false;
        if (\$course_setting == 0) {
            \$feature_enabled = \$site_enabled;
        } else if (\$course_setting == 1) {
            \$feature_enabled = true;
        }

        if (!\$feature_enabled) {
            throw new \moodle_exception('featuredisabled', 'format_topcoll');
        }

        \$noteid = notes_manager::save_note_for_section(\$courseid, \$sectionid, \$USER->id, \$notescontent);

        if (\$noteid !== false) { // save_note_for_section returns ID or true for "empty new note", false on error
            return ['status' => 'success', 'noteid' => (int)\$noteid, 'timemodified' => time()];
        } else {
            return ['status' => 'error', 'message' => 'Failed to save note.'];
        }
    }

    /**
     * Describes the return values of the save_personal_note method.
     * @return external_single_structure
     */
    public static function save_personal_note_returns() {
        return new external_single_structure([
            'status' => new external_value(PARAM_ALPHA, 'Status of the save operation (success/error)'),
            'noteid' => new external_value(PARAM_INT, 'ID of the saved note (0 if new empty note was "saved" by no-op)', VALUE_OPTIONAL),
            'timemodified' => new external_value(PARAM_INT, 'Timestamp of modification', VALUE_OPTIONAL),
            'message' => new external_value(PARAM_TEXT, 'Error message if status is error', VALUE_OPTIONAL),
        ]);
    }
}
