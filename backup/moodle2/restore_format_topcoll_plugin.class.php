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

/**
 * restore plugin class that provides the necessary information
 * needed to restore one grid course format plugin
 */
class restore_format_topcoll_plugin extends restore_format_plugin {
    /**
     * Returns the paths to be handled by the plugin at section level
     */
    protected function define_course_plugin_structure() {

        $paths = array();

        // Add own format stuff
        $elename = 'topcoll';
        $elepath = $this->get_pathfor('/topcoll');
        $paths[] = new restore_path_element($elename, $elepath);

        return $paths; // And we return the interesting paths
    }

    /**
     * Process the format element
     */
    public function process_topcoll($data) {
        global $DB;

		// for getting a stack trace: throw new ddl_exception('ddlunknownerror', NULL, 'incorrect table parameter!');

        $data = (object)$data;
        $oldid = $data->id;

        // We only process this information if the course we are restoring to
        // has 'topcoll' format (target format can change depending of restore options)
        $format = $DB->get_field('course', 'format', array('id' => $this->task->get_courseid()));
        if ($format != 'topcoll') {
            return;
        }

        $data->courseid = $this->task->get_courseid();

        $DB->insert_record('format_topcoll_layout', $data);

        // No need to annotate anything here
    }
    
    protected function after_execute_structure() { }
}
