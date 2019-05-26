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
 * @version    See the value of '$plugin->version' in version.php.
 * @copyright  &copy; 2009-onwards G J Barnard in respect to modifications of standard topics format.
 * @author     G J Barnard - gjbarnard at gmail dot com and {@link http://moodle.org/user/profile.php?id=442195}
 * @link       http://docs.moodle.org/en/Collapsed_Topics_course_format
 * @license    http://www.gnu.org/copyleft/gpl.html GNU Public License
 *
 */

// English Pirate Translation of Collapsed Topics Course Format.

// Used in format.php.
$string['topcollsidewidthlang'] = 'en_ar-40px';

// These are 'topic' as they are only shown in 'topic' based structures.
$string['markedthissection'] = 'Thy topic is illuminated as thee current topic';
$string['markthissection'] = 'Illuminate thy topic as thee current topic';

// Toggle all - Moodle Tracker CONTRIB-3190.
$string['topcollopened'] = 'Untie';
$string['topcollclosed'] = 'Tie';

// Layout enhancement - Moodle Tracker CONTRIB-3378.
$string['formatsettings'] = 'Ye format settings'; // CONTRIB-3529.
$string['setlayout'] = 'Thee layout';
$string['setlayout_default'] = 'Default';
$string['setlayoutelements'] = 'Thee elements';
// Negative view of layout, kept for previous versions until such time as they are updated.
$string['setlayout_no_toggle_section_x'] = "Nay 'Treasure Chest x' / 'Sailing Week x' / 'Sailing Day x'"; // 2.
$string['setlayout_no_toggle_section_x_section_no'] = "Nay 'Treasure Chest x' / 'Sailing Week x' / 'Sailing Day x' and section number"; // 4.
$string['setlayout_no_toggle_word_toggle_section_x'] = "Nay toggle word and 'Treasure Chest x' / 'Sailing Week x' / 'Sailing Day x'"; // 6.
$string['setlayout_no_toggle_word_toggle_section_x_section_no'] = "Nay toggle word, 'Treasure Chest x' / 'Sailing Week x' / 'Sailing Day x' and section number"; // 7.
// Positive view of layout.
$string['setlayout_all'] = "Toggle word, 'Treasure Chest x' / 'Sailing Week x' / 'Sailing Day x' and section number"; // 1.
$string['setlayout_toggle_word_section_x'] = "Toggle word and 'Treasure Chest x' / 'Sailing Week x' / 'Sailing Day x'"; // 3.
$string['setlayout_toggle_section_x'] = "'Treasure Chest x' / 'Sailing Week x' / 'Sailing Day x' and section number"; // 5.
$string['setlayout_toggle_section_x'] = "'Treasure Chest x' / 'Sailing Week x' / 'Sailing Day x'"; // 8.

$string['setlayoutstructure'] = 'Thee structure';
$string['setlayoutstructuretopic'] = 'Treasure Chest';
$string['setlayoutstructureweek'] = 'Sailing Week';
$string['setlayoutstructureday'] = 'Sailing Day';
$string['setlayoutstructurelatweekfirst'] = 'Current Sailing Week First';
$string['setlayoutstructurecurrenttopicfirst'] = 'Current Treasure Chest First';
$string['resetlayout'] = 'Thee layout'; // CONTRIB-3529.
$string['resetalllayout'] = 'Thee layouts';

// Colour enhancement - Moodle Tracker CONTRIB-3529.
$string['setcolour'] = 'Thee colour';
$string['colourrule'] = "Enter a valid RGB colour, a '#' and then six hexadecimal digits or walk thy plank.";
$string['settoggleforegroundcolour'] = 'Thy toggle foreground';
$string['settogglebackgroundhovercolour'] = 'Thy toggle foreground hover';
$string['settoggleforegroundcolour'] = 'Thy toggle foreground';
$string['settogglebackgroundhovercolour'] = 'Thy toggle background hover';
$string['resetcolour'] = 'Thee colour';
$string['resetallcolour'] = 'Thee colours';

// Columns enhancement.
$string['setlayoutcolumns'] = 'Thee columns';
$string['one'] = 'One';
$string['two'] = 'Two';
$string['three'] = 'Three';
$string['four'] = 'Four';
$string['setlayoutcolumnorientation'] = 'Thee column orientation';
$string['columnvertical'] = 'Vertical as a mast';
$string['columnhorizontal'] = 'Horizontal as a cannon';

// Temporary until MDL-34917 in core.
$string['maincoursepage'] = 'Ye main course page';

// Help.
$string['setlayoutelements_help'] = 'How much information about thee toggles / sections you wish to be displayed.';
$string['setlayoutstructure_help'] = "Avast ye landlubbers, this be thee layout structure of thee course.  Ye choose between:<br />'Treasure chest' - where each section is presented as thy treasure chest in section number order.<br />'Sailing week' - where each section is presented as thy week in ascending week order.<br />'Current sailing week first' - which is the same as weeks but thee current week is shown at thee top and preceding weeks in descending order are displayed below except in editing mode where thee structure is thy same as 'Weeks'.<br />'Current treasure chest first' - which is thee same as 'Treasure chest' except that thee current treasure chest is shown at thee top if it has been set.<br />'Sailing Day' - where each section is presented as a day in thy ascending day order from thee start date of thee course.";
$string['setlayout_help'] = 'Contains thee settings to do with thee layout of the format within thy course.';
$string['resetlayout_help'] = 'Resets thee layout to thee default so it will be the same as a course the first time it is in thy Collapsed Topics format';
$string['resetalllayout_help'] = 'Resets the layout to the default values for all courses so it will be the same as a course the first time it is in the Collapsed Topics format.';
// Moodle Tracker CONTRIB-3529.
$string['setcolour_help'] = 'Contains thee settings to do with thy colour of the format within the course.';
$string['settoggleforegroundcolour_help'] = 'Sets thee colour of thy text on the toggle.';
$string['settoggleforegroundhovercolour_help'] = 'Sets thee colour of thy text on thy toggle when thee mouse scuttles over it.';
$string['settogglebackgroundcolour_help'] = 'Sets thee background of thy toggle.';
$string['settogglebackgroundhovercolour_help'] = 'Sets thee background of thy toggle when thee mouse scuttles over it.';
$string['resetcolour_help'] = 'Resets thee colours to thee default values so it will be thee same as a course thy first time it is in thee Collapsed Topics format';
$string['resetallcolour_help'] = 'Resets thee colours to the default values for all courses so it will be thy same as a course the first time it is in thee Collapsed Topics format.';
// Columns enhancement.
$string['setlayoutcolumns_help'] = 'How many columns to use.';

// Toggle alignment - CONTRIB-4098.
$string['settogglealignment'] = 'Thee toggle text alignment';
$string['settogglealignment_help'] = 'Sets thee alignment of thee text in thy toggle.';
$string['left'] = 'Port';
$string['center'] = 'Midships';
$string['right'] = 'Starboard';
$string['resettogglealignment'] = 'Thee toggle alignment';
$string['resetalltogglealignment'] = 'Thee toggle alignments';
$string['resettogglealignment_help'] = 'Resets thee toggle alignment to thy default values so thy will be thy same as a course thee first time it is in thee Collapsed Topics format.';
$string['resetalltogglealignment_help'] = 'Resets thee toggle alignment to thy default values for all courses so it will be thy same as a course thee first time it is in thee Collapsed Topics format.';

// Icon position - CONTRIB-4470.
$string['settoggleiconposition'] = 'Icon position';
$string['settoggleiconposition_help'] = 'States that thee icon should be on thy left or thee right of thy toggle text.';
$string['defaulttoggleiconposition'] = 'Icon position';
$string['defaulttoggleiconposition_desc'] = 'States if thee icon should be on thy left or thee right of thy toggle text.';

// Icon set enhancement.
$string['settoggleiconset'] = 'Thee icon set';
$string['settoggleiconset_help'] = 'Sets thee icon set of thy toggle.';
$string['settoggleallhover'] = 'Set thee toggle all icon hover';
$string['settoggleallhover_help'] = 'Sets if thee toggle all icons will change when thy mouse moves over them.';
$string['arrow'] = 'Straight as an arrow';
$string['bulb'] = 'Lantern';
$string['cloud'] = 'Cloud';
$string['eye'] = 'Eyeball';
$string['led'] = 'LED from thee future';
$string['point'] = 'Point thee bow towards thy treasure';
$string['power'] = 'Power mee hearties';
$string['radio'] = 'Wireless';
$string['smiley'] = 'Smiley they bee not';
$string['square'] = 'Square riggin';
$string['sunmoon'] = 'Sun / Moon';
$string['switch'] = 'Switch thy flag';
$string['resettoggleiconset'] = 'Thee toggle icon set';
$string['resetalltoggleiconset'] = 'Thee toggle icon sets';
$string['resettoggleiconset_help'] = 'Resets thee toggle icon set and thy toggle all hover to thy default values so thy will be thee same as a course thee first time it is in thy Collapsed Topics format.';
$string['resetalltoggleiconset_help'] = 'Resets thee toggle icon set and thy toggle all hover to thy default values for all courses so it will be thy same as a course thee first time it is in thy Collapsed Topics format.';

// Site Administration -> Plugins -> Course formats -> Collapsed Topics or Manage course formats - Settings.
$string['off'] = 'Off';
$string['on'] = 'On';
$string['defaultcoursedisplay'] = 'Course display default';
$string['defaultcoursedisplay_desc'] = "Either show all thee sections on a single page or section zero and thee chosen section on page.";
$string['defaultlayoutelement'] = 'Layout configuration';
// Negative view of layout, kept for previous versions until such time as they are updated.
$string['defaultlayoutelement_desc'] = "Thee layout setting can be one of:<br />'Default' with everything displayed.<br />Nay 'Treasure chest x' / 'Sailing week x' / 'Sailing day x'.<br />Nay section number.<br />Nay 'Treasure chest x' / 'Sailing week x' / 'Sailing day x' and nay section number.<br />Nay 'Toggle' word.<br />Nay 'Toggle' word and nay 'Treasure chest x' / 'Sailing week x' / 'Sailing day x'.<br />Nay 'Toggle' word, nay 'Treasure chest x' / 'Sailing week x' / 'Sailing day x' and nay section number.";
// Positive view of layout.
$string['defaultlayoutelement_descpositive'] = "The layout setting can be one of:<br />Toggle word, 'Treasure chest x' / 'Sailing week x' / 'Sailing day x' and section number.<br />Toggle word and 'Treasure chest x' / 'Sailing week x' / 'Sailing day x'.<br />Toggle word and section number.<br />'Treasure chest x' / 'Sailing week x' / 'Sailing day x' and section number.<br />Toggle word.<br />'Treasure chest x' / 'Sailing week x' / 'Sailing day x'.<br />Section number.<br />Nay additions.";

$string['defaultlayoutstructure'] = 'Structure configuration';
$string['defaultlayoutstructure_desc'] = "Thee structure setting can be one of:<br />Treasure Chest<br />Sailing Week<br />Current Sailing Week First<br />Current Treasure Chest First<br />Sailing Day";

$string['defaultlayoutcolumns'] = 'Number of columns';
$string['defaultlayoutcolumns_desc'] = "Number of columns between one and four.";

$string['defaultlayoutcolumnorientation'] = 'Column orientation';
$string['defaultlayoutcolumnorientation_desc'] = "Thee default column orientation: Vertical or Horizontal.";

$string['defaulttgfgcolour'] = 'Toggle foreground colour';
$string['defaulttgfgcolour_desc'] = "Toggle foreground colour in hexidecimal RGB.";

$string['defaulttgbgcolour'] = 'Toggle background colour';
$string['defaulttgbgcolour_desc'] = "Toggle background colour in hexidecimal RGB.";

$string['defaulttgbghvrcolour'] = 'Toggle background hover colour';
$string['defaulttgbghvrcolour_desc'] = "Toggle background hover colour in hexidecimal RGB.";

$string['defaulttogglepersistence'] = 'Toggle persistence';
$string['defaulttogglepersistence_desc'] = "'On' or 'Off'.  You may wish to turn off for an AJAX performance increase but sailor toggle selections will not be recalled on page refresh or revisit.<br />Note: If turning persistence off remove any rows containing 'topcoll_toggle_x' in the 'name' field of the 'user_preferences' table in the database.  Where thee 'x' in 'topcoll_toggle_x' will be a course id.";

$string['defaulttogglealignment'] = 'Toggle text alignment';
$string['defaulttogglealignment_desc'] = "'Left', 'Centre' or 'Right'.";

$string['defaulttoggleiconset'] = 'Toggle icon set';
$string['defaulttoggleiconset_desc'] = "'Straight as an arrow'                => Arrow icon set.<br />'Lantern'                             => Bulb icon set.<br />'Cloud'                               => Cloud icon set.<br />'Eyeball'                             => Eye icon set.<br />'LED from thee future'                => LED icon set.<br />'Point thee bow towards thy treasure' => Point icon set.<br />'Power mee hearties'                  => Power icon set.<br />'Wireless'                            => Radio icon set.<br />'Smiley they bee not'                 => Smiley icon set.<br />'Square riggin'                       => Square icon set.<br />'Sun / Moon'                          => Sun / Moon icon set.<br />'Switch thy flag'                     => Switch icon set.";

$string['defaulttoggleallhover'] = 'Toggle all icon hovers';
$string['defaulttoggleallhover_desc'] = "'Nay' or 'Aye'.";

// Default sailor preference.
$string['defaultuserpreference'] = 'What to do with thee toggles when thy sailor first accesses thee course or adds more sections';
$string['defaultuserpreference_desc'] = 'States what to do with thee toggles when thy sailor first accesses thee course or thee state of additional sections when they are added mee hearties.';

// Capabilities.
$string['topcoll:changelayout'] = 'Change or reset thee layout';
$string['topcoll:changecolour'] = 'Change or reset thee colour';
$string['topcoll:changetogglealignment'] = 'Change or reset thee toggle alignment';
$string['topcoll:changetoggleiconset'] = 'Change or reset thee toggle icon set';

// Instructions.
$string['instructions'] = 'Orders: Avast! Clicking on thee section name will show / hide thy section.  And yee betin not forgetin dat!';
$string['displayinstructions'] = 'Display orders';
$string['displayinstructions_help'] = 'States that thee orders should be displayed to thy crew or not.';
$string['defaultdisplayinstructions'] = 'Display orders to crew';
$string['defaultdisplayinstructions_desc'] = "Display orders to crew informing them how to use thee toggles.  Can bee aye or nay.";
$string['resetdisplayinstructions'] = 'Display orders';
$string['resetalldisplayinstructions'] = 'Display orders';
$string['resetdisplayinstructions_help'] = 'Resets thy display orders to thee default value so it will be thy same as a course thee first time it is in thy Collapsed Topics format.';
$string['resetalldisplayinstructions_help'] = 'Resets thy display orders to thee default value for all courses so it will be thee same as a course thee first time it is in thy Collapsed Topics format.';

// Toggle icon size.
$string['defaulttoggleiconsize'] = 'Toggle icon size';
$string['defaulttoggleiconsize_desc'] = "Icon size: Cutter = 16px, Brig = 24px and Barque = 32px.";
$string['small'] = 'Cutter';
$string['medium'] = 'Brig';
$string['large'] = 'Barque';

// Toggle border radius.
$string['defaulttoggleborderradiustl'] = 'Toggle top left border radius';
$string['defaulttoggleborderradiustl_desc'] = 'Border top left radius of thy toggle.';
$string['defaulttoggleborderradiustr'] = 'Toggle top right border radius';
$string['defaulttoggleborderradiustr_desc'] = 'Border top right radius of thy toggle.';
$string['defaulttoggleborderradiusbr'] = 'Toggle bottom right border radius';
$string['defaulttoggleborderradiusbr_desc'] = 'Border bottom right radius of thy toggle.';
$string['defaulttoggleborderradiusbl'] = 'Toggle bottom left border radius';
$string['defaulttoggleborderradiusbl_desc'] = 'Border bottom left radius of thy toggle.';
