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

function topcoll_restore_format_data($restore, $data) {
    global $CFG;

    $status = true;

    // Get the backup data
    if (!empty($data['FORMATDATA']['#']['LAYOUTS']['0']['#']['LAYOUT'])) {
        // Get all the pages and restore them, restoring page items along the way.
        // $layouts = $data['FORMATDATA']['#']['LAYOUTS']['0']['#']['PAGE'];
        $layouts = $data['FORMATDATA']['#']['LAYOUTS']['0']['#']['LAYOUT'];
        for ($i = 0; $i < count($layouts); $i++) {
            $layout_info = $layouts[$i];

            // Id will remap later when we know all ids are present
            //$layout_oldid = backup_todb($layout_info['#']['ID']['0']['#']);
            
            $layout = new stdClass;
            $layout->layoutelement = backup_todb($layout_info['#']['LAYOUTELEMENT']['0']['#']);
            $layout->layoutstructure = backup_todb($layout_info['#']['LAYOUTSTRUCTURE']['0']['#']);
            $layout->courseid = $restore->courseid;

            if ($layout_newid = insert_record('format_topcoll_layout', $layout)) {
                //backup_putid($restore->backup_unique_code, 'format_topcoll_layout', $layout_oldid, $layout_newid);
            } else {
                $status = false;
                break;
            }
        }

        //TODO: Need to fix sortorder for old courses.
    }
    return $status;
}

/**
 * This function makes all the necessary calls to {@link restore_decode_content_links_worker()}
 * function inorder to decode contents of this block from the backup 
 * format to destination site/course in order to mantain inter-activities 
 * working in the backup/restore process. 
 * 
 * This is called from {@link restore_decode_content_links()}
 * function in the restore process.  This function is called regarless of
 * the return value from {@link backuprestore_enabled()}.
 *
 * @param object $restore Standard restore object
 * @return boolean
 **/
function topcoll_decode_format_content_links_caller($restore) {
    return true;
}
    
/**
 * Return content decoded to support interactivities linking.
 * This is called automatically from
 * {@link restore_decode_content_links_worker()} function
 * in the restore process.
 *
 * @param string $content Content to be dencoded
 * @param object $restore Restore preferences object
 * @return string The dencoded content
 **/
function topcoll_decode_format_content_links($content, $restore) {
    //TODO: Convert universal id to link;
    return $content;
}