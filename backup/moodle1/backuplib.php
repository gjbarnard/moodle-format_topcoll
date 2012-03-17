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

/**
 * Format's backup routine
 *
 * @param handler $bf Backup file handler
 * @param object $preferences Backup preferences
 * @return boolean Success
 **/
function topcoll_backup_format_data($bf, $preferences) {
    $status = true;

    if ($layout = get_record('format_topcoll_layout', 'courseid',
        $preferences->backup_course)) {

        $status = $status and fwrite ($bf, start_tag('LAYOUT', 3, true));
        $status = $status and fwrite ($bf, full_tag('LAYOUTELEMENT', 4, false, $layout->layoutelement));
        $status = $status and fwrite ($bf, full_tag('LAYOUTSTRUCTURE', 4, false, $layout->layoutstructure));
        $status = $status and fwrite ($bf, end_tag('LAYOUT',3, true));
    }
    return $status;
}

/**
 * Return a content encoded to support interactivities linking. This function is
 * called automatically from the backup procedure by {@link backup_encode_absolute_links()}.
 *
 * @param string $content Content to be encoded
 * @param object $restore Restore preferences object
 * @return string The encoded content
 **/
function topcoll_encode_format_content_links($content, $restore) {
    global $CFG;

    $base = preg_quote($CFG->wwwroot, '/');

    //TODO: Convert lins to universal id;
    return $content;
}