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

//
// Optional course format configuration file
//
// This file contains any specific configuration settings for the 
// format.
//
// The default blocks layout for this course format:
    $format['defaultblocks'] = ':search_forums,news_items,calendar_upcoming,recent_activity';
//

// Layout configuration.
// Here you can see what numbers in the array represent what layout for setting the default value below.
// 1 => Default.
// 2 => No 'Topic x' / 'Week x'.
// 3 => No section number.
// 4 => No 'Topic x' / 'Week x' and no section number.
// 5 => No 'Toggle' word.
// 6 => No 'Toggle' word and no 'Topic x' / 'Week x'.
// 7 => No 'Toggle' word, no 'Topic x' / 'Week x'  and no section number.

// Default layout to use - used when a new Collapsed Topics course is created or an old one is accessed for the first time after installing this functionality introduced in CONTRIB-3378.
$defaultlayoutelement = 1;

// Structure configuration.
// Here so you can see what numbers in the array represent what structure for setting the default value below.
// 1 => Topic
// 2 => Week   
// 3 => Latest Week First 
// 4 => Current Topic First

// Default structure to use - used when a new Collapsed Topics course is created or an old one is accessed for the first time after installing this functionality introduced in CONTRIB-3378.
$defaultlayoutstructure = 1;

?>