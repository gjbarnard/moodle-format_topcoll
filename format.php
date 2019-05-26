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
 * @copyright  &copy; 2009-onwards G J Barnard in respect to modifications of standard topics format.
 * @author     G J Barnard - {@link http://moodle.org/user/profile.php?id=442195}
 * @link       http://docs.moodle.org/en/Collapsed_Topics_course_format
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 */
defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/filelib.php');
require_once($CFG->libdir . '/completionlib.php');

// Horrible backwards compatible parameter aliasing....
if ($ctopic = optional_param('ctopics', 0, PARAM_INT)) { // Collapsed Topics old section parameter.
    $url = $PAGE->url;
    $url->param('section', $ctopic);
    debugging('Outdated collapsed topic param passed to course/view.php', DEBUG_DEVELOPER);
    redirect($url);
}
if ($topic = optional_param('topic', 0, PARAM_INT)) { // Topics and Grid old section parameter.
    $url = $PAGE->url;
    $url->param('section', $topic);
    debugging('Outdated topic / grid param passed to course/view.php', DEBUG_DEVELOPER);
    redirect($url);
}
if ($week = optional_param('week', 0, PARAM_INT)) { // Weeks old section parameter.
    $url = $PAGE->url;
    $url->param('section', $week);
    debugging('Outdated week param passed to course/view.php', DEBUG_DEVELOPER);
    redirect($url);
}
// End backwards-compatible aliasing....

$context = context_course::instance($course->id);

// Retrieve course format option fields and add them to the $course object.
$courseformat = course_get_format($course);
$course = $courseformat->get_course();

if (($marker >= 0) && has_capability('moodle/course:setcurrentsection', $context) && confirm_sesskey()) {
    $course->marker = $marker;
    course_set_marker($course->id, $marker);
}

// Make sure all sections are created.
course_create_sections_if_missing($course, range(0, $course->numsections));

$renderer = $PAGE->get_renderer('format_topcoll');

$devicetype = core_useragent::get_device_type(); // In /lib/classes/useragent.php.
if ($devicetype == "mobile") {
    $portable = 1;
} else if ($devicetype == "tablet") {
    $portable = 2;
} else {
    $portable = 0;
}
$renderer->set_portable($portable);

if (!empty($displaysection)) {
    $renderer->print_single_section_page($course, null, null, null, null, $displaysection);
} else {
    $defaulttogglepersistence = clean_param(get_config('format_topcoll', 'defaulttogglepersistence'), PARAM_INT);

    if ($defaulttogglepersistence == 1) {
        user_preference_allow_ajax_update('topcoll_toggle_' . $course->id, PARAM_RAW);
        $userpreference = get_user_preferences('topcoll_toggle_' . $course->id);
    } else {
        $userpreference = null;
    }

    $defaultuserpreference = clean_param(get_config('format_topcoll', 'defaultuserpreference'), PARAM_INT);

    $renderer->set_user_preference($userpreference, $defaultuserpreference, $defaulttogglepersistence);

    $tcsettings = $courseformat->get_settings();

    echo '<style type="text/css" media="screen">';
    echo '/* <![CDATA[ */';

    echo '/* -- Toggle -- */';
    echo '.course-content ul.ctopics li.section .content .toggle,';
    echo '.course-content ul.ctopics li.section .content.sectionhidden {';
    echo 'background-color: ';
    echo \format_topcoll\toolbox::hex2rgba($tcsettings['togglebackgroundcolour'], $tcsettings['togglebackgroundopacity']);
    echo ';';
    echo '}';

    echo '/* -- Toggle text -- */';
    echo '.course-content ul.ctopics li.section .content .toggle span, ';
    echo '.course-content ul.ctopics li.section .content.sectionhidden {';
    echo 'color: ';
    echo \format_topcoll\toolbox::hex2rgba($tcsettings['toggleforegroundcolour'], $tcsettings['toggleforegroundopacity']);
    echo ';';
    echo 'text-align: ';
    switch ($tcsettings['togglealignment']) {
        case 1:
            echo 'left;';
            break;
        case 3:
            echo 'right;';
            break;
        default:
            echo 'center;';
    }
    echo '}';

    echo '/* Toggle icon position. */';
    echo '.course-content ul.ctopics li.section .content .toggle span, #toggle-all .content h4 span {';
    echo 'background-position: ';
    switch ($tcsettings['toggleiconposition']) {
        case 2:
            echo 'right';
            break;
        default:
            echo 'left';
    };
    echo ' center;';
    echo '}';

    echo '/* -- What happens when a toggle is hovered over -- */';
    echo '.course-content ul.ctopics li.section .content .toggle span:hover,';
    echo '.course-content ul.ctopics li.section .content.sectionhidden .toggle span:hover {';
    echo 'color: ';
    echo \format_topcoll\toolbox::hex2rgba($tcsettings['toggleforegroundhovercolour'], $tcsettings['toggleforegroundhoveropacity']);
    echo ';';
    echo '}';

    echo '.course-content ul.ctopics li.section .content div.toggle:hover {';
    echo 'background-color: ';
    echo \format_topcoll\toolbox::hex2rgba($tcsettings['togglebackgroundhovercolour'], $tcsettings['togglebackgroundhoveropacity']);
    echo ';';
    echo '}';

    $topcollsidewidth = get_string('topcollsidewidthlang', 'format_topcoll');
    $topcollsidewidthdelim = strpos($topcollsidewidth, '-');
    $topcollsidewidthlang = strcmp(substr($topcollsidewidth, 0, $topcollsidewidthdelim), current_language());
    $topcollsidewidthval = substr($topcollsidewidth, $topcollsidewidthdelim + 1);
    // Dynamically changing widths with language.
    if ((!$PAGE->user_is_editing()) && ($portable == 0) && ($topcollsidewidthlang == 0)) {
        echo '.course-content ul.ctopics li.section.main .content, .course-content ul.ctopics li.tcsection .content {';
        echo 'margin: 0 '.$topcollsidewidthval.';';
        echo '}';
    } else if ($PAGE->user_is_editing()) {
        echo '.course-content ul.ctopics li.section.main .content, .course-content ul.ctopics li.tcsection .content {';
        echo 'margin: 0 40px;';
        echo '}';
    }

    // Make room for editing icons.
    if ((!$PAGE->user_is_editing()) && ($topcollsidewidthlang == 0)) {
        echo '.course-content ul.ctopics li.section.main .side, .course-content ul.ctopics li.tcsection .side {';
        echo 'width: '.$topcollsidewidthval.';';
        echo '}';
    }

    // Establish horizontal unordered list for horizontal columns.
    if (($renderer->get_format_responsive()) && ($tcsettings['layoutcolumnorientation'] == 2)) {
        echo '.course-content ul.ctopics li.section {';
        echo 'display: inline-block;';
        echo 'vertical-align: top;';
        echo '}';
        echo '.course-content ul.ctopics li.section.hidden {';
        echo "display: inline-block !important; /* Only using '!important' because of Bootstrap 3. */";
        echo '}';
    }
    // Site wide configuration Site Administration -> Plugins -> Course formats -> Collapsed Topics.
    $tcborderradiustl = clean_param(get_config('format_topcoll', 'defaulttoggleborderradiustl'), PARAM_TEXT);
    $tcborderradiustr = clean_param(get_config('format_topcoll', 'defaulttoggleborderradiustr'), PARAM_TEXT);
    $tcborderradiusbr = clean_param(get_config('format_topcoll', 'defaulttoggleborderradiusbr'), PARAM_TEXT);
    $tcborderradiusbl = clean_param(get_config('format_topcoll', 'defaulttoggleborderradiusbl'), PARAM_TEXT);
    echo '.course-content ul.ctopics li.section .content .toggle, .course-content ul.ctopics li.section .content.sectionhidden {';
    echo '-moz-border-top-left-radius: '.$tcborderradiustl.'em;';
    echo '-webkit-border-top-left-radius: '.$tcborderradiustl.'em;';
    echo 'border-top-left-radius: '.$tcborderradiustl.'em;';
    echo '-moz-border-top-right-radius: '.$tcborderradiustr.'em;';
    echo '-webkit-border-top-right-radius: '.$tcborderradiustr.'em;';
    echo 'border-top-right-radius: '.$tcborderradiustr.'em;';
    echo '-moz-border-bottom-right-radius: '.$tcborderradiusbr.'em;';
    echo '-webkit-border-bottom-right-radius: '.$tcborderradiusbr.'em;';
    echo 'border-bottom-right-radius: '.$tcborderradiusbr.'em;';
    echo '-moz-border-bottom-left-radius: '.$tcborderradiusbl.'em;';
    echo '-webkit-border-bottom-left-radius: '.$tcborderradiusbl.'em;';
    echo 'border-bottom-left-radius: '.$tcborderradiusbl.'em;';
    echo '}';

    echo '/* ]]> */';
    echo '</style>';
    $renderer->print_multiple_section_page($course, null, null, null, null);
}

// Include course format js module.
$PAGE->requires->js('/course/format/topcoll/format.js');
