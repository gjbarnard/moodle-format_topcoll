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

$tcscreenreader = false;
if ($USER->screenreader == 1) {
    $tcscreenreader = true; // CONTRIB-3225 - If screenreader default back to a non-toggle based topics type format.
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
	null)); // Expiring Cookie Initialisation - replace 'null' with your chosen duration - see Readme.txt
	
	//$renderer->set_screen_reader($tcscreenreader);

	global $tcsetting;
	if (empty($tcsetting) == true) {
		$tcsetting = get_topcoll_setting($course->id); // CONTRIB-3378
	}
	//print_object($setting);
	//$renderer->set_tc_setting($tcsetting);

	?>
<style type="text/css" media="screen">
/* <![CDATA[ */
/* -- Images here as need to know the full url due to [[pix:****]] not working with course formats in the css file and the relative position changes between theme designer mode on / off.  -- */

/* -- The clickable element of the Toggle -- */
.course-content ul.topics li.section .content .toggle a.cps_a {
  padding: 7px 0 7px 35px; /* The 35px allows the arrow to be on the left and the text next to it. */
  background: transparent url(<?php echo $CFG->wwwroot?>/course/format/topcoll/images/arrow_up.png) no-repeat 5px 45%; /* Position the arrow roughly in the centre of the Toggle.  This is shown by default when JavaScript is disabled. */
}

body.jsenabled .course-content ul.topics li.section .content .toggle a.cps_a {
  background: transparent url(../course/format/topcoll/images/arrow_down.png) no-repeat 5px 45%; /* Position the arrow roughly in the centre of the Toggle.   This is shown by default when JavaScript is enabled. */
}

#toggle-all .content .sectionbody h4 a {
  padding: 7px 7px 7px 30px; /* The 30px allows the arrow to be on the left and the text next to it. */
  text-align: left;
  width: 35px;
  text-decoration: none;
}

#toggle-all .content .sectionbody h4 a.on {
  background: transparent url(<?php echo $CFG->wwwroot?>/course/format/topcoll/images/arrow_down.png) no-repeat 0px 45%; 
}

#toggle-all .content .sectionbody h4 a.off {
  background: transparent url(<?php echo $CFG->wwwroot?>/course/format/topcoll/images/arrow_up.png) no-repeat 0px 45%; 
}

/* Set settings */
#set-settings {
  background: transparent url(<?php echo $CFG->wwwroot?>/course/format/topcoll/images/tc_logo_spanner.png) no-repeat 0px 0px; 
  width: 128px;
  height: 100px;
  float: right;
  margin: 4px;
}

/* Set cookie consent */
#set-cookie-consent{
  float: right; 
  vertical-align: text-top;
  background: transparent url(<?php echo $CFG->wwwroot?>/course/format/topcoll/images/tc_logo_cookie.png) no-repeat 0px 0px; 
  width: 128px;
  height: 100px;
  margin: 4px;
}

/* -- Toggle -- */
.course-content ul.topics li.section .content .toggle {
	background-color: #<?php echo $tcsetting->tgbgcolour; ?>;
	color: #<?php echo $tcsetting->tgfgcolour; ?>; /* 'Topic x' text colour */
}

/* -- Toggle text -- */
.course-content ul.topics li.section .content .toggle a {
	color: #<?php echo $tcsetting->tgfgcolour; ?>;
}

/* -- What happens when a toggle is hovered over -- */
.course-content ul.topics li.section .content div.toggle:hover,body.jsenabled tr.cps td a:hover
	{
	background-color: #<?php echo $tcsetting->tgbghvrcolour; ?>;
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
	$thecurrentsection = 0; // The section that will be the current section - manipulated in section_header in the renderer.
	$renderer->print_multiple_section_page($course, $sections, $mods, $modnames, $modnamesused);
	//print ($thecurrentsection);

	// Only toggle if no Screen Reader
	if ($tcscreenreader == false) {
		// Establish persistance when we have loaded.
		// Reload the state of the toggles from the data contained within the cookie.
		// Restore the state of the toggles from the cookie.
		echo $PAGE->requires->js_init_call('M.format_topcoll.set_current_section', array($thecurrentsection)); // If thecurrentsection is 0 because it has not been changed from the defualt, then as section 0 is never tested so can be used to set none.
		echo $PAGE->requires->js_init_call('M.format_topcoll.reload_toggles', array($course->numsections)); // reload_toggles uses the value set above.
	}
}

// Include course format js module
$PAGE->requires->js('/course/format/topcoll/format.js');
