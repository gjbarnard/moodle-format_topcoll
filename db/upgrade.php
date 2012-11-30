<?php

/**
 * Collapsed Topics Information
 *
 * A topic based format that solves the issue of the 'Scroll of Death' when a course has many topics. All topics
 * except zero have a toggle that displays that topic. One or more topics can be displayed at any given time.
 * Toggles are persistent on a per browser session per course basis but can be made to persist longer by a small
 * code change. Full installation instructions, code adaptions and credits are included in the 'Readme.txt' file.
 *
 * @package    course/format
 * @subpackage topcoll
 * @version    See the value of '$plugin->version' in below.
 * @copyright  &copy; 2009-onwards G J Barnard in respect to modifications of standard topics format.
 * @author     G J Barnard - gjbarnard at gmail dot com and {@link http://moodle.org/user/profile.php?id=442195}
 * @link       http://docs.moodle.org/en/Collapsed_Topics_course_format
 * @license    http://www.gnu.org/copyleft/gpl.html GNU Public License
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.

 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.

 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */
require_once($CFG->dirroot . '/course/format/topcoll/lib.php');

function xmldb_format_topcoll_upgrade($oldversion = 0) {

    global $DB;
    $dbman = $DB->get_manager();
    $result = true;

    // Note: You must upgrade to 2.3 version before transitioning to 2.4.
    if ($result && $oldversion < 2012113000) { // Note to self, Moodle 2.3 version cannot now be greater than this.
        // Rename table format_topcoll_layout if it exists.
        $table = new xmldb_table('format_topcoll_settings');
        // Rename the table...
        if ($dbman->table_exists($table)) {
            $courseformat = new format_topcoll('topcoll', 0);  // Instance to help us - '/course/format/topcoll/lib.php'.
            // Extract data out of table and put in course settings table for 2.4.
            $records = $DB->get_records('format_topcoll_settings');
            foreach ($records as $record) {
                $courseformat->restore_topcoll_setting($record->courseid, $record->layoutelement, $record->layoutstructure, $record->layoutcolumns, $record->tgfgcolour, $record->tgbgcolour, $record->tgbghvrcolour);
            }
            // Farewell old settings table.
            $dbman->drop_table($table);
        } //else Nothing to do as settings put in DB on first use.
    }
    return $result;
}