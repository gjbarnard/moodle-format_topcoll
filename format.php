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
require_once($CFG->libdir . '/filelib.php');
require_once($CFG->libdir . '/completionlib.php');

$userisediting = $PAGE->user_is_editing();

// Now get the css and JavaScript Lib.  The call to topcoll_init sets things up for JavaScript to work by understanding the particulars of this course.
?>    
<style type="text/css" media="screen">
    /* <![CDATA[ */
    @import url(<?php echo $CFG->wwwroot ?>/course/format/topcoll/topics_collapsed.css);
    /* ]]> */
</style>
<!--[if lte IE 7]>
    <link rel="stylesheet" type="text/css" href="<?php echo $CFG->wwwroot ?>/course/format/topcoll/ie-7-hacks.css" media="screen" />
<![endif]-->
<?php
$PAGE->requires->js_init_call('M.format_topcoll.init', 
                               array($CFG->wwwroot,
                               preg_replace("/[^A-Za-z0-9]/", "", $SITE->shortname),
                               $course->id,
                               null)); // Expiring Cookie Initialisation - replace 'null' with your chosen duration - see Readme.txt.
if (ajaxenabled() && $userisediting) {
    // This overrides the 'swap_with_section' function in /lib/ajax/section_classes.js
    $PAGE->requires->js('/course/format/topcoll/tc_section_classes_min.js');
}

$topic = optional_param('ctopics', -1, PARAM_INT);

if ($topic != -1) {
    $displaysection = course_set_display($course->id, $topic);
} else {
    $displaysection = course_get_display($course->id); // MDL-23939
}

$coursecontext = get_context_instance(CONTEXT_COURSE, $course->id);

if (($marker >= 0) && has_capability('moodle/course:setcurrentsection', $coursecontext) && confirm_sesskey()) {
    $course->marker = $marker;
    $DB->set_field("course", "marker", $marker, array("id" => $course->id));
}

$streditsummary = get_string('editsummary');
$stradd = get_string('add');
$stractivities = get_string('activities');
$strshowalltopics = get_string('showalltopics');
$strtopic = get_string('topic');
$strgroups = get_string('groups');
$strgroupmy = get_string('groupmy');

$screenreader = false;
if ($USER->screenreader == 1) {
    $screenreader = true; // CONTRIB-3225 - If screenreader default back to a non-toggle based topics type format.
}

if ($userisediting) {
    $strtopichide = get_string('hidetopicfromothers');
    $strtopicshow = get_string('showtopicfromothers');
    $strmarkthistopic = get_string('markthistopic');
    $strmarkedthistopic = get_string('markedthistopic');
    $strmoveup = get_string('moveup');
    $strmovedown = get_string('movedown');
}

// Print the Your progress icon if the track completion is enabled
$completioninfo = new completion_info($course);
echo $completioninfo->display_help_icon(); // MDL-25927

echo $OUTPUT->heading(get_string('topicoutline'), 2, 'headingblock header outline');

// Establish the table for the topics with the colgroup and col tags to allow css to set the widths of the columns correctly and fix them in the browser so
// that the columns do not magically resize when the toggle is used or we go into editing mode.
echo '<table id="thetopics" summary="' . get_string('layouttable') . '">';
echo '<colgroup><col class="left" /><col class="content" /><col class="right" style="' . get_string('topcolltogglewidth', 'format_topcoll') . '" /></colgroup>';
// The string 'topcolltogglewidth' above can be set in the language file to allow for different lengths of words for different languages.
// For example $string['topcolltogglewidth']='width: 42px;' - if not defined, then the default '#thetopics col.right' in topics_collapsed.css applies.
// If currently moving a file then show the current clipboard
if (ismoving($course->id)) {
    $stractivityclipboard = strip_tags(get_string('activityclipboard', '', $USER->activitycopyname));
    $strcancel = get_string('cancel');
    echo '<tr class="clipboard">';
    echo '<td colspan="3">';
    echo $stractivityclipboard . '&nbsp;&nbsp;(<a href="mod.php?cancelcopy=true&amp;sesskey=' . $USER->sesskey . '">' . $strcancel . '</a>)';
    echo '</td>';
    echo '</tr>';
}

