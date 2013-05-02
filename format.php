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
require_once($CFG->libdir . '/ajax/ajaxlib.php');
require_once($CFG->dirroot . '/course/format/topcoll/lib.php');

$userisediting = $PAGE->user_is_editing();

// For persistence of toggles.
require_js(array('yui_yahoo', 'yui_cookie', 'yui_event'));
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

<script type="text/javascript" src="<?php echo $CFG->wwwroot ?>/course/format/topcoll/lib_min.js"></script>
<script type="text/javascript">
    //<![CDATA[
    topcoll_init('<?php echo $CFG->wwwroot ?>',
    '<?php echo preg_replace("/[^A-Za-z0-9]/", "", $SITE->shortname) ?>',
    '<?php echo $course->id ?>',
    null); <!-- Expiring Cookie Initialisation - replace 'null' with your chosen duration. -->
    //]]>
</script>
<script type="text/javascript" src="<?php echo $CFG->wwwroot ?>/course/format/topcoll/tc_section_classes_min.js"></script>

<?php
$topic = optional_param('topic', -1, PARAM_INT);

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

$coursecontext = get_context_instance(CONTEXT_COURSE, $course->id);

if ($topic != -1) {
    $displaysection = course_set_display($course->id, $topic);
} else {
    if (isset($USER->display[$course->id])) {       // for admins, mostly
        // If we are editing then we can show only one section.
        if (isediting($course->id) && has_capability('moodle/course:update', $coursecontext)) {
            $displaysection = $USER->display[$course->id];
        } else {
            // Wipe out display section so that when we finish editing and then return we are not confused by
            // only a single section being displayed.
            $displaysection = course_set_display($course->id, 0);
        }
    } else {
        $displaysection = course_set_display($course->id, 0);
    }
}

if (($marker >= 0) && has_capability('moodle/course:setcurrentsection', $coursecontext) && confirm_sesskey()) {
    $course->marker = $marker;
    if (!set_field("course", "marker", $marker, "id", $course->id)) {
        error("Could not mark that topic for this course");
    }
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
    $strstudents = moodle_strtolower($course->students);
    $strtopichide = get_string('topichide', '', $strstudents);
    $strtopicshow = get_string('topicshow', '', $strstudents);
    $strmarkthistopic = get_string('markthistopic');
    $strmarkedthistopic = get_string('markedthistopic');
    $strmoveup = get_string('moveup');
    $strmovedown = get_string('movedown');
}


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
            echo skip_main_destination();

            print_heading_block(get_string('topicoutline'), 'outline');

/// Establish the table for the topics with the colgroup and col tags to allow css to set the widths of the columns correctly and fix them in the browser so
/// that the columns do not magically resize when the toggle is used or we go into editing mode.
            echo '<table id="thetopics" class="topics" summary="' . get_string('layouttable') . '">';
            echo '<colgroup><col class="left" /><col class="content" /><col class="right" style="' . get_string('topcolltogglewidth', 'format_topcoll') . '" /></colgroup>';
            // The string 'topcolltogglewidth' above can be set in the language file to allow for different lengths of words for different languages.
            // For example $string['topcolltogglewidth']='width: 42px;' - if not defined, then the default '#thetopics col.right' in topics_collapsed.css applies.
/// If currently moving a file then show the current clipboard
            if (ismoving($course->id)) {
                $stractivityclipboard = strip_tags(get_string('activityclipboard', '', addslashes($USER->activitycopyname)));
                $strcancel = get_string('cancel');
                echo '<tr class="clipboard">';
                echo '<td colspan="3">';
                echo $stractivityclipboard . '&nbsp;&nbsp;(<a href="mod.php?cancelcopy=true&amp;sesskey=' . $USER->sesskey . '">' . $strcancel . '</a>)';
                echo '</td>';
                echo '</tr>';
            }

