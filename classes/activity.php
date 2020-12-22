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
        } else {
            $meta = new activity_meta(); // Return empty activity meta.
        }

        if ((!empty($meta->timeclose)) && ($meta->timeclose < time())) {
            $meta->expired = true;
        }

        return $meta;
    }

    /**
     * Return standard meta data for module
     *
     * @param cm_info $mod
     * @param string $timeopenfld
     * @param string $timeclosefld
     * @param string $keyfield
     * @param string $submissiontable
     * @param string $submittedonfld
     * @param string $submitstrkey
     * @param bool $isgradeable
     * @param string $submitselect - sql to further filter submission row select statement - e.g. st.status='finished'
     * @param bool $submissionnotrequired
     * @return activity_meta
     */
    protected static function std_meta(cm_info $mod,
            $timeopenfld,
            $timeclosefld,
            $keyfield,
            $submissiontable,
            $submittedonfld,
            $submitstrkey,
            $isgradeable = false,
            $submitselect = '',
            $submissionnotrequired = false
            ) {
        global $USER;

        $courseid = $mod->course;

        // Create meta data object.  Use set_default for when is_set(true) so that only changed metas are valid.
        $meta = new activity_meta();

        // If module is not visible to the user then don't bother getting meta data.
        if (!$mod->uservisible) {
            return $meta;
        }

        $meta->set_default('submissionnotrequired', $submissionnotrequired);
        $meta->set_default('submitstrkey', $submitstrkey);
        $meta->set_default('submittedstr', get_string($submitstrkey, 'format_topcoll'));
        $meta->set_default('notsubmittedstr', get_string('not'.$submitstrkey, 'format_topcoll'));
        $meta->set_default('draftstr', get_string('draft', 'format_topcoll'));
        $meta->set_default('reopenedstr', get_string('reopened', 'format_topcoll'));
        $meta->set_default('expiredstr', get_string('expired', 'format_topcoll'));

        $activitydates = self::instance_activity_dates($courseid, $mod, $timeopenfld, $timeclosefld);
        $meta->timeopen = $activitydates->timeopen;
        $meta->timeclose = $activitydates->timeclose;
        if (isset($activitydates->extension)) {
            $meta->extension = $activitydates->extension;
        }

        // If role has specific "teacher" capabilities.
        if (has_capability('mod/assign:grade', $mod->context)) {
            $meta->isteacher = true;

            if ($mod->modname === 'assign') {
                list(
                    'participants' => $meta->numparticipants,
                    'submissions' => $meta->numsubmissions,
                    'ungraded' => $meta->numrequiregrading,
                ) = self::assign_nums($courseid, $mod);
            } else {
                // Teacher - useful teacher meta data.
                $methodnsubmissions = $mod->modname.'_num_submissions';
                $methodnumgraded = $mod->modname.'_num_submissions_ungraded';
                $methodparticipants = $mod->modname.'_num_participants';

                if (method_exists('format_topcoll\\activity', $methodnsubmissions)) {
                    $meta->numsubmissions = call_user_func('format_topcoll\\activity::'.
                        $methodnsubmissions, $courseid, $mod);
                }
                if (method_exists('format_topcoll\\activity', $methodnumgraded)) {
                    $meta->numrequiregrading = call_user_func('format_topcoll\\activity::'.
                        $methodnumgraded, $courseid, $mod);
                }
                if (method_exists('format_topcoll\\activity', $methodparticipants)) {
                    $meta->numparticipants = call_user_func('format_topcoll\\activity::'.
                        $methodparticipants, $courseid, $mod);
                } else {
                    $meta->numparticipants = self::course_participant_count($courseid, $mod);
                }
            }

        } else {
            // Student - useful student meta data - only display if activity is available.
            if (empty($activitydates->timeopen) || $activitydates->timeopen <= time()) {  // TODO User time needed???

                $submissionrow = self::get_submission_row($courseid, $mod, $submissiontable, $keyfield, $submitselect);

                if (!empty($submissionrow)) {
                    if ($mod->modname === 'assign' && !empty($submissionrow->status)) {
                        switch ($submissionrow->status) {
                            case ASSIGN_SUBMISSION_STATUS_DRAFT:
                                $meta->draft = true;
                                break;

                            case ASSIGN_SUBMISSION_STATUS_REOPENED:
                                $meta->reopened = true;
                                break;

                            case ASSIGN_SUBMISSION_STATUS_SUBMITTED:
                                $meta->submitted = true;
                                break;
                        }
                    } else {
                        $meta->submitted = true;
                        $meta->timesubmitted = !empty($submissionrow->$submittedonfld) ? $submissionrow->$submittedonfld : null;
                    }
                    // If submitted on field uses modified field then fall back to timecreated if modified is 0.
                    if (empty($meta->timesubmitted) && $submittedonfld = 'timemodified') {
                        if (isset($submissionrow->timemodified)) {
                            $meta->timesubmitted = $submissionrow->timemodified;
                        } else {
                            $meta->timesubmitted = $submissionrow->timecreated;
                        }
                    }
                } else if ($mod->modname === 'assign') {
                    return null;
                }
            }

            $graderow = false;
            if ($isgradeable) {
                $graderow = self::grade_row($courseid, $mod);
            }

            if ($graderow) {
                $gradeitem = \grade_item::fetch(array(
                        'itemtype' => 'mod',
                        'itemmodule' => $mod->modname,
                        'iteminstance' => $mod->instance,
                        'outcomeid' => null
                ));

                $grade = new \grade_grade(array('itemid' => $gradeitem->id, 'userid' => $USER->id));

                $coursecontext = \context_course::instance($courseid);
                $canviewhiddengrade = has_capability('moodle/grade:viewhidden', $coursecontext);

                if (!$grade->is_hidden() || $canviewhiddengrade) {
                    $meta->grade = true;
                }
            }
        }

        if (!empty($meta->timeclose)) {
            // Submission required?
            $subreqd = empty($meta->submissionnotrequired);

            // Overdue?
            $meta->overdue = $subreqd && empty($meta->submitted) && (time() > $meta->timeclose);
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
        global $DB, $USER;
        static $submissionsenabled;

        $courseid = $modinst->course;

        /* Get count of enabled submission plugins grouped by assignment id.
           Note, under normal circumstances we only run this once but with PHP unit tests, assignments are being
           created one after the other and so this needs to be run each time during a PHP unit test. */
        if (empty($submissionsenabled) || PHPUNIT_TEST) {
            $sql = "SELECT a.id, count(1) AS submissionsenabled
                      FROM {assign} a
                      JOIN {assign_plugin_config} ac ON ac.assignment = a.id
                     WHERE a.course = ?
                       AND ac.name='enabled'
                       AND ac.value = '1'
                       AND ac.subtype='assignsubmission'
                       AND plugin!='comments'
                  GROUP BY a.id;";
            $submissionsenabled = $DB->get_records_sql($sql, array($courseid));
        }

        $submitselect = '';

        // If there aren't any submission plugins enabled for this module, then submissions are not required.
        if (empty($submissionsenabled[$modinst->instance])) {
            $submissionnotrequired = true;
        } else {
            $submissionnotrequired = false;
        }

        $meta = self::std_meta($modinst, 'allowsubmissionsfromdate', 'duedate', 'assignment', 'submission',
            'timemodified', 'submitted', true, $submitselect, $submissionnotrequired);

        if (!empty($meta)) {
            // Check assignment due date in user and group overrides.
            $context = \context_module::instance($modinst->id);
            $assign = new \assign($context, $modinst, $courseid);
            $assign->update_effective_access($USER->id);
            $submissionstatus = $assign->get_assign_submission_status_renderable($USER, false);
            if (!empty($submissionstatus->duedate) && $submissionstatus->duedate != $meta->timeclose) {
                $meta->timeclose = $submissionstatus->duedate;
            }
        }

        return ($meta);
    }

    /**
     * Get choice module meta data
     *
     * @param cm_info $modinst - module instance
     * @return string
     */
    protected static function choice_meta(cm_info $modinst) {
        return  self::std_meta($modinst, 'timeopen', 'timeclose', 'choiceid', 'answers', 'timeseen', 'answered');
    }

    /**
     * Get database module meta data
     *
     * @param cm_info $modinst - module instance
     * @return string
     */
    protected static function data_meta(cm_info $modinst) {
        return self::std_meta($modinst, 'timeavailablefrom', 'timeavailableto', 'dataid', 'records', 'timemodified', 'contributed');
    }

    /**
     * Get feedback module meta data
     *
     * @param cm_info $modinst - module instance
     * @return string
     */
    protected static function feedback_meta(cm_info $modinst) {
        return self::std_meta($modinst, 'timeopen', 'timeclose', 'feedback', 'completed', 'timemodified', 'submitted');
    }

    /**
     * Get lesson module meta data
     *
     * @param cm_info $modinst - module instance
     * @return string
     */
    protected static function lesson_meta(cm_info $modinst) {
        $meta = self::std_meta($modinst, 'available', 'deadline', 'lessonid', 'timer', 'lessontime', 'attempted', true);
        // TO BE DELETED: $meta->submissionnotrequired = true; ..........
        return $meta;
    }

    /**
     * Get quiz module meta data
     *
     * @param cm_info $modinst - module instance
     * @return string
     */
    protected static function quiz_meta(cm_info $modinst) {
        return self::std_meta($modinst, 'timeopen', 'timeclose', 'quiz',
            'attempts', 'timemodified', 'attempted', true, 'AND st.state=\'finished\'');
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
        global $DB;

        $modid = $mod->id;

        static $modtotalsbyid = array();

        if (!isset($modtotalsbyid['data'][$modid])) {
            $params['dataid'] = $modid;

            // Get the number of contributions for this data activity.
            $sql = '
             SELECT d.id, count(dataid) as total FROM {data_records} r, {data} d
                WHERE r.dataid = d.id AND r.dataid = :dataid GROUP BY d.id';

            $modtotalsbyid['data'][$modid] = $DB->get_records_sql($sql, $params);
        }
        $totalsbyid = $modtotalsbyid['data'][$modid];
        // TO BE DELETED echo '<br>' . print_r($totalsbyid, 1) . '<br>'; ....
        if (!empty($totalsbyid)) {
            if (isset($totalsbyid[$modid])) {
                return intval($totalsbyid[$modid]->total);
            }
        }
        return 0;
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
     * Get number of ungraded quiz attempts for specific quiz.
     *
     * @param int $courseid
     * @param cm_info $mod
     * @return int
     */
    protected static function quiz_num_submissions_ungraded($courseid, $mod) {
        global $DB;

        static $totalsbyquizid;

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
     * Get activity submission row.
     *
     * This method gets all of the possible submission rows for the user for the given module type
     * and then caches them so that there is only one database read for the user per module type
     * regardless of the number on the course.
     *
     * @param int $courseid
     * @param cm_info $mod
     * @param string $submissiontable
     * @param string $modfield
     * @param string $extraselect
     *
     * @return mixed
     */
    protected static function get_submission_row($courseid, $mod, $submissiontable, $modfield, $extraselect='') {
        global $DB, $USER;

        // Note: Caches all submissions to minimise database transactions.
        static $submissions = array();

        // Pull from cache?
        if (!PHPUNIT_TEST) {
            if (isset($submissions[$courseid.'_'.$mod->modname])) {
                if (isset($submissions[$courseid.'_'.$mod->modname][$mod->instance])) {
                    return $submissions[$courseid.'_'.$mod->modname][$mod->instance];
                } else {
                    return false;
                }
            }
        }

        $submissiontable = $mod->modname.'_'.$submissiontable;

        if ($mod->modname === 'assign') {
            $params = [$courseid];
            $sql = "-- Snap sql
                SELECT a.id, st.*
                    FROM {".$submissiontable."} st

                    JOIN {".$mod->modname."} a
                    ON a.id = st.$modfield

                    WHERE a.course = ?
                    AND st.latest = 1 $extraselect
                    ORDER BY $modfield DESC, st.id DESC";
        } else {
            // Less effecient general purpose for other module types.
            $params = [$USER->id, $courseid, $USER->id];
            $sql = "-- Snap sql
                SELECT a.id AS instanceid, st.*
                    FROM {".$submissiontable."} st

                    JOIN {".$mod->modname."} a
                    ON a.id = st.$modfield

                    -- Get only the most recent submission.
                    JOIN (SELECT $modfield AS modid, MAX(id) AS maxattempt
                    FROM {".$submissiontable."}
                    WHERE userid = ?
                    GROUP BY $modfield) AS smx
                    ON smx.modid = st.$modfield
                    AND smx.maxattempt = st.id

                    WHERE a.course = ?
                    AND st.userid = ? $extraselect
                    ORDER BY $modfield DESC, st.id DESC";
        }

        /* Not every activity has a status field...
           Add one if it is missing so code assuming there is a status property doesn't explode. */
        $results = $DB->get_records_sql($sql, $params);
        if (!$results) {
            unset($submissions[$courseid.'_'.$mod->modname]);
            return false;
        }

        foreach ($results as $r) {
            if (!isset($r->status)) {
                $r->status = null;
            }
        }

        if ($mod->modname === 'assign') {
            /* Assignment submissions can either be against the user's id or a group they are in.
               Make a simple list of the groups the user is in. */
            $usergroups = array();
            foreach ($USER->groupmember as $grouparray) {
                foreach ($grouparray as $group) {
                    $usergroups[] = $group;
                }
            }

            $theresults = array();
            foreach ($results as $r) {
                if (!empty($r->userid)) { // User id of 0 means that there should be a groupid.
                    if ($r->userid == $USER->id) { // This record is for us.
                        $theresults[$r->assignment] = $r;
                    }
                } else if (!empty($r->groupid)) {
                    if (in_array($r->groupid, $usergroups)) { // This record is in one of our groups.
                        $theresults[$r->assignment] = $r;
                    }
                }
            }
        } else {
            $theresults = $results;
        }

        $submissions[$courseid.'_'.$mod->modname] = $theresults;

        if (isset($submissions[$courseid.'_'.$mod->modname][$mod->instance])) {
            return $submissions[$courseid.'_'.$mod->modname][$mod->instance];
        } else {
            return false;
        }
    }

    /**
     * Get the activity dates for a specific module instance.
     *
     * @param int $courseid
     * @param cm_info $mod
     * @param string $timeopenfld
     * @param string $timeclosefld
     *
     * @return bool|stdClass
     */
    protected static function instance_activity_dates($courseid, $mod, $timeopenfld = '', $timeclosefld = '') {
        global $DB, $USER;
        // Note: Caches all moduledates to minimise database transactions.
        static $moddates = array();
        if (!isset($moddates[$courseid . '_' . $mod->modname][$mod->instance]) || PHPUNIT_TEST) {
            $timeopenfld = $mod->modname === 'quiz' ? 'timeopen' : ($mod->modname === 'lesson' ? 'available' : $timeopenfld);
            $timeclosefld = $mod->modname === 'quiz' ? 'timeclose' : ($mod->modname === 'lesson' ? 'deadline' : $timeclosefld);
            $sql = "-- Snap sql
            SELECT
            module.id,
            module.$timeopenfld AS timeopen,
            module.$timeclosefld AS timeclose";
            if ($mod->modname === 'assign') {
                $sql .= ",
                    auf.extensionduedate AS extension
                ";
            }
            if ($mod->modname === 'quiz' || $mod->modname === 'lesson') {
                $id = $mod->modname === 'quiz' ? $mod->modname : 'lessonid';
                $groups = groups_get_user_groups($courseid);
                $groupbysql = '';
                $params = array();
                if ($groups[0]) {
                    list ($groupsql, $params) = $DB->get_in_or_equal($groups[0]);
                    if ($DB->get_dbfamily() === 'mysql') {
                        $sql .= ",
                        CASE
                        WHEN ovrd1.$timeopenfld IS NULL
                        THEN MIN(ovrd2.$timeopenfld)
                        ELSE ovrd1.$timeopenfld
                        END AS timeopenover,
                        CASE
                        WHEN ovrd1.$timeclosefld IS NULL
                        THEN MAX(ovrd2.$timeclosefld)
                        ELSE ovrd1.$timeclosefld
                        END AS timecloseover
                        FROM {" . $mod->modname . "} module";
                    } else {
                        $sql .= ",
                        MIN (
                        CASE
                        WHEN ovrd1.$timeopenfld IS NULL
                        THEN ovrd2.$timeopenfld
                        ELSE ovrd1.$timeopenfld
                        END
                        ) AS timeopenover,
                        MAX (
                        CASE
                        WHEN ovrd1.$timeclosefld IS NULL
                        THEN ovrd2.$timeclosefld
                        ELSE ovrd1.$timeclosefld
                        END
                        ) AS timecloseover
                        FROM {" . $mod->modname . "} module";
                    }
                    array_unshift($params, $USER->id); // Add userid to start of params.
                    $sql .= "
                        LEFT JOIN {" . $mod->modname . "_overrides} ovrd1
                        ON module.id=ovrd1.$id
                        AND ovrd1.userid = ?
                        LEFT JOIN {" . $mod->modname . "_overrides} ovrd2
                        ON module.id=ovrd2.$id
                        AND ovrd2.groupid $groupsql";
                    $groupbysql = "
                    GROUP BY module.id, module.$timeopenfld, module.$timeclosefld";

                } else {
                    $params[] = $USER->id;
                    $sql .= ", ovrd1.$timeopenfld AS timeopenover, ovrd1.$timeclosefld AS timecloseover
                    FROM {" . $mod->modname . "} module
                             LEFT JOIN {" . $mod->modname . "_overrides} ovrd1
                             ON module.id=ovrd1.$id AND ovrd1.userid = ?";
                }
                $sql .= " WHERE module.course = ?";
                $sql .= $groupbysql;
                $params[] = $courseid;
                $result = $DB->get_records_sql($sql, $params);
            } else {
                $params = [];
                $sql .= "  FROM {" . $mod->modname . "} module";
                if ($mod->modname === 'assign') {
                    $params[] = $USER->id;
                    $sql .= "
                      LEFT JOIN {assign_user_flags} auf
                             ON module.id = auf.assignment
                            AND auf.userid = ?
                     ";
                }
                $params[] = $courseid;
                $sql .= " WHERE module.course = ?";
                $result = $DB->get_records_sql($sql, $params);
            }
            $moddates[$courseid . '_' . $mod->modname] = $result;
        }
        $modinst = $moddates[$courseid.'_'.$mod->modname][$mod->instance];
        if (!empty($modinst->timecloseover)) {
            $modinst->timeclose = $modinst->timecloseover;
            if ($modinst->timeopenover) {
                $modinst->timeopen = $modinst->timeopenover;
            }
        }
        return $modinst;

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

    /**
     * Get total participant count for specific courseid and module.
     *
     * @param int $courseid
     * @param cm_info $mod
     *
     * @return int
     */
    protected static function course_participant_count($courseid, $mod) {
        static $modulecount = array();  // 3D array on course id then module id.
        static $studentroles = null;
        if (empty($studentroles)) {
            $studentarch = get_archetype_roles('student');
            $studentroles = array();
            foreach ($studentarch as $role) {
                $studentroles[] = $role->shortname;
            }
        }

        if (!isset($modulecount[$courseid])) {
            $modulecount[$courseid] = array();

            // Initialise to zero in case of no enrolled students on the course.
            $modinfo = get_fast_modinfo($courseid, -1);
            $cms = $modinfo->get_cms(); // Array of cm_info objects.
            foreach ($cms as $themod) {
                $modulecount[$courseid][$themod->id] = 0;
            }

            $context = \context_course::instance($courseid);
            $users = get_enrolled_users($context, '', 0, 'u.id', null, 0, 0, true);
            $users = array_keys($users);
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
                }

                $modinfo = get_fast_modinfo($courseid, $userid);
                $cms = $modinfo->get_cms(); // Array of cm_info objects for the user on the course.
                foreach ($cms as $usermod) {
                    // From course_section_cm() in M3.8 - is_visible_on_course_page for M3.9+.
                    if (((method_exists($usermod, 'is_visible_on_course_page')) && ($usermod->is_visible_on_course_page()))
                        || ((!empty($usermod->availableinfo)) && ($usermod->url))) {
                        // From course_section_cm_name_title().
                        if ($usermod->uservisible) {
                            $modulecount[$courseid][$usermod->id]++;
                        }
                    }
                }
            }
        }

        return $modulecount[$courseid][$mod->id];
    }
}
