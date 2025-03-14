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
 * code change. Full installation instructions, code adaptions and credits are included in the 'Readme.md' file.
 *
 * @package    format_topcoll
 * @copyright  &copy; 2009-onwards G J Barnard in respect to modifications of standard topics format.
 * @author     G J Barnard - {@link https://moodle.org/user/profile.php?id=442195}
 * @link       https://docs.moodle.org/en/Collapsed_Topics_course_format
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// phpcs:disable moodle.Files.LangFilesOrdering

// Used in format.php.
$string['topcolltoggle'] = 'Toggle';
$string['topcollsidewidthlang'] = 'en-28px';

// Toggle all - Moodle Tracker CONTRIB-3190.
$string['topcollall'] = 'sections.';  // Leave as AMOS maintains only the latest translation - so previous versions are still supported.
$string['topcollopened'] = 'Open all';
$string['topcollclosed'] = 'Close all';
$string['sctopenall'] = 'Open all {$a}';
$string['sctcloseall'] = 'Close all {$a}';
$string['toggleclose'] = 'Close';
$string['toggleopen'] = 'Open';

$string['settoggleallenabled'] = 'Toggle all enabled';
$string['settoggleallenabled_help'] = 'Toggle all functionality enabled.';
$string['defaulttoggleallenabled'] = 'Toggle all enabled';
$string['defaulttoggleallenabled_desc'] = 'States if the toggle all functionality should be enabled.';

// Moodle 2.0 Enhancement - Moodle Tracker MDL-15252, MDL-21693 & MDL-22056 - http://docs.moodle.org/en/Development:Languages.
$string['sectionname'] = 'Section';
$string['pluginname'] = 'Collapsed Topics';
$string['plugin_description'] = 'The course is divided into toggleable sections.';
$string['section0name'] = 'General';

// MDL-26105.
$string['page-course-view-topcoll'] = 'Any course main page in the collapsed topics format';
$string['page-course-view-topcoll-x'] = 'Any course page in the collapsed topics format';

$string['newsection'] = 'New section';
$string['hidefromothers'] = 'Hide';
$string['showfromothers'] = 'Show';
$string['currentsection'] = 'This section';
$string['editsection'] = 'Edit section';
$string['deletesection'] = 'Delete section';
// These are 'sections' as they are only shown in 'section' based structures.
$string['markedthissection'] = 'This section is highlighted as the current section';
$string['markthissection'] = 'Highlight this section as the current section';

// View single section.
$string['viewonly'] = 'View only \'{$a->sectionname}\'';
$string['setviewsinglesectionenabled'] = 'View single section enabled';
$string['setviewsinglesectionenabled_help'] = 'View single section functionality enabled.';
$string['defaultviewsinglesectionenabled'] = 'View single section enabled';
$string['defaultviewsinglesectionenabled_desc'] = 'States if the view single section functionality should be enabled.';

// MDL-51802.
$string['editsectionname'] = 'Edit section name';
$string['newsectionname'] = 'New name for section {$a}';

// Reset.
$string['resetgrp'] = 'Reset:';
$string['resetallgrp'] = 'Reset all:';

// Layout enhancement - Moodle Tracker CONTRIB-3378.
$string['formatsettings'] = 'Format reset settings'; // CONTRIB-3529.
$string['formatsettingsinformation'] = '<br />To reset the settings of the course format to the defaults, click on the icon to the right.';
$string['setlayout'] = 'Set layout';

// Negative view of layout, kept for previous versions until such time as they are updated.
$string['setlayout_default'] = 'Default'; // 1.
$string['setlayout_no_toggle_section_x'] = 'No toggle section x'; // 2.
$string['setlayout_no_section_no'] = 'No section number'; // 3.
$string['setlayout_no_toggle_section_x_section_no'] = 'No toggle section x and section number'; // 4.
$string['setlayout_no_toggle_word'] = 'No toggle word'; // 5.
$string['setlayout_no_toggle_word_toggle_section_x'] = 'No toggle word and toggle section x'; // 6.
$string['setlayout_no_toggle_word_toggle_section_x_section_no'] = 'No toggle word, toggle section x and section number'; // 7.
// Positive view of layout.
$string['setlayout_all'] = "Toggle word, 'Topic x' / 'Week x' / 'Day x' and section number"; // 1.
$string['setlayout_toggle_word_section_number'] = 'Toggle word and section number'; // 2.
$string['setlayout_toggle_word_section_x'] = "Toggle word and 'Topic x' / 'Week x' / 'Day x'"; // 3.
$string['setlayout_toggle_word'] = 'Toggle word'; // 4.
$string['setlayout_toggle_section_x_section_number'] = "'Topic x' / 'Week x' / 'Day x' and section number"; // 5.
$string['setlayout_section_number'] = 'Section number'; // 6.
$string['setlayout_no_additions'] = 'No additions'; // 7.
$string['setlayout_toggle_section_x'] = "'Topic x' / 'Week x' / 'Day x'"; // 8.

$string['setlayoutelements'] = 'Elements';
$string['setlayoutstructure'] = 'Structure';
$string['setlayoutstructuretopic'] = 'Topic';
$string['setlayoutstructureweek'] = 'Week';
$string['setlayoutstructurelatweekfirst'] = 'Current week first';
$string['setlayoutstructurecurrenttopicfirst'] = 'Current topic first';
$string['setlayoutstructureday'] = 'Day';
$string['resetlayout'] = 'Layout'; // CONTRIB-3529.
$string['resetalllayout'] = 'Layouts';
$string['layoutstructuretopics'] = 'topics';
$string['layoutstructureweeks'] = 'weeks';
$string['layoutstructuredays'] = 'days';

// Coursesetting - Show addtional data for modules.
$string['enableadditionalmoddata'] = 'Enable additional information';
$string['enableadditionalmoddatadesc'] = 'This is a \'Site level\' switch to turn the activity information on or off.  It needs to be \'on\' for the related settings that operate at a course level to take effect.  As this functionality can be computationally expensive, then it is strongly suggested that you undertake full testing before using on a production system.  Note: Purges the cache caches when changed.';

$string['showadditionalmoddata'] = 'Show additional information for: {$a} in the course';
$string['showadditionalmoddata_help'] = 'Allow all users to see the activity deadline and users with grading permission to see the number of submissions on the course page for activities.';
$string['defaultshowadditionalmoddata'] = 'Default course \'Show additional information\'';
$string['defaultshowadditionalmoddatadesc'] = 'If an activity is set at site level (below) to show additional information then this setting states the default state of the course specific instance of it.';
$string['resetactivitymeta'] = 'Additional module information';
$string['resetallactivitymeta'] = 'All additional module information';
$string['resetactivitymeta_help'] = 'Resets the additional module information to follow the site default value.';
$string['resetallactivitymeta_help'] = 'Resets all the additional module information to follow the site default value.';

$string['courseadditionalmoddatamaxstudents'] = 'Set the maximum number of students on a course that \'Show additional information\' will apply to';
$string['courseadditionalmoddatamaxstudentsdesc'] = 'Additional information can take time to calculate, especially on large courses, so here you can set the maximum number of students that a couse can have for the functionality to show on that course.  Above that value, the \'Additional information\' will NOT be calculated or show regardless of the course settings!  A value of \'0\' means \'unlimited\'.  Note: Purges the cache caches when changed.';

$string['courseadditionalmoddatastudentsinfo'] = 'Additional information status:';
$string['courseadditionalmoddatastudentsinfounlimited'] = 'Additional information will show for the enabled activities for {$a} students.';
$string['courseadditionalmoddatastudentsinfolimitedshow'] = 'Additional information will show for the enabled activities for {$a->students} students as the number does not exceed the maximum \'{$a->maxstudents}\' set by the administrator on the Collapsed Topics course format setting \'courseadditionalmoddatamaxstudents\'.';
$string['courseadditionalmoddatastudentsinfolimitednoshow'] = 'Additional information will NOT show for the enabled activities for {$a->students} students as the number exceededs the maximum \'{$a->maxstudents}\' set by the administrator on the Collapsed Topics course format setting \'courseadditionalmoddatamaxstudents\'.';

$string['coursesectionactivityfurtherinformation'] = 'Course page further information';
$string['coursesectionactivityfurtherinformation_desc'] = 'Site level course page further information settings';
$string['coursesectionactivityfurtherinformationassign'] = 'Show assignment information';
$string['coursesectionactivityfurtherinformationassigndesc'] = 'Allow assignment information to be selected to be shown on a course.  For teachers / admins, show number of submissions.';
$string['coursesectionactivityfurtherinformationquiz'] = 'Show quiz information';
$string['coursesectionactivityfurtherinformationquizdesc'] = 'Allow quiz information to be selected to be shown on a course.  For teachers / admins, show number of submissions.';
$string['coursesectionactivityfurtherinformationchoice'] = 'Show choice information';
$string['coursesectionactivityfurtherinformationchoicedesc'] = 'Allow choice information to be selected to be shown on a course.  For teachers / admins, show number of submissions.';
$string['coursesectionactivityfurtherinformationfeedback'] = 'Show feedback information';
$string['coursesectionactivityfurtherinformationfeedbackdesc'] = 'Allow feedback information to be selected to be shown on a course.  For teachers / admins, show number of submissions.';
$string['coursesectionactivityfurtherinformationforum'] = 'Show forum information';
$string['coursesectionactivityfurtherinformationforumdesc'] = 'Allow forum information to be selected to be shown on a course.  For teachers / admins, show number of contributions when whole forum grading is on.';
$string['coursesectionactivityfurtherinformationlesson'] = 'Show lesson information';
$string['coursesectionactivityfurtherinformationlessondesc'] = 'Allow lesson information to be selected to be shown on a course.  For teachers / admins, show number of submissions.';
$string['coursesectionactivityfurtherinformationdata'] = 'Show database information';
$string['coursesectionactivityfurtherinformationdatadesc'] = 'Allow data information to be selected to be shown on a course.  For teachers / admins, show number of submissions.';

$string['cachedef_activitystudentrolescache'] = 'Caches the student roles.';
$string['cachedef_activitymodulecountcache'] = 'Caches the number of students who can access a given module on a given course.';
$string['cachedef_activitystudentscache'] = 'Caches the ids of the students on a given course.';
$string['cachedef_activityusercreatedcache'] = 'Caches the ids of the new users on a given course.';
$string['cannotgetactivitycacheslock'] = 'Cannot get activity caches lock for course id {$a}.';

// Colour enhancement - Moodle Tracker CONTRIB-3529.
$string['setcolour'] = 'Colour';
$string['colourrule'] = "Please enter a valid RGB colour, six hexadecimal digits or '-' for default.";
$string['settoggleforegroundcolour'] = 'Toggle foreground';
$string['settoggleforegroundhovercolour'] = 'Toggle foreground hover';
$string['settogglebackgroundcolour'] = 'Toggle background';
$string['settogglebackgroundhovercolour'] = 'Toggle background hover';
$string['resetcolour'] = 'Colour';
$string['resetallcolour'] = 'Colours';

// Columns enhancement.
$string['setlayoutcolumns'] = 'Columns';
$string['setlayoutcolumns_help'] = 'How many columns to use.';
$string['one'] = 'One';
$string['two'] = 'Two';
$string['three'] = 'Three';
$string['four'] = 'Four';
$string['setlayoutcolumnorientation'] = 'Column orientation';
$string['setlayoutcolumnorientation_help'] = 'Dynamic - Number sections per \'row\' adjust to window size, \'Column\' setting not currently used.<br>Horizontal - Sections go left to right.<br>Vertical - Sections go top to bottom.';
$string['columndynamic'] = 'Dynamic';
$string['columnhorizontal'] = 'Horizontal';
$string['columnvertical'] = 'Vertical';

// Flexible modules.
$string['setflexiblemodules'] = 'Flexible modules';
$string['setflexiblemodules_help'] = 'Use flexible modules?';

// MDL-34917 - implemented in M2.5 but needs to be here to support M2.4- versions.
$string['maincoursepage'] = 'Main course page';

// Help.
$string['setlayoutelements_help'] = 'How much information about the toggles / sections you wish to be displayed.';
$string['setlayoutstructure_help'] = "The layout structure of the course.  You can choose between:<br />'Topics' - where each section is presented as a topic in section number order.<br />'Weeks' - where each section is presented as a week in ascending week order from the start date of the course.<br />'Current week first' - which is the same as weeks but the current week is shown at the top and preceding weeks in descending order are displayed below except in editing mode where the structure is the same as 'Weeks'.<br />'Current topic first' - which is the same as 'Topics' except that the current topic is shown at the top if it has been set.<br />'Day' - where each section is presented as a day in ascending day order from the start date of the course.";
$string['setlayout_help'] = 'Contains the settings to do with the layout of the format within the course.';
$string['resetlayout_help'] = 'Resets the layout element, structure, columns, toggle all, view single section, icon position, one section and shown section summary to follow the site default value.';
$string['resetalllayout_help'] = 'Resets the layout element, structure, columns, toggle all, view single section, icon position, one section and shown section summary to follow the site default value.';
// Moodle Tracker CONTRIB-3529.
$string['setcolour_help'] = 'Contains the settings to do with the colour of the format within the course.';
$string['settoggleforegroundcolour_help'] = 'Sets the colour of the text on the toggle.';
$string['settoggleforegroundhovercolour_help'] = 'Sets the colour of the text on the toggle when the mouse moves over it.';
$string['settogglebackgroundcolour_help'] = 'Sets the background colour of the toggle.';
$string['settogglebackgroundhovercolour_help'] = 'Sets the background colour of the toggle when the mouse moves over it.';
$string['resetcolour_help'] = 'Resets the colours and opacities to follow the site default value.';
$string['resetallcolour_help'] = 'Resets the colours and opacities to follow the site default value.';
// Columns enhancement.

// Moodle 2.4 Course format refactoring - MDL-35218.
$string['ctreset'] = 'Collapsed Topics reset options';
$string['ctreset_help'] = 'Reset to Collapsed Topics defaults.';

// Toggle alignment - CONTRIB-4098.
$string['settogglealignment'] = 'Toggle text alignment';
$string['settogglealignment_help'] = 'Sets the alignment of the text in the toggle.';
$string['left'] = 'Left';
$string['center'] = 'Centre';
$string['right'] = 'Right';
$string['resettogglealignment'] = 'Toggle alignment';
$string['resetalltogglealignment'] = 'Toggle alignments';
$string['resettogglealignment_help'] = 'Resets the toggle alignment to follow the site default value.';
$string['resetalltogglealignment_help'] = 'Resets all the toggle alignment to follow the site default value.';

// Icon position - CONTRIB-4470.
$string['settoggleiconposition'] = 'Icon position';
$string['settoggleiconposition_help'] = 'States that the icon should be on the left or the right of the toggle text.';
$string['defaulttoggleiconposition'] = 'Icon position';
$string['defaulttoggleiconposition_desc'] = 'States if the icon should be on the left or the right of the toggle text.';

// Icon set enhancement.
$string['settoggleiconset'] = 'Icon set';
$string['settoggleiconset_help'] = 'Sets the icon set of the toggle.';
$string['settoggleiconfontclosed'] = 'Closed toggle icon font';
$string['settoggleiconfontclosed_help'] = 'When \'toggleiconset\' is set to \'Icon font\', this states the default CSS classes to use for the closed icon, i.e. see the FontAwesome icon classes.  If set to \'-\' then the default is used.';
$string['settoggleiconfontopen'] = 'Open toggle icon font';
$string['settoggleiconfontopen_help'] = 'When \'toggleiconset\' is set to \'Icon font\', this states the default CSS classes to use for the open icon, i.e. see the FontAwesome icon classes.  If set to \'-\' then the default is used.';
$string['settoggleallhover'] = 'Toggle all icon hover';
$string['settoggleallhover_help'] = 'Sets if the toggle all icons will change when the mouse moves over them.';
$string['arrow'] = 'Arrow';
$string['bulb'] = 'Bulb';
$string['cloud'] = 'Cloud';
$string['eye'] = 'Eye';
$string['folder'] = 'Folder';
$string['groundsignal'] = 'Ground signal';
$string['led'] = 'Light emitting diode';
$string['point'] = 'Point';
$string['power'] = 'Power';
$string['radio'] = 'Radio';
$string['smiley'] = 'Smiley';
$string['square'] = 'Square';
$string['sunmoon'] = 'Sun / Moon';
$string['switch'] = 'Switch';
$string['tif'] = 'Icon font';
$string['resettoggleiconset'] = 'Toggle icon set';
$string['resetalltoggleiconset'] = 'Toggle icon sets';
$string['resettoggleiconset_help'] = 'Resets the toggle icon set and toggle all hover to follow the site default value.';
$string['resetalltoggleiconset_help'] = 'Resets the toggle icon set and toggle all hover to follow the site default value.';

// One section enhancement.
$string['onesection'] = 'One section';
$string['onesection_help'] = 'States if only one section should be open at any given time.  Note: Ignored when editing to allow activities and resources to be moved around the sections.';
$string['defaultonesection'] = 'One section';
$string['defaultonesection_desc'] = "States if only one section should be open at any given time.  Note: Ignored when editing to allow activities and resources to be moved around the sections.";
$string['defaultonesectioniconfont'] = 'One section icon font';
$string['defaultonesectioniconfont_desc'] = 'State the icon font class to use for the one section link icon, i.e. see the FontAwesome icon classes.  If empty, then default icon \'one_section\' in the format\'s \'pix\' folder will be used.';

// Site Administration -> Plugins -> Course formats -> Collapsed Topics.
$string['defaultheadingsub'] = 'Defaults';
$string['defaultheadingsubdesc'] = 'Default settings that can be overridden at the course level';
$string['configurationheadingsub'] = 'Configuration';
$string['configurationheadingsubdesc'] = 'Site level configuration settings';

$string['off'] = 'Off';
$string['on'] = 'On';

$string['default'] = 'Default - {$a}';

$string['defaultlayoutelement'] = 'Layout';
// Negative view of layout, kept for previous versions until such time as they are updated.
$string['defaultlayoutelement_desc'] = "The layout setting can be one of:<br />'Default' with everything displayed.<br />No 'Topic x' / 'Week x' / 'Day x'.<br />No section number.<br />No 'Topic x' / 'Week x' / 'Day x' and no section number.<br />No 'Toggle' word.<br />No 'Toggle' word and no 'Topic x' / 'Week x' / 'Day x'.<br />No 'Toggle' word, no 'Topic x' / 'Week x' / 'Day x' and no section number.";
// Positive view of layout.
$string['defaultlayoutelement_descpositive'] = "The layout setting can be one of:<br />Toggle word, 'Topic x' / 'Week x' / 'Day x' and section number.<br />Toggle word and 'Topic x' / 'Week x' / 'Day x'.<br />Toggle word and section number.<br />'Topic x' / 'Week x' / 'Day x' and section number.<br />Toggle word.<br />'Topic x' / 'Week x' / 'Day x'.<br />Section number.<br />No additions.";

$string['defaultlayoutstructure'] = 'Structure configuration';
$string['defaultlayoutstructure_desc'] = "The structure setting can be one of:<br />Topic<br />Week<br />Latest Week First<br />Current Topic First<br />Day";

$string['defaultlayoutcolumns'] = 'Number of columns';
$string['defaultlayoutcolumns_desc'] = "Number of columns between one and four.";

$string['defaultlayoutcolumnorientation'] = 'Column orientation';
$string['defaultlayoutcolumnorientation_desc'] = 'The default column orientation: Dynamic - Number sections per \'row\' adjust to window size, \'Column\' setting not currently used.<br>Horizontal - Sections go left to right.<br>Vertical - Sections go top to bottom.';

$string['defaultflexiblemodules'] = 'Flexible modules';
$string['defaultflexiblemodules_desc'] = 'Use flexible modules?';

$string['defaulttgfgcolour'] = 'Toggle foreground colour';
$string['defaulttgfgcolour_desc'] = "Toggle foreground colour in hexidecimal RGB.";

$string['defaulttgfghvrcolour'] = 'Toggle foreground hover colour';
$string['defaulttgfghvrcolour_desc'] = "Toggle foreground hover colour in hexidecimal RGB.";

$string['defaulttgbgcolour'] = 'Toggle background colour';
$string['defaulttgbgcolour_desc'] = "Toggle background colour in hexidecimal RGB.";

$string['defaulttgbghvrcolour'] = 'Toggle background hover colour';
$string['defaulttgbghvrcolour_desc'] = "Toggle background hover colour in hexidecimal RGB.";

$string['defaulttogglealignment'] = 'Toggle text alignment';
$string['defaulttogglealignment_desc'] = "'Left', 'Centre' or 'Right'.";

$string['defaulttoggleiconset'] = 'Toggle icon set';
$string['defaulttoggleiconset_desc'] = '<table><tbody><tr><td>Arrow</td><td>{$a->arrow}</td></tr><tr><td>Bulb</td><td>{$a->bulb}</td></tr><tr><td>Cloud</td><td>{$a->cloud}</td></tr><tr><td>Eye</td><td>{$a->eye}</td></tr><tr><td>Folder</td><td>{$a->folder}</td></tr><tr><td>Ground Signal</td><td>{$a->groundsignal}</td></tr><tr><td>Light Emitting Diode</td><td>{$a->led}</td></tr><tr><td>Point</td><td>{$a->point}</td></tr><tr><td>Power</td><td>{$a->power}</td></tr><tr><td>Radio</td><td>{$a->radio}</td></tr><tr><td>Smiley</td><td>{$a->smiley}</td></tr><tr><td>Square</td><td>{$a->square}</td></tr><tr><td>Sun / Moon</td><td>{$a->sunmoon}</td></tr><tr><td>Switch</td><td>{$a->switch}</td></tr><tr><td>Icon font</td><td>{$a->tif}</td></tr></tbody></table>';

$string['defaulttoggleiconfontclosed'] = 'Closed toggle icon font';
$string['defaulttoggleiconfontclosed_desc'] = 'When \'defaulttoggleiconset\' is set to \'Icon font\', this states the default CSS classes to use for the closed icon, i.e. see the FontAwesome icon classes.';

$string['defaulttoggleiconfontopen'] = 'Open toggle icon font';
$string['defaulttoggleiconfontopen_desc'] = 'When \'defaulttoggleiconset\' is set to \'Icon font\', this states the default CSS classes to use for the open icon, i.e. see the FontAwesome icon classes.';

$string['defaulttoggleallhover'] = 'Toggle all icon hovers';
$string['defaulttoggleallhover_desc'] = "'No' or 'Yes'.";

$string['defaulttogglepersistence'] = 'Toggle persistence';
$string['defaulttogglepersistence_desc'] = "'On' or 'Off'.  Turn off for an AJAX performance increase but user toggle selections will not be remembered on page refresh or revisit.<br />Note: When turning persistence off, please remove any rows containing 'topcoll_toggle_x' in the 'name' field of the 'user_preferences' table in the database.  Where the 'x' in 'topcoll_toggle_x' will be a course id.  This is to save space if you do not intend to turn it back on.";

$string['defaultuserpreference'] = 'Initial toggle state';
$string['defaultuserpreference_desc'] = 'States what to do with the toggles when the user first accesses the course, the state of additional sections when they are added or toggle persistence is off.';

// Toggle opacities.
$string['settoggleforegroundopacity'] = 'Toggle foreground opacity';
$string['settoggleforegroundopacity_help'] = 'Sets the opacity of the text on the toggle between 0 and 1 in 0.1 increments.';
$string['defaulttgfgopacity'] = 'Toggle foreground opacity';
$string['defaulttgfgopacity_desc'] = "Toggle foreground text opacity between 0 and 1 in 0.1 increments.";

$string['settoggleforegroundhoveropacity'] = 'Toggle foreground hover opacity';
$string['settoggleforegroundhoveropacity_help'] = 'Sets the opacity of the text on hover on the toggle between 0 and 1 in 0.1 increments.';
$string['defaulttgfghvropacity'] = 'Toggle foreground hover opacity';
$string['defaulttgfghvropacity_desc'] = "Toggle foreground text on hover opacity between 0 and 1 in 0.1 increments.";

$string['settogglebackgroundopacity'] = 'Toggle background opacity';
$string['settogglebackgroundopacity_help'] = 'Sets the opacity of the background on the toggle between 0 and 1 in 0.1 increments.';
$string['defaulttgbgopacity'] = 'Toggle background opacity';
$string['defaulttgbgopacity_desc'] = "Toggle background opacity between 0 and 1 in 0.1 increments.";

$string['settogglebackgroundhoveropacity'] = 'Toggle background hover opacity';
$string['settogglebackgroundhoveropacity_help'] = 'Sets the opacity of the background on hover on the toggle between 0 and 1 in 0.1 increments.';
$string['defaulttgbghvropacity'] = 'Toggle background hover opacity';
$string['defaulttgbghvropacity_desc'] = "Toggle background on hover opacity between 0 and 1 in 0.1 increments.";

// Toggle icon size.
$string['defaulttoggleiconsize'] = 'Toggle icon size';
$string['defaulttoggleiconsize_desc'] = "Icon size: Small = 16px, Medium = 24px and Large = 32px, or Icon font: Small = 0.8em, Medium = 1.2em and Large = 1.8em.";
$string['small'] = 'Small';
$string['medium'] = 'Medium';
$string['large'] = 'Large';

// Toggle border radius.
$string['defaulttoggleborderradiustl'] = 'Toggle top left border radius';
$string['defaulttoggleborderradiustl_desc'] = 'Border top left radius of the toggle.';
$string['defaulttoggleborderradiustr'] = 'Toggle top right border radius';
$string['defaulttoggleborderradiustr_desc'] = 'Border top right radius of the toggle.';
$string['defaulttoggleborderradiusbr'] = 'Toggle bottom right border radius';
$string['defaulttoggleborderradiusbr_desc'] = 'Border bottom right radius of the toggle.';
$string['defaulttoggleborderradiusbl'] = 'Toggle bottom left border radius';
$string['defaulttoggleborderradiusbl_desc'] = 'Border bottom left radius of the toggle.';
$string['em0_0'] = '0.0em';
$string['em0_1'] = '0.1em';
$string['em0_2'] = '0.2em';
$string['em0_3'] = '0.3em';
$string['em0_4'] = '0.4em';
$string['em0_5'] = '0.5em';
$string['em0_6'] = '0.6em';
$string['em0_7'] = '0.7em';
$string['em0_8'] = '0.8em';
$string['em0_9'] = '0.9em';
$string['em1_0'] = '1.0em';
$string['em1_1'] = '1.1em';
$string['em1_2'] = '1.2em';
$string['em1_3'] = '1.3em';
$string['em1_4'] = '1.4em';
$string['em1_5'] = '1.5em';
$string['em1_6'] = '1.6em';
$string['em1_7'] = '1.7em';
$string['em1_8'] = '1.8em';
$string['em1_9'] = '1.9em';
$string['em2_0'] = '2.0em';
$string['em2_1'] = '2.1em';
$string['em2_2'] = '2.2em';
$string['em2_3'] = '2.3em';
$string['em2_4'] = '2.4em';
$string['em2_5'] = '2.5em';
$string['em2_6'] = '2.6em';
$string['em2_7'] = '2.7em';
$string['em2_8'] = '2.8em';
$string['em2_9'] = '2.9em';
$string['em3_0'] = '3.0em';
$string['em3_1'] = '3.1em';
$string['em3_2'] = '3.2em';
$string['em3_3'] = '3.3em';
$string['em3_4'] = '3.4em';
$string['em3_5'] = '3.5em';
$string['em3_6'] = '3.6em';
$string['em3_7'] = '3.7em';
$string['em3_8'] = '3.8em';
$string['em3_9'] = '3.9em';
$string['em4_0'] = '4.0em';

$string['formatresponsive'] = 'Format responsive';
$string['formatresponsive_desc'] = "Turn on if you are using a non-responsive theme and the format will adjust to the screen size / device.  Turn off if you are using a responsive theme.  Bootstrap 2.3.2 support is built in, for other frameworks and versions, override the methods 'get_row_class()' and 'get_column_class()' in renderer.php.";

// Show section summary when collapsed.
$string['setshowsectionsummary'] = 'Show the section summary when collapsed';
$string['setshowsectionsummary_help'] = 'States if the section summary will always be shown regardless of toggle state.';
$string['defaultshowsectionsummary'] = 'Show the section summary when collapsed';
$string['defaultshowsectionsummary_desc'] = 'States if the section summary will always be shown regardless of toggle state.';

// Do not show date.
$string['donotshowdate'] = 'Do not show the date';
$string['donotshowdate_help'] = 'Do not show the date when using a weekly based structure and \'Use default section name\' has been un-ticked.';

// Capabilities.
$string['topcoll:changelayout'] = 'Change or reset the layout';
$string['topcoll:changecolour'] = 'Change or reset the colour';
$string['topcoll:changetogglealignment'] = 'Change or reset the toggle alignment';
$string['topcoll:changetoggleiconset'] = 'Change or reset the toggle icon set';
$string['topcoll:changeactivitymeta'] = 'Change or reset the activity meta';

// Instructions.
$string['instructions'] = 'Instructions: Clicking on the section name will show / hide the section.';
$string['displayinstructions'] = 'Display instructions';
$string['displayinstructions_help'] = 'States that the instructions should be displayed to the user or not.';
$string['defaultdisplayinstructions'] = 'Display instructions to users';
$string['defaultdisplayinstructions_desc'] = "Display instructions to users informing them how to use the toggles.  Can be yes or no.";
$string['resetdisplayinstructions'] = 'Display instructions';
$string['resetalldisplayinstructions'] = 'Display instructions';
$string['resetdisplayinstructions_help'] = 'Resets the display instructions to follow the site default value.';
$string['resetalldisplayinstructions_help'] = 'Resets the display instructions to follow the site default value.';

// Activity display *********************************.
$string['feedbackavailable'] = 'Feedback available';
$string['xofyanswered'] = '{$a->completed} of {$a->participants} answered';
$string['xofyattempted'] = '{$a->completed} of {$a->participants} attempted';
$string['xofycontributed'] = '{$a->completed} of {$a->participants} contributed';
$string['xofyposted'] = '{$a->completed} of {$a->participants} posted';
$string['xofysubmitted'] = '{$a->completed} of {$a->participants} submitted';
$string['xanswered'] = '{$a->completed} answered';
$string['xattempted'] = '{$a->completed} attempted';
$string['xcontributed'] = '{$a->completed} contributed';
$string['xposted'] = '{$a->completed} posted';
$string['xsubmitted'] = '{$a->completed} submitted';
$string['xungraded'] = '{$a} ungraded';

// Readme.
$string['readme_title'] = 'Collapsed Topics read-me';
$string['readme_desc'] = 'Please click on \'{$a->url}\' for lots more information about Collapsed Topics.';

// Toggle Display Blocks.
$string['defaultdisplayblocks'] = 'Blocks to display';
$string['defaultdisplayblocks_desc'] = "Choose the blocks to display in the course when it is first created and this format is selected.  Use the 'Ctrl' key in combination with the mouse to select more than one or none.  Note: This setting will only apply at actual course creation and no other time, i.e. changing to Collapsed Topics from another format.";
$string['defaultdisplayblocksloc'] = 'Block location for display';
$string['defaultdisplayblocksloc_desc'] = "Choose the location for the blocks chosen above to display, pre or post side.";
$string['sidepost'] = 'Post';
$string['sidepre'] = 'Pre';

// Information.
$string['information'] = 'Information';
$string['informationsettings'] = 'Information settings';
$string['informationsettingsdesc'] = 'Collapsed Topics course format information';
$string['informationchanges'] = 'Changes';
$string['settings'] = 'Settings';
$string['settingssettings'] = 'Settings settings';
$string['settingssettingsdesc'] = 'Collapsed Topics course format settings';
$string['love'] = 'love';
$string['versioninfo'] = 'Release {$a->release}, version {$a->version} on Moodle {$a->moodle}.  Made with {$a->love} in Great Britain.';
$string['versionalpha'] = 'Alpha version - Almost certainly contains bugs.  This is a development version for developers \'only\'!  Don\'t even think of installing on a production server!';
$string['versionbeta'] = 'Beta version - Likely to contain bugs.  Ready for testing by administrators on a test server only.';
$string['versionrc'] = 'Release candidate version - May contain bugs.  Check completely on a test server before considering on a production server.';
$string['versionstable'] = 'Stable version - Could contain bugs.  Check on a test server before installing on your production server.';

// Privacy.
$string['privacy:metadata:preference:toggle'] = 'The state of the toggles on a course.';
$string['privacy:request:preference:toggle'] = 'The course id "{$a->name}" has the value "{$a->value}" which represents "{$a->decoded}" for the state of the toggles.';
