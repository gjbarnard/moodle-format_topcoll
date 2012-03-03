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
require_once('../../../config.php');
require_once('./lib.php');
require_once('./set_layout_form.php');

global $DB, $PAGE;

defined('MOODLE_INTERNAL') || die();

$courseid = required_param('id', PARAM_INT); // course id
$setelement = required_param('setelement', PARAM_INT);
$setstructure = required_param('setstructure', PARAM_INT);

if (!($course = get_record('course', 'id',$courseid))) {
    print_error('invalidcourseid', 'error');
} // From /course/view.php

preload_course_contexts($courseid); // From /course/view.php
if (!$coursecontext = get_context_instance(CONTEXT_COURSE, $course->id)) {
    print_error('nocontext');
}
require_login($course); // From /course/view.php - Facilitates the correct population of the setttings block.

//$PAGE->set_context($coursecontext);
//$PAGE->set_url('/course/format/topcoll/set_layout.php&id=', array('id' => $courseid)); // From /course/view.php
//$PAGE->set_pagelayout('course'); // From /course/view.php
//$PAGE->set_pagetype('course-view-topcoll'); // From /course/view.php
//$PAGE->set_other_editing_capability('moodle/course:manageactivities'); // From /course/view.php
//$PAGE->set_title(get_string('setlayout', 'format_topcoll') . ' - ' . $course->fullname . ' ' . get_string('course'));
//$PAGE->set_heading(get_string('setlayout', 'format_topcoll') . ' - ' . $course->fullname . ' ' . get_string('course'));

require_sesskey();
require_capability('moodle/course:update', $coursecontext);

$courseurl = $CFG->wwwroot.'/course/view.php?id='.$courseid;

if (isediting($courseid)) {
    $mform = new set_layout_form(null, array('courseid' => $courseid, 'setelement' => $setelement, 'setstructure' => $setstructure));

    if ($mform->is_cancelled()) {
        redirect($courseurl);
    } else if ($formdata = $mform->get_data()) {
        put_layout($formdata->id, $formdata->set_element, $formdata->set_structure);
        redirect($courseurl);
    }

    $PAGE = page_create_object(PAGE_COURSE_VIEW, $course->id);
    $pageblocks = blocks_setup($PAGE, BLOCKS_PINNED_BOTH);

    $PAGE->print_header(get_string('setlayout', 'format_topcoll') . ' - ' . $course->fullname . ' ' . get_string('course'), null, '', null);

// Bounds for block widths
// more flexible for theme designers taken from theme config.php
$lmin = (empty($THEME->block_l_min_width)) ? 100 : $THEME->block_l_min_width;
$lmax = (empty($THEME->block_l_max_width)) ? 210 : $THEME->block_l_max_width;
$rmin = (empty($THEME->block_r_min_width)) ? 100 : $THEME->block_r_min_width;
$rmax = (empty($THEME->block_r_max_width)) ? 210 : $THEME->block_r_max_width;

define('BLOCK_L_MIN_WIDTH', $lmin);
define('BLOCK_L_MAX_WIDTH', $lmax);
define('BLOCK_R_MIN_WIDTH', $rmin);
define('BLOCK_R_MAX_WIDTH', $rmax);

$preferred_width_left = bounded_number(BLOCK_L_MIN_WIDTH, blocks_preferred_width($pageblocks[BLOCK_POS_LEFT]), BLOCK_L_MAX_WIDTH);
$preferred_width_right = bounded_number(BLOCK_R_MIN_WIDTH, blocks_preferred_width($pageblocks[BLOCK_POS_RIGHT]), BLOCK_R_MAX_WIDTH);

/// Layout the whole page as three big columns.
echo '<table id="layout-table" cellspacing="0" summary="' . get_string('layouttable') . '"><tr>';

/// The left column ...
$lt = (empty($THEME->layouttable)) ? array('left', 'middle', 'right') : $THEME->layouttable;
foreach ($lt as $column) {
    switch ($column) {
        case 'left':

            if (blocks_have_content($pageblocks, BLOCK_POS_LEFT) || $userisediting) {
                echo '<td style="width:' . $preferred_width_left . 'px" id="left-column">';
                print_container_start();
                blocks_print_group($PAGE, $pageblocks, BLOCK_POS_LEFT);
                print_container_end();
                echo '</td>';
            }

            break;
        case 'middle':
/// Start main column
            echo '<td id="middle-column">';
            print_container_start();
    $mform->display();
            print_container_end();
            echo '</td>';

            break;
        case 'right':
            // The right column
            if (blocks_have_content($pageblocks, BLOCK_POS_RIGHT) || $userisediting) {
                echo '<td style="width:' . $preferred_width_right . 'px" id="right-column">';
                print_container_start();
                blocks_print_group($PAGE, $pageblocks, BLOCK_POS_RIGHT);
                print_container_end();
                echo '</td>';
            }

            break;
    }
}
echo '</tr></table>';

    print_footer(NULL, $course);
} else {
    redirect($courseurl);
}