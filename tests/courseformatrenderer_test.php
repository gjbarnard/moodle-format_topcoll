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
 * @package    course/format
 * @subpackage topcoll
 * @version    See the value of '$plugin->version' in version.php.
 * @copyright  &copy; 2015-onwards G J Barnard in respect to modifications of standard topics format.
 * @author     G J Barnard - {@link http://moodle.org/user/profile.php?id=442195}
 * @link       http://docs.moodle.org/en/Collapsed_Topics_course_format
 * @license    http://www.gnu.org/copyleft/gpl.html GNU Public License
 */
defined('MOODLE_INTERNAL') || die();

/**
 * Renderer unit tests for the Collapsed Topics course format.
 * @group format_topcoll
 */
class format_topcoll_courseformatrenderer_testcase extends advanced_testcase {

    protected $outputus;
    protected $course;
    protected $courseformat;

    /**
     * Call protected and private methods for the purpose of testing.
     *
     * @param stdClass $obj The object.
     * @param string $name Name of the method.
     * @param array $args Array of arguments if any, like Monty Python could be no minutes, ten, or even thirty.
     * @return any What the method returns if anything, go, go on, look at the specification, you know you want to.
     */
    protected static function call_method($obj, $name, array $args) {
        // Ref: http://stackoverflow.com/questions/249664/best-practices-to-test-protected-methods-with-phpunit.
        $class = new \ReflectionClass($obj);
        $method = $class->getMethod($name);
        $method->setAccessible(true);
        return $method->invokeArgs($obj, $args);
    }

    /**
     * Set protected and private attributes for the purpose of testing.
     *
     * @param stdClass $obj The object.
     * @param string $name Name of the method.
     * @param any $value Value to set.
     */
    protected static function set_property($obj, $name, $value) {
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
    protected static function get_property($obj, $name) {
        // Ref: http://stackoverflow.com/questions/18558183/phpunit-mockbuilder-set-mock-object-internal-property ish.
        $class = new \ReflectionClass($obj);
        $property = $class->getProperty($name);
        $property->setAccessible(true);
        return $property;
    }

    protected function setUp() {
        $this->resetAfterTest(true);

        set_config('theme', 'clean');
        global $PAGE;
        $this->outputus = $PAGE->get_renderer('format_topcoll');
        // Ref: https://docs.moodle.org/dev/Writing_PHPUnit_tests.
        $this->course = $this->getDataGenerator()->create_course(array('format' => 'topcoll', 'numsections' => 1),
            array('createsections' => true));

        $this->courseformat = course_get_format($this->course);
        self::set_property($this->outputus, 'courseformat', $this->courseformat);
        $target = self::get_property($this->outputus, 'target');
        $ouroutput = $PAGE->get_renderer('core', null, $target);
        self::set_property($this->outputus, 'output', $ouroutput);
        $tcsettings = $this->courseformat->get_settings();
        self::set_property($this->outputus, 'tcsettings', $tcsettings);
    }

    public function test_start_section_list() {
        $theclass = self::call_method($this->outputus, 'start_section_list',
            array());
        $thevalue = '<ul class="ctopics">';

        $this->assertEquals($thevalue, $theclass);
    }

    public function test_start_toggle_section_list() {
        // With defaults unchanged.
        $theclass = self::call_method($this->outputus, 'start_toggle_section_list',
            array());
        $thevalue = '<ul class="ctopics topics row-fluid">';

        $this->assertEquals($thevalue, $theclass);
    }

    public function test_page_title() {
        // With defaults unchanged.
        $theclass = self::call_method($this->outputus, 'page_title', array());
        $thevalue = 'Section';

        $this->assertEquals($thevalue, $theclass);
    }

    public function test_section_right_content() {
        global $CFG;

        // With defaults unchanged.
        $section = $this->courseformat->get_section(1);
        $onsectionpage = false;
        $theclass = self::call_method($this->outputus, 'section_right_content',
            array($section, $this->course, $onsectionpage));
        $thevalue = '<a title="View only &#039;Topic 1&#039;" class="cps_centre" ';
        $thevalue .= 'href="'.$CFG->wwwroot.'/course/view.php?id='.$this->course->id.'&amp;section=1">Topic<br />1</a>';
        $this->assertEquals($thevalue, $theclass);

        $onsectionpage = true;
        $theclass = self::call_method($this->outputus, 'section_right_content',
            array($section, $this->course, $onsectionpage));
        $thevalue = '';
        $this->assertEquals($thevalue, $theclass);
    }

    public function test_section_left_content() {
        $section = $this->courseformat->get_section(1);
        $onsectionpage = false;
        $theclass = self::call_method($this->outputus, 'section_left_content',
            array($section, $this->course, $onsectionpage));
        $thevalue = '<span class="cps_centre">1</span>';
        $this->assertEquals($thevalue, $theclass);

        $onsectionpage = true;
        $theclass = self::call_method($this->outputus, 'section_left_content',
            array($section, $this->course, $onsectionpage));
        $thevalue = '';
        $this->assertEquals($thevalue, $theclass);
    }

    public function test_section_summary() {
        global $CFG;

        $section = $this->courseformat->get_section(1);
        $theclass = self::call_method($this->outputus, 'section_summary',
            array($section, $this->course, null));
        $thevalue = '<li id="section-1" class="section main section-summary clearfix" role="region" aria-label="';
        $thevalue .= 'Section 1"><div class="left side"></div><div class="right side"></div><div class="content">';
        $thevalue .= '<h3 class="section-title"><a href="'.$CFG->wwwroot.'/course/view.php?id=';
        $thevalue .= $this->course->id.'#section-1" class="">Section 1</a></h3><div class="summarytext"></div>';
        $thevalue .= '<div class="section_availability"></div></div></li>';

        $this->assertEquals($thevalue, $theclass);
    }

    public function test_section_header() {
        global $CFG;

        $section = $this->courseformat->get_section(1);
        $section->toggle = false;

        $onsectionpage = false;
        $theclass = self::call_method($this->outputus, 'section_header',
            array($section, $this->course, $onsectionpage));
        $thevalue = '<li id="section-1" class="section main clearfix span12" role="region" aria-label="Section 1">';
        $thevalue .= '<div class="left side"><span class="cps_centre">1</span></div><div class="right side">';
        $thevalue .= '<a title="View only &#039;Topic 1&#039;" class="cps_centre" ';
        $thevalue .= 'href="'.$CFG->wwwroot.'/course/view.php?id='.$this->course->id.'&amp;section=1">Topic<br />1</a>';
        $thevalue .= '</div><div class="content"><div class="sectionhead toggle toggle-arrow" id="toggle-1">';
        $thevalue .= '<span class="toggle_closed the_toggle tc-medium" role="button" aria-pressed="false">';
        $thevalue .= '<h3 class="sectionname">Section 1<div class="cttoggle"> - Toggle</div></h3>';
        $thevalue .= '<div class="section_availability"></div></span></div>';
        $thevalue .= '<div class="sectionbody toggledsection" id="toggledsection-1">';
        $this->assertEquals($thevalue, $theclass);
        $onsectionpage = true;
        $theclass = self::call_method($this->outputus, 'section_header',
            array($section, $this->course, $onsectionpage));
        $thevalue = '<li id="section-1" class="section main clearfix span12" role="region" aria-label="Section 1">';
        $thevalue .= '<div class="left side"></div><div class="right side"></div><div class="content">';
        $thevalue .= '<div class="section_availability"></div><div class="summary"></div>';

        $this->assertEquals($thevalue, $theclass);
    }

    public function test_stealth_section_header() {
        global $CFG;

        $theclass = self::call_method($this->outputus, 'stealth_section_header',
            array(1));
        $thevalue = '<li id="section-1" class="section main clearfix orphaned hidden span12" role="region" ';
        $thevalue .= 'aria-label="Section 1"><div class="left side"></div><div class="right side">';
        $thevalue .= '<a title="View only &#039;Topic 1&#039;" class="cps_centre" ';
        $thevalue .= 'href="'.$CFG->wwwroot.'/course/view.php?id='.$this->course->id.'&amp;section=1">Topic<br />1</a>';
        $thevalue .= '</div><div class="content"><h3 class="sectionname">Orphaned activities (section 1)</h3>';

        $this->assertEquals($thevalue, $theclass);
    }

    public function test_section_hidden() {
        global $CFG;

        $section = $this->courseformat->get_section(1);
        $theclass = self::call_method($this->outputus, 'section_hidden',
            array($section, null));
        $thevalue = '<li id="section-1" class="section main clearfix hidden span12" role="region" aria-label="Section 1">';
        $thevalue .= '<div class="left side"><span class="cps_centre">1</span></div><div class="right side">';
        $thevalue .= '<a title="View only &#039;Topic 1&#039;" class="cps_centre" ';
        $thevalue .= 'href="'.$CFG->wwwroot.'/course/view.php?id='.$this->course->id.'&amp;section=1">Topic<br />1</a></div>';
        $thevalue .= '<div class="content sectionhidden"><h3 class="section-title">Not available</h3></div></li>';

        $this->assertEquals($thevalue, $theclass);
    }

    public function test_print_multiple_section_page() {
        global $CFG;

        self::call_method($this->outputus, 'print_multiple_section_page',
            array($this->course, null, null, null, null, null));
        $theoutput = '<h2 class="accesshide">Section</h2><ul class="ctopics"></ul><ul class="ctopics topics row-fluid">';
        $theoutput .= '<li id="section-1" class="section main clearfix span12" role="region" aria-label="Section 1">';
        $theoutput .= '<div class="left side"><span class="cps_centre">1</span></div><div class="right side">';
        $theoutput .= '<a title="View only &#039;Topic 1&#039;" class="cps_centre" ';
        $theoutput .= 'href="'.$CFG->wwwroot.'/course/view.php?id='.$this->course->id.'&amp;section=1">Topic<br />1</a>';
        $theoutput .= '</div><div class="content"><div class="sectionhead toggle toggle-arrow" id="toggle-1">';
        $theoutput .= '<span class="toggle_closed the_toggle tc-medium" role="button" aria-pressed="false">';
        $theoutput .= '<h3 class="sectionname">Section 1<div class="cttoggle"> - Toggle</div></h3>';
        $theoutput .= '<div class="section_availability"></div></span></div>';
        $theoutput .= '<div class="sectionbody toggledsection" id="toggledsection-1"><ul class="section img-text">';
        $theoutput .= '</ul></div></div></li></ul>';
        $this->expectOutputString($theoutput);
    }

    public function test_toggle_all() {
        global $CFG;

        $theclass = self::call_method($this->outputus, 'toggle_all', array());
        $thevalue = '<li class="tcsection main clearfix" id="toggle-all"><div class="left side"><img class="icon spacer" ';
        $thevalue .= 'width="1" height="1" alt="" src="'.$CFG->wwwroot.'/theme/image.php/_s/clean/core/1/spacer" />';
        $thevalue .= '</div><div class="right side"><img class="icon spacer" width="1" height="1" alt="" src="';
        $thevalue .= $CFG->wwwroot.'/theme/image.php/_s/clean/core/1/spacer" /></div><div class="content">';
        $thevalue .= '<div class="sectionbody toggle-arrow-hover toggle-arrow"><h4><span class="on tc-medium" id="';
        $thevalue .= 'toggles-all-opened" role="button">Open all</span><span class="off tc-medium" id="toggles-all-closed" ';
        $thevalue .= 'role="button">Close all</span></h4></div></div></li>';
        $this->assertEquals($thevalue, $theclass);
    }

    public function test_display_instructions() {
        global $CFG;

        $theclass = self::call_method($this->outputus, 'display_instructions', array());
        $thevalue = '<li class="tcsection main clearfix" id="topcoll-display-instructions"><div class="left side">';
        $thevalue .= '<img class="icon spacer" width="1" height="1" alt="" src="';
        $thevalue .= $CFG->wwwroot.'/theme/image.php/_s/clean/core/1/spacer" /></div><div class="right side">';
        $thevalue .= '<img class="icon spacer" width="1" height="1" alt="" src="';
        $thevalue .= $CFG->wwwroot.'/theme/image.php/_s/clean/core/1/spacer" /></div><div class="content">';
        $thevalue .= '<div class="sectionbody"><p class="topcoll-display-instructions">Instructions: Clicking on the section ';
        $thevalue .= 'name will show / hide the section.</p></div></div></li>';

        $this->assertEquals($thevalue, $theclass);
    }
}
