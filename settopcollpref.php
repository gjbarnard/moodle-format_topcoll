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
 * Code to update a user preference in response to an ajax call.
 *
 * You should not send requests to this script directly.  Instead use the set_user_preference
 * function in /course/format/topcol/module.js.
 *
 * @package    format_topcoll
 * @version    See the value of '$plugin->version' in version.php.
 * @copyright  &copy; 2014-onwards G J Barnard based upon code originally written by Tim Hunt.
 * @author     G J Barnard - gjbarnard at gmail dot com and {@link http://moodle.org/user/profile.php?id=442195}
 * @link       http://docs.moodle.org/en/Collapsed_Topics_course_format
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define('AJAX_SCRIPT', true);
require_once(__DIR__ . '/../../../config.php');

// Check access.
require_login();
require_sesskey();

// Get the name of the preference to update, and check that it is allowed.
$name = required_param('pref', PARAM_RAW);
if (!isset($USER->ajax_updatable_user_prefs[$name])) {
    throw new \moodle_exception('notallowedtoupdateprefremotely');
}

// Get and set the value.
$value = \format_topcoll\togglelib::required_topcoll_param('value');
// Update.
if ($value) {
    if (!set_user_preference($name, $value)) {
        throw new \moodle_exception('errorsettinguserpref');
    }
    echo 'OK';
} else {
    header('HTTP/1.1 406 Not Acceptable');
    echo 'Not Acceptable';
}
