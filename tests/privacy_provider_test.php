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
 * Unit tests for the implementation of the privacy API.
 *
 * @package    format_topcoll
 * @copyright  &copy; 2018-onwards G J Barnard based upon code originally written by Andrew Nicols.
 * @author     G J Barnard - {@link https://moodle.org/user/profile.php?id=442195}
 * @link       https://docs.moodle.org/en/Collapsed_Topics_course_format
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace format_topcoll;

use core_privacy\local\metadata\collection;
use core_privacy\local\request\writer;
use format_topcoll\privacy\provider;

/**
 * Privacy unit tests for the Collapsed Topics course format.
 * @group format_topcoll
 */
final class privacy_provider_test extends \core_privacy\tests\provider_testcase {
    /** @var class $outputus */
    protected $outputus;
    /** @var class $course */
    protected $course;
    /** @var class $courseformat */
    protected $courseformat;
    /** @var int $numsections */
    protected $numsections = 18;

    /**
     * Set protected and private attributes for the purpose of testing.
     *
     * @param stdClass $obj The object.
     * @param string $name Name of the method.
     * @param any $value Value to set.
     */
    protected static function set_property($obj, $name, $value): void {
        // Ref: http://stackoverflow.com/questions/18558183/phpunit-mockbuilder-set-mock-object-internal-property ish.
        $class = new \ReflectionClass($obj);
        $property = $class->getProperty($name);
        $property->setAccessible(true);
        $property->setValue($obj, $value);
    }

    /**
     * Get protected and private methods for the purpose of testing.
     *
     * @param stdClass $obj The object.
     * @param string $name Name of the method.
     */
    protected static function get_property($obj, $name): \ReflectionProperty {
        // Ref: http://stackoverflow.com/questions/18558183/phpunit-mockbuilder-set-mock-object-internal-property ish.
        $class = new \ReflectionClass($obj);
        $property = $class->getProperty($name);
        $property->setAccessible(true);
        return $property;
    }

    /**
     * Set up.
     */
    protected function set_up(): void {
        $this->resetAfterTest(true);

        set_config('theme', 'boost');
        global $PAGE;
        $this->outputus = $PAGE->get_renderer('format_topcoll');
        // Ref: https://docs.moodle.org/dev/Writing_PHPUnit_tests.
        $this->course = $this->getDataGenerator()->create_course(
            ['format' => 'topcoll', 'numsections' => $this->numsections],
            ['createsections' => true]
        );

        $this->courseformat = course_get_format($this->course);
        self::set_property($this->outputus, 'courseformat', $this->courseformat);
        $target = self::get_property($this->outputus, 'target');
        $ouroutput = $PAGE->get_renderer('core', null, $target);
        self::set_property($this->outputus, 'output', $ouroutput);
        $tcsettings = $this->courseformat->get_settings();
        self::set_property($this->outputus, 'tcsettings', $tcsettings);
    }

    /**
     * Ensure that get_metadata exports valid content.
     */
    public function test_get_metadata(): void {
        $items = new collection('format_topcoll');
        $result = provider::get_metadata($items);
        $this->assertSame($items, $result);
        $this->assertInstanceOf(collection::class, $result);
    }

    /**
     * Ensure that export_user_preferences returns no data if the user has not used a CT course.
     */
    public function test_export_user_preferences_no_pref(): void {
        $user = \core_user::get_user_by_username('admin');
        provider::export_user_preferences($user->id);

        $writer = writer::with_context(\context_system::instance());

        $this->assertFalse($writer->has_any_data());
    }

    /**
     * Ensure that export_user_preferences returns request data.
     */
    public function test_export_user_preferences(): void {
        $togglelib = new \format_topcoll\togglelib();

        $this->set_up();
        $this->setAdminUser();

        set_user_preference(\format_topcoll\togglelib::TOPCOLL_TOGGLE.'_' . $this->course->id, 'FAB');

        $user = \core_user::get_user_by_username('admin');
        provider::export_user_preferences($user->id);

        $writer = writer::with_context(\context_system::instance());

        $this->assertTrue($writer->has_any_data());

        $prefs = (array) $writer->get_user_preferences('format_topcoll');

        $this->assertCount(1, $prefs);

        $toggle = $prefs[\format_topcoll\togglelib::TOPCOLL_TOGGLE.'_' . $this->course->id];
        $this->assertEquals('FAB', $toggle->value);

        $description = get_string('privacy:request:preference:toggle', 'format_topcoll', (object) [
            'name' => $this->course->id,
            'value' => 'FAB',
            'decoded' => $togglelib->decode_toggle_state('FAB'),
        ]);
        $this->assertEquals($description, $toggle->description);
    }
}
