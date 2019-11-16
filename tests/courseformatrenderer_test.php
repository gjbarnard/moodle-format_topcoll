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
    protected $cmid;

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

    protected function init($numsections = 1, $layoutcolumnorientation = 2, $toggleallenabled = 2, $viewsinglesectionenabled = 2) {
        $this->resetAfterTest(true);

        set_config('theme', 'boost');
        global $DB, $PAGE;
        $this->outputus = $PAGE->get_renderer('format_topcoll');
        // Ref: https://docs.moodle.org/dev/Writing_PHPUnit_tests.
        $nosections = (empty($numsections)) ? true : false;
        if ($nosections) {
            $numsections = 1;
        }
        $this->course = $this->getDataGenerator()->create_course(array('format' => 'topcoll', 'numsections' => $numsections),
            array('createsections' => true));

        // Make sure all sections are created.
        course_create_sections_if_missing($this->course, range(0, $numsections));
        $this->assertEquals($numsections + 1, $DB->count_records('course_sections', ['course' => $this->course->id]));

        $this->cmid = $this->getDataGenerator()->create_module('forum', ['course' => $this->course->id, 'name' => 'Announcements', 'section' => 0])->cmid;

        if ($nosections) {
            course_delete_section($this->course, 1, true);  // Have only section zero.
        }

        $this->courseformat = course_get_format($this->course);
        self::set_property($this->outputus, 'courseformat', $this->courseformat);
        $target = self::get_property($this->outputus, 'target');
        $ouroutput = $PAGE->get_renderer('core', null, $target);
        self::set_property($this->outputus, 'output', $ouroutput);
        $tcsettings = $this->courseformat->get_settings();
        $tcsettings['layoutcolumnorientation'] = $layoutcolumnorientation;
        $tcsettings['toggleallenabled'] = $toggleallenabled;
        $tcsettings['viewsinglesectionenabled'] = $viewsinglesectionenabled;
        self::set_property($this->outputus, 'tcsettings', $tcsettings);
    }

    public function test_start_section_list() {
        $this->init();
        $theclass = self::call_method($this->outputus, 'start_section_list',
            array());
        $thevalue = '<ul class="ctopics bsnewgrid">';

        $this->assertEquals($thevalue, $theclass);
    }

    public function test_start_toggle_section_list() {
        // With defaults unchanged.
        $this->init();
        $theclass = self::call_method($this->outputus, 'start_toggle_section_list',
            array());
        $thevalue = '<ul class="ctopics topics bsnewgrid row">';

        $this->assertEquals($thevalue, $theclass);
    }

    public function test_page_title() {
        // With defaults unchanged.
        $this->init();
        $theclass = self::call_method($this->outputus, 'page_title', array());
        $thevalue = 'Section';

        $this->assertEquals($thevalue, $theclass);
    }

    public function test_section_right_content() {
        global $CFG;

        // With defaults unchanged.
        $this->init();
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
        $this->init();
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

        $this->init();
        $section = $this->courseformat->get_section(1);
        $theclass = self::call_method($this->outputus, 'section_summary',
            array($section, $this->course, null));
        $thevalue = '<li id="section-1" class="section main section-summary clearfix" role="region" aria-label="';
        $thevalue .= 'Section 1"><div class="left side"></div>';
        $thevalue .= '<div class="content">';
        $thevalue .= '<h3 class="section-title"><a href="'.$CFG->wwwroot.'/course/view.php?id=';
        $thevalue .= $this->course->id.'#section-1" class="">Section 1</a></h3><div class="summarytext"></div>';
        $thevalue .= '<div class="section_availability"></div></div>';
        $thevalue .= '<div class="right side"></div>';
        $thevalue .= '</li>';

        $this->assertEquals($thevalue, $theclass);
    }

    public function test_section_header() {
        global $CFG;

        $this->init();
        $section = $this->courseformat->get_section(1);
        $section->toggle = false;

        $onsectionpage = false;
        $theclass = self::call_method($this->outputus, 'section_header',
            array($section, $this->course, $onsectionpage));
        $thevalue = '<li id="section-1" class="section main clearfix col-sm-12 col-md-12 col-lg-12" role="region" aria-label="Section 1">';
        $thevalue .= '<div class="left side"><span class="cps_centre">1</span></div>';
        $thevalue .= '<div class="content"><div class="sectionhead toggle toggle-arrow" id="toggle-1" tabindex="0">';
        $thevalue .= '<span class="toggle_closed the_toggle tc-medium" role="button" aria-expanded="false">';
        $thevalue .= '<h3 class="sectionname">Section 1<div class="cttoggle"> - Toggle</div></h3>';
        $thevalue .= '<div class="section_availability"></div></span></div>';
        $thevalue .= '<div class="sectionbody toggledsection" id="toggledsection-1">';
        $this->assertEquals($thevalue, $theclass);
        $onsectionpage = true;
        $theclass = self::call_method($this->outputus, 'section_header',
            array($section, $this->course, $onsectionpage));
        $thevalue = '<li id="section-1" class="section main clearfix col-sm-12 col-md-12 col-lg-12" role="region" aria-label="Section 1">';
        $thevalue .= '<div class="left side"></div>';
        $thevalue .= '<div class="content">';
        $thevalue .= '<div class="section_availability"></div><div class="summary"></div>';

        $this->assertEquals($thevalue, $theclass);
    }

    public function test_stealth_section_header() {
        global $CFG;

        $this->init();
        $theclass = self::call_method($this->outputus, 'stealth_section_header',
            array(1));
        $thevalue = '<li id="section-1" class="section main clearfix orphaned hidden col-sm-12 col-md-12 col-lg-12" role="region" ';
        $thevalue .= 'aria-label="Section 1">';
        $thevalue .= '<div class="left side"></div>';
        $thevalue .= '<div class="content"><h3 class="sectionname">Orphaned activities (section 1)</h3>';

        $this->assertEquals($thevalue, $theclass);
    }

    public function test_section_hidden() {
        global $CFG;

        $this->init();
        $section = $this->courseformat->get_section(1);
        $theclass = self::call_method($this->outputus, 'section_hidden',
            array($section, null));
        $thevalue = '<li id="section-1" class="section main clearfix hidden col-sm-12 col-md-12 col-lg-12" role="region" aria-label="Section 1">';
        $thevalue .= '<div class="left side"><span class="cps_centre">1</span></div>';
        $thevalue .= '<div class="content sectionhidden"><h3 class="section-title">Not available</h3></div>';
        $thevalue .= '<div class="right side">';
        $thevalue .= '<a title="View only &#039;Topic 1&#039;" class="cps_centre" ';
        $thevalue .= 'href="'.$CFG->wwwroot.'/course/view.php?id='.$this->course->id.'&amp;section=1">Topic<br />1</a></div>';
        $thevalue .= '</li>';

        $this->assertEquals($thevalue, $theclass);
    }

    public function test_print_multiple_section_page_horizontal() {
        global $CFG;
        $activityicon = 'alt=" " role="presentation" ';
        if ($CFG->version >= 2018120303.06) {
            $activityicon = 'alt="" role="presentation" aria-hidden="true" ';
        }

        $this->init();
        self::call_method($this->outputus, 'print_multiple_section_page',
            array($this->course, null, null, null, null, null));
        $theoutput = '<h2 class="accesshide">Section</h2><ul class="ctopics bsnewgrid">';
        $theoutput .= '<li id="section-0" class="section main clearfix" role="region" aria-label="General">';
        $theoutput .= '<div class="left side"></div>';
        $theoutput .= '<div class="content">';
        $theoutput .= '<div class="section_availability"></div><div class="summary"></div><ul class="section img-text">';
        $theoutput .= '<li class="activity forum modtype_forum " id="module-'.$this->cmid.'"><div><div class="mod-indent-outer">';
        $theoutput .= '<div class="mod-indent"></div><div><div class="activityinstance">';
        $theoutput .= '<a class="" onclick="" href="https://www.example.com/moodle/mod/forum/view.php?id='.$this->cmid.'">';
        $theoutput .= '<img src="https://www.example.com/moodle/theme/image.php/_s/boost/forum/1/icon" class="iconlarge activityicon" '.$activityicon.'/>';
        $theoutput .= '<span class="instancename">Announcements<span class="accesshide " > Forum</span></span></a></div></div>';
        $theoutput .= '</div></div></li></ul></div>';
        $theoutput .= '<div class="right side"></div>';
        $theoutput .= '</li></ul>';
        $theoutput .= '<ul class="ctopics topics bsnewgrid row">';
        $theoutput .= '<li id="section-1" class="section main clearfix col-sm-12 col-md-12 col-lg-12" role="region" aria-label="Section 1">';
        $theoutput .= '<div class="left side"><span class="cps_centre">1</span></div>';
        $theoutput .= '<div class="content"><div class="sectionhead toggle toggle-arrow" id="toggle-1" tabindex="0">';
        $theoutput .= '<span class="toggle_closed the_toggle tc-medium" role="button" aria-expanded="false">';
        $theoutput .= '<h3 class="sectionname">Section 1<div class="cttoggle"> - Toggle</div></h3>';
        $theoutput .= '<div class="section_availability"></div></span></div>';
        $theoutput .= '<div class="sectionbody toggledsection" id="toggledsection-1"><ul class="section img-text">';
        $theoutput .= '</ul></div></div>';
        $theoutput .= '<div class="right side">';
        $theoutput .= '<a title="View only &#039;Topic 1&#039;" class="cps_centre" ';
        $theoutput .= 'href="'.$CFG->wwwroot.'/course/view.php?id='.$this->course->id.'&amp;section=1">Topic<br />1</a>';
        $theoutput .= '</div>';
        $theoutput .= '</li></ul>';
        $this->expectOutputString($theoutput);
    }

    public function test_print_multiple_section_page_vertical() {
        global $CFG;
        $activityicon = 'alt=" " role="presentation" ';
        if ($CFG->version >= 2018120303.06) {
            $activityicon = 'alt="" role="presentation" aria-hidden="true" ';
        }

        $this->init(1, 1);
        self::call_method($this->outputus, 'print_multiple_section_page',
            array($this->course, null, null, null, null, null));
        $theoutput = '<h2 class="accesshide">Section</h2><ul class="ctopics bsnewgrid">';
        $theoutput .= '<li id="section-0" class="section main clearfix" role="region" aria-label="General">';
        $theoutput .= '<div class="left side"></div>';
        $theoutput .= '<div class="content">';
        $theoutput .= '<div class="section_availability"></div><div class="summary"></div>';
        $theoutput .= '<ul class="section img-text"><li class="activity forum modtype_forum " id="module-'.$this->cmid.'"><div>';
        $theoutput .= '<div class="mod-indent-outer"><div class="mod-indent"></div><div><div class="activityinstance">';
        $theoutput .= '<a class="" onclick="" href="https://www.example.com/moodle/mod/forum/view.php?id='.$this->cmid.'">';
        $theoutput .= '<img src="https://www.example.com/moodle/theme/image.php/_s/boost/forum/1/icon" class="iconlarge activityicon" '.$activityicon.'/>';
        $theoutput .= '<span class="instancename">Announcements<span class="accesshide " > Forum</span></span></a>';
        $theoutput .= '</div></div></div></div></li></ul>';
        $theoutput .= '</div>';
        $theoutput .= '<div class="right side"></div>';
        $theoutput .= '</li></ul>';
        $theoutput .= '<div class="row"><ul class="ctopics topics bsnewgrid col-sm-12 col-md-12 col-lg-12">';
        $theoutput .= '<li id="section-1" class="section main clearfix" role="region" aria-label="Section 1">';
        $theoutput .= '<div class="left side"><span class="cps_centre">1</span></div>';
        $theoutput .= '<div class="content"><div class="sectionhead toggle toggle-arrow" id="toggle-1" tabindex="0">';
        $theoutput .= '<span class="toggle_closed the_toggle tc-medium" role="button" aria-expanded="false">';
        $theoutput .= '<h3 class="sectionname">Section 1<div class="cttoggle"> - Toggle</div></h3>';
        $theoutput .= '<div class="section_availability"></div></span></div>';
        $theoutput .= '<div class="sectionbody toggledsection" id="toggledsection-1"><ul class="section img-text">';
        $theoutput .= '</ul></div></div>';
        $theoutput .= '<div class="right side">';
        $theoutput .= '<a title="View only &#039;Topic 1&#039;" class="cps_centre" ';
        $theoutput .= 'href="'.$CFG->wwwroot.'/course/view.php?id='.$this->course->id.'&amp;section=1">Topic<br />1</a>';
        $theoutput .= '</div>';
        $theoutput .= '</li></ul></div>';
        $this->expectOutputString($theoutput);
    }

    public function test_print_multiple_section_page_no_sections_horizontal() {
        global $CFG;
        $activityicon = 'alt=" " role="presentation" ';
        if ($CFG->version >= 2018120303.06) {
            $activityicon = 'alt="" role="presentation" aria-hidden="true" ';
        }

        $this->init(0);
        self::call_method($this->outputus, 'print_multiple_section_page',
            array($this->course, null, null, null, null, null));
        $theoutput = '<h2 class="accesshide">Section</h2>';
        $theoutput .= '<ul class="ctopics bsnewgrid">';
        $theoutput .= '<li id="section-0" class="section main clearfix" role="region" aria-label="General">';
        $theoutput .= '<div class="left side"></div>';
        $theoutput .= '<div class="content">';
        $theoutput .= '<div class="section_availability"></div><div class="summary"></div><ul class="section img-text">';
        $theoutput .= '<li class="activity forum modtype_forum " id="module-'.$this->cmid.'"><div><div class="mod-indent-outer">';
        $theoutput .= '<div class="mod-indent"></div><div><div class="activityinstance">';
        $theoutput .= '<a class="" onclick="" href="https://www.example.com/moodle/mod/forum/view.php?id='.$this->cmid.'">';
        $theoutput .= '<img src="https://www.example.com/moodle/theme/image.php/_s/boost/forum/1/icon" class="iconlarge activityicon" '.$activityicon.'/>';
        $theoutput .= '<span class="instancename">Announcements<span class="accesshide " > Forum</span></span></a></div></div>';
        $theoutput .= '</div></div></li></ul></div>';
        $theoutput .= '<div class="right side"></div>';
        $theoutput .= '</li>';
        $theoutput .= '</ul>';
        $this->expectOutputString($theoutput);
    }

    public function test_print_multiple_section_page_no_sections_vertical() {
        global $CFG;
        $activityicon = 'alt=" " role="presentation" ';
        if ($CFG->version >= 2018120303.06) {
            $activityicon = 'alt="" role="presentation" aria-hidden="true" ';
        }

        $this->init(0, 1);
        self::call_method($this->outputus, 'print_multiple_section_page',
            array($this->course, null, null, null, null, null));
        $theoutput = '<h2 class="accesshide">Section</h2>';
        $theoutput .= '<ul class="ctopics bsnewgrid">';
        $theoutput .= '<li id="section-0" class="section main clearfix" role="region" aria-label="General">';
        $theoutput .= '<div class="left side"></div>';
        $theoutput .= '<div class="content">';
        $theoutput .= '<div class="section_availability"></div><div class="summary"></div><ul class="section img-text">';
        $theoutput .= '<li class="activity forum modtype_forum " id="module-'.$this->cmid.'"><div><div class="mod-indent-outer">';
        $theoutput .= '<div class="mod-indent"></div><div><div class="activityinstance">';
        $theoutput .= '<a class="" onclick="" href="https://www.example.com/moodle/mod/forum/view.php?id='.$this->cmid.'">';
        $theoutput .= '<img src="https://www.example.com/moodle/theme/image.php/_s/boost/forum/1/icon" class="iconlarge activityicon" '.$activityicon.'/>';
        $theoutput .= '<span class="instancename">Announcements<span class="accesshide " > Forum</span></span></a></div></div></div></div></li></ul></div>';
        $theoutput .= '<div class="right side"></div>';
        $theoutput .= '</li>';
        $theoutput .= '</ul>';
        $this->expectOutputString($theoutput);
    }

    public function test_toggle_all() {
        global $CFG;
        $ariahidden = '';
        if ($CFG->version >= 2018120302.07) {
            $ariahidden = 'aria-hidden="true" ';
        }

        $this->init();
        $theclass = self::call_method($this->outputus, 'toggle_all', array());
        $thevalue = '<li class="tcsection main clearfix" id="toggle-all">';
        $thevalue .= '<div class="left side"><img class="icon spacer" ';
        $thevalue .= 'width="1" height="1" alt="" '.$ariahidden.'src="'.$CFG->wwwroot.'/theme/image.php/_s/boost/core/1/spacer" />';
        $thevalue .= '</div>';
        $thevalue .= '<div class="content">';
        $thevalue .= '<div class="sectionbody toggle-arrow-hover toggle-arrow"><h4><span class="on tc-medium" id="';
        $thevalue .= 'toggles-all-opened" role="button" tabindex="0" title="Open all topics">Open all</span>';
        $thevalue .= '<span class="off tc-medium" id="toggles-all-closed" role="button" tabindex="0" ';
        $thevalue .= 'title="Close all topics">Close all</span></h4></div></div>';
        $thevalue .= '<div class="right side"><img class="icon spacer" width="1" height="1" alt="" '.$ariahidden.'src="';
        $thevalue .= $CFG->wwwroot.'/theme/image.php/_s/boost/core/1/spacer" /></div>';
        $thevalue .= '</li>';
        $this->assertEquals($thevalue, $theclass);
    }

    public function test_display_instructions() {
        global $CFG;
        $ariahidden = '';
        if ($CFG->version >= 2018120302.07) {
            $ariahidden = 'aria-hidden="true" ';
        }

        $this->init();
        $theclass = self::call_method($this->outputus, 'display_instructions', array());
        $thevalue = '<li class="tcsection main clearfix" id="topcoll-display-instructions"><div class="left side">';
        $thevalue .= '<img class="icon spacer" width="1" height="1" alt="" '.$ariahidden.'src="';
        $thevalue .= $CFG->wwwroot.'/theme/image.php/_s/boost/core/1/spacer" /></div>';
        $thevalue .= '<div class="content">';
        $thevalue .= '<div class="sectionbody"><p class="topcoll-display-instructions">Instructions: Clicking on the section ';
        $thevalue .= 'name will show / hide the section.</p></div></div>';
        $thevalue .= '<div class="right side">';
        $thevalue .= '<img class="icon spacer" width="1" height="1" alt="" '.$ariahidden.'src="';
        $thevalue .= $CFG->wwwroot.'/theme/image.php/_s/boost/core/1/spacer" /></div>';
        $thevalue .= '</li>';

        $this->assertEquals($thevalue, $theclass);
    }
}
