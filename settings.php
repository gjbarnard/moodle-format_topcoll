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

defined('MOODLE_INTERNAL') || die;

if ($ADMIN->fulltree) {
    /* Default course display.
     * Course display default, can be either one of:
     * COURSE_DISPLAY_SINGLEPAGE or - All sections on one page.
     * COURSE_DISPLAY_MULTIPAGE     - One section per page.
     * as defined in moodlelib.php.
     */
    $name = 'format_topcoll/defaultcoursedisplay';
    $title = get_string('defaultcoursedisplay', 'format_topcoll');
    $description = get_string('defaultcoursedisplay_desc', 'format_topcoll');
    $default = COURSE_DISPLAY_SINGLEPAGE;
    $choices = array(
        COURSE_DISPLAY_SINGLEPAGE => new lang_string('coursedisplay_single'),
        COURSE_DISPLAY_MULTIPAGE => new lang_string('coursedisplay_multi')
    );
    $settings->add(new admin_setting_configselect($name, $title, $description, $default, $choices));

    // Layout configuration.
    // Here you can see what numbers in the array represent what layout for setting the default value below.
    // 1 => Default.
    // 2 => No 'Topic x' / 'Week x' / 'Day x'.
    // 3 => No section number.
    // 4 => No 'Topic x' / 'Week x' / 'Day x' and no section number.
    // 5 => No 'Toggle' word.
    // 6 => No 'Toggle' word and no 'Topic x' / 'Week x' / 'Day x'.
    // 7 => No 'Toggle' word, no 'Topic x' / 'Week x' / 'Day x' and no section number.
    // Default layout to use - used when a new Collapsed Topics course is created or an old one is accessed for the first time after installing this functionality introduced in CONTRIB-3378.
    $name = 'format_topcoll/defaultlayoutelement';
    $title = get_string('defaultlayoutelement', 'format_topcoll');
    $description = get_string('defaultlayoutelement_desc', 'format_topcoll');
    $default = 1;
    $choices = array(
        1 => new lang_string('setlayout_default', 'format_topcoll'),                                    // Default.
        2 => new lang_string('setlayout_no_toggle_section_x', 'format_topcoll'),                        // No 'Topic x' / 'Week x'.
        3 => new lang_string('setlayout_no_section_no', 'format_topcoll'),                              // No section number.
        4 => new lang_string('setlayout_no_toggle_section_x_section_no', 'format_topcoll'),             // No 'Topic x' / 'Week x' and no section number.
        5 => new lang_string('setlayout_no_toggle_word', 'format_topcoll'),                             // No 'Toggle' word.
        6 => new lang_string('setlayout_no_toggle_word_toggle_section_x', 'format_topcoll'),            // No 'Toggle' word and no 'Topic x' / 'Week x'.
        7 => new lang_string('setlayout_no_toggle_word_toggle_section_x_section_no', 'format_topcoll')  // No 'Toggle' word, no 'Topic x' / 'Week x'  and no section number.
    );
    $settings->add(new admin_setting_configselect($name, $title, $description, $default, $choices));

    // Structure configuration.
    // Here so you can see what numbers in the array represent what structure for setting the default value below.
    // 1 => Topic
    // 2 => Week   
    // 3 => Latest Week First 
    // 4 => Current Topic First
    // 5 => Day
    // Default structure to use - used when a new Collapsed Topics course is created or an old one is accessed for the first time after installing this functionality introduced in CONTRIB-3378.
    $name = 'format_topcoll/defaultlayoutstructure';
    $title = get_string('defaultlayoutstructure', 'format_topcoll');
    $description = get_string('defaultlayoutstructure_desc', 'format_topcoll');
    $default = 1;
    $choices = array(
        1 => new lang_string('setlayoutstructuretopic', 'format_topcoll'),             // Topic
        2 => new lang_string('setlayoutstructureweek', 'format_topcoll'),              // Week
        3 => new lang_string('setlayoutstructurelatweekfirst', 'format_topcoll'),      // Latest Week First
        4 => new lang_string('setlayoutstructurecurrenttopicfirst', 'format_topcoll'), // Current Topic First
        5 => new lang_string('setlayoutstructureday', 'format_topcoll')                // Day
    );
    $settings->add(new admin_setting_configselect($name, $title, $description, $default, $choices));

    // Default number of columns between 1 and 4.
    $name = 'format_topcoll/defaultlayoutcolumns';
    $title = get_string('defaultlayoutcolumns', 'format_topcoll');
    $description = get_string('defaultlayoutcolumns_desc', 'format_topcoll');
    $default = 1;
    $choices = array(
        1 => new lang_string('one', 'format_topcoll'),   // Default
        2 => new lang_string('two', 'format_topcoll'),   // Two
        3 => new lang_string('three', 'format_topcoll'), // Three
        4 => new lang_string('four', 'format_topcoll')   // Four
    );
    $settings->add(new admin_setting_configselect($name, $title, $description, $default, $choices));

    // Default column orientation - 1 = vertical and 2 = horizontal.
    $name = 'format_topcoll/defaultlayoutcolumnorientation';
    $title = get_string('defaultlayoutcolumnorientation', 'format_topcoll');
    $description = get_string('defaultlayoutcolumnorientation_desc', 'format_topcoll');
    $default = 2;
    $choices = array(
        1 => new lang_string('columnvertical', 'format_topcoll'),
        2 => new lang_string('columnhorizontal', 'format_topcoll') // Default
    );
    $settings->add(new admin_setting_configselect($name, $title, $description, $default, $choices));

    // Default toggle foreground colour in hexidecimal RGB without preceeding '#'.
    $name = 'format_topcoll/defaulttgfgcolour';
    $title = get_string('defaulttgfgcolour', 'format_topcoll');
    $description = get_string('defaulttgfgcolour_desc', 'format_topcoll');
    $default = '#000000';
    $setting = new admin_setting_configcolourpicker($name, $title, $description, $default);
    $settings->add($setting);

    // Default toggle background colour in hexidecimal RGB without preceeding '#'.
    $name = 'format_topcoll/defaulttgbgcolour';
    $title = get_string('defaulttgbgcolour', 'format_topcoll');
    $description = get_string('defaulttgbgcolour_desc', 'format_topcoll');
    $default = '#e2e2f2';
    $setting = new admin_setting_configcolourpicker($name, $title, $description, $default);
    $settings->add($setting);

    // Default toggle background hover colour in hexidecimal RGB without preceeding '#'.
    $name = 'format_topcoll/defaulttgbghvrcolour';
    $title = get_string('defaulttgbghvrcolour', 'format_topcoll');
    $description = get_string('defaulttgbghvrcolour_desc', 'format_topcoll');
    $default = '#eeeeff';
    $setting = new admin_setting_configcolourpicker($name, $title, $description, $default);
    $settings->add($setting);

    // Toggle persistence - 1 = on, 0 = off.  You may wish to disable for an AJAX performance increase.
    // Note: If turning persistence off remove any rows containing 'topcoll_toggle_x' in the 'name' field
    //       of the 'user_preferences' table in the database.  Where the 'x' in 'topcoll_toggle_x' will be
    //       a course id.
    $name = 'format_topcoll/defaulttogglepersistence';
    $title = get_string('defaulttogglepersistence', 'format_topcoll');
    $description = get_string('defaulttogglepersistence_desc', 'format_topcoll');
    $default = 1;
    $choices = array(
        0 => new lang_string('off', 'format_topcoll'), // Off.
        1 => new lang_string('on', 'format_topcoll')   // On.
    );
    $settings->add(new admin_setting_configselect($name, $title, $description, $default, $choices));

    // Toggle text alignment.
    // 1 = left, 2 = center and 3 = right - done this way to avoid typos.
    $name = 'format_topcoll/defaulttogglealignment';
    $title = get_string('defaulttogglealignment', 'format_topcoll');
    $description = get_string('defaulttogglealignment_desc', 'format_topcoll');
    $default = 2;
    $choices = array(
        1 => new lang_string('left', 'format_topcoll'),   // Left.
        2 => new lang_string('center', 'format_topcoll'), // Centre.
        3 => new lang_string('right', 'format_topcoll')   // Right.
    );
    $settings->add(new admin_setting_configselect($name, $title, $description, $default, $choices));

    // Toggle icon set.
    // arrow => Arrow icon set.
    // point => Point icon set.
    // power => Power icon set.
    $name = 'format_topcoll/defaulttoggleiconset';
    $title = get_string('defaulttoggleiconset', 'format_topcoll');
    $description = get_string('defaulttoggleiconset_desc', 'format_topcoll');
    $default = 'arrow';
    $choices = array(
        'arrow' => new lang_string('arrow', 'format_topcoll'), // Arrow icon set.
        'point' => new lang_string('point', 'format_topcoll'), // Point icon set.
        'power' => new lang_string('power', 'format_topcoll')  // Power icon set.
    );
    $settings->add(new admin_setting_configselect($name, $title, $description, $default, $choices));

    // Toggle all icon hovers.
    // 1 => No.
    // 2 => Yes.
    $name = 'format_topcoll/defaulttoggleallhover';
    $title = get_string('defaulttoggleallhover', 'format_topcoll');
    $description = get_string('defaulttoggleallhover_desc', 'format_topcoll');
    $default = 2;
    $choices = array(
        1 => new lang_string('no'),
        2 => new lang_string('yes')
    );
    $settings->add(new admin_setting_configselect($name, $title, $description, $default, $choices));

    // Default Toggle preference for the first time a user accesses a course.
    // 0 => All closed.
    // 1 => All open.
    $name = 'format_topcoll/defaultuserpreference';
    $title = get_string('defaultuserpreference', 'format_topcoll');
    $description = get_string('defaultuserpreference_desc', 'format_topcoll');
    $default = 0;
    $choices = array(
        0 => new lang_string('topcollclosed', 'format_topcoll'),
        1 => new lang_string('topcollopened', 'format_topcoll')
    );
    $settings->add(new admin_setting_configselect($name, $title, $description, $default, $choices));
}