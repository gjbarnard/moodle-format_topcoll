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
 * @version    See the value of '$plugin->version' in version.php.
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

require_once('config.php'); // For defaults.

/**
 * Gets the layout for the course or if it does not exist, create it.
 * CONTRIB-3378
 * @param int $courseid The course identifier.
 * @return int The layout.
 */
function get_layout($courseid) {
    global $DB;
    global $TCCFG;

    if (!$layout = get_record('format_topcoll_layout', 'courseid',$courseid)) {

        $layout = new stdClass();
        $layout->courseid = $courseid;
        $layout->layoutelement = $TCCFG->defaultlayoutelement; // Default value.
        $layout->layoutstructure = $TCCFG->defaultlayoutstructure; // Default value.

        if (!$layout->id = insert_record('format_topcoll_layout', $layout)) {
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
    if ($layout = get_record('format_topcoll_layout', 'courseid',$courseid)) {
        $layout->layoutelement = $layoutelement;
        $layout->layoutstructure = $layoutstructure;
        update_record('format_topcoll_layout', $layout);
    } else {
        $layout = new stdClass();
        $layout->courseid = $courseid;
        $layout->layoutelement = $layoutelement;
        $layout->layoutstructure = $layoutstructure;
        insert_record('format_topcoll_layout', $layout);
    }
}

/**
 * Deletes the layout entry for the given course.
 * CONTRIB-3520
 */
function topcoll_course_format_delete_course($courseid) {
    global $DB;

    delete_records('format_topcoll_layout', 'courseid', $courseid);
}

/**
 * Gets the format cookie consent for the user or if it does not exist, create it.
 * CONTRIB-3624
 * @param int $userid The user identifier.
 * @return int The format cookie consent.
 */
function get_topcoll_cookie_consent($userid) {
    global $DB;

    if (!$cookie = get_record('format_topcoll_cookie_cnsnt', 'userid',$userid)) {

        // Default values...
        $cookie = new stdClass();
        $cookie->userid = $userid;
        $cookie->cookieconsent = 1;

        if (!$cookie->id = insert_record('format_topcoll_cookie_cnsnt', $cookie)) {
            error('Could not set format cookie consent. Collapsed Topics format database is not ready.  An admin must visit notifications.');
        }
    }

    return $cookie;
}

/**
 * Sets the format cookie consent for the user or if it does not exist, create it.
 * CONTRIB-3624
 * @param int $userid The user identifier.
 * @param int $cookieconsent The layout element value to set.
 */
function put_topcoll_cookie_consent($userid, $cookieconsent) {
    global $DB;
    if ($cookie = get_record('format_topcoll_cookie_cnsnt', 'userid',$userid)) {
        $cookie->cookieconsent = $cookieconsent;
        update_record('format_topcoll_cookie_cnsnt', $cookie);
    } else {
        $cookie = new stdClass();
        $cookie->courseid = $userid;
        $cookie->cookieconsent = $cookieconsent;
        insert_record('format_topcoll_cookie_cnsnt', $cookie);
    }
}