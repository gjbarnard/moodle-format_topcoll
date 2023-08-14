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
 * @version    See the value of '$plugin->version' in version.php.
 * @copyright  &copy; 2020-onwards G J Barnard in respect to modifications of Adaptable activity related functions,
 *             see below.
 * @author     G J Barnard - {@link http://moodle.org/user/profile.php?id=442195}
 * @link       http://docs.moodle.org/en/Collapsed_Topics_course_format
 * @license    http://www.gnu.org/copyleft/gpl.html GNU Public License
 *
 */

/**
 * Activity-related functions.  From the Adaptable theme.
 *
 * This defines the activity class that is used to retrieve activity-related information, such as submission status,
 * due dates etc.
 *
 * @package   format_topcoll
 * @copyright 2018 Manoj Solanki (Coventry University)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 */

namespace format_topcoll;

defined('MOODLE_INTERNAL') || die();

use \cm_info;

require_once($CFG->dirroot.'/mod/assign/locallib.php');

/**
 * Activity functions.
 *
 * These functions are in a class purely for auto loading convenience.
 *
 * @package   format_topcoll
 * @copyright Copyright (c) 2015 Moodlerooms Inc. (http://www.moodlerooms.com)
 * @copyright Copyright (c) 2017 Manoj Solanki (Coventry University)
 * @copyright Copyright (c) 2020 G J Barnard
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class activity {

    /**
     *
     * Main method that calls relevant activity-related method based on the mod name.
     *
     * @param \cm_info $mod
     * @return activity_meta
     */
    public static function module_meta(cm_info $mod) {
        $methodname = $mod->modname . '_meta';
        if (method_exists('format_topcoll\\activity', $methodname)) {
            $meta = call_user_func('format_topcoll\\activity::' . $methodname, $mod);
            if ((!empty($meta->timeclose)) && ($meta->timeclose < time())) {
                $meta->expired = true;
            }
        } else {
            $meta = null; // Return empty activity meta.
        }

        return $meta;
    }

    /**
     * Return standard meta data for module
     *
     * @param cm_info $mod
     * @param string $submitstrkey
     * @param bool $isgradeable
     *
     * @return activity_meta
     */
    protected static function std_meta(
            cm_info $mod,
            $submitstrkey,
            $isgradeable = false
        ) {

        $courseid = $mod->course;
        $meta = null;
        // If role has specific "teacher" capabilities.
        if ((has_capability('mod/assign:grade', $mod->context)) ||
            (has_capability('mod/forum:grade', $mod->context))) {
            $meta = new activity_meta();
            $meta->isteacher = true;
            $meta->submitstrkey = $submitstrkey;

            if ($mod->modname === 'assign') {
                list(
                    'participants' => $meta->numparticipants,
                    'submissions' => $meta->numsubmissions,
                    'ungraded' => $meta->numrequiregrading,
                ) = self::assign_nums($courseid, $mod);
            } else {
                $methodnsubmissions = $mod->modname.'_num_submissions';
                $methodnumungraded = $mod->modname.'_num_submissions_ungraded';

                // Do this before the rest so that the caches are populated for use.
                $meta->numparticipants = self::course_participant_count($courseid, $mod);
                if (!empty($meta->numparticipants)) {
                    // Only need to bother if there are participants!
                    if (method_exists('format_topcoll\\activity', $methodnsubmissions)) {
                        $meta->numsubmissions = call_user_func('format_topcoll\\activity::'.
                            $methodnsubmissions, $courseid, $mod);
                    }
                    if (method_exists('format_topcoll\\activity', $methodnumungraded)) {
                        $meta->numrequiregrading = call_user_func('format_topcoll\\activity::'.
                            $methodnumungraded, $courseid, $mod);
                    }
                    if ($mod->modname === 'forum') {
                        /* Forum has number of students who have 'posted' in 'numsubmissions'
                           and the number of students who's posts have been graded in 'numrequiregrading',
                           so we need to adjust things. */
                        $meta->numrequiregrading = $meta->numsubmissions - $meta->numrequiregrading;
                    }
                }
            }
        } else if ($isgradeable) {
            $graderow = self::grade_row($courseid, $mod);

            if ($graderow) {
                $coursecontext = \context_course::instance($courseid);
                if (has_capability('moodle/grade:viewhidden', $coursecontext)) {
                    $meta = new activity_meta();
                    $meta->grade = true;
                } else {
                    global $USER;

                    $gradeitem = \grade_item::fetch(array(
                        'itemtype' => 'mod',
                        'itemmodule' => $mod->modname,
                        'iteminstance' => $mod->instance,
                        'outcomeid' => null
                    ));

                    $grade = new \grade_grade(array('itemid' => $gradeitem->id, 'userid' => $USER->id));
                    if (!$grade->is_hidden()) {
                        $meta = new activity_meta();
                        $meta->grade = true;
                    }
                }
            }
        }

        return $meta;
    }

    /**
     * Get assignment meta data
     *
     * @param cm_info $modinst - module instance
     * @return activity_meta
     */
    protected static function assign_meta(cm_info $modinst) {
        return self::std_meta($modinst, 'submitted', true);
    }

    /**
     * Get choice module meta data
     *
     * @param cm_info $modinst - module instance
     * @return string
     */
    protected static function choice_meta(cm_info $modinst) {
        return  self::std_meta($modinst, 'answered');
    }

    /**
     * Get database module meta data
     *
     * @param cm_info $modinst - module instance
     * @return string
     */
    protected static function data_meta(cm_info $modinst) {
        return self::std_meta($modinst, 'contributed');
    }

    /**
     * Get feedback module meta data
     *
     * @param cm_info $modinst - module instance
     * @return string
     */
    protected static function feedback_meta(cm_info $modinst) {
        return self::std_meta($modinst, 'submitted');
    }

    /**
     * Get forum module meta data
     *
     * @param cm_info $modinst - module instance
     * @return string
     */
    protected static function forum_meta(cm_info $modinst) {
        global $DB;

        $params['forumid'] = $modinst->instance;
        $sql = "SELECT f.id, f.scale, f.grade_forum
                    FROM {forum} f
                    WHERE f.id = :forumid";
        $forumscale = $DB->get_records_sql($sql, $params);
        if ((!empty($forumscale[$modinst->instance])) &&
            ($forumscale[$modinst->instance]->scale > 0) &&
            ($forumscale[$modinst->instance]->grade_forum != 0)) {
            return self::std_meta($modinst, 'posted');
        }
        return null; // Whole forum grading off for this forum.
    }

    /**
     * Get lesson module meta data
     *
     * @param cm_info $modinst - module instance
     * @return string
     */
    protected static function lesson_meta(cm_info $modinst) {
        return self::std_meta($modinst, 'attempted', true);
    }

    /**
     * Get quiz module meta data
     *
     * @param cm_info $modinst - module instance
     * @return string
     */
    protected static function quiz_meta(cm_info $modinst) {
        return self::std_meta($modinst, 'attempted', true);
    }

    // The lesson_ungraded function has been removed as it was very tricky to implement.
    // This was because it creates a grade record as soon as a student finishes the lesson.

    /**
     * Standard function for getting number of submissions (where sql is not complicated and pretty much standard)
     *
     * @param int $courseid
     * @param cm_info $mod
     * @param string $maintable
     * @param string $mainkey
     * @param string $submittable
     * @param string $extraselect
     *
     * @return int
     */
    protected static function std_num_submissions(
            $courseid,
            $mod,
            $maintable,
            $mainkey,
            $submittable,
            $extraselect = '') {
        global $DB;

        static $modtotalsbyid = array();

        if (!isset($modtotalsbyid[$maintable][$courseid])) {
            // Results are not cached, so lets get them.

            // Get people who are typically not students (people who can view grader report) so that we can exclude them!
            list($graderids, $params) = get_enrolled_sql(\context_course::instance($courseid), 'moodle/grade:viewall');
            $params['courseid'] = $courseid;

            // Get the number of submissions for all $maintable activities in this course.
            $sql = "-- Snap sql
                SELECT m.id, COUNT(DISTINCT sb.userid) as totalsubmitted
                    FROM {".$maintable."} m
                    JOIN {".$submittable."} sb ON m.id = sb.$mainkey
                    WHERE m.course = :courseid
                    AND sb.userid NOT IN ($graderids)
                    $extraselect
                    GROUP BY m.id";
            $modtotalsbyid[$maintable][$courseid] = $DB->get_records_sql($sql, $params);
        }
        $totalsbyid = $modtotalsbyid[$maintable][$courseid];

        if (!empty($totalsbyid)) {
            $instanceid = $mod->instance;
            if (isset($totalsbyid[$instanceid])) {
                return intval($totalsbyid[$instanceid]->totalsubmitted);
            }
        }
        return 0;
    }

    /**
     * Get assignment number information.
     *
     * @param int $courseid
     * @param cm_info $mod
     * @return array
     */
    protected static function assign_nums($courseid, $mod) {
        // Ref: get_assign_grading_summary_renderable().
        $coursemodulecontext = \context_module::instance($mod->id);
        $course = get_course($courseid);
        $assign = new \assign($coursemodulecontext, $mod, $course);
        $activitygroup = groups_get_activity_group($mod);
        $instance = $assign->get_default_instance();
        if ($instance->teamsubmission) {
            $participants = $assign->count_teams($activitygroup);
        } else {
            $participants = $assign->count_participants($activitygroup);
        }
        $submitted = ASSIGN_SUBMISSION_STATUS_SUBMITTED;

        return array(
            'participants' => $participants,
            'submissions' => $assign->count_submissions_with_status($submitted, $activitygroup),
            'ungraded' => $assign->count_submissions_need_grading($activitygroup)
        );
    }

    /**
     * Data module function for getting number of contributions.
     *
     * @param int $courseid
     * @param cm_info $mod
     * @return int
     */
    protected static function data_num_submissions($courseid, $mod) {
        $modinstance = $mod->instance;

        static $modtotalsbyinstance = array();

        if (!isset($modtotalsbyinstance[$modinstance])) {
            global $DB;
            $params['dataid'] = $modinstance;

            // Get the number of contributions for this data activity.
            $sql = 'SELECT count(r.dataid) as total FROM {data_records} r
                        WHERE r.dataid = :dataid GROUP BY r.dataid';

            $modtotalsbyid[$modinstance] = $DB->get_records_sql($sql, $params);
        }

        return intval($modtotalsbyid[$modinstance]);
    }

    /**
     * Get number of answers for specific choice.
     *
     * @param int $courseid
     * @param cm_info $mod
     * @return int
     */
    protected static function choice_num_submissions($courseid, $mod) {
        return self::std_num_submissions($courseid, $mod, 'choice', 'choiceid', 'choice_answers');
    }

    /**
     * Get number of submissions for feedback activity.
     *
     * @param int $courseid
     * @param cm_info $mod
     * @return int
     */
    protected static function feedback_num_submissions($courseid, $mod) {
        return self::std_num_submissions($courseid, $mod, 'feedback', 'feedback', 'feedback_completed');
    }

    /**
     * Get number of students who have 'posted', then combined with knowing the number
     * submitted 'graded' then can deduce the 'ungraded'.
     *
     * @param int $courseid
     * @param cm_info $mod
     * @return int
     */
    protected static function forum_num_submissions($courseid, $mod) {
        global $DB;

        /* Get the 'discussions' id's for the forum id then see which students have
           'posted' in / started them and thus should be graded if they have not
           been. */
        $params['forumid'] = $mod->instance;
        $studentscache = \cache::make('format_topcoll', 'activitystudentscache');
        $students = $studentscache->get($courseid);
        $userids = implode(',', $students);

        $sql = "SELECT count(DISTINCT fp.userid) as total
                FROM {forum_posts} fp, {forum_discussions} fd
                WHERE fd.forum = :forumid
                AND fp.userid IN ($userids)
                AND fp.discussion = fd.id";
        $studentspostedcount = $DB->get_records_sql($sql, $params);

        if (!empty($studentspostedcount)) {
            return implode('', array_keys($studentspostedcount));
        }

        return 0;
    }

    /**
     * Get number of submissions for lesson activity.
     *
     * @param int $courseid
     * @param cm_info $mod
     * @return int
     */
    protected static function lesson_num_submissions($courseid, $mod) {
        return self::std_num_submissions($courseid, $mod, 'lesson', 'lessonid', 'lesson_timer');
    }

    /**
     * Get number of attempts for specific quiz.
     *
     * @param int $courseid
     * @param cm_info $mod
     * @return int
     */
    protected static function quiz_num_submissions($courseid, $mod) {
        return self::std_num_submissions($courseid, $mod, 'quiz', 'quiz', 'quiz_attempts');
    }

    /**
     * Get number of submissions 'graded' for forum activity when whole forum grading.
     *
     * @param int $courseid
     * @param cm_info $mod
     * @return int
     */
    protected static function forum_num_submissions_ungraded($courseid, $mod) {
        global $DB;

        $studentscache = \cache::make('format_topcoll', 'activitystudentscache');
        $students = $studentscache->get($courseid);
        $userids = implode(',', $students);

        $params['forumid'] = $mod->instance;
        /* Note: As soon as a student is graded then it appears that 'grade' changes to
                 a value, so this could be '0', thus be all 'Not set's when using a
                 scale.  Does not seem to be a way to solve this!  But then a student
                 could get nothing and 'saving' is an act of accessment. */
        $sql = "SELECT count(f.id) as total
                    FROM {forum_grades} f

                    WHERE f.userid IN ($userids)
                    AND f.grade IS NOT NULL
                    AND f.forum = :forumid";
        $studentcount = $DB->get_records_sql($sql, $params);

        if (!empty($studentcount)) {
            return implode('', array_keys($studentcount));
        }
        return 0;
    }

    /**
     * Get number of ungraded quiz attempts for specific quiz.
     *
     * @param int $courseid
     * @param cm_info $mod
     * @return int
     */
    protected static function quiz_num_submissions_ungraded($courseid, $mod) {
        global $DB;

        static $totalsbyquizid = null;

        $coursecontext = \context_course::instance($courseid);
        // Get people who are typically not students (people who can view grader report) so that we can exclude them!
        list($graderids, $params) = get_enrolled_sql($coursecontext, 'moodle/grade:viewall');
        $params['courseid'] = $courseid;

        if (!isset($totalsbyquizid)) {
            // Results are not cached.
            $sql = "-- Snap sql
                SELECT q.id, count(DISTINCT qa.userid) as total
                    FROM {quiz} q

                    -- Get ALL ungraded attempts for this quiz
                    JOIN {quiz_attempts} qa ON qa.quiz = q.id
                    AND qa.sumgrades IS NULL

                    -- Exclude those people who can grade quizzes
                    WHERE qa.userid NOT IN ($graderids)
                    AND qa.state = 'finished'
                    AND q.course = :courseid
                    GROUP BY q.id";
            $totalsbyquizid = $DB->get_records_sql($sql, $params);
        }

        if (!empty($totalsbyquizid)) {
            $quizid = $mod->instance;
            if (isset($totalsbyquizid[$quizid])) {
                return intval($totalsbyquizid[$quizid]->total);
            }
        }

        return 0;
    }

    /**
     * Return grade row for specific module instance.
     *
     * @param int $courseid
     * @param cm_info $mod
     *
     * @return bool
     */
    protected static function grade_row($courseid, $mod) {
        global $DB, $USER;

        static $grades = array();

        if (isset($grades[$courseid.'_'.$mod->modname])
            && isset($grades[$courseid.'_'.$mod->modname][$mod->instance])
            ) {
                return $grades[$courseid.'_'.$mod->modname][$mod->instance];
        }

        $sql = "-- Snap sql
            SELECT m.id AS instanceid, gg.*
                FROM {".$mod->modname."} m

                JOIN {grade_items} gi
                ON m.id = gi.iteminstance
                AND gi.itemtype = 'mod'
                AND gi.itemmodule = :modname
                AND gi.courseid = :courseid1
                AND gi.outcomeid IS NULL

                JOIN {grade_grades} gg
                ON gi.id = gg.itemid

                WHERE m.course = :courseid2
                AND gg.userid = :userid
                AND (
                    gg.rawgrade IS NOT NULL
                    OR gg.finalgrade IS NOT NULL
                    OR gg.feedback IS NOT NULL
                )
            ";
        $params = array(
            'modname' => $mod->modname,
            'courseid1' => $courseid,
            'courseid2' => $courseid,
            'userid' => $USER->id
        );
        $grades[$courseid.'_'.$mod->modname] = $DB->get_records_sql($sql, $params);

        if (isset($grades[$courseid.'_'.$mod->modname][$mod->instance])) {
            return $grades[$courseid.'_'.$mod->modname][$mod->instance];
        } else {
            return false;
        }
    }

    // Participant count code.
    /**
     * Get total participant count for a specific courseid and module.
     *
     * @param int $courseid The course id.
     * @param cm_info $mod The module.
     *
     * @return int Number of participants (students) on the module.
     */
    protected static function course_participant_count($courseid, $mod) {
        $students = self::course_get_students($courseid);

        // New users?
        $usercreatedcache = \cache::make('format_topcoll', 'activityusercreatedcache');
        $createdusers = $usercreatedcache->get($courseid);
        $lock = null;
        $newstudents = array();
        if (!empty($createdusers)) {
            $lock = self::lockcaches($courseid);

            $studentrolescache = \cache::make('format_topcoll', 'activitystudentrolescache');
            $studentroles = $studentrolescache->get('roles');
            $context = \context_course::instance($courseid);
            $alluserroles = get_users_roles($context, $createdusers, false);

            foreach ($createdusers as $userid) {
                $usershortnames = array();
                foreach ($alluserroles[$userid] as $userrole) {
                    $usershortnames[] = $userrole->shortname;
                }
                $isstudent = false;
                foreach ($studentroles as $studentrole) {
                    if (in_array($studentrole, $usershortnames)) {
                        // User is in a role that is based on a student archetype on the course.
                        $isstudent = true;
                        break;
                    }
                }
                if (!$isstudent) {
                    // Don't go any further.
                    continue;
                } else {
                    $newstudents[$userid] = $userid;
                }
            }

            $usercreatedcache->set($courseid, null);

            if (is_array($students)) {
                foreach ($newstudents as $newstudent) {
                    if (!array_key_exists($newstudent, $students)) {
                        $students[$newstudent] = $newstudent;
                    }
                }
                $studentscache = \cache::make('format_topcoll', 'activitystudentscache');
                $studentscache->set($courseid, $students);
            } else if (!empty($newstudents)) {
                $students = $newstudents;
                $studentscache = \cache::make('format_topcoll', 'activitystudentscache');
                $studentscache->set($courseid, $students);
            }
        }

        if (is_array($students)) {
            // We have students!
            $modulecountcache = \cache::make('format_topcoll', 'activitymodulecountcache');
            $modulecountcourse = $modulecountcache->get($courseid);
            if (empty($modulecountcourse)) {
                $modulecountcourse = self::calulatecoursemodules($courseid, $students);
                $modulecountcache->set($courseid, $modulecountcourse);
            } else if (!empty($newstudents)) {
                // Update.
                $modulecountcourse = self::calulatecoursemodules($courseid, $newstudents, null, $modulecountcourse);
                $modulecountcache->set($courseid, $modulecountcourse);
            }

            if (!is_null($lock)) {
                $lock->release();
            }

            return $modulecountcourse[$mod->id][0];
        }

        if (!is_null($lock)) {
            $lock->release();
        }

        return 0;
    }

    /**
     * Get students for a specific courseid.
     *
     * @param int $courseid The course id.
     *
     * @return array / string 0 or more student id's in an array or 'nostudents' string.
     */
    public static function course_get_students($courseid) {
        $studentrolescache = \cache::make('format_topcoll', 'activitystudentrolescache');
        $studentroles = $studentrolescache->get('roles');

        if (empty($studentroles)) {
            $studentarch = get_archetype_roles('student');
            $studentroles = array();
            foreach ($studentarch as $role) {
                $studentroles[] = $role->shortname;
            }
            $studentrolescache->set('roles', $studentroles);
        }

        $studentscache = \cache::make('format_topcoll', 'activitystudentscache');
        $students = $studentscache->get($courseid);
        if (empty($students)) {
            $students = array();
            $context = \context_course::instance($courseid);
            $enrolledusers = get_enrolled_users($context, '', 0, 'u.id', null, 0, 0, true);
            $users = array_keys($enrolledusers);
            $alluserroles = get_users_roles($context, $users, false);

            foreach ($users as $userid) {
                $usershortnames = array();
                foreach ($alluserroles[$userid] as $userrole) {
                    $usershortnames[] = $userrole->shortname;
                }
                $isstudent = false;
                foreach ($studentroles as $studentrole) {
                    if (in_array($studentrole, $usershortnames)) {
                        // User is in a role that is based on a student archetype on the course.
                        $isstudent = true;
                        break;
                    }
                }
                if (!$isstudent) {
                    // Don't go any further.
                    continue;
                } else {
                    $students[$userid] = $userid;
                }
            }

            if (empty($students)) {
                $studentscache->set($courseid, 'nostudents');
            } else {
                $studentscache->set($courseid, $students);
            }
        }

        return $students;
    }

    /**
     * States if the format setting for the maximum number of students has not been
     * exceeded for the specific courseid.
     *
     * @param int $courseid The course id.
     * @param boolean $extrainfo Return extra information.
     *
     * @return boolean true = it has not, false = it has /
     *         if $extrainfo then array (boolean, nostudents, maxstudents);
     */
    public static function maxstudentsnotexceeded($courseid, $extrainfo = false) {
        $notexceeded = true;
        $maxstudents = get_config('format_topcoll', 'courseadditionalmoddatamaxstudents');
        $studentcount = 0;
        if (($maxstudents != 0) || ($extrainfo)) {
            $students = self::course_get_students($courseid);
            if (is_array($students)) {
                $studentcount = count($students);
                if ($maxstudents < $studentcount) {
                    $notexceeded = false;
                }
            }
        }

        if ($extrainfo) {
            return array('notexceeded' => $notexceeded, 'nostudents' => $studentcount, 'maxstudents' => $maxstudents);
        }

        return $notexceeded;
    }

    /**
     * Invalidates the activity student roles cache.
     */
    public static function invalidatestudentrolescache() {
        $modulecountcache = \cache::make('format_topcoll', 'activitymodulecountcache');
        $modulecountcache->purge();
    }

    /**
     * Invalidates the activity module count cache.
     */
    public static function invalidatemodulecountcache() {
        $studentrolescache = \cache::make('format_topcoll', 'activitystudentrolescache');
        $studentrolescache->purge();
    }

    /**
     * Invalidates the activity students cache.
     */
    public static function invalidatestudentscache() {
        $studentscache = \cache::make('format_topcoll', 'activitystudentscache');
        $studentscache->purge();
    }

    /* TODO:
       Improve and refine these methods even further along the idea of 'regenerate the actual data
       they need to change'.
    */

    /**
     * A user has been enrolled.
     *
     * @param int $userid User id.
     * @param int $courseid Course id.
     */
    public static function userenrolmentcreated($userid, $courseid, $courseformat) {
        if (self::activitymetaenabled() && self::activitymetaused($courseformat)) {
            self::userenrolmentchanged($userid, $courseid, 1);
        }
    }

    /**
     * A user enrolment has been updated.
     *
     * @param int $userid User id.
     * @param int $courseid Course id.
     */
    public static function userenrolmentupdated($userid, $courseid, $courseformat) {
        if (self::activitymetaenabled() && self::activitymetaused($courseformat)) {
            self::userenrolmentchanged($userid, $courseid, 0);
        }
    }

    /**
     * A user has been unenrolled.
     *
     * @param int $userid User id.
     * @param int $courseid Course id.
     */
    public static function userenrolmentdeleted($userid, $courseid, $courseformat) {
        if (self::activitymetaenabled() && self::activitymetaused($courseformat)) {
            self::userenrolmentchanged($userid, $courseid, -1);
        }
    }

    /**
     * A user enrolment has changed.
     *
     * @param int $userid User id.
     * @param int $courseid Course id.
     * @param int $type -1 = deleted, 0 changed and 1 created.
     */
    private static function userenrolmentchanged($userid, $courseid, $type) {
        $lock = self::lockcaches($courseid);
        if ($type == 1) {
            // Created.
            /* Note: At the time of the event, the DB has not been updated to know that the given user has been assigned a role
                     of 'student' - role_assignments table with data relating to that contained in the event itself. */
            $usercreatedcache = \cache::make('format_topcoll', 'activityusercreatedcache');
            $createdusers = $usercreatedcache->get($courseid);
            if (empty($createdusers)) {
                $createdusers = array();
            }
            $createdusers[] = $userid;
            $usercreatedcache->set($courseid, $createdusers);
        } else if ($type == -1) {
            // Deleted.
            $studentscache = \cache::make('format_topcoll', 'activitystudentscache');
            $students = $studentscache->get($courseid);
            if (!empty($students)) {
                if (array_key_exists($userid, $students)) {
                    unset($students[$userid]);
                    $studentscache->set($courseid, $students);
                    $modulecountcache = \cache::make('format_topcoll', 'activitymodulecountcache');
                    $modulecountcourse = $modulecountcache->get($courseid);
                    if (empty($modulecountcourse)) {
                        if (!empty($students)) {
                            $modulecountcourse = self::calulatecoursemodules($courseid, $students);
                            $modulecountcache->set($courseid, $modulecountcourse);
                        }
                    } else {
                        $modulecountcoursekeys = array_keys($modulecountcourse);
                        foreach ($modulecountcoursekeys as $modid) {
                            if (in_array($userid, $modulecountcourse[$modid][1])) {
                                $modulecountcourse[$modid][0]--;
                                unset($modulecountcourse[$modid][1][$userid]);
                            }
                        }
                        $modulecountcache->set($courseid, $modulecountcourse);
                    }
                }
            } // Else no students no problem.
        }
        $lock->release();
    }

    /**
     * A module has been created.
     *
     * @param int $modid Module id.
     * @param int $courseid Course id.
     */
    public static function modulecreated($modid, $courseid, $courseformat) {
        self::modulechanged($modid, $courseid, $courseformat);
    }

    /**
     * A module has been updated.
     *
     * @param int $modid Module id.
     * @param int $courseid Course id.
     */
    public static function moduleupdated($modid, $courseid, $courseformat) {
        self::modulechanged($modid, $courseid, $courseformat);
    }

    /**
     * A module has changed.
     *
     * @param int $modid Module id.
     * @param int $courseid Course id.
     */
    private static function modulechanged($modid, $courseid, $courseformat) {
        if (self::activitymetaenabled() && self::activitymetaused($courseformat)) {
            $lock = self::lockcaches($courseid);
            $studentscache = \cache::make('format_topcoll', 'activitystudentscache');
            $students = $studentscache->get($courseid);
            if (is_array($students)) {
                $modulecountcache = \cache::make('format_topcoll', 'activitymodulecountcache');
                $modulecountcourse = $modulecountcache->get($courseid);
                if (!empty($modulecountcourse)) {
                    $updated = self::calulatecoursemodules($courseid, $students, $modid);
                    $modulecountcourse[$modid] = $updated[$modid];
                    $modulecountcache->set($courseid, $modulecountcourse);
                }
            }
            $lock->release();
        }
    }

    /**
     * A module has been deleted.
     *
     * @param int $modid Module id.
     * @param int $courseid Course id.
     */
    public static function moduledeleted($modid, $courseid, $courseformat) {
        if (self::activitymetaenabled() && self::activitymetaused($courseformat)) {
            $lock = self::lockcaches($courseid);
            $modulecountcache = \cache::make('format_topcoll', 'activitymodulecountcache');
            $modulecountcourse = $modulecountcache->get($courseid);
            if (!empty($modulecountcourse)) {
                unset($modulecountcourse[$modid]);
                $modulecountcache->set($courseid, $modulecountcourse);
            }

            $lock->release();
        }
    }

    /**
     * Clear the module count cache on the given course.
     *
     * @param int $courseid Course id.
     */
    private static function clearcoursemodulecount($courseid) {
        $lock = self::lockcaches($courseid);
        $modulecountcache = \cache::make('format_topcoll', 'activitymodulecountcache');
        $modulecountcache->set($courseid, null);
        $studentscache = \cache::make('format_topcoll', 'activitystudentscache');
        $studentscache->set($courseid, null);
        $lock->release();
    }

    /**
     * Clear the module count cache on the given course.
     *
     * @param int $courseid Course id.
     * @param array $students Array of student id's on the course.
     * @param int $modid Calculate specific module id or null if calculate all.
     * @param array $modulecount Existing module count if any.
     *
     * @return int Number of participants (students) on the modules requested on the course.
     */
    private static function calulatecoursemodules($courseid, $students, $modid = null, $modulecount = null) {
        if (is_null($modulecount)) {
            if (is_null($modid)) {
                // Initialise to zero in case of no enrolled students on the course.
                $modinfo = get_fast_modinfo($courseid, -1);
                $cms = $modinfo->get_cms(); // Array of cm_info objects.
                foreach ($cms as $themod) {
                    $modulecount[$themod->id] = array(0, array());
                }
            } else {
                $modulecount[$modid] = array(0, array());
            }
        }
        foreach ($students as $userid) {
            $modinfo = get_fast_modinfo($courseid, $userid);
            $cms = $modinfo->get_cms(); // Array of cm_info objects for the user on the course.
            foreach ($cms as $usermod) {
                if ((!is_null($modid)) && ($modid != $usermod->id)) {
                    continue;
                }
                // From course_section_cm() in M3.8 - is_visible_on_course_page for M3.9+.
                if (($usermod->is_visible_on_course_page()) || (!empty($usermod->availableinfo) && ($usermod->url))) {
                    // From course_section_cm_name_title().
                    if ($usermod->uservisible) {
                        $modulecount[$usermod->id][0]++;
                        $modulecount[$usermod->id][1][] = $userid;
                    }
                }
            }
        }

        return $modulecount;
    }

    /**
     * Get a lock for the caches on the given course.
     *
     * @param int $courseid Course id.
     *
     * @return object The lock to release when complete.
     */
    private static function lockcaches($courseid) {
        $lockfactory = \core\lock\lock_config::get_lock_factory('format_topcoll');
        if ($lock = $lockfactory->get_lock('courseid'.$courseid, 5)) {
            return $lock;
        }
        throw new \moodle_exception('cannotgetactivitycacheslock', 'format_topcoll', '',
            get_string('cannotgetactivitycacheslock', 'format_topcoll', $courseid));
    }

    /**
     * State if the site has activity meta enabled.
     *
     * @return boolean True or False.
     */
    public static function activitymetaenabled() {
        return (get_config('format_topcoll', 'enableadditionalmoddata') == 2);
    }

    /**
     * State if the course has activity meta enabled.
     *
     * @param int $courseformat Course format for the course.
     *
     * @return boolean True or False.
     */
    public static function activitymetaused($courseformat) {
        $tcsettings = $courseformat->get_settings();
        if ((!empty($tcsettings['showadditionalmoddata'])) && ($tcsettings['showadditionalmoddata'] == 2)) {
            return true; // Could in theory test the module but then this method wouldn't work for user events.
        }
        return false;
    }
}
