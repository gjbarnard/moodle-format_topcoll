<?php 
// $Id: format.php,v 1.29 2011/10/06 00:55:38 gb2048 Exp $
/**
 * Collapsed Topics Information
 *
 * @package    course/format
 * @subpackage topcoll
 * @copyright  2009-2011 @ G J Barnard in respect to modifications of standard topics format.
 * @link       http://docs.moodle.org/en/Collapsed_Topics_course_format
 * @license    http://creativecommons.org/licenses/by-sa/3.0/ Creative Commons Attribution-ShareAlike 3.0 Unported (CC BY-SA 3.0)
 */

// Display the whole course as "topics" made of of modules
// Included from "view.php"
// Initially modified from format.php in standard topics format.
defined('MOODLE_INTERNAL') || die();  
require_once($CFG->libdir.'/filelib.php');
require_once($CFG->libdir.'/completionlib.php');

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
    $PAGE->requires->js('/course/format/topcoll/lib_min.js');
    $PAGE->requires->js_function_call('topcoll_init',
                                      array($CFG->wwwroot,
                                            preg_replace("/[^A-Za-z0-9]/", "", $SITE->shortname),
                                            $course->id,
                                            null)); // Expiring Cookie Initialisation - replace 'null' with your chosen duration.
    if (ajaxenabled() && $PAGE->user_is_editing()) {
        // This overrides the 'swap_with_section' function in /lib/ajax/section_classes.js
        $PAGE->requires->js('/course/format/topcoll/tc_section_classes_min.js');
    }

    $topic = optional_param('ctopics', -1, PARAM_INT);

    if ($topic != -1) {
        $displaysection = course_set_display($course->id, $topic);
    } else {
        $displaysection = course_get_display($course->id); // MDL-23939
    }

    $context = get_context_instance(CONTEXT_COURSE, $course->id);

    if (($marker >=0) && has_capability('moodle/course:setcurrentsection', $context) && confirm_sesskey()) {
        $course->marker = $marker;
        $DB->set_field("course", "marker", $marker, array("id"=>$course->id));
    }

    $streditsummary  = get_string('editsummary');
    $stradd          = get_string('add');
    $stractivities   = get_string('activities');
    $strshowalltopics = get_string('showalltopics');
    $strtopic         = get_string('topic');
    $strgroups       = get_string('groups');
    $strgroupmy      = get_string('groupmy');
    $editing         = $PAGE->user_is_editing();

    if ($editing) {
        $strtopichide = get_string('hidetopicfromothers');
        $strtopicshow = get_string('showtopicfromothers');
        $strmarkthistopic = get_string('markthistopic');
        $strmarkedthistopic = get_string('markedthistopic');
        $strmoveup   = get_string('moveup');
        $strmovedown = get_string('movedown');
    }

    // Print the Your progress icon if the track completion is enabled
    $completioninfo = new completion_info($course);
    echo $completioninfo->display_help_icon(); // MDL-25927

    echo $OUTPUT->heading(get_string('topicoutline'), 2, 'headingblock header outline');
    
    // Establish the table for the topics with the colgroup and col tags to allow css to set the widths of the columns correctly and fix them in the browser so
    // that the columns do not magically resize when the toggle is used or we go into editing mode.
    echo '<table id="thetopics" summary="'.get_string('layouttable').'">';
    echo '<colgroup><col class="left" /><col class="content" /><col class="right" style="'.get_string('topcolltogglewidth','format_topcoll').'" /></colgroup>';
    // The string 'topcolltogglewidth' above can be set in the language file to allow for different lengths of words for different languages.
    // For example $string['topcolltogglewidth']='width: 42px;' - if not defined, then the default '#thetopics col.right' in topics_collapsed.css applies.

    // If currently moving a file then show the current clipboard
    if (ismoving($course->id)) {
        $stractivityclipboard = strip_tags(get_string('activityclipboard', '', $USER->activitycopyname));
        $strcancel= get_string('cancel');
        echo '<tr class="clipboard">';
        echo '<td colspan="3">';
        echo $stractivityclipboard.'&nbsp;&nbsp;(<a href="mod.php?cancelcopy=true&amp;sesskey='.$USER->sesskey.'">'.$strcancel.'</a>)';
        echo '</td>';
        echo '</tr>';
    }

    // Print Section 0 with general activities
    $section = 0;
    $thissection = $sections[$section];
    unset($sections[0]);

    if ($thissection->summary or $thissection->sequence or $PAGE->user_is_editing()) {
        echo '<tr id="section-0" class="section main">';
        echo '<td class="left side">&nbsp;</td>';
        echo '<td class="content">';

        echo '<div class="summary">';

        $coursecontext = get_context_instance(CONTEXT_COURSE, $course->id);
        $summarytext = file_rewrite_pluginfile_urls($thissection->summary, 'pluginfile.php', $coursecontext->id, 'course','section', $thissection->id);
        $summaryformatoptions = new stdClass();
        $summaryformatoptions->noclean = true;
        $summaryformatoptions->overflowdiv = true;
        echo format_text($summarytext, $thissection->summaryformat, $summaryformatoptions);

        if ($PAGE->user_is_editing() && has_capability('moodle/course:update', $coursecontext)) {
            echo '<a title="'.$streditsummary.'" '.
                 ' href="editsection.php?id='.$thissection->id.'"><img src="'.$OUTPUT->pix_url('t/edit') . '" '.
                 ' class="icon edit" alt="'.$streditsummary.'" /></a>';
        }
        echo '</div>';
        
        print_section($course, $thissection, $mods, $modnamesused);

        if ($PAGE->user_is_editing()) {
            print_section_add_menus($course, $section, $modnames);
        }

        echo '</td>';
        echo '<td class="right side">&nbsp;</td>';
        echo '</tr>';
        echo '<tr class="section separator"><td colspan="3" class="spacer"></td></tr>';
    }

    // Get the specific words from the language files.
    $topictext = get_string('sectionname','format_topcoll'); // This is defined in lang/en of the formats installation directory - basically, the word 'Toggle'.
    $toggletext = get_string('topcolltoggle','format_topcoll'); // The table row of the toggle.

    // Toggle all.
    echo '<tr id="toggle-all" class="section main">';
    echo '<td class="left side toggle-all" colspan="2">';
    echo '<h4><a class="on" href="#" onclick="all_opened(); return false;">'.get_string('topcollopened','format_topcoll').'</a><a class="off" href="#" onclick="all_closed(); return false;">'.get_string('topcollclosed','format_topcoll').'</a>'.get_string('topcollall','format_topcoll').'</h4>';
    echo '</td>';
    echo '<td class="right side">&nbsp;</td>';
    echo '</tr>';
    echo '<tr class="section separator"><td colspan="3" class="spacer"></td></tr>';

    // Now all the normal modules by topic
    // Everything below uses "section" terminology - each "section" is a topic.
    $section = 1;
    $sectionmenu = array();

    while ($section <= $course->numsections) {
        if (!empty($sections[$section])) {
            $thissection = $sections[$section];
        } else {
            $thissection = new stdClass;
            $thissection->course  = $course->id;   // Create a new section structure
            $thissection->section = $section;
            $thissection->name    = null;
            $thissection->summary  = '';
            $thissection->summaryformat = FORMAT_HTML;
            $thissection->visible  = 1;
            $thissection->id = $DB->insert_record('course_sections', $thissection);
        }

        $showsection = (has_capability('moodle/course:viewhiddensections', $context) or $thissection->visible or !$course->hiddensections);

        if (!empty($displaysection) and $displaysection != $section) { // Check this topic is visible
            if ($showsection) {
                $sectionmenu[$section] = get_section_name($course, $thissection);
            }
            $section++;
            continue;
        }

        if ($showsection) {
            $currenttopic = ($course->marker == $section);

            $currenttext = '';
            if (!$thissection->visible) {
                $sectionstyle = ' hidden';
            } else if ($currenttopic) {
                $sectionstyle = ' current';
                $currenttext = get_accesshide(get_string('currenttopic','access'));
            } else {
                $sectionstyle = '';
            }

            echo '<tr class="cps" id="sectionhead-'.$section.'">';
            // Have a different look depending on if the section summary has been completed.
            if (is_null($thissection->name)) {
                echo '<td colspan="3"><a id="sectionatag-'.$section.'" class="cps_nosumm" href="#" onclick="toggle_topic(this,'.$section.'); return false;">'.$topictext.' '.$currenttext.$section.' - '.$toggletext.'</a></td>';
            } else {
                echo '<td colspan="2"><a id="sectionatag-'.$section.'" href="#" onclick="toggle_topic(this,'.$section.'); return false;"><span>'.html_to_text($thissection->name).'</span> - '.$toggletext.'</a></td><td class="cps_centre">'.$topictext.'<br />'.$currenttext.$section.'</td>';
                // Comment out the above line and uncomment the line below if you do not want 'Topic x' displayed on the right hand side of the toggle.
                //echo '<td colspan="3"><a id="sectionatag-'.$section.'" href="#" onclick="toggle_topic(this,'.$section.'); return false;"><span>'.html_to_text($thissection->name).'</span> - '.$toggletext.'</a></td>';
            }
            echo '</tr>';

            // Now the section itself.  The css class of 'hid' contains the display attribute that manipulated by the JavaScript to show and hide the section.  It is defined in js-override-topcoll.css which 
            // is loaded into the DOM by the JavaScript function topcoll_init.  Therefore having a logical separation between static and JavaScript manipulated css.  Nothing else here differs from 
            // the standard Topics format in the core distribution.  The next change is at the bottom.
            echo '<tr id="section-'.$section.'" class="section main'.$sectionstyle.'" style="display:none;">';
            echo '<td class="left side">'.$currenttext.$section.'</td>';
            // Comment out the above line and uncomment the line below if you do not want the section number displayed on the left hand side of the section.
            //echo '<td class="left side">&nbsp;</td>';
            
            echo '<td class="content">';
            if (!has_capability('moodle/course:viewhiddensections', $context) and !$thissection->visible) {   // Hidden for students
                //echo get_string('notavailable');
            } else {
                echo '<div class="summary">';
                if ($thissection->summary) {
                    $coursecontext = get_context_instance(CONTEXT_COURSE, $course->id);
                    $summarytext = file_rewrite_pluginfile_urls($thissection->summary, 'pluginfile.php', $coursecontext->id, 'course','section', $thissection->id);
                    $summaryformatoptions = new stdClass();
                    $summaryformatoptions->noclean = true;
                    $summaryformatoptions->overflowdiv = true;
                    echo format_text($summarytext, $thissection->summaryformat, $summaryformatoptions);
                }
                if ($PAGE->user_is_editing() && has_capability('moodle/course:update', $coursecontext)) {
                    echo '<a title="'.$streditsummary.'" href="editsection.php?id='.$thissection->id.'">'.
                         '<img src="'.$OUTPUT->pix_url('t/edit') . '" class="icon edit" alt="'.$streditsummary.'" /></a><br /><br />';
                }
                echo '</div>';

                print_section($course, $thissection, $mods, $modnamesused);

                if ($PAGE->user_is_editing()) {
                    print_section_add_menus($course, $section, $modnames);
                }
            }
            echo '</td>';
            
            echo '<td class="right side">';
            if ($displaysection == $section) {    // Show the zoom boxes
                echo '<a href="view.php?id='.$course->id.'&amp;ctopics=0#section-'.$section.'" title="'.$strshowalltopics.'">'.
                     '<img src="'.$OUTPUT->pix_url('i/all') . '" class="icon" alt="'.$strshowalltopics.'" /></a><br />';
            } else {
                $strshowonlytopic = get_string("showonlytopic", "", $section);
                echo '<a href="view.php?id='.$course->id.'&amp;ctopics='.$section.'" title="'.$strshowonlytopic.'">'.
                     '<img src="'.$OUTPUT->pix_url('i/one') . '" class="icon" alt="'.$strshowonlytopic.'" /></a><br />';
            }

            if ($PAGE->user_is_editing() && has_capability('moodle/course:update', $context)) {
                if ($course->marker == $section) { // Show the "light globe" on/off
                       echo '<a href="view.php?id='.$course->id.'&amp;marker=0&amp;sesskey='.sesskey().'#section-'.$section.'" title="'.$strmarkedthistopic.'">'.'<img src="'.$OUTPUT->pix_url('i/marked') . '" alt="'.$strmarkedthistopic.'" /></a><br />';
                } else {
                    echo '<a href="view.php?id='.$course->id.'&amp;marker='.$section.'&amp;sesskey='.sesskey().'#section-'.$section.'" title="'.$strmarkthistopic.'">'.'<img src="'.$OUTPUT->pix_url('i/marker') . '" alt="'.$strmarkthistopic.'" /></a><br />';
                }

                if ($thissection->visible) { // Show the hide/show eye
                    echo '<a href="view.php?id='.$course->id.'&amp;hide='.$section.'&amp;sesskey='.sesskey().'#section-'.$section.'" title="'.$strtopichide.'">'.
                         '<img src="'.$OUTPUT->pix_url('i/hide') . '" class="icon hide" alt="'.$strtopichide.'" /></a><br />';
                } else {
                    echo '<a href="view.php?id='.$course->id.'&amp;show='.$section.'&amp;sesskey='.sesskey().'#section-'.$section.'" title="'.$strtopicshow.'">'.
                         '<img src="'.$OUTPUT->pix_url('i/show') . '" class="icon hide" alt="'.$strtopicshow.'" /></a><br />';
                }
                if ($section > 1) { // Add a arrow to move section up
                    echo '<a href="view.php?id='.$course->id.'&amp;random='.rand(1,10000).'&amp;section='.$section.'&amp;move=-1&amp;sesskey='.sesskey().'#section-'.($section-1).'" title="'.$strmoveup.'">'.
                         '<img src="'.$OUTPUT->pix_url('t/up') . '" class="icon up" alt="'.$strmoveup.'" /></a><br />';
                }

                if ($section < $course->numsections) { // Add a arrow to move section down
                    echo '<a href="view.php?id='.$course->id.'&amp;random='.rand(1,10000).'&amp;section='.$section.'&amp;move=1&amp;sesskey='.sesskey().'#section-'.($section+1).'" title="'.$strmovedown.'">'.
                         '<img src="'.$OUTPUT->pix_url('t/down') . '" class="icon down" alt="'.$strmovedown.'" /></a><br />';
                }
            }
            echo '</td></tr>';
            
            echo '<tr class="section separator"><td colspan="3" class="spacer"></td></tr>';
        }

        unset($sections[$section]);
        $section++;
    }

    if (!$displaysection and $PAGE->user_is_editing() and has_capability('moodle/course:update', $context)) {
        // print stealth sections if present
        $modinfo = get_fast_modinfo($course);
        foreach ($sections as $section=>$thissection) {
            if (empty($modinfo->sections[$section])) {
                continue;
            }

            echo '<tr id="section-'.$section.'" class="section main clearfix orphaned hidden">'; 
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
        $select = new single_select(new moodle_url('/course/view.php', array('id'=>$course->id)), 'ctopics', $sectionmenu);
        $select->label = get_string('jumpto');
        $select->class = 'jumpmenu';
        $select->formid = 'sectionmenu';
        echo $OUTPUT->render($select);
    }

    // Establish persistance when we have loaded.
    // Reload the state of the toggles from the data contained within the cookie.
    // Restore the state of the toggles from the cookie if not in 'Show topic x' mode, otherwise show that topic.
    if ($displaysection == 0) {
        echo $PAGE->requires->js_function_call('reload_toggles',array($course->numsections));
    } else {
        echo $PAGE->requires->js_function_call('show_topic',array($displaysection));
    }