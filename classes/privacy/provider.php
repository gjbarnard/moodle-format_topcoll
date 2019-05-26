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
 * Privacy Subsystem implementation for format_topcoll.
 *
 * @package    format_topcoll
 * @version    See the value of '$plugin->version' in version.php.
 * @copyright  &copy; 2018-onwards G J Barnard based upon work done by Andrew Nicols <andrew@nicols.co.uk>.
 * @author     G J Barnard - {@link http://moodle.org/user/profile.php?id=442195}
 * @link       http://docs.moodle.org/en/Collapsed_Topics_course_format
 * @license    http://www.gnu.org/copyleft/gpl.html GNU Public License
 */

namespace format_topcoll\privacy;

use \core_privacy\local\request\writer;
use \core_privacy\local\metadata\collection;
use \core_privacy\local\request\transform;

defined('MOODLE_INTERNAL') || die();

/**
 * Implementation of the privacy subsystem plugin provider.
 */
class provider implements
    // This plugin has data.
    \core_privacy\local\metadata\provider,

    // This plugin has some sitewide user preferences to export.
    \core_privacy\local\request\user_preference_provider {

    /**
     * Returns meta data about this system.
     *
     * @param   collection $itemcollection The initialised item collection to add items to.
     * @return  collection A listing of user data stored through this system.
     */
    public static function get_metadata(collection $items) : collection {
        $items->add_user_preference(\format_topcoll\toolbox::TOPCOLL_TOGGLE, 'privacy:metadata:preference:toggle');

        return $items;
    }

    /**
     * Store all user preferences for the plugin.
     *
     * @param int $userid The userid of the user whose data is to be exported.
     */
    public static function export_user_preferences(int $userid) {
        $preferences = get_user_preferences(null, null, $userid);
        $togglelib = new \format_topcoll\togglelib;
        foreach ($preferences as $name => $value) {
            $courseid = null;
            if (strpos($name, \format_topcoll\toolbox::TOPCOLL_TOGGLE) === 0) {
                $courseid = substr($name, strlen(\format_topcoll\toolbox::TOPCOLL_TOGGLE) + 1);

                writer::export_user_preference(
                    'format_topcoll',
                    $name,
                    $value,
                    get_string('privacy:request:preference:toggle', 'format_topcoll', (object) [
                        'name' => $courseid,
                        'value' => $value,
                        'decoded' => $togglelib->decode_toggle_state($value),
                    ])
                );
            }
        }
    }
}
