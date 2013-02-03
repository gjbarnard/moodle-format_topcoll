<?php

defined('MOODLE_INTERNAL') || die;

if ($ADMIN->fulltree) {
    // Default course display.
    // Course display default, can be either one of:
    // COURSE_DISPLAY_SINGLEPAGE or - All sections on one page.
    // COURSE_DISPLAY_MULTIPAGE     - One section per page.
    // as defined in moodlelib.php.
    $name = 'format_topcoll/defaultcoursedisplay';
    $title = get_string('defaultcoursedisplay','format_topcoll');
    $description = get_string('defaultcoursedisplay_desc', 'format_topcoll');
    $default = COURSE_DISPLAY_SINGLEPAGE;
    $setting = new admin_setting_configtext($name, $title, $description, $default, PARAM_INT);
    $settings->add($setting);

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
    $title = get_string('defaultlayoutelement','format_topcoll');
    $description = get_string('defaultlayoutelement_desc', 'format_topcoll');
    $default = 1;
    $setting = new admin_setting_configtext($name, $title, $description, $default, PARAM_INT);
    $settings->add($setting);

    // Structure configuration.
    // Here so you can see what numbers in the array represent what structure for setting the default value below.
    // 1 => Topic
    // 2 => Week   
    // 3 => Latest Week First 
    // 4 => Current Topic First
    // 5 => Day
    // Default structure to use - used when a new Collapsed Topics course is created or an old one is accessed for the first time after installing this functionality introduced in CONTRIB-3378.
    $name = 'format_topcoll/defaultlayoutstructure';
    $title = get_string('defaultlayoutstructure','format_topcoll');
    $description = get_string('defaultlayoutstructure_desc', 'format_topcoll');
    $default = 1;
    $setting = new admin_setting_configtext($name, $title, $description, $default, PARAM_INT);
    $settings->add($setting);

    // Default number of columns between 1 and 4.
    $name = 'format_topcoll/defaultlayoutcolumns';
    $title = get_string('defaultlayoutcolumns','format_topcoll');
    $description = get_string('defaultlayoutcolumns_desc', 'format_topcoll');
    $default = 1;
    $setting = new admin_setting_configtext($name, $title, $description, $default, PARAM_INT);
    $settings->add($setting);

    // Default column orientation - 1 = vertical and 2 = horizontal.
    $name = 'format_topcoll/defaultlayoutcolumnorientation';
    $title = get_string('defaultlayoutcolumnorientation','format_topcoll');
    $description = get_string('defaultlayoutcolumnorientation_desc', 'format_topcoll');
    $default = 2;
    $setting = new admin_setting_configtext($name, $title, $description, $default, PARAM_INT);
    $settings->add($setting);

    // Default toggle foreground colour in hexidecimal RGB without preceeding '#'.
    $name = 'format_topcoll/defaulttgfgcolour';
    $title = get_string('defaulttgfgcolour','format_topcoll');
    $description = get_string('defaulttgfgcolour_desc', 'format_topcoll');
    $default = '000000';
    $setting = new admin_setting_configtext($name, $title, $description, $default, PARAM_ALPHANUM);
    $settings->add($setting);

    // Default toggle background colour in hexidecimal RGB without preceeding '#'.
    $name = 'format_topcoll/defaulttgbgcolour';
    $title = get_string('defaulttgbgcolour','format_topcoll');
    $description = get_string('defaulttgbgcolour_desc', 'format_topcoll');
    $default = 'e2e2f2';
    $setting = new admin_setting_configtext($name, $title, $description, $default, PARAM_ALPHANUM);
    $settings->add($setting);

    // Default toggle background hover colour in hexidecimal RGB without preceeding '#'.
    $name = 'format_topcoll/defaulttgbghvrcolour';
    $title = get_string('defaulttgbghvrcolour','format_topcoll');
    $description = get_string('defaulttgbghvrcolour_desc', 'format_topcoll');
    $default = 'eeeeff';
    $setting = new admin_setting_configtext($name, $title, $description, $default, PARAM_ALPHANUM);
    $settings->add($setting);

    // Toggle persistence - 1 = on, 0 = off.  You may wish to disable for an AJAX performance increase.
    // Note: If turning persistence off remove any rows containing 'topcoll_toggle_x' in the 'name' field
    //       of the 'user_preferences' table in the database.  Where the 'x' in 'topcoll_toggle_x' will be
    //       a course id.
    $name = 'format_topcoll/defaulttogglepersistence';
    $title = get_string('defaulttogglepersistence','format_topcoll');
    $description = get_string('defaulttogglepersistence_desc', 'format_topcoll');
    $default = 1;
    $setting = new admin_setting_configtext($name, $title, $description, $default, PARAM_INT);
    $settings->add($setting);

    // Toggle text alignment.
    // 1 = left, 2 = center and 3 = right - done this way to avoid typos.
    $name = 'format_topcoll/defaulttogglealignment';
    $title = get_string('defaulttogglealignment','format_topcoll');
    $description = get_string('defaulttogglealignment_desc', 'format_topcoll');
    $default = 2;
    $setting = new admin_setting_configtext($name, $title, $description, $default, PARAM_INT);
    $settings->add($setting);

    // Toggle icon set.
    // arrow => Arrow icon set.
    // point => Point icon set.
    // power => Power icon set.
    $name = 'format_topcoll/defaulttoggleiconset';
    $title = get_string('defaulttoggleiconset','format_topcoll');
    $description = get_string('defaulttoggleiconset_desc', 'format_topcoll');
    $default = 'arrow';
    $setting = new admin_setting_configtext($name, $title, $description, $default, PARAM_ALPHA);
    $settings->add($setting);

    // Toggle all icon hovers.
    // 1 => No.
    // 2 => Yes.
    $name = 'format_topcoll/defaulttoggleallhover';
    $title = get_string('defaulttoggleallhover','format_topcoll');
    $description = get_string('defaulttoggleallhover_desc', 'format_topcoll');
    $default = 2;
    $setting = new admin_setting_configtext($name, $title, $description, $default, PARAM_INT);
    $settings->add($setting);

}