// CONTRIB-3624 - Cookie consent.
if ($TCCFG->defaultcookieconsent  == true) {
    $usercookieconsent = get_topcoll_cookie_consent($USER->id); // In topcoll/lib.php

    // Tell the JavaScript code of the state.  Upon user choice, this page will refresh and a new value sent...
?>
<script type="text/javascript" defer="defer"> // Defer running of the script until all HMTL has been passed.
    //<![CDATA[
<?php
    echo 'set_cookie_consent('.$usercookieconsent->cookieconsent.')';
?>
    //]]>
</script>
<?php
    if ($usercookieconsent->cookieconsent == 1) {
        // Display message to ask for consent.
        echo '<tr class="section main">';
        echo '<td class="left side">&nbsp;</td>';
        echo '<td class="content">';
        echo '<div class="cookieConsentContainer">';
        echo '<a "title="' . get_string('cookieconsentform','format_topcoll') . '" href="format/topcoll/cookie_consent.php?userid=' . $USER->id . '&courseid=' . $course->id . '&sesskey=' . sesskey() . '"><div id="set-cookie-consent"></div></a>';
        echo '<div>'.print_heading_block(get_string('setcookieconsent','format_topcoll'), 'sectionname').get_string('cookieconsent','format_topcoll').'</div>';
        echo '</div>';
        echo '</td>';
        echo '<td class="right side">&nbsp;</td>';
        echo '</tr>';
        echo '<tr class="section separator"><td colspan="3" class="spacer"></td></tr>';
    }
} else {
    // Cookie consent turned off by administrator, so allow...
?>
<script type="text/javascript" defer="defer"> // Defer running of the script until all HMTL has been passed.
    //<![CDATA[
<?php
    echo 'set_cookie_consent(2)';
?>
    //]]>
</script>
<?php
}

// CONTRIB-3378
            $layoutsetting = get_layout($course->id);
            if ($userisediting && has_capability('moodle/course:update', $coursecontext)) {
                echo '<tr class="section main">';
                echo '<td class="left side">&nbsp;</td>';
                echo '<td class="content">';
                print_heading_block(get_string('setlayout','format_topcoll'), 'sectionname');
                echo '<a title="' . get_string('setlayout', 'format_topcoll') . '" href="format/topcoll/set_layout.php?id=' . $course->id . '&setelement=' . $layoutsetting->layoutelement . '&setstructure=' . $layoutsetting->layoutstructure . '&sesskey=' . sesskey() . '"><div id="set-layout"></div></a>';
                echo '</td>';
                echo '<td class="right side">&nbsp;</td>';
                echo '</tr>';
                echo '<tr class="section separator"><td colspan="3" class="spacer"></td></tr>';
            }