// CONTRIB-3378
$layoutsetting = get_layout($course->id);
if ($userisediting && has_capability('moodle/course:update', $coursecontext)) {
    echo '<tr class="section main">';
    echo '<td class="left side">&nbsp;</td>';
    echo '<td class="content">';
    echo '<a title="' . get_string('setlayout', 'format_topcoll') . '" href="format/topcoll/set_layout.php?id=' . $course->id . '&setelement=' . $layoutsetting->layoutelement . '&setstructure=' . $layoutsetting->layoutstructure . '&sesskey=' . sesskey() . '"><div id="set-layout"></div></a>';
    echo '</td>';
    echo '<td class="right side">&nbsp;</td>';
    echo '</tr>';
    echo '<tr class="section separator"><td colspan="3" class="spacer"></td></tr>';
}

// Print Section 0 with general activities
$section = 0;
$thissection = $sections[$section];
unset($sections[0]);

if ($thissection->summary or $thissection->sequence or $userisediting) {
    echo '<tr id="section-0" class="section main">';
    echo '<td class="left side">&nbsp;</td>';
    echo '<td class="content">';

    if (!is_null($thissection->name)) { // MDL-20628
        echo $OUTPUT->heading(format_string($thissection->name, true, array('context' => $coursecontext)), 3, 'sectionname'); // MDL-29188
    }

    echo '<div class="summary">';

    $summarytext = file_rewrite_pluginfile_urls($thissection->summary, 'pluginfile.php', $coursecontext->id, 'course', 'section', $thissection->id);
    $summaryformatoptions = new stdClass();
    $summaryformatoptions->noclean = true;
    $summaryformatoptions->overflowdiv = true;
    echo format_text($summarytext, $thissection->summaryformat, $summaryformatoptions);

    if ($userisediting && has_capability('moodle/course:update', $coursecontext)) {
        echo '<a title="' . $streditsummary . '" ' .
        ' href="editsection.php?id=' . $thissection->id . '"><img src="' . $OUTPUT->pix_url('t/edit') . '" ' .
        ' class="iconsmall edit" alt="' . $streditsummary . '" /></a>';
    }
    echo '</div>';

    print_section($course, $thissection, $mods, $modnamesused);

    if ($userisediting) {
        print_section_add_menus($course, $section, $modnames);
    }

    echo '</td>';
    echo '<td class="right side">&nbsp;</td>';
    echo '</tr>';
    echo '<tr class="section separator"><td colspan="3" class="spacer"></td></tr>';
}

// Get the specific words from the language files.
$topictext = null;
if (($layoutsetting->layoutstructure == 1) || ($layoutsetting->layoutstructure == 4)) {
    $topictext = get_string('setlayoutstructuretopic', 'format_topcoll');
} else {
    $topictext = get_string('setlayoutstructureweek', 'format_topcoll');
}

$toggletext = "";
if ($screenreader == false) { // No need to show if in screen reader mode.
    $toggletext = get_string('topcolltoggle', 'format_topcoll'); // The word 'Toggle'.
    if (empty($displaysection)) { // or showing only one section.
        // Toggle all.
        echo '<tr id="toggle-all" class="section main">';
        echo '<td class="left side toggle-all" colspan="2">';
        echo '<h4><a class="on" href="#" onclick="all_opened(); return false;">' . get_string('topcollopened', 'format_topcoll') . '</a><a class="off" href="#" onclick="all_closed(); return false;">' . get_string('topcollclosed', 'format_topcoll') . '</a>' . get_string('topcollall', 'format_topcoll') . '</h4>';
        echo '</td>';
        echo '<td class="right side">&nbsp;</td>';
        echo '</tr>';
        echo '<tr class="section separator"><td colspan="3" class="spacer"></td></tr>';
    }
}
// Now all the normal modules by topic or week
// Everything below uses "section" terminology - each "section" is a topic or a week.
$timenow = time();
$weekofseconds = 604800;
$course->enddate = $course->startdate + ($weekofseconds * $course->numsections);
if (($layoutsetting->layoutstructure != 3) || ($userisediting)) {
    $section = 1;
    $weekdate = $course->startdate;    // this should be 0:00 Monday of that week
    $weekdate += 7200;                 // Add two hours to avoid possible DST problems
} else {
    $section = $course->numsections;
    $weekdate = $course->enddate;    // this should be 0:00 Monday of that week
    $weekdate -= 7200;                 // Subtract two hours to avoid possible DST problems
}
$sectionmenu = array();

