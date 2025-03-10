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

//
// Util Module based on core_user/repository.
//
// @module     format_topcoll/util
// @copyright  2020 Andrew Nicols <andrew@nicols.co.uk>
// @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.
// @copyright  2025 G J Barnard.
// @author     G J Barnard -
//               {@link https://moodle.org/user/profile.php?id=442195}
//               {@link https://gjbarnard.co.uk}
// @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.
//

import {call as fetchMany} from 'core/ajax';

/**
 * Set single user toggle
 *
 * @param {Integer} courseid Course id.
 * @param {Integer} togglenum Toggle number.
 * @param {Boolean} togglestate State of the toggle.
 * @return {Promise}
 */
export const setUserTopcollToggle = (courseid, togglenum, togglestate) => {
    return setUserTopcollToggles([{courseid, togglenum, togglestate}]);
};

/**
 * Set multiple user topcoll toggles.
 *
 * @param {Object[]} togglestates Array of states.
 * @return {Promise}
 */
const setUserTopcollToggles = (togglestates) => {
    return fetchMany([{
        methodname: 'format_topcoll_user_set_toggle',
        args: {togglestates}
    }])[0];
};

/**
 * Set toggle all.
 *
 * @param {Integer} courseid Course id.
 * @param {Boolean} toggleallstate State of all the toggles.
 * @return {Promise}
 */
export const userSetUserToggleAll = (courseid, toggleallstate) => {
    return userSetUserTogglesAll([{courseid, toggleallstate}]);
};

/**
 * Set multiple user topcoll toggles.
 *
 * @param {Object[]} toggleallstates Array of states.
 * @return {Promise}
 */
const userSetUserTogglesAll = (toggleallstates) => {
    return fetchMany([{
        methodname: 'format_topcoll_user_set_toggleall',
        args: {toggleallstates}
    }])[0];
};
