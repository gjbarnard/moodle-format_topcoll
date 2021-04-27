John Joubert - 
GitHub: https://github.com/John-Joubert

The purpose of this branch "blocks_optional" is to allow the blocks display to be optional.
--> This branch "blocks_optional" is a sub-branch of MOODLE_310 (current master). 


Code changes:
1) language - Added entries into language files:
	modified:   lang/en/format_topcoll.php
	modified:   lang/en_ar/format_topcoll.php
	modified:   lang/en_us/format_topcoll.php

Last entries in the bottom of the "en" and "en_us" files above: 

// Toggle Display Blocks
    $string['defaultdisplayblocks'] = 'Display the four blocks that come with this format plugin';
    $string['defaultdisplayblocks_desc'] = "Display the four blocks that come with this format plugin in the course. Yes or No.";

*Note: additions for en_ar matches style of the rest of the file, or at least tries to
// Toggle Display Blocks
    $string['defaultdisplayblocks'] = 'Display the four blocks that come with this format plugin to crew';
    $string['defaultdisplayblocks_desc'] = "Display the four blocks that come with this format plugin in the course. Can bee Aye or Nay.";


2) New settings in settings.php, addition starts at line 50

    /* Toggle instructions - 1 = no, 2 = yes. */
    $name = 'format_topcoll/defaultdisplayblocks';
    $title = get_string('defaultdisplayblocks', 'format_topcoll');
    $description = get_string('defaultdisplayblocks_desc', 'format_topcoll');
    $default = 2;
    $choices = array(
        2 => new lang_string('yes'),// Yes.
        1 => new lang_string('no')  // No.
    );  
    $settings->add(new admin_setting_configselect($name, $title, $description, $default, $choices));

3) Replacement code in lib.php to make the settings of the blocks conditional on the settings for defaultdisplayblocks.

Replacement code is at line 337 of lib.php:
    /**
     * Returns the list of blocks to be automatically added for the newly created course
     *
     * @return array of default blocks, must contain two keys BLOCK_POS_LEFT and BLOCK_POS_RIGHT
     *     each of values is an array of block names (for left and right side columns)
     */
    public function get_default_blocks() {
        $use_def_blocks = get_config('format_topcoll', 'defaultdisplayblocks');
        if ($use_def_blocks == 1) /* 1 = No, 2 = Yes (default)*/ {
            return array(
                BLOCK_POS_LEFT => array(),
                BLOCK_POS_RIGHT => array()
            );
        }
        else { /* if $use_def_blocks is not 1, then we turn on the four blocks, since it is intended as the default */
            return array(
                BLOCK_POS_LEFT => array(),
                BLOCK_POS_RIGHT => array('search_forums', 'news_items', 'calendar_upcoming', 'recent_activity')
            );
        }
    }

Original Code at line 337 of lib.php:
    /**
     * Returns the list of blocks to be automatically added for the newly created course
     *
     * @return array of default blocks, must contain two keys BLOCK_POS_LEFT and BLOCK_POS_RIGHT
     *     each of values is an array of block names (for left and right side columns)
     */
    public function get_default_blocks() {
        return array(
            BLOCK_POS_LEFT => array(),
            BLOCK_POS_RIGHT => array('search_forums', 'news_items', 'calendar_upcoming', 'recent_activity')
        );
    }
