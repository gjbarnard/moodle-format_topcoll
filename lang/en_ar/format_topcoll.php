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

// English Pirate Translation of Collapsed Topics Course Format

// Used by the Moodle Core for identifing the format and displaying in the list of formats for a course in its settings.
// Possibly legacy to be removed after Moodle 2.0 is stable.
$string['nametopcoll']='Collapsed Topics';
$string['formattopcoll']='Collapsed Topics';

// Used in format.php.
$string['topcolltoggle']='Toggle';
$string['topcollsidewidth']='40px';

// Toggle all - Moodle Tracker CONTRIB-3190.
$string['topcollall']='all sections.';
$string['topcollopened']='Untie';
$string['topcollclosed']='Tie';

// Moodle 2.0 Enhancement - Moodle Tracker MDL-15252, MDL-21693 & MDL-22056 - http://docs.moodle.org/en/Development:Languages.
$string['sectionname'] = 'Section';
$string['pluginname'] = 'Collapsed Topics';
$string['section0name'] = 'General';

// MDL-26105.
$string['page-course-view-topcoll'] = 'Any course main page in collapsed topics format';
$string['page-course-view-topcoll-x'] = 'Any course page in collapsed topics format';

// Layout enhancement - Moodle Tracker CONTRIB-3378.
$string['formatsettings'] = 'Ye format settings'; // CONTRIB-3529.
$string['setlayout'] = 'Set thee layout';
$string['setlayout_default'] = 'Default';
$string['setlayout_no_toggle_section_x'] = 'No toggle section x';
$string['setlayout_no_section_no'] = 'No section number';
$string['setlayout_no_toggle_section_x_section_no'] = 'No toggle section x and section number';
$string['setlayout_no_toggle_word'] = 'No toggle word';
$string['setlayout_no_toggle_word_toggle_section_x'] = 'No toggle word and toggle section x';
$string['setlayout_no_toggle_word_toggle_section_x_section_no'] = 'No toggle word, toggle section x and section number';
$string['setlayoutelements'] = 'Set thee elements';
$string['setlayoutstructure'] = 'Set thee structure';
$string['setlayoutstructuretopic']='Treasure Chest';
$string['setlayoutstructureweek']='Sailing Week';
$string['setlayoutstructureday'] = 'Sailing Day';
$string['setlayoutstructurelatweekfirst']='Current Sailing Week First';
$string['setlayoutstructurecurrenttopicfirst']='Current Treasure Chest First';
$string['resetlayout'] = 'Reset thee layout'; // CONTRIB-3529.
$string['resetalllayout'] = 'Reset thee layouts for all thy Collapsed Topics courses';

// Colour enhancement - Moodle Tracker CONTRIB-3529.
$string['setcolour'] = 'Set thee colour';
$string['colourrule'] = "Enter a valid RGB colour, a '#' and then six hexadecimal digits or walk thy plank.";
$string['settoggleforegroundcolour'] = 'Thy toggle foreground';
$string['settogglebackgroundcolour'] = 'Thy toggle background';
$string['settogglebackgroundhovercolour'] = 'Thy toggle background hover';
$string['resetcolour'] = 'Reset thee colour';
$string['resetallcolour'] = 'Reset thee colours for all thy Collapsed Topics courses';

// Columns enhancement.
$string['setlayoutcolumns'] = 'Set thee columns';
$string['one'] = 'One';
$string['two'] = 'Two';
$string['three'] = 'Three';
$string['four'] = 'Four';
$string['setlayoutcolumnorientation'] = 'Set the column orientation';
$string['columnvertical'] = 'Vertical as a mast';
$string['columnhorizontal'] = 'Horizontal as a cannon';

// Temporary until MDL-34917 in core.
$string['maincoursepage'] = 'Ye main course page';

// Help.
$string['setlayoutelements_help']='How much information about thee toggles / sections you wish to be displayed.';
$string['setlayoutstructure_help']="Avast ye landlubbers, this be thee layout structure of thee course.  Ye choose between:

'Treasure Chest' - where each section is presented as thy treasure chest in section number order.

'Sailing Week' - where each section is presented as thy week in ascending week order.

'Current Sailing Week First' - which is the same as weeks but thee current week is shown at thee top and preceding weeks in decending order are displayed below except in editing mode where thee structure is thy same as 'Weeks'.

'Current Treasure Chest First' - which is thee same as 'Treasure Chest' except that thee current treasure chest is shown at thee top if it has been set.

'Sailing Day' - where each section is presented as a day in thy ascending day order from thee start date of thee course.";
$string['setlayout_help'] = 'Contains thee settings to do with thee layout of the format within thy course.';
$string['resetlayout_help'] = 'Resets thee layout to thee default so it will be the same as a course the first time it is in thy Collapsed Topics format';
$string['resetalllayout_help'] = 'Resets the layout to the default values for all courses so it will be the same as a course the first time it is in the Collapsed Topics format.';
// Moodle Tracker CONTRIB-3529.
$string['setcolour_help'] = 'Contains thee settings to do with thy colour of the format within the course.';
$string['settoggleforegroundcolour_help'] = 'Sets thee colour of thy text on the toggle.';
$string['settogglebackgroundcolour_help'] = 'Sets thee background of thy toggle.';
$string['settogglebackgroundhovercolour_help'] = 'Sets thee background of thy toggle when thee mouse scuttles over it.';
$string['resetcolour_help'] = 'Resets thee colours to thee default values so it will be thee same as a course thy first time it is in thee Collapsed Topics format';
$string['resetallcolour_help'] = 'Resets thee colours to the default values for all courses so it will be thy same as a course the first time it is in thee Collapsed Topics format.';
// Columns enhancement.
$string['setlayoutcolumns_help'] = 'How many columns to use.';

