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
 * Class to manage personal notes for sections in Collapsed Topics format.
 *
 * @package    format_topcoll
 * @copyright  &copy; 2024 Your Name / Current Year
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace format_topcoll;

defined('MOODLE_INTERNAL') || die();

class notes_manager {

    const TABLE_NAME = 'format_topcoll_notes';

    /**
     * Retrieves a student's note for a specific section in a course.
     *
     * @param int \$courseid The ID of the course.
     * @param int \$sectionid The ID of the course_sections record.
     * @param int \$userid The ID of the user.
     * @return \stdClass|false The note object or false if not found or error.
     */
    public static function get_note_for_section(int \$courseid, int \$sectionid, int \$userid) {
        global \$DB;

        if (!\$courseid || !\$sectionid || !\$userid) {
            return false;
        }

        return \$DB->get_record(self::TABLE_NAME, [
            'courseid' => \$courseid,
            'sectionid' => \$sectionid,
            'userid' => \$userid
        ]);
    }

    /**
     * Creates or updates a student's note for a specific section.
     *
     * @param int \$courseid The ID of the course.
     * @param int \$sectionid The ID of the course_sections record.
     * @param int \$userid The ID of the user.
     * @param string \$notescontent The content of the note.
     * @return int|bool The ID of the saved note record, true if no action needed for new empty note, or false on failure.
     * @throws \dml_exception If database operation fails.
     */
    public static function save_note_for_section(int \$courseid, int \$sectionid, int \$userid, string \$notescontent) {
        global \$DB;

        if (!\$courseid || !\$sectionid || !\$userid) {
            // Or throw an invalid_parameter_exception
            return false;
        }

        \$existingnote = self::get_note_for_section(\$courseid, \$sectionid, \$userid);

        \$record = new \stdClass();
        \$record->courseid = \$courseid;
        \$record->sectionid = \$sectionid;
        \$record->userid = \$userid;
        \$record->notescontent = \$notescontent;
        \$record->timemodified = time();

        if (\$existingnote) {
            \$record->id = \$existingnote->id;
            \$record->timecreated = \$existingnote->timecreated; // Keep original creation time
            \$DB->update_record(self::TABLE_NAME, \$record);
            return \$record->id;
        } else {
            // If content is empty and no existing note, don't create a new empty note.
            if (trim(\$notescontent) === '') {
                return true; // Or false, depending on desired behavior for "saving" an empty new note.
                             // Let's assume true, as no action is needed and it's not an error.
            }
            \$record->timecreated = time();
            \$newid = \$DB->insert_record(self::TABLE_NAME, \$record);
            return \$newid;
        }
    }

    /**
     * Deletes a student's note for a specific section.
     * (Optional - alternatively, saving an empty note effectively deletes it for the user).
     *
     * @param int \$courseid The ID of the course.
     * @param int \$sectionid The ID of the course_sections record.
     * @param int \$userid The ID of the user.
     * @return bool True on success, false on failure or if no note existed.
     * @throws \dml_exception If database operation fails.
     */
    public static function delete_note_for_section(int \$courseid, int \$sectionid, int \$userid): bool {
        global \$DB;

        if (!\$courseid || !\$sectionid || !\$userid) {
            return false;
        }

        return \$DB->delete_records(self::TABLE_NAME, [
            'courseid' => \$courseid,
            'sectionid' => \$sectionid,
            'userid' => \$userid
        ]);
    }
}