$thecurrentsection = 0; // The section that will be the current section.
$currentsectionfirst = false;
if ($layoutsetting->layoutstructure == 4) {
    $currentsectionfirst = true;
}

$strftimedateshort = ' ' . get_string('strftimedateshort');

$loopsection = 1;
while ($loopsection <= $course->numsections) {
    // This will still work as with a weekly format you define the number of topics / weeks not the end date.
    if (($layoutsetting->layoutstructure != 3) || ($userisediting)) {
        $nextweekdate = $weekdate + ($weekofseconds);
        $weekday = userdate($weekdate, $strftimedateshort);
        $endweekday = userdate($weekdate + 518400, $strftimedateshort);
    } else {
        $nextweekdate = $weekdate - ($weekofseconds);
        $weekday = userdate($weekdate - 518400, $strftimedateshort);
        $endweekday = userdate($weekdate, $strftimedateshort);
    }

    if (!empty($sections[$section])) {
        $thissection = $sections[$section];
    } else {
        $thissection = new stdClass;
        $thissection->course = $course->id;   // Create a new section structure
        $thissection->section = $section;
        $thissection->name = null;
        $thissection->summary = '';
        $thissection->summaryformat = FORMAT_HTML;
        $thissection->visible = 1;
        $thissection->id = $DB->insert_record('course_sections', $thissection);
        $sections[$section] = $thissection; // Ensure that the '!empty' works above if we are looped twice in the Current Topic First format when creating a new course and it is the default as set in 'config.php' of this course format.
    }

    //$showsection = (has_capability('moodle/course:viewhiddensections', $coursecontext) or $thissection->visible or !$course->hiddensections);
    if (($layoutsetting->layoutstructure != 3) || ($userisediting)) {
        $showsection = (has_capability('moodle/course:viewhiddensections', $coursecontext) or $thissection->visible or !$course->hiddensections);
    } else {
        $showsection = ((has_capability('moodle/course:viewhiddensections', $coursecontext) or $thissection->visible or !$course->hiddensections) and ($nextweekdate <= $timenow));
    }

    if (!empty($displaysection) and $displaysection != $section) { // If the display section is not null then skip if it is not the section to show.
        if ($showsection) {
            $sectionmenu[$section] = get_section_name($course, $thissection);
        }

        if ($currentsectionfirst == false) {
            if (($layoutsetting->layoutstructure != 3) || ($userisediting)) {
                $section++;
            } else {
                $section--;
            }
            $loopsection++;
            $weekdate = $nextweekdate;
            continue; // Need the code to execute below when $currentsectionfirst is true so that the right decisions can be made.
        } else {
            $showsection = false;
        }
    }

    if (($currentsectionfirst == true) && ($showsection == true)){
        $showsection = ($course->marker == $section);  // Show  the section if we were meant to and it is the current section.
    } else if (($layoutsetting->layoutstructure == 4) && ($course->marker == $section)) {
        $showsection = false; // Do not reshow current section. 
    }

    if ($showsection) {
        $currenttopic = null;
        $currentweek = null;
        if (($layoutsetting->layoutstructure == 1) || ($layoutsetting->layoutstructure == 4)) {
            $currenttopic = ($course->marker == $section);
        } else {
            if (($userisediting) || ($layoutsetting->layoutstructure != 3)) {
                $currentweek = (($weekdate <= $timenow) && ($timenow < $nextweekdate));
            } else {
                $currentweek = (($weekdate > $timenow) && ($timenow >= $nextweekdate));
            }
        }

        $currenttext = '';
        if (!$thissection->visible) {
            $sectionstyle = ' hidden';
        } else if ($currenttopic) {
            $sectionstyle = ' current';
            $currenttext = get_accesshide(get_string('currenttopic', 'access'));
            $thecurrentsection = $section;
        } else if ($currentweek) {
            $sectionstyle = ' current';
            $currenttext = get_accesshide(get_string('currentweek', 'access'));
            $thecurrentsection = $section;
        } else {
            $sectionstyle = '';
        }

        $weekperiod = $weekday . ' - ' . $endweekday;

        if ($screenreader == false) {

            echo '<tr class="cps" id="sectionhead-' . $section . '">';

            // Have a different look depending on if the section summary has been completed.
            if (is_null($thissection->name)) {
                echo '<td colspan="3"><a id="sectionatag-' . $section . '" class="cps_nosumm" href="#" onclick="toggle_topic(this,' . $section . '); return false;">';
                if (($layoutsetting->layoutstructure != 1) && ($layoutsetting->layoutstructure != 4)) {
                    echo '<span>' . $weekperiod . '</span><br />';
                }
                echo $topictext . ' ' . $currenttext . $section;
                switch ($layoutsetting->layoutelement) {
                    case 1: // Default
                    case 3: // No section no.
                    case 2: // No Toggle Section x
                    case 4: // No Toggle Section x & section no.
                        echo ' - ' . $toggletext . '</a></td>';
                        break;
                    case 5: // No Toggle
                    case 6: // No Toggle and Toggle Section x
                    case 7: // No Toggle, Toggle Section x and section no.
                        echo'</a></td>';
                        break;
                    default:
                        echo' - ' . $toggletext . '</a></td>';
                }
            } else {
                $colspan = 0;
                switch ($layoutsetting->layoutelement) {
                    case 1: // Default
                    case 3: // No section no.
                    case 5: // No Toggle
                        $colspan = 2;
                        break;
                    case 2: // No Toggle Section x
                    case 4: // No Toggle Section x & section no.
                    case 6: // No Toggle and Toggle Section x
                    case 7: // No Toggle, Toggle Section x and section no.
                        $colspan = 3;
                        break;
                }
                echo '<td colspan="' . $colspan . '"><a id="sectionatag-' . $section . '" href="#" onclick="toggle_topic(this,' . $section . '); return false;"><span>';
                if (($layoutsetting->layoutstructure != 1) && ($layoutsetting->layoutstructure != 4)) {
                    echo $weekperiod . '<br />';
                }
                echo html_to_text(format_string($thissection->name, true, array('context' => $coursecontext))) . '</span>';

                switch ($layoutsetting->layoutelement) {
                    case 1: // Default
                    case 3: // No section no.
                        echo ' - ' . $toggletext . '</a></td><td class="cps_centre">' . $topictext . '<br />' . $currenttext . $section . '</td>';  // format_string from MDL-29188
                        break;
                    case 2: // No Toggle Section x
                    case 4: // No Toggle Section x & section no.
                        echo' - ' . $toggletext . '</a></td>';
                        break;
                    case 5: // No Toggle
                        echo '</a></td><td class="cps_centre">' . $topictext . '<br />' . $currenttext . $section . '</td>';  // format_string from MDL-29188
                        break;
                    case 6: // No Toggle and Toggle Section x
                    case 7: // No Toggle, Toggle Section x and section no.
                        echo '</a></td>';  // format_string from MDL-29188
                        break;
                    default:
                        echo ' - ' . $toggletext . '</a></td><td class="cps_centre">' . $topictext . '<br />' . $currenttext . $section . '</td>';  // format_string from MDL-29188
                }
            }
            echo '</tr>';
        }

        // Now the section itself.  The css class of 'hid' contains the display attribute that manipulated by the JavaScript to show and hide the section.  It is defined in js-override-topcoll.css which 
        // is loaded into the DOM by the JavaScript function topcoll_init.  Therefore having a logical separation between static and JavaScript manipulated css.  Nothing else here differs from 
        // the standard Topics format in the core distribution.  The next change is at the bottom.
        if ($screenreader == true) {
            echo '<tr id="section-' . $section . '" class="section main' . $sectionstyle . '">';
        } else {
            echo '<tr id="section-' . $section . '" class="section main' . $sectionstyle . '" style="display:none;">';
        }

        if ($screenreader == true) {
            echo '<td class="left side">' . $currenttext . $section . '</td>';
        } else {
            switch ($layoutsetting->layoutelement) {
                case 1: // Default
                case 2: // No Toggle Section x.
                    echo '<td class="left side">' . $currenttext . $section . '</td>';
                    break;
                case 3: // No section no.
                case 4: // No Toggle Section x & section no.
                case 7: // No Toggle, Toggle Section x and section no.
                    echo '<td class="left side">&nbsp;</td>';
                    break;
                default:
                    echo '<td class="left side">' . $currenttext . $section . '</td>';
            }
        }

        echo '<td class="content">';
        if (!has_capability('moodle/course:viewhiddensections', $coursecontext) and !$thissection->visible) {   // Hidden for students
            //echo get_string('notavailable');
        } else {
            if ($screenreader == true) {
                if (($layoutsetting->layoutstructure == 1) || ($layoutsetting->layoutstructure == 4)) {
                    // Topic type structure.
                    if (!is_null($thissection->name)) {
                        echo $OUTPUT->heading(format_string($thissection->name, true, array('context' => $coursecontext)), 3, 'sectionname');
                    } else {
                        echo $OUTPUT->heading($topictext . ' ' . $currenttext . $section, 3, 'sectionname');
                    }
                } else {
                    // Week structure.
                    if (isset($thissection->name) && ($thissection->name !== NULL)) {  // empty string is ok
                        echo $OUTPUT->heading(format_string($currenttext . $weekperiod, true, array('context' => $coursecontext)), 3, 'weekdates');
                        echo $OUTPUT->heading(format_string($thissection->name, true, array('context' => $coursecontext)), 3, 'weekdates');
                    } else {
                        echo $OUTPUT->heading($currenttext . $weekperiod, 3, 'weekdates');
                    }
                }
            }

            echo '<div class="summary">';
            if ($thissection->summary) {
                $summarytext = file_rewrite_pluginfile_urls($thissection->summary, 'pluginfile.php', $coursecontext->id, 'course', 'section', $thissection->id);
                $summaryformatoptions = new stdClass();
                $summaryformatoptions->noclean = true;
                $summaryformatoptions->overflowdiv = true;
                echo format_text($summarytext, $thissection->summaryformat, $summaryformatoptions);
            }
            if ($userisediting && has_capability('moodle/course:update', $coursecontext)) {
                echo '<a title="' . $streditsummary . '" href="editsection.php?id=' . $thissection->id . '">' .
                '<img src="' . $OUTPUT->pix_url('t/edit') . '" class="iconsmall edit" alt="' . $streditsummary . '" /></a><br /><br />';
            }
            echo '</div>';

            print_section($course, $thissection, $mods, $modnamesused);

            if ($userisediting) {
                print_section_add_menus($course, $section, $modnames);
            }
        }
        echo '</td>';

        echo '<td class="right side">';
        if ($displaysection == $section) {    // Show the zoom boxes
            echo '<a href="view.php?id=' . $course->id . '&amp;ctopics=0#section-' . $section . '" title="' . $strshowalltopics . '">' .
            '<img src="' . $OUTPUT->pix_url('i/all') . '" class="icon" alt="' . $strshowalltopics . '" /></a><br />';
        } else {
            $strshowonlytopic = get_string("showonlytopic", "", $section);
            echo '<a href="view.php?id=' . $course->id . '&amp;ctopics=' . $section . '" title="' . $strshowonlytopic . '">' .
            '<img src="' . $OUTPUT->pix_url('i/one') . '" class="icon" alt="' . $strshowonlytopic . '" /></a><br />';
        }

        if ($userisediting && has_capability('moodle/course:update', $coursecontext)) {
            if ($course->marker == $section) {  // Show the "light globe" on/off
                echo '<a href="view.php?id=' . $course->id . '&amp;marker=0&amp;sesskey=' . sesskey() . '#section-' . $section . '" title="' . $strmarkedthistopic . '">' . '<img src="' . $OUTPUT->pix_url('i/marked') . '" alt="' . $strmarkedthistopic . '" class="icon"/></a><br />'; // MDL-32145
            } else {
                echo '<a href="view.php?id=' . $course->id . '&amp;marker=' . $section . '&amp;sesskey=' . sesskey() . '#section-' . $section . '" title="' . $strmarkthistopic . '">' . '<img src="' . $OUTPUT->pix_url('i/marker') . '" alt="' . $strmarkthistopic . '" class="icon"/></a><br />';
            } // MDL-32145

            if ($thissection->visible) { // Show the hide/show eye
                echo '<a href="view.php?id=' . $course->id . '&amp;hide=' . $section . '&amp;sesskey=' . sesskey() . '#section-' . $section . '" title="' . $strtopichide . '">' .
                '<img src="' . $OUTPUT->pix_url('i/hide') . '" class="icon hide" alt="' . $strtopichide . '" /></a><br />';
            } else {
                echo '<a href="view.php?id=' . $course->id . '&amp;show=' . $section . '&amp;sesskey=' . sesskey() . '#section-' . $section . '" title="' . $strtopicshow . '">' .
                '<img src="' . $OUTPUT->pix_url('i/show') . '" class="icon hide" alt="' . $strtopicshow . '" /></a><br />';
            }
            if ($section > 1) { // Add a arrow to move section up
                echo '<a href="view.php?id=' . $course->id . '&amp;random=' . rand(1, 10000) . '&amp;section=' . $section . '&amp;move=-1&amp;sesskey=' . sesskey() . '#section-' . ($section - 1) . '" title="' . $strmoveup . '">' .
                '<img src="' . $OUTPUT->pix_url('t/up') . '" class="icon up" alt="' . $strmoveup . '" /></a><br />';
            }

            if ($section < $course->numsections) { // Add a arrow to move section down
                echo '<a href="view.php?id=' . $course->id . '&amp;random=' . rand(1, 10000) . '&amp;section=' . $section . '&amp;move=1&amp;sesskey=' . sesskey() . '#section-' . ($section + 1) . '" title="' . $strmovedown . '">' .
                '<img src="' . $OUTPUT->pix_url('t/down') . '" class="icon down" alt="' . $strmovedown . '" /></a><br />';
            }
        }
        echo '</td></tr>';

        echo '<tr class="section separator"><td colspan="3" class="spacer"></td></tr>';
    }

    if ($currentsectionfirst == false) {
        unset($sections[$section]); // Only need to do this on the iteration when $currentsectionfirst is not true as this iteration will always happen.  Otherwise you get duplicate entries in course_sections in the DB.
    }
    if (($layoutsetting->layoutstructure != 3) || ($userisediting)) {
        $section++;
    } else {
        $section--;
    }
    $loopsection++;
    $weekdate = $nextweekdate;

    if (($currentsectionfirst == true) && ($loopsection > $course->numsections)) {
        // Now show the rest.
        $currentsectionfirst = false; 
        $loopsection = 1;
        $section = 1;
    }
}

