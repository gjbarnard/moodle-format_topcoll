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

use stdClass;

/**
 * Library unit tests for the Collapsed Topics course format.
 * @group format_topcoll
 */
final class courseformatlib_test extends \advanced_testcase {
    /** @var class $course */
    protected $course;
    /** @var class $courseformat */
    protected $courseformat;

    protected function setUp(): void {
        $this->resetAfterTest(true);

        set_config('theme', 'boost');
        set_config('enableadditionalmoddata', 2, 'format_topcoll');

        // Ref: https://docs.moodle.org/dev/Writing_PHPUnit_tests.
        $this->course = $this->getDataGenerator()->create_course(
            [
            'format' => 'topcoll',
            'numsections' => 1,
            'toggleforegroundopacity' => '0.1',
            'toggleforegroundhoveropacity' => '0.2',
            'togglebackgroundopacity' => '0.3',
            'togglebackgroundhoveropacity' => '0.4',
            ],
            ['createsections' => true]
        );

        $this->courseformat = course_get_format($this->course);
    }

    public function test_set_up(): void {
        $this->setAdminUser();
        // Check that the defaults have the correct starting values.
        $this->assertEquals('1.0', get_config('format_topcoll', 'defaulttoggleforegroundopacity'));
        $this->assertEquals('1.0', get_config('format_topcoll', 'defaulttoggleforegroundhoveropacity'));
        $this->assertEquals('1.0', get_config('format_topcoll', 'defaulttogglebackgroundopacity'));
        $this->assertEquals('1.0', get_config('format_topcoll', 'defaulttogglebackgroundhoveropacity'));

        set_config('defaulttoggleforegroundopacity', '0.5', 'format_topcoll');
        set_config('defaulttoggleforegroundhoveropacity', '0.6', 'format_topcoll');
        set_config('defaulttogglebackgroundopacity', '0.7', 'format_topcoll');
        set_config('defaulttogglebackgroundhoveropacity', '0.8', 'format_topcoll');

        // Check that the defaults now have the new values.
        $this->assertEquals('0.5', get_config('format_topcoll', 'defaulttoggleforegroundopacity'));
        $this->assertEquals('0.6', get_config('format_topcoll', 'defaulttoggleforegroundhoveropacity'));
        $this->assertEquals('0.7', get_config('format_topcoll', 'defaulttogglebackgroundopacity'));
        $this->assertEquals('0.8', get_config('format_topcoll', 'defaulttogglebackgroundhoveropacity'));

        $thesettings = $this->courseformat->get_settings();

        // Check that the course has been created with the correct values.
        $this->assertEquals('0.1', $thesettings['toggleforegroundopacity']);
        $this->assertEquals('0.2', $thesettings['toggleforegroundhoveropacity']);
        $this->assertEquals('0.3', $thesettings['togglebackgroundopacity']);
        $this->assertEquals('0.4', $thesettings['togglebackgroundhoveropacity']);
    }

    public function test_reset_opacity(): void {
        $teacher = $this->getDataGenerator()->create_user();
        $this->setUser($teacher);

        global $DB;
        $roleids = $DB->get_records_menu('role', null, '', 'shortname, id');
        $this->getDataGenerator()->enrol_user($teacher->id, $this->course->id, $roleids['editingteacher']);

        set_config('defaulttoggleforegroundopacity', '0.5', 'format_topcoll');
        set_config('defaulttoggleforegroundhoveropacity', '0.6', 'format_topcoll');
        set_config('defaulttogglebackgroundopacity', '0.7', 'format_topcoll');
        set_config('defaulttogglebackgroundhoveropacity', '0.8', 'format_topcoll');

        $testdata = new stdClass();
        $testdata->resetcolour = true;
        $this->courseformat->update_course_format_options($testdata);

        $thesettings = $this->courseformat->get_settings();

        $this->assertEquals('0.5', $thesettings['toggleforegroundopacity']);
        $this->assertEquals('0.6', $thesettings['toggleforegroundhoveropacity']);
        $this->assertEquals('0.7', $thesettings['togglebackgroundopacity']);
        $this->assertEquals('0.8', $thesettings['togglebackgroundhoveropacity']);
    }

    public function test_reset_all_opacity(): void {
        $this->setAdminUser();

        set_config('defaulttoggleforegroundopacity', '0.5', 'format_topcoll');
        set_config('defaulttoggleforegroundhoveropacity', '0.6', 'format_topcoll');
        set_config('defaulttogglebackgroundopacity', '0.7', 'format_topcoll');
        set_config('defaulttogglebackgroundhoveropacity', '0.8', 'format_topcoll');

        $testdata = new stdClass();
        $testdata->resetallcolour = true;
        $this->courseformat->update_course_format_options($testdata);

        $thesettings = $this->courseformat->get_settings();

        $this->assertEquals('0.5', $thesettings['toggleforegroundopacity']);
        $this->assertEquals('0.6', $thesettings['toggleforegroundhoveropacity']);
        $this->assertEquals('0.7', $thesettings['togglebackgroundopacity']);
        $this->assertEquals('0.8', $thesettings['togglebackgroundhoveropacity']);
    }

    public function test_showadditionalmoddata_default_yes(): void {
        $this->setAdminUser();

        set_config('defaultshowadditionalmoddata', 2, 'format_topcoll');
        set_config('coursesectionactivityfurtherinformationchoice', 2, 'format_topcoll');
        set_config('coursesectionactivityfurtherinformationdata', 2, 'format_topcoll');

        $thesettings = $this->courseformat->get_settings();
        $this->assertEquals(2, $thesettings['showadditionalmoddata']);

        set_config('coursesectionactivityfurtherinformationchoice', 1, 'format_topcoll');
        set_config('coursesectionactivityfurtherinformationdata', 1, 'format_topcoll');
        set_config('coursesectionactivityfurtherinformationlesson', 2, 'format_topcoll');

        $thesettings = $this->courseformat->get_settings();
        $this->assertEquals(2, $thesettings['showadditionalmoddata']);
    }

    public function test_showadditionalmoddata_reset(): void {
        $this->setAdminUser();

        set_config('defaultshowadditionalmoddata', 1, 'format_topcoll');
        set_config('coursesectionactivityfurtherinformationlesson', 2, 'format_topcoll');

        $testdata = new stdClass();
        $testdata->resetalllayout = true;
        $this->courseformat->update_course_format_options($testdata);
        $thesettings = $this->courseformat->get_settings();
        $this->assertEquals(1, $thesettings['showadditionalmoddata']);
    }
}
