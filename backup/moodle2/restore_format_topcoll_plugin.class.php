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
 * @copyright  &copy; 2012-onwards G J Barnard in respect to modifications of standard topics format.
 * @author     G J Barnard - gjbarnard at gmail dot com and {@link http://moodle.org/user/profile.php?id=442195}
 * @link       http://docs.moodle.org/en/Collapsed_Topics_course_format
 * @license    http://www.gnu.org/copyleft/gpl.html GNU Public License
 *
 */
defined('MOODLE_INTERNAL') || die();
require_once($CFG->dirroot . '/course/format/topcoll/lib.php');

/**
 * Restore plugin class that provides the necessary information
 * needed to restore one topcoll course format.
 */
class restore_format_topcoll_plugin extends restore_format_plugin {

    /**
     * Returns the paths to be handled by the plugin at course level
     */
    protected function define_course_plugin_structure() {

        $paths = array();

        // Add own format stuff.
        $elename = 'topcoll'; // This defines the postfix of 'process_*' below.
        $elepath = $this->get_pathfor('/'); // This is defines the nested tag within 'plugin_format_topcoll_course' to allow
                                            // '/course/plugin_format_topcoll_course' in the path therefore as a path structure
                                            // representing the levels in course.xml in the backup file.
        $paths[] = new restore_path_element($elename, $elepath);

        return $paths; // And we return the interesting paths.
    }

    /**
     * Process the 'plugin_format_topcoll_course' element within the 'course' element in the 'course.xml' file in the
     * '/course' folder of the zipped backup 'mbz' file.
     */
    public function process_topcoll($data) {
        global $DB;

        $data = (object) $data;

        // We only process this information if the course we are restoring to
        // has 'topcoll' format (target format can change depending of restore options).
        $format = $DB->get_field('course', 'format', array('id' => $this->task->get_courseid()));
        if ($format != 'topcoll') {
            return;
        }

        $data->courseid = $this->task->get_courseid();

        if (!($course = $DB->get_record('course', array('id' => $data->courseid)))) {
            print_error('invalidcourseid', 'error');
        } // From /course/view.php.
        $courseformat = course_get_format($course);

        if (empty($data->layoutcolumns)) {
            // Cope with backups from Moodle 2.0, 2.1 and 2.2 versions.
            $data->layoutcolumns = get_config('format_topcoll', 'defaultlayoutcolumns');
        }

        $courseformat->restore_topcoll_setting(
            $data->courseid,
            $data->layoutelement,
            $data->layoutstructure,
            $data->layoutcolumns,
            $data->tgfgcolour,
            $data->tgbgcolour,
            $data->tgbghvrcolour);

        // No need to annotate anything here.
    }

    protected function after_execute_structure() {
    }
}