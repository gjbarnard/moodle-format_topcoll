<?php 
/**
  This file contains general functions for the course format Collapsed Topics
  Thanks to Sam Hemelryk who modified the Moodle core code for 2.0, and
  I have copied and modified under the terms of the following license:
  http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later

  This program is free software: you can redistribute it and/or modify
  it under the terms of the GNU General Public License as published by
  the Free Software Foundation, either version 3 of the License, or
  any later version.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with this program.  If not, see http://www.gnu.org/licenses/.
 */

/**
 * Indicates this format uses sections.
 *
 * @return bool Returns true
 */
function callback_topcoll_uses_sections() {
    return true;
}

/**
 * Used to display the course structure for a course where format=Collapsed Topics
 *
 * This is called automatically by {@link load_course()} if the current course
 * format = Collapsed Topics.
 *
 * @param navigation_node $navigation The course node
 * @param array $path An array of keys to the course node
 * @param stdClass $course The course we are loading the section for
 */
function callback_topcoll_load_content(&$navigation, $course, $coursenode) {
    return $navigation->load_generic_course_sections($course, $coursenode, 'topcoll');
}

/**
 * The string that is used to describe a section of the course
 *
 * @return string
 */
function callback_topcoll_definition() {
    return get_string('sectionname', 'format_topcoll');
}

/**
 * The GET argument variable that is used to identify the section being
 * viewed by the user (if there is one)
 *
 * @return string
 */
function callback_topcoll_request_key() {
    return 'topcoll';
}

/**
 * Gets the name for the provided section.
 *
 * @param stdClass $course
 * @param stdClass $section
 * @return string
 */
function callback_topcoll_get_section_name($course, $section) {
    // We can't add a node without any text
    if (!empty($section->name)) {
        return format_string($section->name, true, array('context' => get_context_instance(CONTEXT_COURSE, $course->id)));  // MDL-29188
    } else if ($section->section == 0) {
        return get_string('section0name', 'format_topcoll');
    } else {
        return get_string('sectionname', 'format_topcoll') . ' ' . $section->section;
    }
}

/**
 * Declares support for course AJAX features
 *
 * @see course_format_ajax_support()
 * @return stdClass
 */
function callback_topcoll_ajax_support() {
    $ajaxsupport = new stdClass();
    $ajaxsupport->capable = true;  // See CONTRIB-2975 for information on how fixed.
    $ajaxsupport->testedbrowsers = array('MSIE' => 6.0, 'Gecko' => 20061111, 'Opera' => 9.0, 'Safari' => 531, 'Chrome' => 6.0);
    return $ajaxsupport;
}

/**
 * Returns a URL to arrive directly at a section
 *
 * @param int $courseid The id of the course to get the link for
 * @param int $sectionnum The section number to jump to
 * @return moodle_url
 */
function callback_topcoll_get_section_url($courseid, $sectionnum) {
    return new moodle_url('/course/view.php', array('id' => $courseid, 'ctopic' => $sectionnum));
}

/**
 * Gets the layout for the course or if it does not exist, create it.
 * CONTRIB-3378
 * @param int $courseid The course identifier.
 * @return int The layout.
 */
function get_layout($courseid) {
    require_once('config.php'); // For defaults - NOTE: For some reason 'globals' not working if it is used and this is outside this function.
    global $DB;

    if (!$layout = $DB->get_record('format_topcoll_layout', array('courseid' => $courseid))) {

        $layout = new stdClass();
        $layout->courseid = $courseid;
        $layout->layoutelement = $defaultlayoutelement; // Default value.
        $layout->layoutstructure = $defaultlayoutstructure; // Default value.

        if (!$layout->id = $DB->insert_record('format_topcoll_layout', $layout)) {
            error('Could not set layout setting. Collapsed Topics format database is not ready.  An admin must visit notifications.');
        }
    }

    return $layout;
}

/**
 * Sets the layout setting for the course or if it does not exist, create it.
 * CONTRIB-3378
 * @param int $courseid The course identifier.
 * @param int $layoutelement The layout element value to set.
 * @param int $layoutstructure The layout structure value to set.
 */
function put_layout($courseid, $layoutelement, $layoutstructure) {
    global $DB;
    if ($layout = $DB->get_record('format_topcoll_layout', array('courseid' => $courseid))) {
        $layout->layoutelement = $layoutelement;
        $layout->layoutstructure = $layoutstructure;
        $DB->update_record('format_topcoll_layout', $layout);
    } else {
        $layout = new stdClass();
        $layout->courseid = $courseid;
        $layout->layoutelement = $layoutelement;
        $layout->layoutstructure = $layoutstructure;
        $DB->insert_record('format_topcoll_layout', $layout);
    }
}

/**
 * Deletes the layout entry for the given course.
 * CONTRIB-3520
 */
function format_topcoll_delete_course($courseid) {
    global $DB;

    $DB->delete_records("format_topcoll_layout", array("courseid"=>$courseid));
}
