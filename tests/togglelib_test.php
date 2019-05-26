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
 * Collapsed Topics course format.
 *
 * @package    format_topcoll
 * @version    See the value of '$plugin->version' in version.php.
 * @copyright  &copy; 2018-onwards G J Barnard in respect to modifications of standard topics format.
 * @author     G J Barnard - {@link http://moodle.org/user/profile.php?id=442195}
 * @link       http://docs.moodle.org/en/Collapsed_Topics_course_format
 * @license    http://www.gnu.org/copyleft/gpl.html GNU Public License
 */
defined('MOODLE_INTERNAL') || die();

/**
 * Togglelib unit tests for the Collapsed Topics course format.
 * @group format_topcoll
 */
class format_topcoll_togglelib_testcase extends advanced_testcase {

    public function test_decode_toggle_state() {
        $togglelib = new \format_topcoll\togglelib;

        $mindigit = $togglelib->get_min_digit();
        $maxdigit = $togglelib->get_max_digit();
        $currentdigit = $mindigit;
        $testval = 0;
        while ($currentdigit != $maxdigit) {
            $this->assertEquals(sprintf('%06d', decbin($testval)), $togglelib->decode_toggle_state($currentdigit));
            $currentdigit = chr(ord($currentdigit) + 1);
            $testval++;
        }
        $this->assertEquals(sprintf('%06d', decbin($testval)), $togglelib->decode_toggle_state($maxdigit));

        $currentouterdigit = $mindigit;
        $currentinnerdigit = $mindigit;
        $testval = 0;
        while ($currentouterdigit != $maxdigit) {
            while ($currentinnerdigit != $maxdigit) {
                $this->assertEquals(sprintf('%012d', decbin($testval)),
                    $togglelib->decode_toggle_state($currentouterdigit.$currentinnerdigit));
                $currentinnerdigit = chr(ord($currentinnerdigit) + 1);
                $testval++;
            }
            $this->assertEquals(sprintf('%012d', decbin($testval)), $togglelib->decode_toggle_state($currentouterdigit.$maxdigit));
            $testval++;
            $currentinnerdigit = $mindigit;
            $currentouterdigit = chr(ord($currentouterdigit) + 1);
        }
        $currentinnerdigit = $mindigit;
        while ($currentinnerdigit != $maxdigit) {
            $this->assertEquals(sprintf('%012d', decbin($testval)), $togglelib->decode_toggle_state($maxdigit.$currentinnerdigit));
            $currentinnerdigit = chr(ord($currentinnerdigit) + 1);
            $testval++;
        }
        $this->assertEquals(sprintf('%012d', decbin($testval)), $togglelib->decode_toggle_state($maxdigit.$maxdigit));
    }
}
