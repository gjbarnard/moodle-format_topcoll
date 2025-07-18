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
 * @copyright  &copy; 2015-onwards G J Barnard in respect to modifications of standard topics format.
 * @author     G J Barnard - {@link https://moodle.org/user/profile.php?id=442195}
 * @link       https://docs.moodle.org/en/Collapsed_Topics_course_format
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace format_topcoll;

use format_topcoll\togglelib;
use PHPUnit\Framework\Attributes\Group;
use ReflectionClass;

/**
 * Renderer unit tests for the Collapsed Topics course format.
 * @group format_topcoll
 */
#[Group('format_topcoll')]
final class courseformatrenderer_test extends \advanced_testcase {
    /** @var class $outputus */
    protected $outputus;
    /** @var class $ouroutput */
    protected $ouroutput;
    /** @var class $course */
    protected $course;
    /** @var class $courseformat */
    protected $courseformat;
    /** @var int $cmid */
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
        $class = new ReflectionClass($obj);
        $method = $class->getMethod($name);
        $method->setAccessible(true);
        return $method->invokeArgs($obj, $args);
    }

    /**
     * Set protected and private attributes for the purpose of testing.
     *
     * @param stdClass $obj The object.
     * @param string $name Name of the attribute.
     * @param any $value Value to set.
     */
    protected static function set_property($obj, $name, $value) {
        // Ref: http://stackoverflow.com/questions/18558183/phpunit-mockbuilder-set-mock-object-internal-property ish.
        $class = new ReflectionClass($obj);
        $property = $class->getProperty($name);
        $property->setAccessible(true);
        $property->setValue($obj, $value);
    }

    /**
     * Get protected and private attributes for the purpose of testing.
     *
     * @param stdClass $obj The object.
     * @param string $name Name of the attribute.
     */
    protected static function get_property($obj, $name) {
        // Ref: http://stackoverflow.com/questions/18558183/phpunit-mockbuilder-set-mock-object-internal-property ish.
        $class = new ReflectionClass($obj);
        $property = $class->getProperty($name);
        $property->setAccessible(true);
        return $property;
    }

    /**
     * Init.
     */
    protected function init($numsections = 1, $layoutcolumnorientation = 2, $flexiblemodules = 1,
        $toggleallenabled = 2, $viewsinglesectionenabled = 2): void {
        $this->resetAfterTest(true);

        set_config('theme', 'boost');
        global $DB, $PAGE;
        $this->outputus = $PAGE->get_renderer('format_topcoll');
        // Ref: https://docs.moodle.org/dev/Writing_PHPUnit_tests.
        $nosections = (empty($numsections)) ? true : false;
        if ($nosections) {
            $numsections = 1;
        }
        $this->course = $this->getDataGenerator()->create_course(
            ['format' => 'topcoll', 'numsections' => $numsections],
            ['createsections' => true]
        );

        // Make sure all sections are created.
        course_create_sections_if_missing($this->course, range(0, $numsections));
        $this->assertEquals($numsections + 1, $DB->count_records('course_sections', ['course' => $this->course->id]));

        $this->cmid = $this->getDataGenerator()->create_module('forum', ['course' => $this->course->id, 'name' => 'Announcements',
            'section' => 0, ])->cmid;

        if ($nosections) {
            course_delete_section($this->course, 1, true);  // Have only section zero.
        }

        self::set_property($this->outputus, 'course', $this->course);
        $this->courseformat = course_get_format($this->course);
        self::set_property($this->outputus, 'courseformat', $this->courseformat);
        $target = self::get_property($this->outputus, 'target');
        $this->ouroutput = $PAGE->get_renderer('core', null, $target);
        self::set_property($this->outputus, 'output', $this->ouroutput);
        $tcsettings = $this->courseformat->get_settings();
        $tcsettings['layoutcolumnorientation'] = $layoutcolumnorientation;
        $tcsettings['flexiblemodules'] = $flexiblemodules;
        $tcsettings['toggleallenabled'] = $toggleallenabled;
        $tcsettings['viewsinglesectionenabled'] = $viewsinglesectionenabled;
        $tcsettings['toggleiconset'] = 'arrow';
        self::set_property($this->outputus, 'tcsettings', $tcsettings);
    }

    public function test_start_section_list(): void {
        $this->init();
        $theclass = self::call_method(
            $this->outputus,
            'start_section_list',
            []
        );
        $thevalue = '<ul class="ctopics">';

        $this->assertEquals($thevalue, $theclass);
    }

    public function test_start_toggle_section_list(): void {
        // With defaults unchanged.
        $this->init();
        $theclass = self::call_method(
            $this->outputus,
            'start_toggle_section_list',
            []
        );
        $thevalue = '<ul class="ctopics ctoggled topics row">';

        $this->assertEquals($thevalue, $theclass);
    }

    public function test_page_title(): void {
        // With defaults unchanged.
        $this->init();
        $theclass = self::call_method($this->outputus, 'page_title', []);
        $thevalue = 'Section';

        $this->assertEquals($thevalue, $theclass);
    }

    public function test_section_right_content(): void {
        global $CFG;

        // With defaults unchanged.
        $this->init();
        $section = $this->courseformat->get_section(1);
        $onsectionpage = false;
        $theclass = self::call_method(
            $this->outputus,
            'section_right_content',
            [$section, $this->course, $onsectionpage]
        );
        $thevalue = '<div class="right side">';
        $thevalue .= '<a title="View only &#039;Topic 1&#039;" class="cps_centre" ';
        $thevalue .= 'href="' . $CFG->wwwroot . '/course/view.php?id=' . $this->course->id . '&amp;section=1">Topic<br />1</a>';
        $thevalue .= '</div>';
        $this->assertEquals($thevalue, $theclass);

        $onsectionpage = true;
        $theclass = self::call_method(
            $this->outputus,
            'section_right_content',
            [$section, $this->course, $onsectionpage]
        );
        $thevalue = '';
        $this->assertEquals($thevalue, $theclass);
    }

    public function test_section_left_content(): void {
        $this->init();
        $section = $this->courseformat->get_section(1);
        $onsectionpage = false;
        $theclass = self::call_method(
            $this->outputus,
            'section_left_content',
            [$section, $this->course, $onsectionpage]
        );
        $thevalue = '<div class="left side"><span class="cps_centre">1</span></div>';
        $this->assertEquals($thevalue, $theclass);

        $onsectionpage = true;
        $theclass = self::call_method(
            $this->outputus,
            'section_left_content',
            [$section, $this->course, $onsectionpage]
        );
        $thevalue = '';
        $this->assertEquals($thevalue, $theclass);
    }

    public function test_section_summary(): void {
        global $CFG;

        $this->init();
        self::set_property($this->outputus, 'formatresponsive', true);
        $section = $this->courseformat->get_section(1);
        $theclass = self::call_method(
            $this->outputus,
            'section_summary',
            [$section, $this->course, null]
        );

        $sectionsummarycontext = [
            'heading' => '<h3 data-for="section_title" data-id="' . $section->id . '" data-number="1" id="sectionid-' .
                $section->id . '-title" class="section-title"><a href="' . $CFG->wwwroot . '/course/view.php?id=' .
                $this->course->id . '#section-1"' . ' class="">Section 1</a></h3>',
            'columnwidth' => '100',
            'rtl' => false,
            'sectionavailability' => '<div class="section_availability"></div>',
            'sectionno' => '1',
            'title' => 'Section 1',
        ];
        $sectionsummarycontext['formatsummarytext'] = self::call_method($this->outputus, 'format_summary_text', [$section]);
        $sectionsummarycontext['sectionactivitysummary'] = self::call_method(
            $this->outputus,
            'section_activity_summary',
            [$section, $this->course, null]
        );
        $sectionsummarycontext['sectionavailability'] = self::call_method(
            $this->outputus,
            'section_availability',
            [$section]
        );

        $thevalue = self::call_method($this->outputus, 'render_from_template', ['format_topcoll/sectionsummary',
            $sectionsummarycontext, ]);

        $this->assertEquals($thevalue, $theclass);
    }

    public function test_topcoll_section(): void {
        $this->init();
        set_user_preference(togglelib::TOPCOLL_TOGGLE.'_' . $this->course->id, 'Z');
        set_config('defaultuserpreference', 0, 'format_topcoll');
        set_config('defaulttogglepersistence', 1, 'format_topcoll');
        self::set_property($this->outputus, 'formatresponsive', false);
        $section1 = $this->courseformat->get_section(1);
        $toggle = true;

        $onsectionpage = false;
        $sectionreturn = null;
        $theclass = self::call_method(
            $this->outputus,
            'topcoll_section',
            [$section1, $this->course, $onsectionpage, null, $toggle]
        );

        $sectioncontext = [
            'columnclass' => 'col-sm-12',
            'contentaria' => true,
            'cscml' => self::call_method($this->outputus, 'course_section_cmlist', [$section1]) .
                self::call_method($this->outputus, 'course_section_add_cm_control', [$this->course, $section1->section,
                    $sectionreturn, ]),
            'leftcontent' => self::call_method($this->outputus, 'section_left_content', [$section1, $this->course,
                $onsectionpage, ]),
            'heading' => '<h3 data-for="section_title" data-id="' . $section1->id . '" data-number="1" id="sectionid-' . $section1->id .
                '-title" class="sectionname">Section 1<div class="cttoggle"> - Toggle</div></h3>',
            'nomtore' => true,
            'rightcontent' => self::call_method($this->outputus, 'section_right_content', [$section1, $this->course,
                $onsectionpage, ]),
            'rtl' => false,
            'sectionavailability' => self::call_method($this->outputus, 'section_availability', [$section1]),
            'sectionid' => $section1->id,
            'sectionno' => $section1->section,
            'sectionpage' => $onsectionpage,
            'sectionreturn' => $sectionreturn,
            'sectionsummary' => self::call_method($this->outputus, 'section_summary_container', [$section1]),
            'sectionsummarywhencollapsed' => false,
            'toggleiconset' => 'arrow',
            'toggleiconsize' => 'tc-medium',
            'toggleopen' => $toggle,
        ];
        $thevalue = self::call_method($this->outputus, 'render_from_template', ['format_topcoll/section', $sectioncontext]);
        $this->assertEquals($thevalue, $theclass);

        $onsectionpage = true;
        self::set_property($this->outputus, 'formatresponsive', true);
        $theclass = self::call_method(
            $this->outputus,
            'topcoll_section',
            [$section1, $this->course, $onsectionpage]
        );

        $sectioncontext['columnclass'] = '';
        $sectioncontext['columnwidth'] = '';
        $sectioncontext['leftcontent'] = self::call_method(
            $this->outputus,
            'section_left_content',
            [$section1, $this->course, $onsectionpage]
        );
        $sectioncontext['rightcontent'] = self::call_method(
            $this->outputus,
            'section_right_content',
            [$section1, $this->course, $onsectionpage]
        );
        $sectioncontext['sectionpage'] = $onsectionpage;
        $sectioncontext['heading'] = '<h3 data-for="section_title" data-id="' . $section1->id . '" data-number="1" id="sectionid-' .
            $section1->id . '-title" class="accesshide">Section 1</h3>';

        $thevalue = self::call_method($this->outputus, 'render_from_template', ['format_topcoll/section', $sectioncontext]);
        $this->assertEquals($thevalue, $theclass);
    }

    public function test_section_hidden(): void {
        $this->init();
        $section = $this->courseformat->get_section(1);

        $theclass = self::call_method(
            $this->outputus,
            'section_hidden',
            [$section, null]
        );

        $sectionhiddencontext = [
            'columnclass' => 'col-sm-12',
            'heading' => '<h3 data-for="section_title" data-id="' . $section->id . '" data-number="1" id="sectionid-' .
                $section->id . '-title" class="section-title">Section 1</h3>',
            'leftcontent' => '<div class="left side"><span class="cps_centre">1</span></div>',
            'nomtore' => true,
            'rightcontent' => '<div class="right side"><span class="cps_centre">Topic<br />1</span></div>',
            'rtl' => false,
            'sectionid' => $section->id,
            'sectionno' => '1',
            'toggleiconsize' => 'tc-medium',
        ];
        $sectionhiddencontext['sectionavailability'] = self::call_method($this->outputus, 'section_availability', [$section]);

        $thevalue = self::call_method($this->outputus, 'render_from_template', ['format_topcoll/sectionhidden',
            $sectionhiddencontext, ]);
        $this->assertEquals($thevalue, $theclass);
    }

    public function test_stealth_section(): void {
        $this->init();
        $section = $this->courseformat->get_section(1);

        $theclass = self::call_method(
            $this->outputus,
            'stealth_section',
            [$section, $this->course]
        );

        $stealthsectioncontext = [
            'columnclass' => 'col-sm-12',
            'cscml' => self::call_method($this->outputus, 'course_section_cmlist', [$section]),
            'heading' => '<h3 data-for="section_title" data-id="' . $section->id . '" data-number="1" id="sectionid-' .
                $section->id . '-title" class="section-title">' . get_string('orphanedactivitiesinsectionno', '',
                $section->section) . '</h3>',
            'rightcontent' => self::call_method($this->outputus, 'section_right_content', [$section, $this->course, false]),
            'rtl' => false,
            'sectionid' => $section->id,
            'sectionno' => $section->section,
            'sectionvisibility' => true,
        ];

        $thevalue = self::call_method($this->outputus, 'render_from_template', ['format_topcoll/stealthsection',
            $stealthsectioncontext, ]);
        $this->assertEquals($thevalue, $theclass);
    }

    /* Jump menu breaks this, not sure how to fix....
    public function test_single_section_page() {
        $this->init();
        self::call_method($this->outputus, 'single_section_page', array($this->course, 1));

        $modinfo = get_fast_modinfo($this->course);
        $course = $this->courseformat->get_course();
        $maincoursepage = get_string('maincoursepage', 'format_topcoll');
        $displaysection = 1;
        $sectionzero = $modinfo->get_section_info(0);
        $thissection = $modinfo->get_section_info($displaysection);

        $singlesectioncontext = array(
            'activityclipboard' => self::call_method($this->outputus, 'course_activity_clipboard', array($course, $displaysection)),
            'maincoursepageicon' => $this->ouroutput->pix_icon('t/less', $maincoursepage),
            'maincoursepagestr' =>  $maincoursepage,
            'maincoursepageurl' => new url('/course/view.php', array('id' => $course->id)),
            'sectionnavselection' => self::call_method(
                $this->outputus,
                'section_nav_selection',
                array($course, null, $displaysection)
            ),
            'sectiontitle' => '<h3 class="sectionname">Section 1</h3>',
            'sectionzero' => self::call_method(
                $this->outputus, 'topcoll_section',
                array($sectionzero, $course, true, $displaysection, array('sr' => $displaysection))
            ),
            'thissection' => self::call_method(
                $this->outputus,
                'topcoll_section',
                array($thissection, $course, true, $displaysection, array('sr' => $displaysection))
            )
        );
        $sectionnavlinks = self::call_method(
            $this->outputus,
            'get_nav_links',
            array($this->course,
            $modinfo->get_section_info_all(),
            $displaysection)
        );
        $singlesectioncontext['sectionnavlinksprevious'] = $sectionnavlinks['previous'];
        $singlesectioncontext['sectionnavlinksnext'] = $sectionnavlinks['next'];

        $theoutput = self::call_method(
            $this->outputus,
            'render_from_template',
            array('format_topcoll/singlesection', $singlesectioncontext)
        );

        $this->expectOutputString($theoutput);
    }
    */

    public function test_multiple_section_page_horizontal(): void {
        global $CFG;

        $this->init();
        set_user_preference(togglelib::TOPCOLL_TOGGLE.'_' . $this->course->id, null);
        set_config('defaultuserpreference', 0, 'format_topcoll');
        set_config('defaulttogglepersistence', 1, 'format_topcoll');
        $section0 = $this->courseformat->get_section(0);
        $section1 = $this->courseformat->get_section(1);
        $toggle = false;

        $thevalue = self::call_method($this->outputus, 'multiple_section_page', []);

        $theoutput = file_get_contents($CFG->dirroot . '/course/format/topcoll/tests/phpu_data/test_multiple_section_page_css.txt');
        $theoutput .= '<ul class="ctopics">';
        $theoutput .= self::call_method($this->outputus, 'topcoll_section', [$section0, $this->course, false, 0, $toggle]);
        $theoutput .= '</ul><ul class="ctopics ctoggled topics row">';
        $theoutput .= self::call_method($this->outputus, 'topcoll_section', [$section1, $this->course, false, null, $toggle]);
        $theoutput .= '</ul>';
        $theoutput .= '<span id="tcdata" class="d-none" data-onetopic="false" data-onetopictoggle="false"'.
            ' data-defaulttogglepersistence="true"></span>';

        $this->assertEquals($theoutput, $thevalue);
    }

    public function test_multiple_section_page_vertical(): void {
        global $CFG;

        $this->init(1, 1);
        set_user_preference(togglelib::TOPCOLL_TOGGLE.'_' . $this->course->id, 'Z');
        set_config('defaultuserpreference', 0, 'format_topcoll');
        set_config('defaulttogglepersistence', 1, 'format_topcoll');

        $section0 = $this->courseformat->get_section(0);
        $section1 = $this->courseformat->get_section(1);
        $toggle = true;

        $thevalue = self::call_method($this->outputus, 'multiple_section_page', []);

        $theoutput = file_get_contents($CFG->dirroot . '/course/format/topcoll/tests/phpu_data/test_multiple_section_page_css.txt');
        $theoutput .= '<ul class="ctopics">';
        $theoutput .= self::call_method($this->outputus, 'topcoll_section', [$section0, $this->course, false, 0, $toggle]);
        $theoutput .= '</ul><div class="row">';
        $theoutput .= '<ul class="ctopics ctoggled topics col-sm-12">';
        $theoutput .= self::call_method($this->outputus, 'topcoll_section', [$section1, $this->course, false, null, $toggle]);
        $theoutput .= '</ul></div>';
        $theoutput .= '<span id="tcdata" class="d-none" data-onetopic="false" data-onetopictoggle="false"'.
            ' data-defaulttogglepersistence="true"></span>';

        $this->assertEquals($theoutput, $thevalue);
    }

    public function test_multiple_section_page_no_sections(): void {
        global $CFG;

        $this->init(0);
        set_user_preference(togglelib::TOPCOLL_TOGGLE.'_' . $this->course->id, null);
        set_config('defaultuserpreference', 0, 'format_topcoll');
        set_config('defaulttogglepersistence', 1, 'format_topcoll');
        $section0 = $this->courseformat->get_section(0);

        $thevalue = self::call_method($this->outputus, 'multiple_section_page', []);

        $theoutput = file_get_contents($CFG->dirroot . '/course/format/topcoll/tests/phpu_data/test_multiple_section_page_css.txt');
        $theoutput .= '<ul class="ctopics">';
        $theoutput .= self::call_method($this->outputus, 'topcoll_section', [$section0, $this->course, false, 0]);
        $theoutput .= '</ul>';
        $theoutput .= '<span id="tcdata" class="d-none" data-onetopic="false" data-onetopictoggle="false"'.
            ' data-defaulttogglepersistence="true"></span>';

        $this->assertEquals($theoutput, $thevalue);
    }

    public function test_toggle_all(): void {
        global $CFG;

        $this->init();
        $theclass = self::call_method($this->outputus, 'toggle_all', [[1]]);

        $toggleallcontext = [
            'ariacontrols' => 'toggledsection-1',
            'toggleiconset' => 'arrow',
            'rtl' => false,
            'sctcloseall' => 'Close all topics',
            'sctopenall' => 'Open all topics',
            'spacer' => '<img class="icon spacer" width="1" height="1" alt="" aria-hidden="true" src="' . $CFG->wwwroot .
                '/theme/image.php/boost/core/1/spacer" />',
            'toggleallhover' => true,
            'tctoggleiconsize' => 'tc-medium',
            'toggleiconposition' => 'start',

        ];
        $thevalue = self::call_method($this->outputus, 'render_from_template', ['format_topcoll/toggleall',
            $toggleallcontext, ]);

        $this->assertEquals($thevalue, $theclass);
    }

    public function test_display_instructions(): void {
        global $CFG;

        $this->init();
        $theclass = self::call_method($this->outputus, 'display_instructions', []);

        $displayinstructionscontext = [
            'rtl' => false,
            'spacer' => '<img class="icon spacer" width="1" height="1" alt="" aria-hidden="true" src="' . $CFG->wwwroot .
                '/theme/image.php/boost/core/1/spacer" />',
        ];
        $thevalue = self::call_method($this->outputus, 'render_from_template', ['format_topcoll/displayinstructions',
            $displayinstructionscontext, ]);

        $this->assertEquals($thevalue, $theclass);
    }
}