if (!$displaysection and $userisediting and has_capability('moodle/course:update', $coursecontext)) {
    // print stealth sections if present
    $modinfo = get_fast_modinfo($course);
    foreach ($sections as $section => $thissection) {
        if (empty($modinfo->sections[$section])) {
            continue;
        }

        echo '<tr id="section-' . $section . '" class="section main clearfix orphaned hidden">';
        echo '<td class="left side">';
        echo '</td>';
        echo '<td class="content">';
        echo $OUTPUT->heading(get_string('orphanedactivities'), 3, 'sectionname');
        print_section($course, $thissection, $mods, $modnamesused);
        echo '</td>';
        echo '<td class="right side">';
        echo '</td>';
        echo "</tr>\n";
    }
}
echo '</table>';

if (!empty($sectionmenu)) {
    $select = new single_select(new moodle_url('/course/view.php', array('id' => $course->id)), 'ctopics', $sectionmenu);
    $select->label = get_string('jumpto');
    $select->class = 'jumpmenu';
    $select->formid = 'sectionmenu';
    echo $OUTPUT->render($select);
}

// Only toggle if no Screen Reader
if ($screenreader == false) {
// Establish persistance when we have loaded.
// Reload the state of the toggles from the data contained within the cookie.
// Restore the state of the toggles from the cookie if not in 'Show topic x' mode, otherwise show that topic.
    if ($displaysection == 0) {
        echo $PAGE->requires->js_init_call('M.format_topcoll.set_current_section', array($thecurrentsection)); // If thecurrentsection is 0 because it has not been changed from the defualt, then as section 0 is never tested so can be used to set none.
        echo $PAGE->requires->js_init_call('M.format_topcoll.reload_toggles', array($course->numsections)); // reload_toggles uses the value set above.
    } else {
        echo $PAGE->requires->js_init_call('M.format_topcoll.show_topic', array($displaysection));
    }
}