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
 * @copyright  &copy; 2017-onwards G J Barnard in respect to modifications of standard topics format.
 * @author     G J Barnard - {@link https://moodle.org/user/profile.php?id=442195}
 * @link       https://docs.moodle.org/en/Collapsed_Topics_course_format
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace format_topcoll;

/**
 * Toolbox unit tests for the Collapsed Topics course format.
 * @group format_topcoll
 */
final class courseformattoolbox_test extends \advanced_testcase {
    protected function setUp(): void {
        $this->resetAfterTest(true);

        set_config('theme', 'boost');
    }

    public function test_hex2rgba(): void {
        $theoutput = \format_topcoll\toolbox::hex2rgba('ffaabb', '0.8');
        ;
        $thevalue = 'rgba(255, 170, 187, 0.8)';

        $this->assertEquals($thevalue, $theoutput);
    }
}