// Toggle alignment - CONTRIB-4098.
$string['settogglealignment'] = 'Set thee toggle text alignment';
$string['settogglealignment_help'] = 'Sets thee alignment of thee text in thy toggle.';
$string['left'] = 'Port';
$string['center'] = 'Midships';
$string['right'] = 'Starboard';
$string['resettogglealignment'] = 'Reset thee toggle alignment';
$string['resetalltogglealignment'] = 'Reset thee toggle alignments for all thy Collapsed Topics courses';
$string['resettogglealignment_help'] = 'Resets thee toggle alignment to thy default values so thy will be thy same as a course thee first time it is in thee Collapsed Topics format.';
$string['resetalltogglealignment_help'] = 'Resets thee toggle alignment to thy default values for all courses so it will be thy same as a course thee first time it is in thee Collapsed Topics format.';

// Icon set enhancement.
$string['settoggleiconset'] = 'Set thee icon set';
$string['settoggleiconset_help'] = 'Sets thee icon set of thy toggle.';
$string['settoggleallhover'] = 'Set thee toggle all icon hover';
$string['settoggleallhover_help'] = 'Sets if thee toggle all icons will change when thy mouse moves over them.';
$string['arrow'] = 'Straight as an arrow';
$string['point'] = 'Point thee bow towards thy treasure';
$string['power'] = 'Power me hearties';
$string['resettoggleiconset'] = 'Reset thee toggle icon set';
$string['resetalltoggleiconset'] = 'Reset thee toggle icon set for all thy Collapsed Topics courses';
$string['resettoggleiconset_help'] = 'Resets thee toggle icon set and thy toggle all hover to thy default values so thy will be thee same as a course thee first time it is in thy Collapsed Topics format.';
$string['resetalltoggleiconset_help'] = 'Resets thee toggle icon set and thy toggle all hover to thy default values for all courses so it will be thy same as a course thee first time it is in thy Collapsed Topics format.';

// Site Administration -> Plugins -> Course formats -> Collapsed Topics or Manage course formats - Settings.
$string['off'] = 'Off';
$string['on'] = 'On';
$string['defaultcoursedisplay'] = 'Course display default';
$string['defaultcoursedisplay_desc'] = "Either show all thee sections on a single page or section zero and thee chosen section on page.";
$string['defaultlayoutelement'] = 'Default layout configuration';
$string['defaultlayoutelement_desc'] = "Thee layout setting can be one of:

'Default' with everything displayed.

No 'Treasure Chest x' / 'Sailing Week x' / 'Sailing Day x'.

No section number.

No 'Treasure Chest x' / 'Sailing Week x' / 'Sailing Day x' and no section number.

No 'Toggle' word.

No 'Toggle' word and no 'Treasure Chest x' / 'Sailing Week x' / 'Sailing Day x'.

No 'Toggle' word, no 'Treasure Chest x' / 'Sailing Week x' / 'Sailing Day x' and no section number.";

$string['defaultlayoutstructure'] = 'Default structure configuration';
$string['defaultlayoutstructure_desc'] = "Thee structure setting can be one of:

Treasure Chest

Sailing Week

Current Sailing Week First

Current Treasure Chest First

Sailing Day";

$string['defaultlayoutcolumns'] = 'Default number of columns';
$string['defaultlayoutcolumns_desc'] = "Number of columns between one and four.";

$string['defaultlayoutcolumnorientation'] = 'Default column orientation';
$string['defaultlayoutcolumnorientation_desc'] = "Thee default column orientation: Vertical or Horizontal.";

$string['defaulttgfgcolour'] = 'Default toggle foreground colour';
$string['defaulttgfgcolour_desc'] = "Toggle foreground colour in hexidecimal RGB.";

$string['defaulttgbgcolour'] = 'Default toggle background colour';
$string['defaulttgbgcolour_desc'] = "Toggle background colour in hexidecimal RGB.";

$string['defaulttgbghvrcolour'] = 'Default toggle background hover colour';
$string['defaulttgbghvrcolour_desc'] = "Toggle background hover colour in hexidecimal RGB.";

$string['defaulttogglepersistence'] = 'Toggle persistence';
$string['defaulttogglepersistence_desc'] = "'On' or 'Off'.  You may wish to turn off for an AJAX performance increase but user toggle selections will not be recalled on page refresh or revisit.

Note: If turning persistence off remove any rows containing 'topcoll_toggle_x' in the 'name' field
      of the 'user_preferences' table in the database.  Where thee 'x' in 'topcoll_toggle_x' will be
      a course id.";

$string['defaulttogglealignment'] = 'Default toggle text alignment';
$string['defaulttogglealignment_desc'] = "'Left', 'Centre' or 'Right'.";

$string['defaulttoggleiconset'] = 'Default toggle icon set';
$string['defaulttoggleiconset_desc'] = "'Arrow' => Arrow icon set.

'Point' => Point icon set.

'Power' => Power icon set.";

$string['defaulttoggleallhover'] = 'Default toggle all icon hovers';
$string['defaulttoggleallhover_desc'] = "'No' or 'Yes'.";

// Capabilities.
$string['topcoll:changelayout'] = 'Change or reset thee layout';
$string['topcoll:changecolour'] = 'Change or reset thee colour';
$string['topcoll:changetogglealignment'] = 'Change or reset thee toggle alignment';
$string['topcoll:changetoggleiconset'] = 'Change or reset thee toggle icon set';
