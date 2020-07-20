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
 * Duplicate resources on a section as a new section adapted from code written by:
 * @copyright 2015 David Herney Bernal - cirano - in format_onetopic.
 *
 * @package    format_topcoll
 * @version    See the value of '$plugin->version' in version.php.
 * @copyright  &copy; 2020-onwards G J Barnard.
 * @author     G J Barnard - gjbarnard at gmail dot com and {@link http://moodle.org/user/profile.php?id=442195}
 * @link       http://docs.moodle.org/en/Collapsed_Topics_course_format
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define('NO_OUTPUT_BUFFERING', true);

require_once('../../../config.php');
require_once($CFG->dirroot.'/course/lib.php');

$courseid = required_param('courseid', PARAM_INT);
$sectionno = required_param('sectionno', PARAM_INT);
$course = $DB->get_record('course', array('id' => $courseid), '*', MUST_EXIST);

$PAGE->set_url('/course/format/topcoll/duplicate.php', array('courseid' => $courseid, 'sectionno' => $sectionno));

// Authorization checks.
require_login($course);
$context = context_course::instance($course->id);
require_capability('moodle/course:update', $context);
require_capability('moodle/course:manageactivities', $context);
require_sesskey();

$courseformat = course_get_format($course);
$course = $courseformat->get_course();
$modinfo = get_fast_modinfo($course);
$sectioninfo = $modinfo->get_section_info($sectionno);
$context = context_course::instance($course->id);
$numnewsection = null;

$PAGE->set_pagelayout('course');
$PAGE->set_heading($course->fullname);

$PAGE->set_title(get_string('coursetitle', 'moodle', array('course' => $course->fullname)));

echo $OUTPUT->header();

if (!empty($sectioninfo)) {
    $pbar = new progress_bar('topcoll_duplicate_bar', 500, true);
    $pbar->update_full(1, get_string('duplicating', 'format_topcoll'));

    $lastsectionnum = $DB->get_field('course_sections', 'MAX(section)', array('course' => $courseid), MUST_EXIST);

    $numnewsection = $lastsectionnum + 1;

    $pbar->update_full(5, get_string('creatingsection', 'format_topcoll'));

    // Assign same section info.
    $data = new stdClass();
    $data->course = $sectioninfo->course;
    $data->section = $numnewsection;
    // The name is not duplicated.
    $data->summary = $sectioninfo->summary;
    $data->summaryformat = $sectioninfo->summaryformat;
    $data->visible = $sectioninfo->visible;
    $data->availability = $sectioninfo->availability;

    $newsectionid = $DB->insert_record('course_sections', $data, true);

    // Update 'numsections'.
    $courseformatdata = array('numsections' => $numnewsection);
    $courseformat->update_course_format_options($courseformatdata);

    try {
        $fs = get_file_storage();
        $files = $fs->get_area_files($context->id, 'course', 'section', $sectioninfo->id);

        if ($files && is_array($files)) {
            $fileinfo = array(
                'component' => 'course',
                'filearea' => 'section'
            );
            foreach ($files as $file) {
                $fileinfo['contextid'] = $context->id;
                $fileinfo['itemid'] = $newsectionid;

                $fs->create_file_from_storedfile($fileinfo, $file);
            }
        }
    } catch (Exception $e) {
        debugging('Error copying section files.'.$e->getMessage(), DEBUG_DEVELOPER);
    }

    $moved = move_section_to($course, $numnewsection, $sectionno + 1);
    if ($moved) {
        $numnewsection = $sectionno + 1;
    }

    $formatoptions = $courseformat->get_format_options($sectionno);
    if (is_array($formatoptions) && count($formatoptions) > 0) {
        $formatoptions['id'] = $newsectionid;
        $courseformat->update_section_format_options($formatoptions);
    }

    // Trigger an event for course section update.
    $event = \core\event\course_section_updated::create(
        array(
            'objectid' => $newsectionid,
            'courseid' => $course->id,
            'context' => $context,
            'other' => array('sectionnum' => $numnewsection)
        )
    );
    $event->trigger();

    $course = course_get_format($course)->get_course();
    $modinfo = get_fast_modinfo($course);

    $pbar->update_full(10, get_string('rebuildcoursecache', 'format_topcoll'));
    $newsectioninfo = $modinfo->get_section_info($numnewsection);
    $modules = array();

    if (is_object($modinfo) && isset($modinfo->sections[$sectionno])) {
        $sectionmods = $modinfo->sections[$sectionno];

        if (is_array($sectionmods)) {
            $progressbarelements = count($sectionmods);
            $dataprogress = new stdClass();
            $dataprogress->current = 0;
            $dataprogress->size = $progressbarelements;
            $k = 0;
            $pbar->update_full(40, get_string('progresscounter', 'format_topcoll', $dataprogress));
            foreach ($sectionmods as $modnumber) {
                $k++;
                $mod = $modinfo->cms[$modnumber];
                $cm  = get_coursemodule_from_id('', $mod->id, 0, true, MUST_EXIST);

                $modcontext = context_module::instance($cm->id);
                if (has_capability('moodle/course:manageactivities', $modcontext)) {
                    // Duplicate the module.
                    $newcm = duplicate_module($course, $cm);

                    // Move new module to new section.
                    if ($newcm && is_object($newcm)) {
                        moveto_module($newcm, $newsectioninfo);
                    }
                }
                $dataprogress->current = $k;
                $percent = 40 + ($k / $progressbarelements) * 60;
                $pbar->update_full($percent, get_string('progresscounter', 'format_topcoll', $dataprogress));
            }
        }
    } else {
        $pbar->update_full(100, get_string('progressfull', 'format_topcoll'));
    }

    $sectiontogo = $numnewsection;
} else {
    $sectiontogo = $sectionno;
    echo get_string('errornosectioninfo', 'format_topcoll');
    echo $OUTPUT->continue_button(course_get_url($course, $sectionno));
    echo $OUTPUT->footer();
}

echo $OUTPUT->continue_button(course_get_url($course, $numnewsection));
echo $OUTPUT->footer();
