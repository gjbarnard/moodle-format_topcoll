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
 * Topics course format.  Display the whole course as "topics" made of modules.
 *
 * @package format_topics
 * @copyright 2006 The Open University
 * @author N.D.Freear@open.ac.uk, and others.
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir.'/filelib.php');
require_once($CFG->libdir.'/completionlib.php');

// Horrible backwards compatible parameter aliasing..
if ($topic = optional_param('ctopics', 0, PARAM_INT)) {
    $url = $PAGE->url;
    $url->param('section', $topic);
    debugging('Outdated topic param passed to course/view.php', DEBUG_DEVELOPER);
    redirect($url);
}
// End backwards-compatible aliasing..

$context = context_course::instance($course->id);

if (($marker >=0) && has_capability('moodle/course:setcurrentsection', $context) && confirm_sesskey()) {
    $course->marker = $marker;
    course_set_marker($course->id, $marker);
}

$renderer = $PAGE->get_renderer('format_topcoll');

if (!empty($displaysection) && $course->coursedisplay == COURSE_DISPLAY_MULTIPAGE) {
    $renderer->print_single_section_page($course, $sections, $mods, $modnames, $modnamesused, $displaysection);
} else {
require_once($CFG->dirroot . '/course/format/topcoll/config.php');

$PAGE->requires->js_init_call('M.format_topcoll.init', 
                               array($CFG->wwwroot,
                               preg_replace("/[^A-Za-z0-9]/", "", $SITE->shortname),
                               $course->id,
                               null)); // Expiring Cookie Initialisation - replace 'null' with your chosen duration - see Readme.tx
							   
$screenreader = false;
if ($USER->screenreader == 1) {
    $screenreader = true; // CONTRIB-3225 - If screenreader default back to a non-toggle based topics type format.
}

$renderer->set_screen_reader($screenreader);

global $tcsetting;
if (empty($tcsetting) == true) {
$tcsetting = get_topcoll_setting($course->id); // CONTRIB-3378
}
//print_object($setting);
//$renderer->set_tc_setting($tcsetting);

?>    
<style type="text/css" media="screen">
    /* <![CDATA[ */
/* -- Toggle -- */
.course-content ul.topics li.section .content .toggle {
  background-color: #<?php echo $tcsetting->tgbgcolour;?>;
  color: #<?php echo $tcsetting->tgfgcolour;?>; /* 'Topic x' text colour */
}

/* -- Toggle text -- */
.course-content ul.topics li.section .content .toggle a {
  color: #<?php echo $tcsetting->tgfgcolour;?>;
}

/* -- What happens when a toggle is hovered over -- */
.course-content ul.topics li.section .content div.toggle:hover, body.jsenabled tr.cps td a:hover {
  background-color: #<?php echo $tcsetting->tgbghvrcolour;?>;
}
    /* ]]> */
</style>
<?php
// CONTRIB-3624 - Cookie consent.
if ($TCCFG->defaultcookieconsent  == true) {
    $usercookieconsent = get_topcoll_cookie_consent($USER->id); // In topcoll/lib.php

    // Tell the JavaScript code of the state.  Upon user choice, this page will refresh and a new value sent...
    echo $PAGE->requires->js_init_call('M.format_topcoll.set_cookie_consent', array($usercookieconsent->cookieconsent));
    $renderer->set_cookie_consent($usercookieconsent->cookieconsent);
} else {
    // Cookie consent turned off by administrator, so allow...
    echo $PAGE->requires->js_init_call('M.format_topcoll.set_cookie_consent', array(2));
}

    $renderer->print_multiple_section_page($course, $sections, $mods, $modnames, $modnamesused);


// Only toggle if no Screen Reader
if ($screenreader == false) {
// Establish persistance when we have loaded.
// Reload the state of the toggles from the data contained within the cookie.
// Restore the state of the toggles from the cookie.
        echo $PAGE->requires->js_init_call('M.format_topcoll.set_current_section', array($thecurrentsection)); // If thecurrentsection is 0 because it has not been changed from the defualt, then as section 0 is never tested so can be used to set none.
        echo $PAGE->requires->js_init_call('M.format_topcoll.reload_toggles', array($course->numsections)); // reload_toggles uses the value set above.
}
}

// Include course format js module
$PAGE->requires->js('/course/format/topcoll/format.js');