/// Print Section 0
            $section = 0;
            $thissection = $sections[$section];

            if ($thissection->summary or $thissection->sequence or isediting($course->id)) {
                echo '<tr id="section-0" class="section main">';
                echo '<td id="sectionblock-0" class="left side">&nbsp;</td>'; // MDL-18232
                echo '<td class="content">';

                echo '<div class="summary">';
                $summaryformatoptions->noclean = true;
                echo format_text($thissection->summary, FORMAT_HTML, $summaryformatoptions);

                if (isediting($course->id) && has_capability('moodle/course:update', $coursecontext)) {
                    echo '<a title="' . $streditsummary . '" ' .
                    ' href="editsection.php?id=' . $thissection->id . '"><img src="' . $CFG->pixpath . '/t/edit.gif" ' .
                    ' alt="' . $streditsummary . '" /></a><br /><br />';
                }
                echo '</div>';

                print_section($course, $thissection, $mods, $modnamesused);

                if (isediting($course->id)) {
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


/// Now all the normal modules by topic
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
                    unset($thissection);
                    $thissection->course = $course->id;   // Create a new section structure
                    $thissection->section = $section;
                    $thissection->summary = '';
                    $thissection->visible = 1;
                    if (!$thissection->id = insert_record('course_sections', $thissection)) {
                        notify('Error inserting new topic!');
                    }
                    $sections[$section] = $thissection; // Ensure that the '!empty' works above if we are looped twice in the Current Topic First format when creating a new course and it is the default as set in 'config.php' of this course format.
                }

                if (($layoutsetting->layoutstructure != 3) || ($userisediting)) {
                    $showsection = (has_capability('moodle/course:viewhiddensections', $coursecontext) or $thissection->visible or !$course->hiddensections);
                } else {
                    $showsection = ((has_capability('moodle/course:viewhiddensections', $coursecontext) or $thissection->visible or !$course->hiddensections) and ($nextweekdate <= $timenow));
                }

                if (isediting($course->id) && has_capability('moodle/course:update', $coursecontext)) {
                    // Only contemplate allowing a single viewable section when editing, other situations confusing!
                    if (!empty($displaysection) and $displaysection != $section) {
                        if ($showsection) {
                            $strsummary = strip_tags(format_string($thissection->summary, true));
                            if (strlen($strsummary) < 57) {
                                $strsummary = ' - ' . $strsummary;
                            } else {
                                $strsummary = ' - ' . substr($strsummary, 0, 60) . '...';
                            }
                            $sectionmenu['topic=' . $section] = s($section . $strsummary);
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
                }

                if (($currentsectionfirst == true) && ($showsection == true)) {
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
                        if (empty($thissection->summary)) {
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
                            echo html_to_text(format_string($thissection->summary, true, array('context' => $coursecontext))) . '</span>';

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
                    echo '<tr id="section-' . $section . '" class="section main' . $sectionstyle;
                    if ($screenreader == true) {
                        echo '">';
                    } else {
                        echo '" style="display:none;">';
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
                        // Do not show anything!
                        // echo get_string('notavailable');
                    } else {
                        echo '<div class="summary">';

                        if ($screenreader == true) {
                            if (($layoutsetting->layoutstructure == 2) || ($layoutsetting->layoutstructure == 3)) {
                                // Week structure.
                                print_heading($weekperiod, null, 3, 'weekdates');
                            }
                            $summaryformatoptions->noclean = true;
                            if (!empty($thissection->summary)) {
                                echo format_text($thissection->summary, FORMAT_HTML, $summaryformatoptions);
                            }
                        } else if (isediting($course->id) && has_capability('moodle/course:update', $coursecontext)) {
                            if (($layoutsetting->layoutstructure == 2) || ($layoutsetting->layoutstructure == 3)) {
                                // Week structure.
                                print_heading($weekperiod, null, 3, 'weekdates');
                            }
                            if (!empty($thissection->summary)) {
                                $summaryformatoptions->noclean = true;
                                echo format_text($thissection->summary, FORMAT_HTML, $summaryformatoptions);
                            }
                            echo ' <a title="' . $streditsummary . '" href="editsection.php?id=' . $thissection->id . '">' .
                            '<img src="' . $CFG->pixpath . '/t/edit.gif" class="iconsmall edit" alt="' . $streditsummary . '" /></a><br /><br />';
                        } else if (!empty($thissection->summary)) {
                            $summaryformatoptions->noclean = true;
                            echo format_text($thissection->summary, FORMAT_HTML, $summaryformatoptions);
                        }
                        echo '</div>';

                        print_section($course, $thissection, $mods, $modnamesused);

                        if (isediting($course->id)) {
                            print_section_add_menus($course, $section, $modnames);
                        }
                    }
                    echo '</td>';

                    echo '<td class="right side">';
                    if (isediting($course->id) && has_capability('moodle/course:update', $coursecontext)) {
                        // Only contemplate allowing a single viewable section when editing, other situations confusing!
                        if ($displaysection == $section) {      // Show the zoom boxes
                            echo '<a href="view.php?id=' . $course->id . '&amp;topic=0#section-' . $section . '" title="' . $strshowalltopics . '">' .
                            '<img src="' . $CFG->pixpath . '/i/all.gif" class="icon topicall" alt="' . $strshowalltopics . '" /></a><br />'; // MDL-20757
                        } else {
                            $strshowonlytopic = get_string('showonlytopic', '', $section);
                            echo '<a href="view.php?id=' . $course->id . '&amp;topic=' . $section . '" title="' . $strshowonlytopic . '">' .
                            '<img src="' . $CFG->pixpath . '/i/one.gif" class="icon topicone" alt="' . $strshowonlytopic . '" /></a><br />'; // MDL-20757
                        }
                    }

                    if (isediting($course->id) && has_capability('moodle/course:update', $coursecontext)) {
                        if ($course->marker == $section) {  // Show the "light globe" on/off
                            echo '<a href="view.php?id=' . $course->id . '&amp;marker=0&amp;sesskey=' . $USER->sesskey . '#section-' . $section . '" title="' . $strmarkedthistopic . '">' .
                            '<img src="' . $CFG->pixpath . '/i/marked.gif" alt="' . $strmarkedthistopic . '" /></a><br />';
                        } else {
                            echo '<a href="view.php?id=' . $course->id . '&amp;marker=' . $section . '&amp;sesskey=' . $USER->sesskey . '#section-' . $section . '" title="' . $strmarkthistopic . '">' .
                            '<img src="' . $CFG->pixpath . '/i/marker.gif" alt="' . $strmarkthistopic . '" /></a><br />';
                        }

                        if ($thissection->visible) {        // Show the hide/show eye
                            echo '<a href="view.php?id=' . $course->id . '&amp;hide=' . $section . '&amp;sesskey=' . $USER->sesskey . '#section-' . $section . '" title="' . $strtopichide . '">' .
                            '<img src="' . $CFG->pixpath . '/i/hide.gif" alt="' . $strtopichide . '" /></a><br />';
                        } else {
                            echo '<a href="view.php?id=' . $course->id . '&amp;show=' . $section . '&amp;sesskey=' . $USER->sesskey . '#section-' . $section . '" title="' . $strtopicshow . '">' .
                            '<img src="' . $CFG->pixpath . '/i/show.gif" alt="' . $strtopicshow . '" /></a><br />';
                        }

                        if ($section > 1) {                       // Add a arrow to move section up
                            echo '<a href="view.php?id=' . $course->id . '&amp;random=' . rand(1, 10000) . '&amp;section=' . $section . '&amp;move=-1&amp;sesskey=' . $USER->sesskey . '#section-' . ($section - 1) . '" title="' . $strmoveup . '">' .
                            '<img src="' . $CFG->pixpath . '/t/up.gif" alt="' . $strmoveup . '" /></a><br />';
                        }

                        if ($section < $course->numsections) {    // Add a arrow to move section down
                            echo '<a href="view.php?id=' . $course->id . '&amp;random=' . rand(1, 10000) . '&amp;section=' . $section . '&amp;move=1&amp;sesskey=' . $USER->sesskey . '#section-' . ($section + 1) . '" title="' . $strmovedown . '">' .
                            '<img src="' . $CFG->pixpath . '/t/down.gif" alt="' . $strmovedown . '" /></a><br />';
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
            echo '</table>';

            if (!empty($sectionmenu)) {
                echo '<div align="center" class="jumpmenu">';
                echo popup_form($CFG->wwwroot . '/course/view.php?id=' . $course->id . '&amp;', $sectionmenu, 'sectionmenu', '', get_string('jumpto'), '', '', true);
                echo '</div>';
            }

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


// Establish persistance when  we have loaded!
?>
<script type="text/javascript" defer="defer"> // Defer running of the script until all HMTL has been passed.
    //<![CDATA[
<?php
// Only toggle if no Screen Reader
if ($screenreader == false) {
    echo 'set_number_of_toggles(' . $course->numsections . ');'; // Tell JavaScript how many Toggles to reset.
// Restore the state of the toggles from the cookie if not in 'Show topic x' mode, otherwise show that topic.
    if ($displaysection == 0) {
        echo 'set_current_section(' . $thecurrentsection . ');'; // If thecurrentsection is 0 because it has not been changed from the defualt, then as section 0 is never tested so can be used to set none.
        echo 'YAHOO.util.Event.onDOMReady(reload_toggles);';
        // TODO: Use below later instead of above, for reason see below for save_toggles.
        //echo 'window.addEventListener("load",reload_toggles,false);'; 
    } else {
        echo 'show_topic(' . $displaysection . ');';
    }
// Save the state of the toggles when the page unloads.  This is a stopgap as toggle state is saved every time
// they change.  This is because there is no 'refresh' event yet which would be the best implementation.
// TODO: Uncomment line 611 (save_toggles call in togglebinary function) of lib.js and make into lib_min.js when
//       IE9 fully established with proper DOM event handling -
//       http://blogs.msdn.com/ie/archive/2010/03/26/dom-level-3-events-support-in-ie9.aspx &
//       http://dev.w3.org/2006/webapi/DOM-Level-3-Events/html/DOM3-Events.html#event-types-list
//echo 'window.addEventListener("unload",save_toggles,false);';  TODO Comment
}
?>
    //]]>
</script>
