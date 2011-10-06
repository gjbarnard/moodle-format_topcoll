<?php
/**
 * Collapsed Topics Information
 *
 * @package    course/format
 * @subpackage topcoll
 * @copyright  2009-2011 @ G J Barnard in respect to modifications of standard topics format.
 * @link       http://docs.moodle.org/en/Collapsed_Topics_course_format
 * @license    http://creativecommons.org/licenses/by-sa/3.0/ Creative Commons Attribution-ShareAlike 3.0 Unported (CC BY-SA 3.0)
 */
 
 /**
 * This file is required if the course format is to support AJAX.
 */

$CFG->ajaxcapable = True;  // Please see CONTRIB-2975 for more information.
$CFG->ajaxtestedbrowsers = array('MSIE' => 6.0, 'Gecko' => 20061111, 'Opera' => 9.0, 'Safari' => 531, 'Chrome' => 6.0); // Used with ajaxenabled() in /lib/ajax/ajaxlib.php which uses check_browser_version in /lib/moodlelib.php which checks against $_SERVER['HTTP_USER_AGENT'];
?>
