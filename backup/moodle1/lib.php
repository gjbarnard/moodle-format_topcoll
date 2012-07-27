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
 
defined('MOODLE_INTERNAL') || die();
 
require_once($CFG->dirroot . '/course/format/topcoll/config.php'); // For Collaped Topics defaults.

class moodle1_format_topcoll_handler extends moodle1_xml_handler {
    
    
    /**
     * Declare the paths in moodle.xml we are able to convert
     */
    public function get_paths() {
        // Obtained through examination of the 'course' element in the 'moodle.xml' file in the Moodle 1.9 backup zip file for a course using the format.
        return array(
            new convert_path('layout', '/MOODLE_BACKUP/COURSE/FORMATDATA/LAYOUT')
        );
    }
 
    /**
     * This is executed every time we have one /MOODLE_BACKUP/COURSE/FORMATDATA/LAYOUT
     * data available
     */
    public function process_layout($data) {
        global $TCCFG;
        // Default values...
	$colourdefaults = array('tgfgcolour' => $TCCFG->defaulttgfgcolour, 'tgbgcolour' => $TCCFG->defaulttgbgcolour, 'tgbghvrcolour' => $TCCFG->defaulttgbghvrcolour );
	$data = array_merge($data, $colourdefaults);
        // MDL-32205 - Currently only works for course level data in the course format, not sections or modules.
        $completedata = array('plugin_format_topcoll_course' => $data); // Tag name obtained through examination of the 'course/course.xml' file in a Moodle 2 backup 'mbz' (zip) file for a course using the format.
        $this->converter->set_stash('course_formatdata', $completedata);
    }
 }
 
 ?>