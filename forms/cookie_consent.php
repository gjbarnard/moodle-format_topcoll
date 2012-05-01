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
require_once('../../../../config.php');
require_once('../lib.php');
require_once('./cookie_consent_form.php');
require_once('../config.php');

defined('MOODLE_INTERNAL') || die();

$courseid = required_param('courseid', PARAM_INT); // course id
$userid = required_param('userid', PARAM_INT); // user id

if (!($course = $DB->get_record('course', array('id' => $courseid)))) {
    print_error('invalidcourseid', 'error');
} // From /course/view.php

preload_course_contexts($courseid); // From /course/view.php
if (!$coursecontext = get_context_instance(CONTEXT_COURSE, $course->id)) {
    print_error('nocontext');
}
require_login($course); // From /course/view.php - Facilitates the correct population of the setttings block.

$PAGE->set_context($coursecontext);
$PAGE->set_url('/course/format/topcoll/forms/cookie_consent.php', array('courseid' => $courseid, 'userid' => $userid, 'sesskey' => sesskey())); // From /course/view.php
$PAGE->set_pagelayout('course'); // From /course/view.php
$PAGE->set_pagetype('course-view-topcoll'); // From /course/view.php
$PAGE->set_other_editing_capability('moodle/course:manageactivities'); // From /course/view.php
$PAGE->set_title(get_string('cookieconsentform','format_topcoll'));
$PAGE->set_heading(get_string('cookieconsentform','format_topcoll'));

require_sesskey();
require_capability('moodle/course:update', $coursecontext);

$courseurl = new moodle_url('/course/view.php', array('id' => $courseid));

$mform = new set_cookie_consent_form(null, array('courseid' => $courseid, 'userid' => $userid ));

if ($mform->is_cancelled()) {
    redirect($courseurl);
} else if ($formdata = $mform->get_data()) {
    put_topcoll_cookie_consent($userid,$formdata->setcookieconsent);
    redirect($courseurl);
}

echo $OUTPUT->header();
echo $OUTPUT->box_start('generalbox');
$mform->display();
echo $OUTPUT->box_end();
echo $OUTPUT->footer();
