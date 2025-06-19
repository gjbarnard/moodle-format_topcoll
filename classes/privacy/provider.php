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
 * Privacy Subsystem implementation for format_topcoll.
 *
 * @package    format_topcoll
 * @copyright  &copy; 2018-onwards G J Barnard based upon work done by Andrew Nicols <andrew@nicols.co.uk>.
 * @author     G J Barnard - {@link https://moodle.org/user/profile.php?id=442195}
 * @link       https://docs.moodle.org/en/Collapsed_Topics_course_format
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace format_topcoll\privacy;

use core_privacy\local\request\writer;
use core_privacy\local\metadata\collection;
use format_topcoll\togglelib;
use \core_privacy\local\request\contextlist;
use \core_privacy\local\request\approved_contextlist;
use \context_course;
use \format_topcoll\notes_manager; // Assuming notes_manager.php is in format_topcoll/classes/

/**
 * Implementation of the privacy subsystem plugin provider.
 */
class provider implements
    // This plugin has data.
    \core_privacy\local\metadata\provider,

    // This plugin has some sitewide user preferences to export.
    \core_privacy\local\request\user_preference_provider,
    // This plugin stores data in plugin tables.
    \core_privacy\local\request\plugin\provider {
    /**
     * Returns meta data about this system.
     *
     * @param   collection $itemcollection The initialised item collection to add items to.
     * @return  collection A listing of user data stored through this system.
     */
    public static function get_metadata(collection $items): collection {
        $items->add_user_preference(togglelib::TOPCOLL_TOGGLE, 'privacy:metadata:preference:toggle');

        $items->add_database_table(
            notes_manager::TABLE_NAME,
            [
                'userid' => 'privacy:metadata:userid',
                'notescontent' => 'privacy:metadata:format_topcoll_notes:notescontent',
                'timecreated' => 'privacy:metadata:timecreated',
                'timemodified' => 'privacy:metadata:timemodified',
            ],
            'privacy:metadata:format_topcoll_notes:table'
        );

        return $items;
    }

    /**
     * Store all user preferences for the plugin.
     *
     * @param int $userid The userid of the user whose data is to be exported.
     */
    public static function export_user_preferences(int $userid) {
        $preferences = get_user_preferences(null, null, $userid);
        $togglelib = new togglelib();
        foreach ($preferences as $name => $value) {
            $courseid = null;
            if (strpos($name, togglelib::TOPCOLL_TOGGLE) === 0) {
                $courseid = substr($name, strlen(togglelib::TOPCOLL_TOGGLE) + 1);

                writer::export_user_preference(
                    'format_topcoll',
                    $name,
                    $value,
                    get_string('privacy:request:preference:toggle', 'format_topcoll', (object) [
                        'name' => $courseid,
                        'value' => $value,
                        'decoded' => $togglelib->decode_toggle_state($value),
                    ])
                );
            }
        }
    }

    /**
     * Gets all the contexts in which a user has data in this plugin.
     *
     * @param   int \$userid The user to search.
     * @return  contextlist Contextlist of all contexts that apply to this user.
     */
    public static function get_contexts_for_userid(int \$userid): contextlist {
        global \$DB;
        \$contextlist = new contextlist();
        // Using notes_manager::TABLE_NAME constant
        \$sql = "SELECT DISTINCT c.id
                  FROM {course} c
                  JOIN {".notes_manager::TABLE_NAME."} ftn ON ftn.courseid = c.id
                 WHERE ftn.userid = :userid";
        if (\$courseids = \$DB->get_fieldset_sql(\$sql, ['userid' => \$userid])) {
            foreach (\$courseids as \$courseid) {
                \$contextlist->add(context_course::instance(\$courseid));
            }
        }
        return \$contextlist;
    }

    /**
     * Export all user data for the specified user, in the specified contexts.
     *
     * @param   approved_contextlist \$contextlist The approved contexts to export information for.
     */
    public static function export_user_data(approved_contextlist \$contextlist) {
        global \$DB;
        \$userid = \$contextlist->get_user()->id;

        foreach (\$contextlist->get_contexts() as \$context) {
            if (!\$context instanceof \context_course) {
                continue;
            }
            \$courseid = \$context->instanceid;
            \$params = ['userid' => \$userid, 'courseid' => \$courseid];
            // Using notes_manager::TABLE_NAME constant
            \$sql = "SELECT ftn.id, ftn.sectionid, cs.section as sectionnum, ftn.notescontent, ftn.timecreated, ftn.timemodified
                      FROM {".notes_manager::TABLE_NAME."} ftn
                      JOIN {course_sections} cs ON ftn.sectionid = cs.id
                     WHERE ftn.userid = :userid AND ftn.courseid = :courseid
                  ORDER BY cs.section";

            if (\$notes = \$DB->get_records_sql(\$sql, \$params)) {
                foreach (\$notes as \$note) {
                    writer::with_context(\$context)->export_database_record(
                        notes_manager::TABLE_NAME, // Use the constant
                        \$note,
                        'privacy:export:format_topcoll_note',
                        ['sectionnum' => \$note->sectionnum]
                    );
                }
            }
        }
    }

    /**
     * Delete all data for all users in the specified context.
     *
     * @param   \context \$context The context to delete data for.
     */
    public static function delete_data_for_all_users_in_context(\context \$context) {
        global \$DB;
        if (!\$context instanceof \context_course) {
            return;
        }
        \$courseid = \$context->instanceid;
        // Using notes_manager::TABLE_NAME constant
        \$DB->delete_records(notes_manager::TABLE_NAME, ['courseid' => \$courseid]);
    }

    /**
     * Delete all user data for the specified user, in the specified contexts.
     *
     * @param   approved_contextlist \$contextlist The approved contexts and user to delete information for.
     */
    public static function delete_data_for_user(approved_contextlist \$contextlist) {
        global \$DB;
        \$userid = \$contextlist->get_user()->id;

        foreach (\$contextlist->get_contexts() as \$context) {
            if (!\$context instanceof \context_course) {
                continue;
            }
            \$courseid = \$context->instanceid;
            // Using notes_manager::TABLE_NAME constant
            \$DB->delete_records(notes_manager::TABLE_NAME, ['userid' => \$userid, 'courseid' => \$courseid]);
        }
    }
}
