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
 * @copyright  &copy; 2025-onwards G J Barnard in respect to modifications of standard topics format.
 * @author     G J Barnard - {@link https://moodle.org/user/profile.php?id=442195}
 * @link       https://docs.moodle.org/en/Collapsed_Topics_course_format
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace format_topcoll\output;

use core\output\external as external_core;
use core_external\external_function_parameters;
use core_external\external_multiple_structure;
use core_external\external_single_structure;
use core_external\external_value;
use core_external\external_warnings;
use core_user;
use format_topcoll\togglelib;

defined('MOODLE_INTERNAL') || die;

require_once($CFG->dirroot . '/course/format/lib.php'); // For course_get_format.

/**
 * External.
 */
class external extends external_core {
    /**
     * Returns description of method parameters
     *
     * @return external_function_parameters
     */
    public static function user_set_toggle_parameters() {
        return new external_function_parameters(
            [
                'togglestates' => new external_multiple_structure(
                    new external_single_structure(
                        [
                            'courseid' => new external_value(PARAM_INT, 'The course id'),
                            'togglenum' => new external_value(PARAM_INT, 'The toggle number'),
                            'togglestate' => new external_value(PARAM_BOOL, 'The toggle state'),
                        ]
                    )
                ),
            ],
        );
    }

    /**
     * Set user toggle states.
     *
     * @param array $states list of toggle states
     * @return array of warnings and preferences saved
     * @throws moodle_exception
     */
    public static function user_set_toggle($states) {
        global $PAGE, $USER;

        $params = self::validate_parameters(self::user_set_toggle_parameters(), ['togglestates' => $states]);
        $warnings = [];
        $saved = [];

        $context = \context_system::instance();
        $PAGE->set_context($context);

        $userscache = [];
        // Check to which user set the preference.
        if (!empty($userscache[$USER->id])) {
            $user = $userscache[$USER->id];
        } else {
            try {
                $user = core_user::get_user($USER->id, '*', MUST_EXIST);
                core_user::require_active_user($user);
                $userscache[$user->id] = $user;
            } catch (Exception $e) {
                $warnings[] = [
                    'item' => 'user',
                    'itemid' => $USER->id,
                    'warningcode' => 'invaliduser',
                    'message' => $e->getMessage(),
                ];
            }
        }

        foreach ($params['togglestates'] as $pref) {
            try {
                // Support legacy preferences from the old M.util.set_user_preference API (always using the current user).
                $name = togglelib::TOPCOLL_TOGGLE . '_' . $pref['courseid'];
                if (isset($USER->topcoll_user_pref[$name])) {
                    // Update...
                    $userpreference = get_user_preferences($name);
                    $userpreference = togglelib::update_toggle_state($userpreference, $pref['togglenum'], $pref['togglestate']);
                    set_user_preference($name, $userpreference);

                    $saved[] = [
                        'name' => $name,
                        'userid' => $USER->id,
                    ];
                } else {
                    $warnings[] = [
                        'item' => 'user',
                        'itemid' => $user->id,
                        'warningcode' => 'nopermission',
                        'message' => 'You are not allowed to change the preference ' . s($pref['name']) . ' for user ' . $user->id,
                    ];
                }
            } catch (Exception $e) {
                $warnings[] = [
                    'item' => 'user',
                    'itemid' => $user->id,
                    'warningcode' => 'errorsavingpreference',
                    'message' => $e->getMessage(),
                ];
            }
        }

        $result = [];
        $result['saved'] = $saved;
        $result['warnings'] = $warnings;
        return $result;
    }

    /**
     * Returns description of method result value
     *
     * @return external_description
     */
    public static function user_set_toggle_returns() {
        return new external_single_structure(
            [
                'saved' => new external_multiple_structure(
                    new external_single_structure(
                        [
                            'name' => new external_value(PARAM_RAW, 'The name of the preference'),
                            'userid' => new external_value(PARAM_INT, 'The user the preference was set for'),
                        ],
                    ),
                    'Preferences saved'
                ),
                'warnings' => new external_warnings(),
            ]
        );
    }

    /**
     * Returns description of method parameters
     *
     * @return external_function_parameters
     */
    public static function user_set_toggleall_parameters() {
        return new external_function_parameters(
            [
                'toggleallstates' => new external_multiple_structure(
                    new external_single_structure(
                        [
                            'courseid' => new external_value(PARAM_INT, 'The course id'),
                            'toggleallstate' => new external_value(PARAM_BOOL, 'The toggle state'),
                        ]
                    )
                ),
            ],
        );
    }

    /**
     * Set user toggle states.
     *
     * @param array $states list of toggle states
     * @return array of warnings and preferences saved
     * @throws moodle_exception
     */
    public static function user_set_toggleall($states) {
        global $PAGE, $USER;

        $params = self::validate_parameters(self::user_set_toggleall_parameters(), ['toggleallstates' => $states]);
        $warnings = [];
        $saved = [];

        $context = \context_system::instance();
        $PAGE->set_context($context);

        $userscache = [];
        // Check to which user set the preference.
        if (!empty($userscache[$USER->id])) {
            $user = $userscache[$USER->id];
        } else {
            try {
                $user = core_user::get_user($USER->id, '*', MUST_EXIST);
                core_user::require_active_user($user);
                $userscache[$user->id] = $user;
            } catch (Exception $e) {
                $warnings[] = [
                    'item' => 'user',
                    'itemid' => $USER->id,
                    'warningcode' => 'invaliduser',
                    'message' => $e->getMessage(),
                ];
            }
        }

        foreach ($params['toggleallstates'] as $pref) {
            try {
                // Support legacy preferences from the old M.util.set_user_preference API (always using the current user).
                $name = togglelib::TOPCOLL_TOGGLE . '_' . $pref['courseid'];
                if (isset($USER->topcoll_user_pref[$name])) {
                    // Update...
                    $courseformat = course_get_format($pref['courseid']);
                    $coursenumsections = $courseformat->get_last_section_number();
                    $numdigits = togglelib::get_required_digits($coursenumsections);
                    if ($pref['toggleallstate']) {
                        $dchar = togglelib::get_max_digit();
                    } else {
                        $dchar = togglelib::get_min_digit();
                    }
                    $userpreference = '';
                    for ($i = 0; $i < $numdigits; $i++) {
                        $userpreference .= $dchar;
                    }
                    set_user_preference($name, $userpreference);

                    $saved[] = [
                        'name' => $name,
                        'userid' => $USER->id,
                    ];
                } else {
                    $warnings[] = [
                        'item' => 'user',
                        'itemid' => $user->id,
                        'warningcode' => 'nopermission',
                        'message' => 'You are not allowed to change the preference ' . s($pref['name']) . ' for user ' . $user->id,
                    ];
                }
            } catch (Exception $e) {
                $warnings[] = [
                    'item' => 'user',
                    'itemid' => $user->id,
                    'warningcode' => 'errorsavingpreference',
                    'message' => $e->getMessage(),
                ];
            }
        }

        $result = [];
        $result['saved'] = $saved;
        $result['warnings'] = $warnings;
        return $result;
    }

    /**
     * Returns description of method result value
     *
     * @return external_description
     */
    public static function user_set_toggleall_returns() {
        return new external_single_structure(
            [
                'saved' => new external_multiple_structure(
                    new external_single_structure(
                        [
                            'name' => new external_value(PARAM_RAW, 'The name of the preference'),
                            'userid' => new external_value(PARAM_INT, 'The user the preference was set for'),
                        ],
                    ),
                    'Preferences saved'
                ),
                'warnings' => new external_warnings(),
            ]
        );
    }
}
