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
 * @copyright  &copy; 2017-onwards G J Barnard in respect to modifications of standard topics format.
 * @author     G J Barnard - {@link http://moodle.org/user/profile.php?id=442195}
 * @link       http://docs.moodle.org/en/Collapsed_Topics_course_format
 * @license    http://www.gnu.org/copyleft/gpl.html GNU Public License
 */
defined('MOODLE_INTERNAL') || die();

/**
 * Library unit tests for the Collapsed Topics course format.
 * @group format_topcoll
 */
class format_topcoll_courseformatlib_testcase extends advanced_testcase {

    protected $course;
    protected $courseformat;

    protected function setUp() {
        $this->resetAfterTest(true);

        set_config('theme', 'boost');
        // Ref: https://docs.moodle.org/dev/Writing_PHPUnit_tests.
        $this->course = $this->getDataGenerator()->create_course(array(
            'format' => 'topcoll',
            'numsections' => 1,
            'toggleforegroundopacity' => '0.1',
            'toggleforegroundhoveropacity' => '0.2',
            'togglebackgroundopacity' => '0.3',
            'togglebackgroundhoveropacity' => '0.4'
        ),
        array('createsections' => true));

        $this->courseformat = course_get_format($this->course);
    }

    public function test_set_up() {
        $this->setAdminUser();
        // Check that the defaults have the correct starting values.
        $this->assertEquals('1.0', get_config('format_topcoll', 'defaulttgfgopacity'));
        $this->assertEquals('1.0', get_config('format_topcoll', 'defaulttgfghvropacity'));
        $this->assertEquals('1.0', get_config('format_topcoll', 'defaulttgbgopacity'));
        $this->assertEquals('1.0', get_config('format_topcoll', 'defaulttgbghvropacity'));

        set_config('defaulttgfgopacity', '0.5', 'format_topcoll');
        set_config('defaulttgfghvropacity', '0.6', 'format_topcoll');
        set_config('defaulttgbgopacity', '0.7', 'format_topcoll');
        set_config('defaulttgbghvropacity', '0.8', 'format_topcoll');

        // Check that the defaults now have the new values.
        $this->assertEquals('0.5', get_config('format_topcoll', 'defaulttgfgopacity'));
        $this->assertEquals('0.6', get_config('format_topcoll', 'defaulttgfghvropacity'));
        $this->assertEquals('0.7', get_config('format_topcoll', 'defaulttgbgopacity'));
        $this->assertEquals('0.8', get_config('format_topcoll', 'defaulttgbghvropacity'));

        $thesettings = $this->courseformat->get_settings();

        // Check that the course has been created with the correct values.
        $this->assertEquals('0.1', $thesettings['toggleforegroundopacity']);
        $this->assertEquals('0.2', $thesettings['toggleforegroundhoveropacity']);
        $this->assertEquals('0.3', $thesettings['togglebackgroundopacity']);
        $this->assertEquals('0.4', $thesettings['togglebackgroundhoveropacity']);
    }

    public function test_reset_opacity() {
        $teacher = $this->getDataGenerator()->create_user();
        $this->setUser($teacher);

        global $DB;
        $roleids = $DB->get_records_menu('role', null, '', 'shortname, id');
        $this->getDataGenerator()->enrol_user($teacher->id, $this->course->id, $roleids['editingteacher']);

        set_config('defaulttgfgopacity', '0.5', 'format_topcoll');
        set_config('defaulttgfghvropacity', '0.6', 'format_topcoll');
        set_config('defaulttgbgopacity', '0.7', 'format_topcoll');
        set_config('defaulttgbghvropacity', '0.8', 'format_topcoll');

        $testdata = new stdClass;
        $testdata->resetcolour = true;
        $this->courseformat->update_course_format_options($testdata);

        $thesettings = $this->courseformat->get_settings();

        $this->assertEquals('0.5', $thesettings['toggleforegroundopacity']);
        $this->assertEquals('0.6', $thesettings['toggleforegroundhoveropacity']);
        $this->assertEquals('0.7', $thesettings['togglebackgroundopacity']);
        $this->assertEquals('0.8', $thesettings['togglebackgroundhoveropacity']);
    }

    public function test_reset_all_opacity() {
        $this->setAdminUser();

        set_config('defaulttgfgopacity', '0.5', 'format_topcoll');
        set_config('defaulttgfghvropacity', '0.6', 'format_topcoll');
        set_config('defaulttgbgopacity', '0.7', 'format_topcoll');
        set_config('defaulttgbghvropacity', '0.8', 'format_topcoll');

        $testdata = new stdClass;
        $testdata->resetallcolour = true;
        $this->courseformat->update_course_format_options($testdata);

        $thesettings = $this->courseformat->get_settings();

        $this->assertEquals('0.5', $thesettings['toggleforegroundopacity']);
        $this->assertEquals('0.6', $thesettings['toggleforegroundhoveropacity']);
        $this->assertEquals('0.7', $thesettings['togglebackgroundopacity']);
        $this->assertEquals('0.8', $thesettings['togglebackgroundhoveropacity']);
    }
}
