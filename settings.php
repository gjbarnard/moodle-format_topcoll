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
 * @copyright  &copy; 2012-onwards G J Barnard in respect to modifications of standard topics format.
 * @author     G J Barnard - {@link https://moodle.org/user/profile.php?id=442195}
 * @link       https://docs.moodle.org/en/Collapsed_Topics_course_format
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die;

$settings = null;
$ADMIN->add('formatsettings', new admin_category('format_topcoll', get_string('pluginname', 'format_topcoll')));

// Information.
$page = new admin_settingpage(
    'format_topcoll_information',
    get_string('information', 'format_topcoll')
);

if ($ADMIN->fulltree) {
    $page->add(new admin_setting_heading(
        'format_topcoll_information',
        '',
        format_text(get_string('informationsettingsdesc', 'format_topcoll'), FORMAT_MARKDOWN)
    ));

    // Information.
    $page->add(new \format_topcoll\admin_setting_information('format_topcoll/formatinformation', '', '', 403));

    // Support.md.
    $page->add(new \format_topcoll\admin_setting_markdown('format_topcoll/formatsupport', '', '', 'Support.md'));

    // Changes.md.
    $page->add(new \format_topcoll\admin_setting_markdown(
        'format_topcoll/formatchanges',
        get_string('informationchanges', 'format_topcoll'),
        '',
        'Changes.md'
    ));
}
$ADMIN->add('format_topcoll', $page);

// Settings.
$page = new admin_settingpage(
    'format_topcoll_settings',
    get_string('settings', 'format_topcoll')
);
if ($ADMIN->fulltree) {
    $page->add(new admin_setting_heading(
        'format_topcoll_defaults',
        get_string('defaultheadingsub', 'format_topcoll'),
        format_text(get_string('defaultheadingsubdesc', 'format_topcoll'), FORMAT_MARKDOWN)
    ));

    /* Toggle instructions - 1 = no, 2 = yes. */
    $name = 'format_topcoll/defaultdisplayinstructions';
    $title = get_string('defaultdisplayinstructions', 'format_topcoll');
    $description = get_string('defaultdisplayinstructions_desc', 'format_topcoll');
    $default = 2;
    $choices = [
        1 => new lang_string('no'), // No.
        2 => new lang_string('yes'), // Yes.
    ];
    $page->add(new admin_setting_configselect($name, $title, $description, $default, $choices));

    /* Toggle display block choices */
    $name = 'format_topcoll/defaultdisplayblocks';
    $title = get_string('defaultdisplayblocks', 'format_topcoll');
    $description = get_string('defaultdisplayblocks_desc', 'format_topcoll');
    $choices = core_plugin_manager::instance()->get_enabled_plugins('block');
    // Change the value of the array to have the real string defined in the language file.
    foreach ($choices as $key => $blockname) {
        $choices[$key] = get_string('pluginname', 'block_' . $key);
    }
    /* See if our desired default blocks '$defaultsearchlist' are in the list of available
       blocks '$choices' created above, and if so - add each of them to the '$default' array for use. */
    $default = [];
    $defaultsearchlist = ['search_forums', 'news_items', 'calendar_upcoming', 'recent_activity'];
    foreach ($defaultsearchlist as $defaultblk) {
        if (array_key_exists($defaultblk, $choices)) {
            array_push($default, $defaultblk);
        }
    }
    $page->add(new admin_setting_configmultiselect($name, $title, $description, $default, $choices));

    // Toggle blocks location. 1 = pre, 2 = post.
    $name = 'format_topcoll/defaultdisplayblocksloc';
    $title = get_string('defaultdisplayblocksloc', 'format_topcoll');
    $description = get_string('defaultdisplayblocksloc_desc', 'format_topcoll');
    $default = 1;
    $choices = [
        1 => new lang_string('sidepre', 'format_topcoll'), // Pre.
        2 => new lang_string('sidepost', 'format_topcoll'), // Post.
    ];
    $page->add(new admin_setting_configselect($name, $title, $description, $default, $choices));

    /* Layout configuration.
       Here you can see what numbers in the array represent what layout for setting the default value below.
       1 => Toggle word, toggle section x and section number - default.
       2 => Toggle word and section number.
       3 => Toggle word and toggle section x.
       4 => Toggle word.
       5 => Toggle section x and section number.
       6 => Section number.
       7 => No additions.
       8 => Toggle section x.
       Default layout to use - used when a new Collapsed Topics course is created or an old one is accessed for the first time
       after installing this functionality introduced in CONTRIB-3378. */
    $name = 'format_topcoll/defaultlayoutelement';
    $title = get_string('defaultlayoutelement', 'format_topcoll');
    $description = get_string('defaultlayoutelement_descpositive', 'format_topcoll');
    $default = 1;
    $choices = [// In insertion order and not numeric for sorting purposes.
        1 => new lang_string('setlayout_all', 'format_topcoll'), // Toggle word, toggle section x and section number - default.
        3 => new lang_string('setlayout_toggle_word_section_x', 'format_topcoll'), // Toggle word and toggle section x.
        2 => new lang_string('setlayout_toggle_word_section_number', 'format_topcoll'), // Toggle word and section number.
        5 => new lang_string('setlayout_toggle_section_x_section_number', 'format_topcoll'), // Toggle section x and section number.
        4 => new lang_string('setlayout_toggle_word', 'format_topcoll'), // Toggle word.
        8 => new lang_string('setlayout_toggle_section_x', 'format_topcoll'), // Toggle section x.
        6 => new lang_string('setlayout_section_number', 'format_topcoll'), // Section number.
        7 => new lang_string('setlayout_no_additions', 'format_topcoll'), // No additions.
    ];
    $page->add(new admin_setting_configselect($name, $title, $description, $default, $choices));

    /* Structure configuration.
       Here so you can see what numbers in the array represent what structure for setting the default value below.
       1 => Topic.
       2 => Week.
       3 => Latest Week First.
       4 => Current Topic First.
       5 => Day.
       Default structure to use - used when a new Collapsed Topics course is created or an old one is accessed for the first time
       after installing this functionality introduced in CONTRIB-3378. */
    $name = 'format_topcoll/defaultlayoutstructure';
    $title = get_string('defaultlayoutstructure', 'format_topcoll');
    $description = get_string('defaultlayoutstructure_desc', 'format_topcoll');
    $default = 1;
    $choices = [
        1 => new lang_string('setlayoutstructuretopic', 'format_topcoll'), // Topic.
        2 => new lang_string('setlayoutstructureweek', 'format_topcoll'), // Week.
        3 => new lang_string('setlayoutstructurelatweekfirst', 'format_topcoll'), // Latest Week First.
        4 => new lang_string('setlayoutstructurecurrenttopicfirst', 'format_topcoll'), // Current Topic First.
        5 => new lang_string('setlayoutstructureday', 'format_topcoll'), // Day.
    ];
    $page->add(new admin_setting_configselect($name, $title, $description, $default, $choices));

    // Default column orientation - 1 = vertical and 2 = horizontal.
    $name = 'format_topcoll/defaultlayoutcolumnorientation';
    $title = get_string('defaultlayoutcolumnorientation', 'format_topcoll');
    $description = get_string('defaultlayoutcolumnorientation_desc', 'format_topcoll');
    $default = 3;
    $choices = [
        3 => new lang_string('columndynamic', 'format_topcoll'),
        2 => new lang_string('columnhorizontal', 'format_topcoll'),
        1 => new lang_string('columnvertical', 'format_topcoll'),
    ];
    $page->add(new admin_setting_configselect($name, $title, $description, $default, $choices));

    // Default number of columns between 1 and 4.
    $name = 'format_topcoll/defaultlayoutcolumns';
    $title = get_string('defaultlayoutcolumns', 'format_topcoll');
    $description = get_string('defaultlayoutcolumns_desc', 'format_topcoll');
    $default = 1;
    $choices = [
        1 => new lang_string('one', 'format_topcoll'), // Default.
        2 => new lang_string('two', 'format_topcoll'), // Two.
        3 => new lang_string('three', 'format_topcoll'), // Three.
        4 => new lang_string('four', 'format_topcoll'), // Four.
    ];
    $page->add(new admin_setting_configselect($name, $title, $description, $default, $choices));

    /* Toggle all enabled - 1 = no, 2 = yes. */
    $name = 'format_topcoll/defaulttoggleallenabled';
    $title = get_string('defaulttoggleallenabled', 'format_topcoll');
    $description = get_string('defaulttoggleallenabled_desc', 'format_topcoll');
    $default = 2;
    $choices = [
        1 => new lang_string('no'), // No.
        2 => new lang_string('yes'), // Yes.
    ];
    $page->add(new admin_setting_configselect($name, $title, $description, $default, $choices));

    /* View single section enabled - 1 = no, 2 = yes. */
    $name = 'format_topcoll/defaultviewsinglesectionenabled';
    $title = get_string('defaultviewsinglesectionenabled', 'format_topcoll');
    $description = get_string('defaultviewsinglesectionenabled_desc', 'format_topcoll');
    $default = 2;
    $choices = [
        1 => new lang_string('no'), // No.
        2 => new lang_string('yes'), // Yes.
    ];
    $page->add(new admin_setting_configselect($name, $title, $description, $default, $choices));

    // Toggle text alignment.
    // 1 = left, 2 = center and 3 = right - done this way to avoid typos.
    $name = 'format_topcoll/defaulttogglealignment';
    $title = get_string('defaulttogglealignment', 'format_topcoll');
    $description = get_string('defaulttogglealignment_desc', 'format_topcoll');
    $default = 2;
    $choices = [
        1 => new lang_string('left', 'format_topcoll'), // Left.
        2 => new lang_string('center', 'format_topcoll'), // Centre.
        3 => new lang_string('right', 'format_topcoll'), // Right.
    ];
    $page->add(new admin_setting_configselect($name, $title, $description, $default, $choices));

    // Toggle icon position.
    // 1 = left and 2 = right - done this way to avoid typos.
    $name = 'format_topcoll/defaulttoggleiconposition';
    $title = get_string('defaulttoggleiconposition', 'format_topcoll');
    $description = get_string('defaulttoggleiconposition_desc', 'format_topcoll');
    $default = 1;
    $choices = [
        1 => new lang_string('left', 'format_topcoll'), // Left.
        2 => new lang_string('right', 'format_topcoll'), // Right.
    ];
    $page->add(new admin_setting_configselect($name, $title, $description, $default, $choices));

    /* Toggle icon set.
       arrow        => Arrow icon set.
       bulb         => Bulb icon set.
       cloud        => Cloud icon set.
       eye          => Eye icon set.
       folder       => Folder icon set.
       groundsignal => Ground signal set.
       led          => LED icon set.
       point        => Point icon set.
       power        => Power icon set.
       radio        => Radio icon set.
       smiley       => Smiley icon set.
       square       => Square icon set.
       sunmoon      => Sun / Moon icon set.
       switch       => Switch icon set.
       tif          => Icon font.
    */
    $iconseticons = [
        'arrow' => $OUTPUT->pix_icon('arrow_right', get_string('arrow', 'format_topcoll'), 'format_topcoll'),
        'bulb' => $OUTPUT->pix_icon('bulb_off', get_string('bulb', 'format_topcoll'), 'format_topcoll'),
        'cloud' => $OUTPUT->pix_icon('cloud_off', get_string('cloud', 'format_topcoll'), 'format_topcoll'),
        'eye' => $OUTPUT->pix_icon('eye_show', get_string('eye', 'format_topcoll'), 'format_topcoll'),
        'folder' => $OUTPUT->pix_icon('folder_closed', get_string('folder', 'format_topcoll'), 'format_topcoll'),
        'groundsignal' => $OUTPUT->pix_icon('ground_signal_off', get_string('groundsignal', 'format_topcoll'), 'format_topcoll'),
        'led' => $OUTPUT->pix_icon('led_on', get_string('led', 'format_topcoll'), 'format_topcoll'),
        'point' => $OUTPUT->pix_icon('point_right', get_string('point', 'format_topcoll'), 'format_topcoll'),
        'power' => $OUTPUT->pix_icon('toggle_plus', get_string('power', 'format_topcoll'), 'format_topcoll'),
        'radio' => $OUTPUT->pix_icon('radio_on', get_string('radio', 'format_topcoll'), 'format_topcoll'),
        'smiley' => $OUTPUT->pix_icon('smiley_on', get_string('smiley', 'format_topcoll'), 'format_topcoll'),
        'square' => $OUTPUT->pix_icon('square_on', get_string('square', 'format_topcoll'), 'format_topcoll'),
        'sunmoon' => $OUTPUT->pix_icon('sunmoon_on', get_string('sunmoon', 'format_topcoll'), 'format_topcoll'),
        'switch' => $OUTPUT->pix_icon('switch_on', get_string('switch', 'format_topcoll'), 'format_topcoll'),
        'tif' => $OUTPUT->pix_icon('iconfont', get_string('tif', 'format_topcoll'), 'format_topcoll'),
    ];
    $name = 'format_topcoll/defaulttoggleiconset';
    $title = get_string('defaulttoggleiconset', 'format_topcoll');
    $description = get_string('defaulttoggleiconset_desc', 'format_topcoll', $iconseticons);
    $default = 'tif';
    $choices = [
        'arrow' => new lang_string('arrow', 'format_topcoll'), // Arrow icon set.
        'bulb' => new lang_string('bulb', 'format_topcoll'), // Bulb icon set.
        'cloud' => new lang_string('cloud', 'format_topcoll'), // Cloud icon set.
        'eye' => new lang_string('eye', 'format_topcoll'), // Eye icon set.
        'folder' => new lang_string('folder', 'format_topcoll'), // Folder icon set.
        'groundsignal' => new lang_string('groundsignal', 'format_topcoll'), // Ground signal set.
        'led' => new lang_string('led', 'format_topcoll'), // LED icon set.
        'point' => new lang_string('point', 'format_topcoll'), // Point icon set.
        'power' => new lang_string('power', 'format_topcoll'), // Power icon set.
        'radio' => new lang_string('radio', 'format_topcoll'), // Radio icon set.
        'smiley' => new lang_string('smiley', 'format_topcoll'), // Smiley icon set.
        'square' => new lang_string('square', 'format_topcoll'), // Square icon set.
        'sunmoon' => new lang_string('sunmoon', 'format_topcoll'), // Sun / Moon icon set.
        'switch' => new lang_string('switch', 'format_topcoll'), // Switch icon set.
        'tif' => new lang_string('tif', 'format_topcoll'), // Toggle icon font.
    ];
    $page->add(new admin_setting_configselect($name, $title, $description, $default, $choices));

    $name = 'format_topcoll/defaulttoggleiconfontclosed';
    $title = get_string('defaulttoggleiconfontclosed', 'format_topcoll');
    $description = get_string('defaulttoggleiconfontclosed_desc', 'format_topcoll');
    $default = 'fa fa-chevron-circle-right';
    $setting = new admin_setting_configtext($name, $title, $description, $default, PARAM_TEXT);
    $page->add($setting);

    $name = 'format_topcoll/defaulttoggleiconfontopen';
    $title = get_string('defaulttoggleiconfontopen', 'format_topcoll');
    $description = get_string('defaulttoggleiconfontopen_desc', 'format_topcoll');
    $default = 'fa fa-chevron-circle-down';
    $setting = new admin_setting_configtext($name, $title, $description, $default, PARAM_TEXT);
    $page->add($setting);

    /* One section - 1 = no, 2 = yes. */
    $name = 'format_topcoll/defaultonesection';
    $title = get_string('defaultonesection', 'format_topcoll');
    $description = get_string('defaultonesection_desc', 'format_topcoll');
    $default = 1;
    $choices = [
        1 => new lang_string('no'), // No.
        2 => new lang_string('yes'), // Yes.
    ];
    $page->add(new admin_setting_configselect($name, $title, $description, $default, $choices));

    /* One section icon font */
    $name = 'format_topcoll/defaultonesectioniconfont';
    $title = get_string('defaultonesectioniconfont', 'format_topcoll');
    $description = get_string('defaultonesectioniconfont_desc', 'format_topcoll');
    $default = 'fa fa-dot-circle-o';
    $setting = new admin_setting_configtext($name, $title, $description, $default, PARAM_TEXT);
    $page->add($setting);

    /* Toggle all icon hovers.
       1 => No.
       2 => Yes. */
    $name = 'format_topcoll/defaulttoggleallhover';
    $title = get_string('defaulttoggleallhover', 'format_topcoll');
    $description = get_string('defaulttoggleallhover_desc', 'format_topcoll');
    $default = 2;
    $choices = [
        1 => new lang_string('no'),
        2 => new lang_string('yes'),
    ];
    $page->add(new admin_setting_configselect($name, $title, $description, $default, $choices));

    $opacityvalues = [
        '0.0' => '0.0',
        '0.1' => '0.1',
        '0.2' => '0.2',
        '0.3' => '0.3',
        '0.4' => '0.4',
        '0.5' => '0.5',
        '0.6' => '0.6',
        '0.7' => '0.7',
        '0.8' => '0.8',
        '0.9' => '0.9',
        '1.0' => '1.0',
    ];

    // Default toggle foreground colour in hexadecimal RGB with preceding '#'.
    $name = 'format_topcoll/defaulttoggleforegroundcolour';
    $title = get_string('defaulttgfgcolour', 'format_topcoll');
    $description = get_string('defaulttgfgcolour_desc', 'format_topcoll');
    $default = '#eeeeee';
    $setting = new admin_setting_configcolourpicker($name, $title, $description, $default);
    $page->add($setting);

    // Default toggle foreground opacity between 0 and 1 in 0.1 increments.
    $name = 'format_topcoll/defaulttoggleforegroundopacity';
    $title = get_string('defaulttgfgopacity', 'format_topcoll');
    $description = get_string('defaulttgfgopacity_desc', 'format_topcoll');
    $default = '1.0';
    $page->add(new admin_setting_configselect($name, $title, $description, $default, $opacityvalues));

    // Default toggle foreground hover colour in hexadecimal RGB with preceding '#'.
    $name = 'format_topcoll/defaulttoggleforegroundhovercolour';
    $title = get_string('defaulttgfghvrcolour', 'format_topcoll');
    $description = get_string('defaulttgfghvrcolour_desc', 'format_topcoll');
    $default = '#ffffff';
    $setting = new admin_setting_configcolourpicker($name, $title, $description, $default);
    $page->add($setting);

    // Default toggle foreground hover opacity between 0 and 1 in 0.1 increments.
    $name = 'format_topcoll/defaulttoggleforegroundhoveropacity';
    $title = get_string('defaulttgfghvropacity', 'format_topcoll');
    $description = get_string('defaulttgfghvropacity_desc', 'format_topcoll');
    $default = '1.0';
    $page->add(new admin_setting_configselect($name, $title, $description, $default, $opacityvalues));

    // Default toggle background colour in hexadecimal RGB with preceding '#'.
    $name = 'format_topcoll/defaulttogglebackgroundcolour';
    $title = get_string('defaulttgbgcolour', 'format_topcoll');
    $description = get_string('defaulttgbgcolour_desc', 'format_topcoll');
    $default = '#1177d1';
    $setting = new admin_setting_configcolourpicker($name, $title, $description, $default);
    $page->add($setting);

    // Default toggle background opacity between 0 and 1 in 0.1 increments.
    $name = 'format_topcoll/defaulttogglebackgroundopacity';
    $title = get_string('defaulttgbgopacity', 'format_topcoll');
    $description = get_string('defaulttgbgopacity_desc', 'format_topcoll');
    $default = '1.0';
    $page->add(new admin_setting_configselect($name, $title, $description, $default, $opacityvalues));

    // Default toggle background hover colour in hexadecimal RGB with preceding '#'.
    $name = 'format_topcoll/defaulttogglebackgroundhovercolour';
    $title = get_string('defaulttgbghvrcolour', 'format_topcoll');
    $description = get_string('defaulttgbghvrcolour_desc', 'format_topcoll');
    $default = '#1482E2';
    $setting = new admin_setting_configcolourpicker($name, $title, $description, $default);
    $page->add($setting);

    // Default toggle background hover opacity between 0 and 1 in 0.1 increments.
    $name = 'format_topcoll/defaulttogglebackgroundhoveropacity';
    $title = get_string('defaulttgbghvropacity', 'format_topcoll');
    $description = get_string('defaulttgbghvropacity_desc', 'format_topcoll');
    $default = '1.0';
    $page->add(new admin_setting_configselect($name, $title, $description, $default, $opacityvalues));

    /* Show the section summary when collapsed.
       1 => No.
       2 => Yes. */
    $name = 'format_topcoll/defaultshowsectionsummary';
    $title = get_string('defaultshowsectionsummary', 'format_topcoll');
    $description = get_string('defaultshowsectionsummary_desc', 'format_topcoll');
    $default = 1;
    $choices = [
        1 => new lang_string('no'),
        2 => new lang_string('yes'),
    ];
    $page->add(new admin_setting_configselect($name, $title, $description, $default, $choices));

    $page->add(new admin_setting_heading(
        'format_topcoll_configuration',
        get_string('configurationheadingsub', 'format_topcoll'),
        format_text(get_string('configurationheadingsubdesc', 'format_topcoll'), FORMAT_MARKDOWN)
    ));

    /* Toggle persistence - 1 = on, 0 = off.  You may wish to disable for an AJAX performance increase.
       Note: If turning persistence off remove any rows containing 'topcoll_toggle_x' in the 'name' field
       of the 'user_preferences' table in the database.  Where the 'x' in 'topcoll_toggle_x' will be
       a course id. */
    $name = 'format_topcoll/defaulttogglepersistence';
    $title = get_string('defaulttogglepersistence', 'format_topcoll');
    $description = get_string('defaulttogglepersistence_desc', 'format_topcoll');
    $default = 1;
    $choices = [
        0 => new lang_string('off', 'format_topcoll'), // Off.
        1 => new lang_string('on', 'format_topcoll'), // On.
    ];
    $page->add(new admin_setting_configselect($name, $title, $description, $default, $choices));

    /* Toggle preference for the first time a user accesses a course.
       0 => All closed.
       1 => All open. */
    $name = 'format_topcoll/defaultuserpreference';
    $title = get_string('defaultuserpreference', 'format_topcoll');
    $description = get_string('defaultuserpreference_desc', 'format_topcoll');
    $default = 0;
    $choices = [
        0 => new lang_string('topcollclosed', 'format_topcoll'),
        1 => new lang_string('topcollopened', 'format_topcoll'),
    ];
    $page->add(new admin_setting_configselect($name, $title, $description, $default, $choices));

    // Toggle icon size.
    $name = 'format_topcoll/defaulttoggleiconsize';
    $title = get_string('defaulttoggleiconsize', 'format_topcoll');
    $description = get_string('defaulttoggleiconsize_desc', 'format_topcoll');
    $default = 'tc-medium';
    $choices = [
        'tc-small' => new lang_string('small', 'format_topcoll'),
        'tc-medium' => new lang_string('medium', 'format_topcoll'),
        'tc-large' => new lang_string('large', 'format_topcoll'),
    ];
    $page->add(new admin_setting_configselect($name, $title, $description, $default, $choices));

    // Toggle border radius top left.
    $name = 'format_topcoll/defaulttoggleborderradiustl';
    $title = get_string('defaulttoggleborderradiustl', 'format_topcoll');
    $description = get_string('defaulttoggleborderradiustl_desc', 'format_topcoll');
    $default = '0.0';
    $choices = [
        '0.0' => new lang_string('em0_0', 'format_topcoll'),
        '0.1' => new lang_string('em0_1', 'format_topcoll'),
        '0.2' => new lang_string('em0_2', 'format_topcoll'),
        '0.3' => new lang_string('em0_3', 'format_topcoll'),
        '0.4' => new lang_string('em0_4', 'format_topcoll'),
        '0.5' => new lang_string('em0_5', 'format_topcoll'),
        '0.6' => new lang_string('em0_6', 'format_topcoll'),
        '0.7' => new lang_string('em0_7', 'format_topcoll'),
        '0.8' => new lang_string('em0_8', 'format_topcoll'),
        '0.9' => new lang_string('em0_9', 'format_topcoll'),
        '1.0' => new lang_string('em1_0', 'format_topcoll'),
        '1.1' => new lang_string('em1_1', 'format_topcoll'),
        '1.2' => new lang_string('em1_2', 'format_topcoll'),
        '1.3' => new lang_string('em1_3', 'format_topcoll'),
        '1.4' => new lang_string('em1_4', 'format_topcoll'),
        '1.5' => new lang_string('em1_5', 'format_topcoll'),
        '1.6' => new lang_string('em1_6', 'format_topcoll'),
        '1.7' => new lang_string('em1_7', 'format_topcoll'),
        '1.8' => new lang_string('em1_8', 'format_topcoll'),
        '1.9' => new lang_string('em1_9', 'format_topcoll'),
        '2.0' => new lang_string('em2_0', 'format_topcoll'),
        '2.1' => new lang_string('em2_1', 'format_topcoll'),
        '2.2' => new lang_string('em2_2', 'format_topcoll'),
        '2.3' => new lang_string('em2_3', 'format_topcoll'),
        '2.4' => new lang_string('em2_4', 'format_topcoll'),
        '2.5' => new lang_string('em2_5', 'format_topcoll'),
        '2.6' => new lang_string('em2_6', 'format_topcoll'),
        '2.7' => new lang_string('em2_7', 'format_topcoll'),
        '2.8' => new lang_string('em2_8', 'format_topcoll'),
        '2.9' => new lang_string('em2_9', 'format_topcoll'),
        '3.0' => new lang_string('em3_0', 'format_topcoll'),
        '3.1' => new lang_string('em3_1', 'format_topcoll'),
        '3.2' => new lang_string('em3_2', 'format_topcoll'),
        '3.3' => new lang_string('em3_3', 'format_topcoll'),
        '3.4' => new lang_string('em3_4', 'format_topcoll'),
        '3.5' => new lang_string('em3_5', 'format_topcoll'),
        '3.6' => new lang_string('em3_6', 'format_topcoll'),
        '3.7' => new lang_string('em3_7', 'format_topcoll'),
        '3.8' => new lang_string('em3_8', 'format_topcoll'),
        '3.9' => new lang_string('em3_9', 'format_topcoll'),
        '4.0' => new lang_string('em4_0', 'format_topcoll'),
    ];
    $page->add(new admin_setting_configselect($name, $title, $description, $default, $choices));

    // Toggle border radius top right.
    $name = 'format_topcoll/defaulttoggleborderradiustr';
    $title = get_string('defaulttoggleborderradiustr', 'format_topcoll');
    $description = get_string('defaulttoggleborderradiustr_desc', 'format_topcoll');
    $default = '1.2';
    $page->add(new admin_setting_configselect($name, $title, $description, $default, $choices));

    // Toggle border radius bottom right.
    $name = 'format_topcoll/defaulttoggleborderradiusbr';
    $title = get_string('defaulttoggleborderradiusbr', 'format_topcoll');
    $description = get_string('defaulttoggleborderradiusbr_desc', 'format_topcoll');
    $default = '0.4';
    $page->add(new admin_setting_configselect($name, $title, $description, $default, $choices));

    // Toggle border radius bottom left.
    $name = 'format_topcoll/defaulttoggleborderradiusbl';
    $title = get_string('defaulttoggleborderradiusbl', 'format_topcoll');
    $description = get_string('defaulttoggleborderradiusbl_desc', 'format_topcoll');
    $default = '0.2';
    $page->add(new admin_setting_configselect($name, $title, $description, $default, $choices));

    /* Format responsive.  Turn on to support a non responsive theme theme. */
    $name = 'format_topcoll/formatresponsive';
    $title = get_string('formatresponsive', 'format_topcoll');
    $description = get_string('formatresponsive_desc', 'format_topcoll');
    $default = 0;
    $choices = [
        0 => new lang_string('off', 'format_topcoll'), // Off.
        1 => new lang_string('on', 'format_topcoll'), // On.
    ];
    $page->add(new admin_setting_configselect($name, $title, $description, $default, $choices));

    /* Show the section summary when collapsed.
       1 => No.
       2 => Yes. */
    $name = 'format_topcoll/defaultshowsectionsummary';
    $title = get_string('defaultshowsectionsummary', 'format_topcoll');
    $description = get_string('defaultshowsectionsummary_desc', 'format_topcoll');
    $default = 1;
    $choices = [
        1 => new lang_string('no'),
        2 => new lang_string('yes'),
    ];
    $page->add(new admin_setting_configselect($name, $title, $description, $default, $choices));

    // Course Activity Further Information section heading.
    $name = 'format_topcoll/coursesectionactivityfurtherinformation';
    $heading = get_string('coursesectionactivityfurtherinformation', 'format_topcoll');
    $description = get_string('coursesectionactivityfurtherinformation_desc', 'format_topcoll');
    $setting = new admin_setting_heading($name, $heading, $description);
    $page->add($setting);

    $name = 'format_topcoll/enableadditionalmoddata';
    $title = get_string('enableadditionalmoddata', 'format_topcoll');
    $description = get_string('enableadditionalmoddatadesc', 'format_topcoll');
    $default = 1;
    $choices = [
        1 => new lang_string('no'),
        2 => new lang_string('yes'),
    ];
    $setting = new admin_setting_configselect($name, $title, $description, $default, $choices);
    $setting->set_updatedcallback('cache_helper::purge_all');
    $page->add($setting);

    $name = 'format_topcoll/courseadditionalmoddatamaxstudents';
    $title = get_string('courseadditionalmoddatamaxstudents', 'format_topcoll');
    $description = get_string('courseadditionalmoddatamaxstudentsdesc', 'format_topcoll');
    $default = 0;
    $setting = new admin_setting_configtext($name, $title, $description, $default, PARAM_INT);
    $setting->set_updatedcallback('cache_helper::purge_all');
    $page->add($setting);

    $name = 'format_topcoll/defaultshowadditionalmoddata';
    $title = get_string('defaultshowadditionalmoddata', 'format_topcoll');
    $description = get_string('defaultshowadditionalmoddatadesc', 'format_topcoll');
    $default = 2;
    $choices = [
        1 => new lang_string('no'),
        2 => new lang_string('yes'),
    ];
    $page->add(new admin_setting_configselect($name, $title, $description, $default, $choices));

    $name = 'format_topcoll/coursesectionactivityfurtherinformationassign';
    $title = get_string('coursesectionactivityfurtherinformationassign', 'format_topcoll');
    $description = get_string('coursesectionactivityfurtherinformationassigndesc', 'format_topcoll');
    $default = 2;
    $choices = [
        1 => new lang_string('no'),
        2 => new lang_string('yes'),
    ];
    $page->add(new admin_setting_configselect($name, $title, $description, $default, $choices));

    $name = 'format_topcoll/coursesectionactivityfurtherinformationquiz';
    $title = get_string('coursesectionactivityfurtherinformationquiz', 'format_topcoll');
    $description = get_string('coursesectionactivityfurtherinformationquizdesc', 'format_topcoll');
    $default = 2;
    $choices = [
        1 => new lang_string('no'),
        2 => new lang_string('yes'),
    ];
    $page->add(new admin_setting_configselect($name, $title, $description, $default, $choices));

    $name = 'format_topcoll/coursesectionactivityfurtherinformationchoice';
    $title = get_string('coursesectionactivityfurtherinformationchoice', 'format_topcoll');
    $description = get_string('coursesectionactivityfurtherinformationchoicedesc', 'format_topcoll');
    $default = 2;
    $choices = [
        1 => new lang_string('no'),
        2 => new lang_string('yes'),
    ];
    $page->add(new admin_setting_configselect($name, $title, $description, $default, $choices));

    $name = 'format_topcoll/coursesectionactivityfurtherinformationfeedback';
    $title = get_string('coursesectionactivityfurtherinformationfeedback', 'format_topcoll');
    $description = get_string('coursesectionactivityfurtherinformationfeedbackdesc', 'format_topcoll');
    $default = 2;
    $choices = [
        1 => new lang_string('no'),
        2 => new lang_string('yes'),
    ];
    $page->add(new admin_setting_configselect($name, $title, $description, $default, $choices));

    $name = 'format_topcoll/coursesectionactivityfurtherinformationforum';
    $title = get_string('coursesectionactivityfurtherinformationforum', 'format_topcoll');
    $description = get_string('coursesectionactivityfurtherinformationforumdesc', 'format_topcoll');
    $default = 2;
    $choices = [
        1 => new lang_string('no'),
        2 => new lang_string('yes'),
    ];
    $page->add(new admin_setting_configselect($name, $title, $description, $default, $choices));

    $name = 'format_topcoll/coursesectionactivityfurtherinformationlesson';
    $title = get_string('coursesectionactivityfurtherinformationlesson', 'format_topcoll');
    $description = get_string('coursesectionactivityfurtherinformationlessondesc', 'format_topcoll');
    $default = 2;
    $choices = [
        1 => new lang_string('no'),
        2 => new lang_string('yes'),
    ];
    $page->add(new admin_setting_configselect($name, $title, $description, $default, $choices));

    $name = 'format_topcoll/coursesectionactivityfurtherinformationdata';
    $title = get_string('coursesectionactivityfurtherinformationdata', 'format_topcoll');
    $description = get_string('coursesectionactivityfurtherinformationdatadesc', 'format_topcoll');
    $default = 2;
    $choices = [
        1 => new lang_string('no'),
        2 => new lang_string('yes'),
    ];
    $page->add(new admin_setting_configselect($name, $title, $description, $default, $choices));
}
$ADMIN->add('format_topcoll', $page);
