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
function xmldb_format_topcoll_upgrade($oldversion = 0) {

    global $DB;
    $dbman = $DB->get_manager();

    if ($oldversion < 2012030100) {

        // Define table format_topcoll_layout.
        $table = new xmldb_table('format_topcoll_layout');

        // Adding fields.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null, null);
        $table->add_field('courseid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0', null);
        $table->add_field('layoutelement', XMLDB_TYPE_INTEGER, '2', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '1', null);
        $table->add_field('layoutstructure', XMLDB_TYPE_INTEGER, '1', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '1', null);

        // Adding key.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        // Create table.
        $dbman->create_table($table);

        upgrade_plugin_savepoint(true, '2012030100', 'format', 'topcoll');
    }

    if ($oldversion < 2012042600) {
        // Change table name...
        $table = new xmldb_table('format_topcoll_layout');
        $dbman->rename_table($table, 'format_topcoll_settings');
        $table = new xmldb_table('format_topcoll_settings');   // Use the new table.
        // Define field tgfgcolour to be added to format_topcoll_settings
        $field = new xmldb_field('tgfgcolour', XMLDB_TYPE_CHAR, '6', null, XMLDB_NOTNULL, null, '000000', 'layoutstructure');

        // Conditionally launch add field tgfgcolour
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Define field tgbgcolour to be added to format_topcoll_settings
        $field = new xmldb_field('tgbgcolour', XMLDB_TYPE_CHAR, '6', null, XMLDB_NOTNULL, null, 'e2e2f2', 'tgfgcolour');

        // Conditionally launch add field tgbgcolour
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Define field tgbghvrcolour to be added to format_topcoll_settings
        $field = new xmldb_field('tgbghvrcolour', XMLDB_TYPE_CHAR, '6', null, XMLDB_NOTNULL, null, 'eeeeff', 'tgbgcolour');

        // Conditionally launch add field tgbghvrcolour
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        upgrade_plugin_savepoint(true, '2012042600', 'format', 'topcoll');
    }

    if ($oldversion < 2012050300) {

        // Define table format_topcoll_cookie_cnsnt to be created
        $table = new xmldb_table('format_topcoll_cookie_cnsnt');

        // Adding fields to table format_topcoll_cookie_cnsnt
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('userid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->add_field('cookieconsent', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '1');

        // Adding keys to table format_topcoll_cookie_cnsnt
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        // Conditionally launch create table for format_topcoll_cookie_cnsnt
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        upgrade_plugin_savepoint(true, '2012050300', 'format', 'topcoll');
    }

    if ($oldversion < 2012053101) {
        // Drop table format_topcoll_cookie_cnsnt if it exists - this may not work, please check db to see that the table has really gone.
        $table = new xmldb_table('format_topcoll_cookie_cnsnt');

        // Drop the table...
        if ($dbman->table_exists($table)) {
            $dbman->drop_table($table);
        }

        upgrade_plugin_savepoint(true, '2012053101', 'format', 'topcoll');
    }
    return true;
}