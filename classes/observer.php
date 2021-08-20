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
 * @package    format_topcoll
 * @category   event
 * @version    See the value of '$plugin->version' in version.php.
 * @copyright  &copy; 2017-onwards G J Barnard based upon work done by Marina Glancy.
 * @author     G J Barnard - {@link http://moodle.org/user/profile.php?id=442195}
 * @link       http://docs.moodle.org/en/Collapsed_Topics_course_format
 * @license    http://www.gnu.org/copyleft/gpl.html GNU Public License
 *
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot.'/course/format/lib.php'); // For course_get_format.

/**
 * Event observers supported by this format.
 */
class format_topcoll_observer {

    /**
     * Observer for the course_content_deleted event.
     *
     * Deletes the user preference entries for the given course upon course deletion.
     * CONTRIB-3520.
     *
     * @param \core\event\course_content_deleted $event
     */
    public static function course_content_deleted(\core\event\course_content_deleted $event) {
        global $DB;
        $DB->delete_records("user_preferences", array("name" => 'topcoll_toggle_'.$event->objectid)); // This is the $courseid.
    }

    /* Events observed for the purpose of the activty functionality.
       TODO: Do need to monitor when a course is changed to CT and clear the cache for
             that course?  i.e. scenario of using CT, then changing to Topics then changing
             back again -> data would be invalid.
    */

    /**
     * Observer for the role_allow_view_updated event.
     */
    public static function role_allow_view_updated() {
        /* Subsitute for a 'role created' event that does not exist in core!
           But this seems to happen when a role is created.  See 'create_role'
           in lib/accesslib.php. */
        \format_topcoll\activity::invalidatestudentrolescache();
        \format_topcoll\activity::invalidatemodulecountcache();
        \format_topcoll\activity::invalidatestudentscache();
    }

    /**
     * Observer for the role_updated event.
     */
    public static function role_updated() {
        \format_topcoll\activity::invalidatestudentrolescache();
        \format_topcoll\activity::invalidatemodulecountcache();
        \format_topcoll\activity::invalidatestudentscache();
    }

    /**
     * Observer for the role_deleted event.
     */
    public static function role_deleted() {
        \format_topcoll\activity::invalidatestudentrolescache();
        \format_topcoll\activity::invalidatemodulecountcache();
        \format_topcoll\activity::invalidatestudentscache();
    }

    /**
     * Observer for the user_enrolment_created event.
     *
     * @param \core\event\user_enrolment_created $event
     */
    public static function user_enrolment_created(\core\event\user_enrolment_created $event) {
        if ($courseformat = self::istopcoll($event->courseid)) {
            \format_topcoll\activity::userenrolmentcreated($event->relateduserid, $event->courseid, $courseformat);
        }
    }

    /**
     * Observer for the user_enrolment_updated event.
     *
     * @param \core\event\user_enrolment_updated $event
     */
    public static function user_enrolment_updated(\core\event\user_enrolment_updated $event) {
        if ($courseformat = self::istopcoll($event->courseid)) {
            \format_topcoll\activity::userenrolmentupdated($event->relateduserid, $event->courseid, $courseformat);
        }
    }

    /**
     * Observer for the user_enrolment_deleted event.
     *
     * @param \core\event\user_enrolment_deleted $event
     */
    public static function user_enrolment_deleted(\core\event\user_enrolment_deleted $event) {
        if ($courseformat = self::istopcoll($event->courseid)) {
            \format_topcoll\activity::userenrolmentdeleted($event->relateduserid, $event->courseid, $courseformat);
        }
    }

    /**
     * Observer for the course_module_created event.
     *
     * @param \core\event\course_module_created $event
     */
    public static function course_module_created(\core\event\course_module_created $event) {
        if ($courseformat = self::istopcoll($event->courseid)) {
            \format_topcoll\activity::modulecreated($event->objectid, $event->courseid, $courseformat);
        }
    }

    /**
     * Observer for the course_module_updated event.
     *
     * @param \core\event\course_module_updated $event
     */
    public static function course_module_updated(\core\event\course_module_updated $event) {
        if ($courseformat = self::istopcoll($event->courseid)) {
            \format_topcoll\activity::moduleupdated($event->objectid, $event->courseid, $courseformat);
        }
    }

    /**
     * Observer for the course_module_deleted event.
     *
     * @param \core\event\course_module_deleted $event
     */
    public static function course_module_deleted(\core\event\course_module_deleted $event) {
        if ($courseformat = self::istopcoll($event->courseid)) {
            \format_topcoll\activity::moduledeleted($event->objectid, $event->courseid, $courseformat);
        }
    }

    /**
     * Is the course using the Collapsed Topics course format?
     *
     * @param int $courseid Course id.
     *
     * @return object | bool format_topcoll object or false if not a Collapsed Topics course.
     */
    private static function istopcoll($courseid) {
        $courseformat = course_get_format($courseid);
        if ($courseformat instanceof format_topcoll) {
            return $courseformat;
        }
        return false;
    }
}
