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
 * code change. Full installation instructions, code adaptions and credits are included in the 'Readme.md' file.
 *
 * @package    format_topcoll
 * @copyright  &copy; 2012-onwards G J Barnard in respect to modifications of standard topics format.
 * @author     G J Barnard - {@link https://moodle.org/user/profile.php?id=442195}
 * @link       https://docs.moodle.org/en/Collapsed_Topics_course_format
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/course/format/lib.php'); // For format_base.

/**
 * Format class.
 */
class format_topcoll extends core_courseformat\base {
    /** @var array $settings */
    private $settings;

    /**
     * Creates a new instance of class
     *
     * Please use {@link course_get_format($courseorid)} to get an instance of the format class
     *
     * @param string $format
     * @param int $courseid
     * @return format_topcoll
     */
    protected function __construct($format, $courseid) {
        if ($courseid === 0) {
            global $COURSE;
            $courseid = $COURSE->id;  // Save lots of global $COURSE as we will never be the site course.
        }
        parent::__construct($format, $courseid);
    }

    /**
     * Returns the format's settings and gets them if they do not exist.
     * @return array The settings as an array.
     */
    public function get_settings() {
        if (empty($this->settings) == true) {
            $this->settings = $this->get_format_options();
            foreach ($this->settings as $settingname => $settingvalue) {
                if (isset($settingvalue)) {
                    $settingvtype = gettype($settingvalue);
                    if (
                        (($settingvtype == 'string') && ($settingvalue === '-')) ||
                        (($settingvtype == 'integer') && ($settingvalue === 0))
                    ) {
                        // Default value indicator is a hyphen or a number equal to 0.
                        $this->settings[$settingname] = get_config('format_topcoll', 'default' . $settingname);
                    }
                }
            }
        }
        return $this->settings;
    }

    /**
     * Indicates this format uses sections.
     *
     * @return bool Returns true
     */
    public function uses_sections() {
        return true;
    }

    /**
     * Indicates this format uses course index.
     *
     * @return bool Returns true
     */
    public function uses_course_index() {
        return true;
    }

    /**
     * Indicates this format uses indentation.
     *
     * @return bool Returns true
     */
    public function uses_indentation(): bool {
        return true;
    }

    /**
     * Indicates whether the course format supports the creation of a news forum.
     *
     * @return bool
     */
    public function supports_news() {
        return true;
    }


    /**
     * Returns the information about the ajax support. Topcoll uses ajax.
     *
     * @return stdClass
     */
    public function supports_ajax() {
        $ajaxsupport = new stdClass();
        $ajaxsupport->capable = true;
        return $ajaxsupport;
    }

    /**
     * This format is compatible with the React updates.
     */
    public function supports_components() {
        return true;  // I.e. Allows section drag and drop to work!  Off until I can work out how to make it work!
    }

    /**
     * Get the number of sections not counting delegated ones.
     *
     * @return int The last section number, or -1 if sections are entirely missing
     */
    public function get_last_section_number_without_delegated() {
        $lastsectionno = parent::get_last_section_number();

        if (!empty($lastsectionno)) {
            $lastsectionno -= $this->get_number_of_delegated_sections();
        }

        return $lastsectionno;
    }

    /**
     * Method used to get the maximum number of sections for this course format without delegated.
     * @return int Maximum number of sections.
     */
    public function get_max_sections_without_delegated() {
        $maxsections = $this->get_max_sections();

        if (!empty($maxsections)) {
            $maxsections -= $this->get_number_of_delegated_sections();
        }

        return $maxsections;
    }

    /**
     * Get the number of delegated sections.
     *
     * @return int Number of delegated sections.
     */
    protected function get_number_of_delegated_sections() {
        global $DB;
        $delegatedcount = 0;

        $subsectionsenabled = $DB->get_field('modules', 'visible', ['name' => 'subsection']);
        if ($subsectionsenabled) {
            // Add in our delegated sections.  The 'subsection' table is unreliable in this regard.
            $modinfo = $this->get_modinfo();
            $sectioninfos = $modinfo->get_section_info_all();
            $delegatedcount = 0;

            foreach ($sectioninfos as $sectioninfo) {
                if (!empty($sectioninfo->component)) {
                    // Delegated section.
                    $delegatedcount++;
                }
            }
        }

        return $delegatedcount;
    }

    /**
     * Gets the name for the provided section.
     *
     * @param int|stdClass $section Section object from database or just field section.section
     * @return string The section name.
     */
    public function get_section_name($section) {
        // If delegated then return the standard name.
        if (!empty($section->component)) {
            return $this->get_delegated_section_name($section);
        }

        $course = $this->get_course();
        // Don't add additional text as called in creating the navigation.
        return $this->get_topcoll_section_name($course, $section, false);
    }

    /**
     * Returns the default section name for the format.
     *
     * @param stdClass $section Section object from database or just field course_sections section
     * @return string The default value for the section name.
     */
    public function get_default_section_name($section) {
        /* Follow the same logic so that this method is supported.  The MDL-51610 enchancement refactored things,
           but that is not appropriate for us. */
        return $this->get_section_name($section);
    }

    /**
     * Gets the name for the provided course, section and state if need to add addional text.
     *
     * @param stdClass $course The course entry from DB
     * @param int|stdClass $section Section object from database or just field section.section
     * @param boolean $additional State to add additional text yes = true or no = false.
     * @return string The section name.
     */
    public function get_topcoll_section_name($course, $section, $additional) {
        $thesection = $this->get_section($section);
        if (is_null($thesection)) {
            $thesection = new stdClass();
            $thesection->name = '';
            if (is_object($section)) {
                $thesection->section = $section->section;
            } else {
                $thesection->section = $section;
            }
        }
        $o = '';
        $tcsettings = $this->get_settings();
        $tcsectionsettings = $this->get_format_options($thesection->section);
        // Use supplied course as could be a different course to us due to a navigation block call.
        $context = context_course::instance($course->id);

        // We can't add a node without any text.
        if ((string) $thesection->name !== '') {
            $o .= format_string($thesection->name, true, ['context' => $context]);
            if (
                ($thesection->section != 0) && (($tcsettings['layoutstructure'] == 2) ||
                ($tcsettings['layoutstructure'] == 3) || ($tcsettings['layoutstructure'] == 5))
            ) {
                $o .= ' ';
                if (empty($tcsectionsettings['donotshowdate'])) {
                    if ($additional == true) { // Break 'br' tags break backups!
                        $o .= html_writer::empty_tag('br');
                    }
                    $o .= $this->get_section_dates($section, $course, $tcsettings);
                }
            }
        } else if ($thesection->section == 0) {
            $o = get_string('section0name', 'format_topcoll');
        } else {
            if (($tcsettings['layoutstructure'] == 1) || ($tcsettings['layoutstructure'] == 4)) {
                $o = get_string('sectionname', 'format_topcoll') . ' ' . $thesection->section;
            } else {
                $o .= $this->get_section_dates($section, $course, $tcsettings);
            }
        }

        /*
         * Now done here so that the drag and drop titles will be the correct strings as swapped in format.js.
         * But only if we are using toggles which will be if all sections are on one page or we are editing the main page
         * when in one section per page which is coded in 'renderer.php/print_multiple_section_page()' when it calls
         * 'section_header()' as that gets called from 'format.php' when there is no entry for '$displaysetting' - confused?
         * I was, took ages to figure.
         */
        if (($additional == true) && ($thesection->section != 0)) {
            switch ($tcsettings['layoutelement']) {
                case 1:
                case 2:
                case 3:
                case 4:
                    // The word 'Toggle'.
                    $o .= '<div class="cttoggle"> - ' . get_string('topcolltoggle', 'format_topcoll') . '</div>';
                    break;
            }
        }

        return $o;
    }

    /**
     * Returns the display name of the given delegated section.
     *
     * @param int|stdClass $section Section object from database or just field section.section.
     * @return string Display name that the course format prefers, e.g. "Section 2".
     */
    protected function get_delegated_section_name($section) {
        $section = $this->get_section($section);
        if ((string)$section->name !== '') {
            return format_string($section->name, true,
                ['context' => context_course::instance($this->courseid)]);
        } else {
            if ($section->sectionnum == 0) {
                return get_string('section0name', 'format_topcoll');
            }
            return get_string('newsectionname', 'format_topcoll', $section->sectionnum);
        }
    }

    /**
     * Returns if an specific section is visible to the current user.
     *
     * Formats can overrride this method to implement any special section logic.
     *
     * @param section_info $section the section modinfo
     * @return bool;
     */
    public function is_section_visible(section_info $section): bool {
        if (!$section->uservisible) {
            return false;
        }
        if (($section->section > $this->get_last_section_number_without_delegated()) && (empty($section->component))) {
            // Stealth section that is not a delegated one.
            global $PAGE;
            $context = context_course::instance($this->course->id);
            if ($PAGE->user_is_editing() && has_capability('moodle/course:update', $context)) {
                $modinfo = get_fast_modinfo($this->course);
                // If the stealth section has modules then is visible.
                return (!empty($modinfo->sections[$section->section]));
            }
            // Don't show.
            return false;
        }
        $shown = parent::is_section_visible($section);
        if (($shown) && ($section->sectionnum == 0)) {
            // Show section zero if summary has content, otherwise check modules.
            if (empty(strip_tags($section->summary))) {
                // Don't show section zero if no modules or all modules unavailable to user.
                $showmovehere = ismoving($this->course->id);
                if (!$showmovehere) {
                    global $PAGE;
                    $context = context_course::instance($this->course->id);
                    if (!($PAGE->user_is_editing() && has_capability('moodle/course:update', $context))) {
                        $modshown = false;
                        $modinfo = get_fast_modinfo($this->course);

                        if (!empty($modinfo->sections[$section->section])) {
                            foreach ($modinfo->sections[$section->section] as $modnumber) {
                                $mod = $modinfo->cms[$modnumber];
                                if ($mod->is_visible_on_course_page()) {
                                    // At least one is.
                                    $modshown = true;
                                    break;
                                }
                            }
                        }
                        $shown = $modshown;
                    }
                }
            }
        }

        return $shown;
    }

    /**
     * get_section_dates.
     */
    public function get_section_dates($section, $course = null, $tcsettings = null) {
        if (empty($tcsettings) && empty($course)) {
            return $this->format_topcoll_get_section_dates($section, $this->get_course());
        }

        if (empty($tcsettings)) {
            $tcsettings = $this->get_settings();
        }

        $dateformat = get_string('strftimedateshort');
        $o = '';
        if ($tcsettings['layoutstructure'] == 5) {
            $day = $this->format_topcoll_get_section_day($section, $course);

            $weekday = userdate($day, $dateformat);
            $o = $weekday;
        } else {
            $dates = $this->format_topcoll_get_section_dates($section, $course);

            // We subtract 24 hours for display purposes.
            $dates->end = ($dates->end - 86400);

            $weekday = userdate($dates->start, $dateformat);
            $endweekday = userdate($dates->end, $dateformat);
            $o = $weekday . ' - ' . $endweekday;
        }
        return $o;
    }

    /**
     * What structure collection type are we using?
     *
     * @return string Structure collection type.
     */
    public function get_structure_collection_type() {
        $tcsettings = $this->get_settings();
        $o = '';

        switch ($tcsettings['layoutstructure']) {
            case 1:
            case 4:
                $o = get_string('layoutstructuretopics', 'format_topcoll');
                break;
            case 2:
            case 3:
                $o = get_string('layoutstructureweeks', 'format_topcoll');
                break;
            case 5:
                $o = get_string('layoutstructuredays', 'format_topcoll');
                break;
        }

        return $o;
    }

    /**
     * Custom action after section has been moved in AJAX mode
     *
     * Used in course/rest.php
     *
     * @return array This will be passed in ajax respose
     */
    public function ajax_section_move() {
        global $PAGE;

        $titles = [];
        $current = -1;  // MDL-33546.
        $weekformat = false;
        $tcsettings = $this->get_settings();
        if (
            ($tcsettings['layoutstructure'] == 2) || ($tcsettings['layoutstructure'] == 3) ||
            ($tcsettings['layoutstructure'] == 5)
        ) {
            $weekformat = true;
        }
        $course = $this->get_course();
        $modinfo = get_fast_modinfo($course);
        $formatrenderer = $PAGE->get_renderer('format_topcoll');
        if ($formatrenderer && $sections = $modinfo->get_section_info_all()) {
            foreach ($sections as $sectionnumber => $section) {
                $titles[$sectionnumber] = $formatrenderer->section_title($section, null); // Course not needed.
                if (($weekformat == true) && ($this->is_section_current($section))) {
                    $current = $sectionnumber;  // Only set if a week based course to keep the current week in the same place.
                }
            }
        }
        return ['sectiontitles' => $titles, 'current' => $current, 'action' => 'move'];
    }

    /**
     * The URL to use for the specified course (with section)
     *
     * Please note that course view page /course/view.php?id=COURSEID is hardcoded in many
     * places in core and contributed modules. If course format wants to change the location
     * of the view script, it is not enough to change just this function. Do not forget
     * to add proper redirection.
     *
     * @param int|stdClass $section Section object from database or just field course_sections.section
     *     if null the course view page is returned
     * @param array $options options for view URL. At the moment core uses:
     *     'navigation' (bool) if true and section not empty, the function returns section page; otherwise, it returns course page.
     *     'sr' (int) used by course formats to specify to which section to return
     *     'expanded' (bool) if true the section will be shown expanded, true by default
     * @return null|moodle_url
     */
    public function get_view_url($section, $options = []) {
        $course = $this->get_course();
        $url = new moodle_url('/course/view.php', ['id' => $course->id]);

        $sr = false;
        if (array_key_exists('sr', $options)) {
            $sectionno = $options['sr'];
            $sr = true;
        } else if (is_object($section)) {
            $sectionno = $section->section;
        } else {
            $sectionno = $section;
        }
        if ($sectionno !== null) {
            if (!empty($options['navigation'])) {
                // Unlike core, navigate to section on course page.
                $url->set_anchor('section-'.$sectionno);
            } else if (!empty($options['state'])) {
                // Navigate to section on course page from course index.
                // Yes I know this is the same but at this stage I want to be sure.
                $url->set_anchor('section-'.$sectionno);
            } else if ((!empty($options['singlenavigation'])) || ($sr)) {
                $url->param('section', $sectionno);
            } else {
                // I know, odd logic but more of an explaination!
                $url->set_anchor('section-'.$sectionno);
            }
        }

        return $url;
    }

    /**
     * Returns the list of blocks to be automatically added for the newly created course
     *
     * First we check defaultdisplayblocks to see which of the four default blocks should be displayed,
     * then build an array of strings that will hold the list of blocks to be displayed.
     *
     * @return array of default blocks, must contain two keys BLOCK_POS_LEFT and BLOCK_POS_RIGHT
     *     each of values is an array of block names (for pre and post side columns respectively).
     *     Confused?  See the definitions in /lib/blocklib.php.
     */
    public function get_default_blocks() {

        /* Assign the location side for the blocks. defaultdisplayblocksloc: 1=pre, 2=post
           Then put the string list of blocks on the side location. */
        $blocklist = explode(',', get_config('format_topcoll', 'defaultdisplayblocks'));
        if (empty($blocklist[0])) {
            return [];
        }
        if (get_config('format_topcoll', 'defaultdisplayblocksloc') == 2) {
            $bpr = $blocklist;
            $bpl = [];
        } else {
            $bpr = [];
            $bpl = $blocklist;
        }

        // Return our block list on the correct side.
        return [
            BLOCK_POS_RIGHT => $bpr,
            BLOCK_POS_LEFT => $bpl,
        ];
    }

    /**
     * Definitions of the additional options that this course format uses for section
     *
     * See course_format::course_format_options() for return array definition.
     *
     * Additionally section format options may have property 'cache' set to true
     * if this option needs to be cached in get_fast_modinfo(). The 'cache' property
     * is recommended to be set only for fields used in course_format::get_section_name(),
     * course_format::extend_course_navigation() and course_format::get_view_url()
     *
     * For better performance cached options are recommended to have 'cachedefault' property
     * Unlike 'default', 'cachedefault' should be static and not access get_config().
     *
     * Regardless of value of 'cache' all options are accessed in the code as
     * $sectioninfo->OPTIONNAME
     * where $sectioninfo is instance of section_info, returned by
     * get_fast_modinfo($course)->get_section_info($sectionnum)
     * or get_fast_modinfo($course)->get_section_info_all()
     *
     * All format options for particular section are returned by calling:
     * $this->get_format_options($section);
     *
     * @param bool $foreditform
     * @return array
     */
    public function section_format_options($foreditform = false) {
        static $sectionformatoptions = false;

        if ($sectionformatoptions === false) {
            $sectionformatoptions = [
                'donotshowdate' => [
                    'default' => 0,
                    'type' => PARAM_INT,
                ],
            ];
        }
        if ($foreditform && !isset($sectionformatoptions['donotshowdate']['label'])) {
            $sectionformatoptionsedit = [
                'donotshowdate' => [
                    'label' => new lang_string('donotshowdate', 'format_topcoll'),
                    'help' => 'donotshowdate',
                    'help_component' => 'format_topcoll',
                    'element_type' => 'checkbox',
                ],
            ];
            $sectionformatoptions = array_merge_recursive($sectionformatoptions, $sectionformatoptionsedit);
        }

        $tcsettings = $this->get_settings();
        if (
            ($tcsettings['layoutstructure'] == 2) || ($tcsettings['layoutstructure'] == 3) ||
            ($tcsettings['layoutstructure'] == 5)
        ) {
            // Weekly layout.
            return $sectionformatoptions;
        } else {
            return [];
        }
    }

    /**
     * Definitions of the additional options that this course format uses for course
     *
     * Collapsed Topics format uses the following options (until extras are migrated):
     * - numsections
     * - hiddensections
     *
     * @param bool $foreditform
     * @return array of options
     */
    public function course_format_options($foreditform = false) {
        static $courseformatoptions = false;
        $courseconfig = null;
        $enabledplugins = [];

        $courseid = $this->get_courseid();
        if ($courseformatoptions === false) {
            $courseconfig = get_config('moodlecourse');

            if (\format_topcoll\activity::activitymetaenabled()) {
                $engagementactivities = ['assign', 'quiz', 'choice', 'feedback', 'forum', 'lesson', 'data'];
                foreach ($engagementactivities as $plugintype) {
                    if (get_config('format_topcoll', 'coursesectionactivityfurtherinformation' . $plugintype) == 2) {
                        switch ($plugintype) {
                            case 'assign':
                                array_push($enabledplugins, 'assignments');
                                break;
                            case 'quiz':
                                array_push($enabledplugins, 'quizzes');
                                break;
                            case 'data':
                                array_push($enabledplugins, 'databases');
                                break;
                            case 'choice' || 'feedback' || 'forum' || 'lesson':
                                array_push($enabledplugins, $plugintype . 's');
                                break;
                            default:
                                coding_exception(
                                    'Try to process invalid plugin',
                                    'Check the plugintypes that are supported by format_topcoll'
                                );
                        }
                    }
                }
            }

            $courseformatoptions = [
                'hiddensections' => [
                    'default' => $courseconfig->hiddensections ?? 0,
                    'type' => PARAM_INT,
                ],
                'displayinstructions' => [
                    'default' => 0,
                    'type' => PARAM_INT,
                ],
                'layoutelement' => [
                    'default' => 0,
                    'type' => PARAM_INT,
                ],
                'layoutstructure' => [
                    'default' => 0,
                    'type' => PARAM_INT,
                ],
                'layoutcolumnorientation' => [
                    'default' => 0,
                    'type' => PARAM_INT,
                ],
                'layoutcolumns' => [
                    'default' => 0,
                    'type' => PARAM_INT,
                ],
                'flexiblemodules' => [
                    'default' => 0,
                    'type' => PARAM_INT,
                ],
                'toggleallenabled' => [
                    'default' => 0,
                    'type' => PARAM_INT,
                ],
                'viewsinglesectionenabled' => [
                    'default' => 0,
                    'type' => PARAM_INT,
                ],
                'togglealignment' => [
                    'default' => 0,
                    'type' => PARAM_INT,
                ],
                'toggleiconposition' => [
                    'default' => 0,
                    'type' => PARAM_INT,
                ],
                'toggleiconset' => [
                    'default' => '-',
                    'type' => PARAM_ALPHAEXT,
                ],
                'toggleiconfontclosed' => [
                    'default' => '-',
                    'type' => PARAM_TEXT,
                ],
                'toggleiconfontopen' => [
                    'default' => '-',
                    'type' => PARAM_TEXT,
                ],
                'onesection' => [
                    'default' => 0,
                    'type' => PARAM_INT,
                ],
                'toggleallhover' => [
                    'default' => 0,
                    'type' => PARAM_INT,
                ],
                'toggleforegroundcolour' => [
                    'default' => '-',
                    'type' => PARAM_ALPHANUMEXT,
                ],
                'toggleforegroundopacity' => [
                    'default' => '-',
                    'type' => PARAM_RAW,
                ],
                'toggleforegroundhovercolour' => [
                    'default' => '-',
                    'type' => PARAM_ALPHANUMEXT,
                ],
                'toggleforegroundhoveropacity' => [
                    'default' => '-',
                    'type' => PARAM_RAW,
                ],
                'togglebackgroundcolour' => [
                    'default' => '-',
                    'type' => PARAM_ALPHANUMEXT,
                ],
                'togglebackgroundopacity' => [
                    'default' => '-',
                    'type' => PARAM_RAW,
                ],
                'togglebackgroundhovercolour' => [
                    'default' => '-',
                    'type' => PARAM_ALPHANUMEXT,
                ],
                'togglebackgroundhoveropacity' => [
                    'default' => '-',
                    'type' => PARAM_RAW,
                ],
                'showsectionsummary' => [
                    'default' => 0,
                    'type' => PARAM_INT,
                ],
            ];

            /* If at least one plugin is set to 'yes' then show config with default of 'yes',
               otherwise will use the value prevously stored. */
            if (!empty($enabledplugins)) {
                $courseformatoptions['showadditionalmoddata'] = [
                    'default' => 0,
                    'type' => PARAM_INT,
                ];
            }
        }
        if ($foreditform && !isset($courseformatoptions['displayinstructions']['label'])) {
            /* Note: Because 'admin_setting_configcolourpicker' in 'settings.php' needs to use a prefixing '#'
                     this needs to be stripped off here if it's there for the format's specific colour picker. */
            $defaulttgfgcolour = get_config('format_topcoll', 'defaulttoggleforegroundcolour');
            if ($defaulttgfgcolour[0] == '#') {
                $defaulttgfgcolour = substr($defaulttgfgcolour, 1);
            }
            $defaulttgfghvrcolour = get_config('format_topcoll', 'defaulttoggleforegroundhovercolour');
            if ($defaulttgfghvrcolour[0] == '#') {
                $defaulttgfghvrcolour = substr($defaulttgfghvrcolour, 1);
            }
            $defaulttgbgcolour = get_config('format_topcoll', 'defaulttogglebackgroundcolour');
            if ($defaulttgbgcolour[0] == '#') {
                $defaulttgbgcolour = substr($defaulttgbgcolour, 1);
            }
            $defaulttgbghvrcolour = get_config('format_topcoll', 'defaulttogglebackgroundhovercolour');
            if ($defaulttgbghvrcolour[0] == '#') {
                $defaulttgbghvrcolour = substr($defaulttgbghvrcolour, 1);
            }

            $context = $this->get_context();

            $displayinstructionsvalues = $this->generate_default_entry(
                'displayinstructions',
                0,
                [
                    1 => new lang_string('no'),
                    2 => new lang_string('yes'),
                ]
            );
            $courseformatoptionsedit = [
                'hiddensections' => [
                    'label' => new lang_string('hiddensections'),
                    'help' => 'hiddensections',
                    'help_component' => 'moodle',
                    'element_type' => 'select',
                    'element_attributes' => [
                        [
                            0 => new lang_string('hiddensectionscollapsed'),
                            1 => new lang_string('hiddensectionsinvisible'),
                        ],
                    ],
                ],
                'displayinstructions' => [
                    'label' => new lang_string('displayinstructions', 'format_topcoll'),
                    'help' => 'displayinstructions',
                    'help_component' => 'format_topcoll',
                    'element_type' => 'select',
                    'element_attributes' => [$displayinstructionsvalues],
                ],
            ];
            if (has_capability('format/topcoll:changelayout', $context)) {
                $layoutelementvalues = $this->generate_default_entry(
                    'layoutelement',
                    0,
                    // In insertion order and not numeric for sorting purposes.
                     [
                        // Toggle word, toggle section x and section number.
                        1 => new lang_string('setlayout_all', 'format_topcoll'),
                        // Toggle word and toggle section x.
                        3 => new lang_string('setlayout_toggle_word_section_x', 'format_topcoll'),
                        // Toggle word and section number.
                        2 => new lang_string('setlayout_toggle_word_section_number', 'format_topcoll'),
                        // Toggle section x and section number.
                        5 => new lang_string('setlayout_toggle_section_x_section_number', 'format_topcoll'),
                        // Toggle word.
                        4 => new lang_string('setlayout_toggle_word', 'format_topcoll'),
                        // Toggle section x.
                        8 => new lang_string('setlayout_toggle_section_x', 'format_topcoll'),
                        // Section number.
                        6 => new lang_string('setlayout_section_number', 'format_topcoll'),
                        // No additions.
                        7 => new lang_string('setlayout_no_additions', 'format_topcoll'),
                    ]
                );
                $courseformatoptionsedit['layoutelement'] = [
                    'label' => new lang_string('setlayoutelements', 'format_topcoll'),
                    'help' => 'setlayoutelements',
                    'help_component' => 'format_topcoll',
                    'element_type' => 'select',
                    'element_attributes' => [$layoutelementvalues],
                ];
                $layoutstructurevalues = $this->generate_default_entry(
                    'layoutstructure',
                    0,
                    [
                        // Topic.
                        1 => new lang_string('setlayoutstructuretopic', 'format_topcoll'),
                        // Week.
                        2 => new lang_string('setlayoutstructureweek', 'format_topcoll'),
                        // Current Week First.
                        3 => new lang_string('setlayoutstructurelatweekfirst', 'format_topcoll'),
                        // Current Topic First.
                        4 => new lang_string('setlayoutstructurecurrenttopicfirst', 'format_topcoll'),
                        // Day.
                        5 => new lang_string('setlayoutstructureday', 'format_topcoll'),
                    ]
                );
                $courseformatoptionsedit['layoutstructure'] = [
                    'label' => new lang_string('setlayoutstructure', 'format_topcoll'),
                    'help' => 'setlayoutstructure',
                    'help_component' => 'format_topcoll',
                    'element_type' => 'select',
                    'element_attributes' => [$layoutstructurevalues],
                ];
                $layoutcolumnorientationvalues = $this->generate_default_entry(
                    'layoutcolumnorientation',
                    0,
                    [
                        3 => new lang_string('columndynamic', 'format_topcoll'),
                        2 => new lang_string('columnhorizontal', 'format_topcoll'),
                        1 => new lang_string('columnvertical', 'format_topcoll'),
                    ]
                );
                $courseformatoptionsedit['layoutcolumnorientation'] = [
                    'label' => new lang_string('setlayoutcolumnorientation', 'format_topcoll'),
                    'help' => 'setlayoutcolumnorientation',
                    'help_component' => 'format_topcoll',
                    'element_type' => 'select',
                    'element_attributes' => [$layoutcolumnorientationvalues],
                ];
                $layoutcolumnsvalues = $this->generate_default_entry(
                    'layoutcolumns',
                    0,
                    [
                        1 => new lang_string('one', 'format_topcoll'),
                        2 => new lang_string('two', 'format_topcoll'),
                        3 => new lang_string('three', 'format_topcoll'),
                        4 => new lang_string('four', 'format_topcoll'),
                    ]
                );
                $courseformatoptionsedit['layoutcolumns'] = [
                    'label' => new lang_string('setlayoutcolumns', 'format_topcoll'),
                    'help' => 'setlayoutcolumns',
                    'help_component' => 'format_topcoll',
                    'element_type' => 'select',
                    'element_attributes' => [$layoutcolumnsvalues],
                ];
                $flexiblemodulesvalues = $this->generate_default_entry(
                    'flexiblemodules',
                    0,
                    [
                        1 => new lang_string('no'),
                        2 => new lang_string('yes'),
                    ]
                );
                $courseformatoptionsedit['flexiblemodules'] = [
                    'label' => new lang_string('setflexiblemodules', 'format_topcoll'),
                    'help' => 'setflexiblemodules',
                    'help_component' => 'format_topcoll',
                    'element_type' => 'select',
                    'element_attributes' => [$flexiblemodulesvalues],
                ];
                $toggleallenabledvalues = $this->generate_default_entry(
                    'toggleallenabled',
                    0,
                    [
                        1 => new lang_string('no'),
                        2 => new lang_string('yes'),
                    ]
                );
                $courseformatoptionsedit['toggleallenabled'] = [
                    'label' => new lang_string('settoggleallenabled', 'format_topcoll'),
                    'help' => 'settoggleallenabled',
                    'help_component' => 'format_topcoll',
                    'element_type' => 'select',
                    'element_attributes' => [$toggleallenabledvalues],
                ];
                $viewsinglesectionenabledvalues = $this->generate_default_entry(
                    'viewsinglesectionenabled',
                    0,
                    [
                        1 => new lang_string('no'),
                        2 => new lang_string('yes'),
                    ]
                );
                $courseformatoptionsedit['viewsinglesectionenabled'] = [
                    'label' => new lang_string('setviewsinglesectionenabled', 'format_topcoll'),
                    'help' => 'setviewsinglesectionenabled',
                    'help_component' => 'format_topcoll',
                    'element_type' => 'select',
                    'element_attributes' => [$viewsinglesectionenabledvalues],
                ];
                $toggleiconpositionvalues = $this->generate_default_entry(
                    'toggleiconposition',
                    0,
                    [
                        1 => new lang_string('start', 'format_topcoll'),
                        2 => new lang_string('end', 'format_topcoll'),
                    ]
                );
                $courseformatoptionsedit['toggleiconposition'] = [
                    'label' => new lang_string('settoggleiconposition', 'format_topcoll'),
                    'help' => 'settoggleiconposition',
                    'help_component' => 'format_topcoll',
                    'element_type' => 'select',
                    'element_attributes' => [$toggleiconpositionvalues],
                ];
                $onesectionvalues = $this->generate_default_entry(
                    'onesection',
                    0,
                    [
                        1 => new lang_string('no'),
                        2 => new lang_string('yes'),
                    ]
                );
                $courseformatoptionsedit['onesection'] = [
                    'label' => new lang_string('onesection', 'format_topcoll'),
                    'help' => 'onesection',
                    'help_component' => 'format_topcoll',
                    'element_type' => 'select',
                    'element_attributes' => [$onesectionvalues],
                ];
                $showsectionsummaryvalues = $this->generate_default_entry(
                    'showsectionsummary',
                    0,
                    [
                        1 => new lang_string('no'),
                        2 => new lang_string('yes'),
                    ]
                );
                $courseformatoptionsedit['showsectionsummary'] = [
                    'label' => new lang_string('setshowsectionsummary', 'format_topcoll'),
                    'help' => 'setshowsectionsummary',
                    'help_component' => 'format_topcoll',
                    'element_type' => 'select',
                    'element_attributes' => [$showsectionsummaryvalues],
                ];
            } else {
                $courseformatoptionsedit['layoutelement'] = [
                    'label' => 0, 'element_type' => 'hidden', ];
                $courseformatoptionsedit['layoutstructure'] = [
                    'label' => 0, 'element_type' => 'hidden', ];
                $courseformatoptionsedit['layoutcolumns'] = [
                    'label' => 0, 'element_type' => 'hidden', ];
                $courseformatoptionsedit['layoutcolumnorientation'] = [
                    'label' => 0, 'element_type' => 'hidden', ];
                $courseformatoptionsedit['flexiblemodules'] = [
                    'label' => 0, 'element_type' => 'hidden', ];
                $courseformatoptionsedit['toggleallenabled'] = [
                    'label' => 0, 'element_type' => 'hidden', ];
                $courseformatoptionsedit['viewsinglesectionenabled'] = [
                    'label' => 0, 'element_type' => 'hidden', ];
                $courseformatoptionsedit['toggleiconposition'] = [
                    'label' => 0, 'element_type' => 'hidden', ];
                $courseformatoptionsedit['onesection'] = [
                    'label' => 0, 'element_type' => 'hidden', ];
                $courseformatoptionsedit['showsectionsummary'] = [
                    'label' => 0, 'element_type' => 'hidden', ];
            }

            if ((!empty($enabledplugins)) && (has_capability('format/topcoll:changeactivitymeta', $context))) {
                $showadditionalmoddatavalues = $this->generate_default_entry(
                    'showadditionalmoddata',
                    0,
                    [
                        1 => new lang_string('no'),
                        2 => new lang_string('yes'),
                    ]
                );
                $stringenabled = implode(', ', $enabledplugins);
                $portion = strrchr($stringenabled, ',');
                $stringenabled = str_replace($portion, (" and" . substr($portion, 1)), $stringenabled);
                $courseformatoptionsedit['showadditionalmoddata'] = [
                    'label' => new lang_string('showadditionalmoddata', 'format_topcoll', $stringenabled),
                    'help' => 'showadditionalmoddata',
                    'help_component' => 'format_topcoll',
                    'element_type' => 'select',
                    'element_attributes' => [$showadditionalmoddatavalues],
                ];

                $tcsettings = $this->get_settings();
                if ((!empty($tcsettings['showadditionalmoddata'])) && ($tcsettings['showadditionalmoddata'] == 2)) {
                    $maxstudentsinfo = \format_topcoll\activity::maxstudentsnotexceeded($courseid, true);
                    if ($maxstudentsinfo['maxstudents'] == 0) {
                        $activityinfostring = get_string(
                            'courseadditionalmoddatastudentsinfounlimited',
                            'format_topcoll',
                            $maxstudentsinfo['nostudents']
                        );
                    } else if (!$maxstudentsinfo['notexceeded']) {
                        $activityinfostring = get_string(
                            'courseadditionalmoddatastudentsinfolimitednoshow',
                            'format_topcoll',
                            ['students' => $maxstudentsinfo['nostudents'], 'maxstudents' => $maxstudentsinfo['maxstudents']]
                        );
                    } else {
                        $activityinfostring = get_string(
                            'courseadditionalmoddatastudentsinfolimitedshow',
                            'format_topcoll',
                            ['students' => $maxstudentsinfo['nostudents'], 'maxstudents' => $maxstudentsinfo['maxstudents']]
                        );
                    }

                    $courseformatoptionsedit['courseadditionalmoddatastudentsinfo'] = [
                        'label' => get_string('courseadditionalmoddatastudentsinfo', 'format_topcoll'),
                        'element_type' => 'static',
                        'element_attributes' => [$activityinfostring],
                    ];
                }
            } else {
                $courseformatoptionsedit['showadditionalmoddata'] = [
                    'label' => 0, 'element_type' => 'hidden', ];
            }

            if (has_capability('format/topcoll:changetogglealignment', $context)) {
                $togglealignmentvalues = $this->generate_default_entry(
                    'togglealignment',
                    0,
                    [
                        1 => new lang_string('start', 'format_topcoll'),
                        2 => new lang_string('center', 'format_topcoll'),
                        3 => new lang_string('end', 'format_topcoll'),
                    ]
                );
                $courseformatoptionsedit['togglealignment'] = [
                    'label' => new lang_string('settogglealignment', 'format_topcoll'),
                    'help' => 'settogglealignment',
                    'help_component' => 'format_topcoll',
                    'element_type' => 'select',
                    'element_attributes' => [$togglealignmentvalues],
                ];
            } else {
                $courseformatoptionsedit['togglealignment'] = [
                    'label' => get_config('format_topcoll', 'defaulttogglealignment'), 'element_type' => 'hidden', ];
            }
            if (has_capability('format/topcoll:changetoggleiconset', $context)) {
                $toggleiconsetvalues = $this->generate_default_entry(
                    'toggleiconset',
                    '-',
                    [
                        'arrow' => new lang_string('arrow', 'format_topcoll'), // Arrow icon set.
                        'bulb' => new lang_string('bulb', 'format_topcoll'), // Bulb icon set.
                        'cloud' => new lang_string('cloud', 'format_topcoll'), // Cloud icon set.
                        'eye' => new lang_string('eye', 'format_topcoll'), // Eye icon set.
                        'folder' => new lang_string('folder', 'format_topcoll'), // Folder icon set.
                        'groundsignal' => new lang_string('groundsignal', 'format_topcoll'), // Ground signal set.
                        'led' => new lang_string('led', 'format_topcoll'), // LED icon set.
                        'point' => new lang_string('point', 'format_topcoll'), // Point icon set.
                        'power' => new lang_string('power', 'format_topcoll'), // Power icon set.
                        'radio' => new lang_string('radio', 'format_topcoll'), // Radio icon set.
                        'smiley' => new lang_string('smiley', 'format_topcoll'), // Smiley icon set.
                        'square' => new lang_string('square', 'format_topcoll'), // Square icon set.
                        'sunmoon' => new lang_string('sunmoon', 'format_topcoll'), // Sun / Moon icon set.
                        'switch' => new lang_string('switch', 'format_topcoll'), // Switch icon set.
                        'tif' => new lang_string('tif', 'format_topcoll'), // Toggle icon font.
                    ]
                );
                $courseformatoptionsedit['toggleiconset'] = [
                    'label' => new lang_string('settoggleiconset', 'format_topcoll'),
                    'help' => 'settoggleiconset',
                    'help_component' => 'format_topcoll',
                    'element_type' => 'select',
                    'element_attributes' => [$toggleiconsetvalues],
                ];
                $courseformatoptionsedit['toggleiconfontclosed'] = [
                    'label' => new lang_string('settoggleiconfontclosed', 'format_topcoll'),
                    'help' => 'settoggleiconfontclosed',
                    'help_component' => 'format_topcoll',
                    'element_type' => 'text',
                ];
                $courseformatoptionsedit['toggleiconfontopen'] = [
                    'label' => new lang_string('settoggleiconfontopen', 'format_topcoll'),
                    'help' => 'settoggleiconfontopen',
                    'help_component' => 'format_topcoll',
                    'element_type' => 'text',
                ];
                $toggleallhovervalues = $this->generate_default_entry(
                    'toggleallhover',
                    0,
                    [
                        1 => new lang_string('no'),
                        2 => new lang_string('yes'),
                    ]
                );
                $courseformatoptionsedit['toggleallhover'] = [
                    'label' => new lang_string('settoggleallhover', 'format_topcoll'),
                    'help' => 'settoggleallhover',
                    'help_component' => 'format_topcoll',
                    'element_type' => 'select',
                    'element_attributes' => [$toggleallhovervalues],
                ];
            } else {
                $courseformatoptionsedit['toggleiconset'] = [
                    'label' => '-', 'element_type' => 'hidden', ];
                $courseformatoptionsedit['toggleiconfontclosed'] = [
                    'label' => '-', 'element_type' => 'hidden', ];
                $courseformatoptionsedit['toggleiconfontopen'] = [
                    'label' => '-', 'element_type' => 'hidden', ];
                $courseformatoptionsedit['toggleallhover'] = [
                    'label' => 0, 'element_type' => 'hidden', ];
            }

            if (has_capability('format/topcoll:changecolour', $context)) {
                $opacityvalues = [
                    '-' => '',
                    '0.0' => '0.0',
                    '0.1' => '0.1',
                    '0.2' => '0.2',
                    '0.3' => '0.3',
                    '0.4' => '0.4',
                    '0.5' => '0.5',
                    '0.6' => '0.6',
                    '0.7' => '0.7',
                    '0.8' => '0.8',
                    '0.9' => '0.9',
                    '1.0' => '1.0',
                ];
                $courseformatoptionsedit['toggleforegroundcolour'] = [
                    'label' => new lang_string('settoggleforegroundcolour', 'format_topcoll'),
                    'help' => 'settoggleforegroundcolour',
                    'help_component' => 'format_topcoll',
                    'element_type' => 'tccolourpopup',
                    'element_attributes' => [
                        [
                            'defaultcolour' => $defaulttgfgcolour,
                            'value' => $defaulttgfgcolour,
                        ],
                    ],
                ];
                $opacityvalues['-'] = new lang_string(
                    'default',
                    'format_topcoll',
                    get_config('format_topcoll', 'defaulttoggleforegroundopacity')
                );
                $courseformatoptionsedit['toggleforegroundopacity'] = [
                    'label' => new lang_string('settoggleforegroundopacity', 'format_topcoll'),
                    'help' => 'settoggleforegroundopacity',
                    'help_component' => 'format_topcoll',
                    'element_type' => 'select',
                    'element_attributes' => [$opacityvalues],
                ];
                $courseformatoptionsedit['toggleforegroundhovercolour'] = [
                    'label' => new lang_string('settoggleforegroundhovercolour', 'format_topcoll'),
                    'help' => 'settoggleforegroundhovercolour',
                    'help_component' => 'format_topcoll',
                    'element_type' => 'tccolourpopup',
                    'element_attributes' => [
                        [
                            'defaultcolour' => $defaulttgfghvrcolour,
                            'value' => $defaulttgfghvrcolour,
                        ],
                    ],
                ];
                $opacityvalues['-'] = new lang_string(
                    'default',
                    'format_topcoll',
                    get_config('format_topcoll', 'defaulttoggleforegroundhoveropacity')
                );
                $courseformatoptionsedit['toggleforegroundhoveropacity'] = [
                    'label' => new lang_string('settoggleforegroundhoveropacity', 'format_topcoll'),
                    'help' => 'settoggleforegroundhoveropacity',
                    'help_component' => 'format_topcoll',
                    'element_type' => 'select',
                    'element_attributes' => [$opacityvalues],
                ];
                $courseformatoptionsedit['togglebackgroundcolour'] = [
                    'label' => new lang_string('settogglebackgroundcolour', 'format_topcoll'),
                    'help' => 'settogglebackgroundcolour',
                    'help_component' => 'format_topcoll',
                    'element_type' => 'tccolourpopup',
                    'element_attributes' => [
                        [
                            'defaultcolour' => $defaulttgbgcolour,
                            'value' => $defaulttgbgcolour,
                        ],
                    ],
                ];
                $opacityvalues['-'] = new lang_string(
                    'default',
                    'format_topcoll',
                    get_config('format_topcoll', 'defaulttogglebackgroundopacity')
                );
                $courseformatoptionsedit['togglebackgroundopacity'] = [
                    'label' => new lang_string('settogglebackgroundopacity', 'format_topcoll'),
                    'help' => 'settogglebackgroundopacity',
                    'help_component' => 'format_topcoll',
                    'element_type' => 'select',
                    'element_attributes' => [$opacityvalues],
                ];
                $courseformatoptionsedit['togglebackgroundhovercolour'] = [
                    'label' => new lang_string('settogglebackgroundhovercolour', 'format_topcoll'),
                    'help' => 'settogglebackgroundhovercolour',
                    'help_component' => 'format_topcoll',
                    'element_type' => 'tccolourpopup',
                    'element_attributes' => [
                        [
                            'defaultcolour' => $defaulttgbghvrcolour,
                            'value' => $defaulttgbghvrcolour,
                        ],
                    ],
                ];
                $opacityvalues['-'] = new lang_string(
                    'default',
                    'format_topcoll',
                    get_config('format_topcoll', 'defaulttogglebackgroundhoveropacity')
                );
                $courseformatoptionsedit['togglebackgroundhoveropacity'] = [
                    'label' => new lang_string('settogglebackgroundhoveropacity', 'format_topcoll'),
                    'help' => 'settogglebackgroundhoveropacity',
                    'help_component' => 'format_topcoll',
                    'element_type' => 'select',
                    'element_attributes' => [$opacityvalues],
                ];
            } else {
                $courseformatoptionsedit['toggleforegroundcolour'] = [
                    'label' => '-', 'element_type' => 'hidden', ];
                $courseformatoptionsedit['toggleforegroundopacity'] = [
                    'label' => '-', 'element_type' => 'hidden', ];
                $courseformatoptionsedit['toggleforegroundhovercolour'] = [
                    'label' => '-', 'element_type' => 'hidden', ];
                $courseformatoptionsedit['toggleforegroundhoveropacity'] = [
                    'label' => '-', 'element_type' => 'hidden', ];
                $courseformatoptionsedit['togglebackgroundcolour'] = [
                    'label' => '-', 'element_type' => 'hidden', ];
                $courseformatoptionsedit['togglebackgroundopacity'] = [
                    'label' => '-', 'element_type' => 'hidden', ];
                $courseformatoptionsedit['togglebackgroundhovercolour'] = [
                    'label' => '-', 'element_type' => 'hidden', ];
                $courseformatoptionsedit['togglebackgroundhoveropacity'] = [
                    'label' => '-', 'element_type' => 'hidden', ];
            }
            $readme = new moodle_url('/course/format/topcoll/Readme.md');
            $readme = html_writer::link($readme, 'Readme.md', ['target' => '_blank']);
            $courseformatoptionsedit['readme'] = [
                    'label' => get_string('readme_title', 'format_topcoll'),
                    'element_type' => 'static',
                    'element_attributes' => [get_string('readme_desc', 'format_topcoll', ['url' => $readme])],
                ];
            $courseformatoptions = array_merge_recursive($courseformatoptions, $courseformatoptionsedit);
        }
        return $courseformatoptions;
    }

    /**
     * Generates the default setting value entry.
     *
     * @param string $settingname Setting name.
     * @param string/int $defaultindex Default index.
     * @param array $values Setting value array to add the default entry to.
     * @return array Updated value array with the added default entry.
     */
    private function generate_default_entry($settingname, $defaultindex, $values) {
        $defaultvalue = get_config('format_topcoll', 'default' . $settingname);
        $defarray = [$defaultindex => new lang_string('default', 'format_topcoll', $values[$defaultvalue])];

        return array_replace($defarray, $values);
    }

    /**
     * Adds format options elements to the course/section edit form
     *
     * This function is called from {@link course_edit_form::definition_after_data()}
     *
     * @param MoodleQuickForm $mform form the elements are added to
     * @param bool $forsection 'true' if this is a section edit form, 'false' if this is course edit form
     * @return array array of references to the added form elements
     */
    public function create_edit_form_elements(&$mform, $forsection = false) {
        global $CFG, $COURSE, $OUTPUT, $USER;
        MoodleQuickForm::registerElementType(
            'tccolourpopup',
            "$CFG->dirroot/course/format/topcoll/js/tc_colourpopup.php",
            'MoodleQuickForm_tccolourpopup'
        );

        $elements = parent::create_edit_form_elements($mform, $forsection);

        if (!$forsection && (empty($COURSE->id) || $COURSE->id == SITEID)) {
            // Add "numsections" element to the create course form - it will force new course to be prepopulated
            // with empty sections.
            // The "Number of sections" option is no longer available when editing course, instead teachers should
            // delete and add sections when needed.
            $courseconfig = get_config('moodlecourse');
            $max = (int)$courseconfig->maxsections;
            $element = $mform->addElement('select', 'numsections', get_string('numberweeks'), range(0, $max ?: 52));
            $mform->setType('numsections', PARAM_INT);
            if (is_null($mform->getElementValue('numsections'))) {
                $mform->setDefault('numsections', $courseconfig->numsections);
            }
            array_unshift($elements, $element);
        }

        $context = $this->get_context();

        $changelayout = has_capability('format/topcoll:changelayout', $context);
        $changecolour = has_capability('format/topcoll:changecolour', $context);
        $changetogglealignment = has_capability('format/topcoll:changetogglealignment', $context);
        $changetoggleiconset = has_capability('format/topcoll:changetoggleiconset', $context);
        $changeactivitymeta = has_capability('format/topcoll:changeactivitymeta', $context);
        $resetall = is_siteadmin($USER); // Site admins only.

        $elements[] = $mform->addElement('header', 'ctreset', get_string('ctreset', 'format_topcoll'));
        $mform->addHelpButton('ctreset', 'ctreset', 'format_topcoll', '', true);

        $resetelements = [];
        $checkboxname = get_string('resetdisplayinstructions', 'format_topcoll');
        $resetelements[] = & $mform->createElement('checkbox', 'resetdisplayinstructions', '', $checkboxname);
        $resetelements[] = & $mform->createElement('html', $OUTPUT->help_icon('resetdisplayinstructions', 'format_topcoll'));

        if ($changelayout) {
            $checkboxname = get_string('resetlayout', 'format_topcoll');
            $resetelements[] = & $mform->createElement('checkbox', 'resetlayout', '', $checkboxname);
            $resetelements[] = & $mform->createElement('html', $OUTPUT->help_icon('resetlayout', 'format_topcoll'));
        }

        if ($changecolour) {
            $checkboxname = get_string('resetcolour', 'format_topcoll');
            $resetelements[] = & $mform->createElement('checkbox', 'resetcolour', '', $checkboxname);
            $resetelements[] = & $mform->createElement('html', $OUTPUT->help_icon('resetcolour', 'format_topcoll'));
        }

        if ($changetogglealignment) {
            $checkboxname = get_string('resettogglealignment', 'format_topcoll');
            $resetelements[] = & $mform->createElement('checkbox', 'resettogglealignment', '', $checkboxname);
            $resetelements[] = & $mform->createElement('html', $OUTPUT->help_icon('resettogglealignment', 'format_topcoll'));
        }

        if ($changetoggleiconset) {
            $checkboxname = get_string('resettoggleiconset', 'format_topcoll');
            $resetelements[] = & $mform->createElement('checkbox', 'resettoggleiconset', '', $checkboxname);
            $resetelements[] = & $mform->createElement('html', $OUTPUT->help_icon('resettoggleiconset', 'format_topcoll'));
        }

        if ($changeactivitymeta) {
            $checkboxname = get_string('resetactivitymeta', 'format_topcoll');
            $resetelements[] = & $mform->createElement('checkbox', 'resetactivitymeta', '', $checkboxname);
            $resetelements[] = & $mform->createElement('html', $OUTPUT->help_icon('resetactivitymeta', 'format_topcoll'));
        }

        $elements[] = $mform->addGroup($resetelements, 'resetgroup', get_string('resetgrp', 'format_topcoll'), null, false);

        if ($resetall) {
            $resetallelements = [];

            $checkboxname = get_string('resetalldisplayinstructions', 'format_topcoll');
            $resetallelements[] = & $mform->createElement('checkbox', 'resetalldisplayinstructions', '', $checkboxname);
            $resetallelements[] = & $mform->createElement(
                'html',
                $OUTPUT->help_icon('resetalldisplayinstructions', 'format_topcoll')
            );

            $checkboxname = get_string('resetalllayout', 'format_topcoll');
            $resetallelements[] = & $mform->createElement('checkbox', 'resetalllayout', '', $checkboxname);
            $resetallelements[] = & $mform->createElement('html', $OUTPUT->help_icon('resetalllayout', 'format_topcoll'));

            $checkboxname = get_string('resetallcolour', 'format_topcoll');
            $resetallelements[] = & $mform->createElement('checkbox', 'resetallcolour', '', $checkboxname);
            $resetallelements[] = & $mform->createElement('html', $OUTPUT->help_icon('resetallcolour', 'format_topcoll'));

            $checkboxname = get_string('resetalltogglealignment', 'format_topcoll');
            $resetallelements[] = & $mform->createElement('checkbox', 'resetalltogglealignment', '', $checkboxname);
            $resetallelements[] = & $mform->createElement('html', $OUTPUT->help_icon('resetalltogglealignment', 'format_topcoll'));

            $checkboxname = get_string('resetalltoggleiconset', 'format_topcoll');
            $resetallelements[] = & $mform->createElement('checkbox', 'resetalltoggleiconset', '', $checkboxname);
            $resetallelements[] = & $mform->createElement('html', $OUTPUT->help_icon('resetalltoggleiconset', 'format_topcoll'));

            $checkboxname = get_string('resetallactivitymeta', 'format_topcoll');
            $resetallelements[] = & $mform->createElement('checkbox', 'resetallactivitymeta', '', $checkboxname);
            $resetallelements[] = & $mform->createElement('html', $OUTPUT->help_icon('resetallactivitymeta', 'format_topcoll'));

            $elements[] = $mform->addGroup(
                $resetallelements,
                'resetallgroup',
                get_string('resetallgrp', 'format_topcoll'),
                null,
                false
            );
        }

        return $elements;
    }

    /**
     * Override if you need to perform some extra validation of the format options
     *
     * @param array $data array of ("fieldname"=>value) of submitted data
     * @param array $files array of uploaded files "element_name"=>tmp_file_path
     * @param array $errors errors already discovered in edit form validation
     * @return array of "element_name"=>"error_description" if there are errors,
     *         or an empty array if everything is OK.
     *         Do not repeat errors from $errors param here
     */
    public function edit_form_validation($data, $files, $errors) {
        $retr = [];

        if ($this->validate_colour($data['toggleforegroundcolour']) === false) {
            $retr['toggleforegroundcolour'] = get_string('colourrule', 'format_topcoll');
        }
        if ($this->validate_colour($data['toggleforegroundhovercolour']) === false) {
            $retr['toggleforegroundhovercolour'] = get_string('colourrule', 'format_topcoll');
        }
        if ($this->validate_colour($data['togglebackgroundcolour']) === false) {
            $retr['togglebackgroundcolour'] = get_string('colourrule', 'format_topcoll');
        }
        if ($this->validate_colour($data['togglebackgroundhovercolour']) === false) {
            $retr['togglebackgroundhovercolour'] = get_string('colourrule', 'format_topcoll');
        }

        return $retr;
    }

    /**
     * Validates the colour that was entered by the user.
     * Borrowed from 'admin_setting_configcolourpicker' in '/lib/adminlib.php'.
     *
     * I'm not completely happy with this solution as would rather embed in the colour
     * picker code in the form, however I find this area rather fraut and I hear that
     * Dan Poltawski (via MDL-42270) will be re-writing the forms lib so hopefully more
     * developer friendly.
     *
     * Note: Colour names removed, but might consider putting them back in if asked, but
     *       at the moment that would require quite a few changes and coping with existing
     *       settings.  Either convert the names to hex or allow them as valid values and
     *       fix the colour picker code and the CSS code in 'format.php' for the setting.
     *
     * Colour name to hex on: http://www.w3schools.com/cssref/css_colornames.asp.
     *
     * @param string $data the colour string to validate.
     * @return true|false
     */
    private function validate_colour($data) {
        if ($data == '-') {
            return true;
        } else if (preg_match('/^#?([[:xdigit:]]{3}){1,2}$/', $data)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Updates format options for a course
     *
     * In case if course format was changed to 'Collapsed Topics', we try to copy options
     * 'numsections' and 'hiddensections' from the previous format.
     * If previous course format did not have 'numsections' option, we populate it with the
     * current number of sections.  The layout and colour defaults will come from 'course_format_options'.
     *
     * @param stdClass|array $data return value from {@link moodleform::get_data()} or array with data
     * @param stdClass $oldcourse if this function is called from {@link update_course()}
     *     this object contains information about the course before update
     * @return bool whether there were any changes to the options values
     */
    public function update_course_format_options($data, $oldcourse = null) {
        global $DB;
        /*
         * Notes: Using 'unset' to really ensure that the reset form elements never get into the database.
         *        This has to be done here so that the reset occurs after we have done updates such that the
         *        reset itself is not seen as an update.
         */
        $resetdisplayinstructions = false;
        $resetlayout = false;
        $resetcolour = false;
        $resettogglealignment = false;
        $resettoggleiconset = false;
        $resetactivitymeta = false;
        $resetalldisplayinstructions = false;
        $resetalllayout = false;
        $resetallcolour = false;
        $resetalltogglealignment = false;
        $resetalltoggleiconset = false;
        $resetallactivitymeta = false;
        if (isset($data->resetdisplayinstructions) == true) {
            $resetdisplayinstructions = true;
            unset($data->resetdisplayinstructions);
        }
        if (isset($data->resetlayout) == true) {
            $resetlayout = true;
            unset($data->resetlayout);
        }
        if (isset($data->resetcolour) == true) {
            $resetcolour = true;
            unset($data->resetcolour);
        }
        if (isset($data->resettogglealignment) == true) {
            $resettogglealignment = true;
            unset($data->resettogglealignment);
        }
        if (isset($data->resettoggleiconset) == true) {
            $resettoggleiconset = true;
            unset($data->resettoggleiconset);
        }
        if (isset($data->resetactivitymeta) == true) {
            $resetactivitymeta = true;
            unset($data->resetactivitymeta);
        }
        if (isset($data->resetalldisplayinstructions) == true) {
            $resetalldisplayinstructions = true;
            unset($data->resetalldisplayinstructions);
        }
        if (isset($data->resetalllayout) == true) {
            $resetalllayout = true;
            unset($data->resetalllayout);
        }
        if (isset($data->resetallcolour) == true) {
            $resetallcolour = true;
            unset($data->resetallcolour);
        }
        if (isset($data->resetalltogglealignment) == true) {
            $resetalltogglealignment = true;
            unset($data->resetalltogglealignment);
        }
        if (isset($data->resetalltoggleiconset) == true) {
            $resetalltoggleiconset = true;
            unset($data->resetalltoggleiconset);
        }
        if (isset($data->resetallactivitymeta) == true) {
            $resetallactivitymeta = true;
            unset($data->resetallactivitymeta);
        }

        $data = (array) $data;
        if ($oldcourse !== null) {
            $oldcourse = (array)$oldcourse;
            $options = $this->course_format_options();
            foreach ($options as $key => $unused) {
                if (!array_key_exists($key, $data)) {
                    if (array_key_exists($key, $oldcourse)) {
                        $data[$key] = $oldcourse[$key];
                    }
                }
            }
        }

        $changes = $this->update_format_options($data);

        // Now we can do the reset.
        if (
            ($resetalldisplayinstructions) ||
            ($resetalllayout) ||
            ($resetallcolour) ||
            ($resetalltogglealignment) ||
            ($resetalltoggleiconset) ||
            ($resetallactivitymeta)
        ) {
                $this->reset_topcoll_setting(
                    0,
                    $resetalldisplayinstructions,
                    $resetalllayout,
                    $resetallcolour,
                    $resetalltogglealignment,
                    $resetalltoggleiconset,
                    $resetallactivitymeta
                );
            $changes = true;
        } else if (
            ($resetdisplayinstructions) ||
            ($resetlayout) ||
            ($resetcolour) ||
            ($resettogglealignment) ||
            ($resettoggleiconset) ||
            ($resetactivitymeta)
        ) {
                $this->reset_topcoll_setting(
                    $this->courseid,
                    $resetdisplayinstructions,
                    $resetlayout,
                    $resetcolour,
                    $resettogglealignment,
                    $resettoggleiconset,
                    $resetactivitymeta
                );
            $changes = true;
        }

        if ($changes) {
            // Invalidate the settings.
            $this->settings = null;
        }

        return $changes;
    }

    /**
     * Is the section passed in the current section?
     *
     * @param stdClass $section The course_section entry from the DB
     * @return bool true if the section is current
     */
    public function is_section_current($section) {
        $tcsettings = $this->get_settings();
        if (($tcsettings['layoutstructure'] == 2) || ($tcsettings['layoutstructure'] == 3)) {
            if ($section->section < 1) {
                return false;
            }

            $timenow = time();
            $dates = $this->format_topcoll_get_section_dates($section, $this->get_course());

            return (($timenow >= $dates->start) && ($timenow < $dates->end));
        } else if ($tcsettings['layoutstructure'] == 5) {
            if ($section->section < 1) {
                return false;
            }

            $timenow = time();
            $day = $this->format_topcoll_get_section_day($section, $this->get_course());
            $onedayseconds = 86400;
            return (($timenow >= $day) && ($timenow < ($day + $onedayseconds)));
        } else {
            return parent::is_section_current($section);
        }
    }

    /**
     * Return the start and end date of the passed section.
     *
     * @param int|stdClass $section The course_section entry from the DB.
     * @param stdClass $course The course entry from DB.
     * @return stdClass property start for startdate, property end for enddate.
     */
    private function format_topcoll_get_section_dates($section, $course) {
        $oneweekseconds = 604800;
        /* Hack alert. We add 2 hours to avoid possible DST problems. (e.g. we go into daylight
           savings and the date changes. */
        $startdate = $course->startdate + 7200;

        $dates = new stdClass();
        if (is_object($section)) {
            $section = $section->section;
        }

        $dates->start = $startdate + ($oneweekseconds * ($section - 1));
        $dates->end = $dates->start + $oneweekseconds;

        return $dates;
    }

    /**
     * Return the date of the passed section.
     *
     * @param int|stdClass $section The course_section entry from the DB.
     * @param stdClass $course The course entry from DB.
     * @return stdClass property date.
     */
    private function format_topcoll_get_section_day($section, $course) {
        $onedayseconds = 86400;
        /* Hack alert. We add 2 hours to avoid possible DST problems. (e.g. we go into daylight
           savings and the date changes. */
        $startdate = $course->startdate + 7200;

        if (is_object($section)) {
            $section = $section->section;
        }

        $day = $startdate + ($onedayseconds * ($section - 1));

        return $day;
    }

    /**
     * Return the shown sections.
     * Takes into account the course settings beyond visibility and returns them in the order to be shown.
     *
     * @return array of data required, indexes containing:
     *      'sectionzero' Section_info instance if section zero is shown.
     *      'sectionsdisplayed' Section_info instances, array, that are shown.
     *      'delegatedsectionsdisplayed' Delegated section_info instances, array, that are shown.
     *      'currentsectionno' Section number of the current section or if none then false.
     *      'hiddensectionids' Section id's, array, of hidden sections if any.
     *      'coursenumsections' Number of sections in the course regardless if shown or not.
     */
    public function get_shown_sections() {
        $course = $this->get_course();
        $modinfo = get_fast_modinfo($course);
        $sections = $modinfo->get_section_info_all();
        $delegatedsections = [];
        // Array of parent section number with an array of delegated section id's if they have them.
        $delegatedsectionparents = [];
        $sectionsdisplayed = [];
        $delegatedsectionsdisplayed = [];
        $currentsectionno = false;
        $hiddensectionids = [];

        // Find the subsections and who their parent is.
        // All the delegated sections as modules in the course.
        foreach ($modinfo->delegatedbycm as $delegatedcmsectioninfokey => $delegatedcmsectioninfo) {
            // Note: cm_info object contains parent sectionid / sectionnum.  As a subsection is a module
            // here then this will be the parent section.
            $cminfoparentsectionnum = $modinfo->cms[$delegatedcmsectioninfokey]->sectionnum;
            if (empty($delegatedsectionparents[$cminfoparentsectionnum])) {
                $delegatedsectionparents[$cminfoparentsectionnum] = [];
            }
            $delegatedsectionparents[$cminfoparentsectionnum][] = $delegatedcmsectioninfo->id;
            $delegatedsections[$delegatedcmsectioninfo->id] = $delegatedcmsectioninfo;
        }

        // General section if non-empty.
        $thissection = $sections[0];
        if ($this->is_section_visible($thissection)) {
            $sectionzero = $thissection;
        } else {
            $sectionzero = null;
        }

        $coursenumsections = $this->get_last_section_number_without_delegated();
        if ($coursenumsections > 0) {
            $tcsettings = $this->get_settings();
            $coursenumsections = $this->get_last_section_number_without_delegated();
            $userisediting = $this->show_editor();
            $sectionskeys = array_keys($sections);

            $currentsectionfirst = false;
            if (($tcsettings['layoutstructure'] == 4) && (!$userisediting)) {
                $currentsectionfirst = true;
            }

            if (($tcsettings['layoutstructure'] != 3) || ($userisediting)) {
                $section = 1;
            } else {
                $timenow = time();
                $weekofseconds = 604800;
                $course->enddate = $course->startdate + ($weekofseconds * $coursenumsections);
                $section = $coursenumsections;
                $weekdate = $course->enddate;      // This should be 0:00 Monday of that week.
                $weekdate -= 7200;                 // Subtract two hours to avoid possible DST problems.
            }

            $loopsection = 1;
            while ($loopsection <= $coursenumsections) {
                if (($tcsettings['layoutstructure'] == 3) && ($userisediting == false)) {
                    $nextweekdate = $weekdate - ($weekofseconds);
                }
                $thissection = $modinfo->get_section_info($sectionskeys[$section]);

                /* Show the section if the user is permitted to access it, OR if it's not available
                   but there is some available info text which explains the reason & should display. */
                $showsection = ($this->is_section_visible($thissection));
                if ($showsection && ($tcsettings['layoutstructure'] == 3) && (!$userisediting)) {
                    $showsection = ($nextweekdate <= $timenow);
                }

                if ($currentsectionfirst && $showsection) {
                    // Show the section if we were meant to and it is the current section:....
                    $showsection = ($course->marker == $section);
                } else if (
                    ($tcsettings['layoutstructure'] == 4) &&
                    ($course->marker == $section) && (!$userisediting)
                ) {
                    $showsection = false; // Do not reshow current section.
                }
                $addsection = false;
                if ($showsection) {
                    if ($this->is_section_current($thissection)) {
                        $currentsectionno = $thissection->section;
                    }
                    // This section is shown.
                    $addsection = true;
                } else {
                    // Hidden section message is overridden by 'unavailable' control.
                    $testhidden = false;
                    if ($tcsettings['layoutstructure'] != 4) {
                        if (($tcsettings['layoutstructure'] != 3) || ($userisediting)) {
                            $testhidden = true;
                        } else if ($nextweekdate <= $timenow) {
                            $testhidden = true;
                        }
                    } else {
                        if (($currentsectionfirst == true) && ($course->marker == $section)) {
                            $testhidden = true;
                        } else if (($currentsectionfirst == false) && ($course->marker != $section)) {
                            $testhidden = true;
                        }
                    }
                    if ($testhidden) {
                        if (!$course->hiddensections) {
                            // This section is shown as hidden.
                            $addsection = true;
                            $hiddensectionids[] = $thissection->id;
                        }
                    }
                }
                if ($addsection) {
                    $sectionsdisplayed[$thissection->section] = $thissection;
                    // Does it contain visible subsection(s)?
                    if (!empty($delegatedsectionparents[$thissection->section])) {
                        foreach ($delegatedsectionparents[$thissection->section] as $delegatedsectionid) {
                            if (parent::is_section_visible($delegatedsections[$delegatedsectionid])) {
                                $delegatedsectionsdisplayed[$delegatedsections[$delegatedsectionid]->section] =
                                    $delegatedsections[$delegatedsectionid];
                            }
                        }
                    }
                }

                if (($tcsettings['layoutstructure'] != 3) || ($userisediting)) {
                    $section++;
                } else {
                    $section--;
                    if (($tcsettings['layoutstructure'] == 3) && ($userisediting == false)) {
                        $weekdate = $nextweekdate;
                    }
                }

                $loopsection++;
                if (($currentsectionfirst == true) && ($loopsection > $coursenumsections)) {
                    // Now show the rest.
                    $currentsectionfirst = false;
                    $loopsection = 1;
                    $section = 1;
                }
                if ($section > $coursenumsections) {
                    // Activities inside this section are 'orphaned', this section will be printed as 'stealth' in the renderer.
                    break;
                }
            }
        }

        return [
            'sectionzero' => $sectionzero,
            'sectionsdisplayed' => $sectionsdisplayed,
            'delegatedsectionsdisplayed' => $delegatedsectionsdisplayed,
            'currentsectionno' => $currentsectionno,
            'hiddensectionids' => $hiddensectionids,
            'coursenumsections' => $coursenumsections,
        ];
    }

    /**
     * Resets the format setting to the default.
     * @param int $courseid If not 0, then a specific course to reset.
     * @param int $displayinstructions If true, reset the display instructions to the default in the settings for the format.
     * @param int $layout If true, reset the layout to the default in the settings for the format.
     * @param int $colour If true, reset the colour to the default in the settings for the format.
     * @param int $togglealignment If true, reset the toggle alignment to the default in the settings for the format.
     * @param int $toggleiconset If true, reset the toggle icon set to the default in the settings for the format.
     * @param int $activitymeta If true, reset the activity meta to the default in the settings for the format.
     */
    public function reset_topcoll_setting(
        $courseid,
        $displayinstructions,
        $layout,
        $colour,
        $togglealignment,
        $toggleiconset,
        $activitymeta
    ) {
        global $DB, $USER;

        $context = $this->get_context();

        $currentcourseid = 0;
        if ($courseid == 0) {
            $records = $DB->get_records('course_format_options', ['format' => $this->format], '', 'id,courseid');
        } else {
            $records = $DB->get_records(
                'course_format_options',
                ['courseid' => $courseid, 'format' => $this->format],
                '',
                'id,courseid'
            );
        }

        $resetallifall = ((is_siteadmin($USER)) || ($courseid != 0)); // Will be true if reset all capability or a single course.

        $updatedata = [];
        $updatedisplayinstructions = false;
        $updatelayout = false;
        $updatetogglealignment = false;
        $updatecolour = false;
        $updatetoggleiconset = false;
        $updateactivitymeta = false;
        if ($displayinstructions && $resetallifall) {
            $updatedata['displayinstructions'] = 0;
            $updatedisplayinstructions = true;
        }
        if ($layout && has_capability('format/topcoll:changelayout', $context) && $resetallifall) {
            $updatedata['layoutelement'] = 0;
            $updatedata['layoutstructure'] = 0;
            $updatedata['layoutcolumns'] = 0;
            $updatedata['layoutcolumnorientation'] = 0;
            $updatedata['flexiblemodules'] = 0;
            $updatedata['toggleallenabled'] = 0;
            $updatedata['viewsinglesectionenabled'] = 0;
            $updatedata['toggleiconposition'] = 0;
            $updatedata['onesection'] = 0;
            $updatedata['showsectionsummary'] = 0;
            $updatelayout = true;
        }
        if ($activitymeta && has_capability('format/topcoll:changeactivitymeta', $context) && $resetallifall) {
            $updatedata['showadditionalmoddata'] = 0;
            $updateactivitymeta = true;
        }
        if ($togglealignment && has_capability('format/topcoll:changetogglealignment', $context) && $resetallifall) {
            $updatedata['togglealignment'] = 0;
            $updatetogglealignment = true;
        }
        if ($colour && has_capability('format/topcoll:changecolour', $context) && $resetallifall) {
            $updatedata['toggleforegroundcolour'] = '-';
            $updatedata['toggleforegroundopacity'] = '-';
            $updatedata['toggleforegroundhovercolour'] = '-';
            $updatedata['toggleforegroundhoveropacity'] = '-';
            $updatedata['togglebackgroundcolour'] = '-';
            $updatedata['togglebackgroundopacity'] = '-';
            $updatedata['togglebackgroundhovercolour'] = '-';
            $updatedata['togglebackgroundhoveropacity'] = '-';
            $updatecolour = true;
        }
        if ($toggleiconset && has_capability('format/topcoll:changetoggleiconset', $context) && $resetallifall) {
            $updatedata['toggleiconset'] = '-';
            $updatedata['toggleiconfontclosed'] = '-';
            $updatedata['toggleiconfontopen'] = '-';
            $updatedata['toggleallhover'] = 0;
            $updatetoggleiconset = true;
        }

        foreach ($records as $record) {
            if ($currentcourseid != $record->courseid) {
                $currentcourseid = $record->courseid; // Only do once per course.
                if (
                    ($updatedisplayinstructions) ||
                    ($updatelayout) ||
                    ($updatetogglealignment) ||
                    ($updatecolour) ||
                    ($updatetoggleiconset) ||
                    ($updateactivitymeta)
                ) {
                    $ourcourseid = $this->courseid;
                    $this->courseid = $currentcourseid;
                    $this->update_format_options($updatedata);
                    $this->courseid = $ourcourseid;
                }
            }
        }
    }

    /**
     * Restores the course settings when restoring a Moodle 2.3 or below (bar 1.9) course and sets the settings when upgrading
     * from a prevous version.  Hence no need for 'coursedisplay' as that is a core rather than CT specific setting and not
     * in the old 'format_topcoll_settings' table.
     * @param int $courseid If not 0, then a specific course to reset.
     * @param int $layoutelement The layout element.
     * @param int $layoutstructure The layout structure.
     * @param int $layoutcolumns The layout columns.
     * @param int $tgfgcolour The foreground colour.
     * @param int $tgbgcolour The background colour.
     * @param int $tgbghvrcolour The background hover colour.
     */
    public function restore_topcoll_setting(
        $courseid,
        $layoutelement,
        $layoutstructure,
        $layoutcolumns,
        $tgfgcolour,
        $tgbgcolour,
        $tgbghvrcolour
    ) {
        $currentcourseid = $this->courseid;  // Save for later - stack data model.
        $this->courseid = $courseid;
        // Create data array.
        $data = [
            'layoutelement' => $layoutelement,
            'layoutstructure' => $layoutstructure,
            'layoutcolumns' => $layoutcolumns,
            'toggleforegroundcolour' => $tgfgcolour,
            'togglebackgroundcolour' => $tgbgcolour,
            'togglebackgroundhovercolour' => $tgbghvrcolour, ];

        $lco = get_config('format_topcoll', 'defaultlayoutcolumnorientation');
        if (empty($lco)) {
            // Upgrading from M2.3 and the defaults in 'settings.php' have not been processed at this time.
            // Defaults taken from 'settings.php'.
            $data['displayinstructions'] = 0;
            $data['layoutcolumnorientation'] = 0;
            $data['flexiblemodules'] = 0;
            $data['toggleallenabled'] = 0;
            $data['viewsinglesectionenabled'] = 0;
            $data['showsectionsummary'] = 0;
            $data['togglealignment'] = 0;
            $data['toggleallhover'] = 0;
            $data['toggleiconposition'] = 0;
            $data['toggleiconset'] = '-';
            $data['toggleiconfontclosed'] = '-';
            $data['toggleiconfontopen'] = '-';
            $data['showadditionalmoddata'] = 0;
        }
        $this->update_course_format_options($data);

        $this->courseid = $currentcourseid;
    }

    /**
     * Updates the number of columns when the renderer detects that they are wrong.
     * @param int $layoutcolumns The layout columns to use, see tcconfig.php.
     */
    public function update_topcoll_columns_setting($layoutcolumns) {
        // Create data array.
        $data = ['layoutcolumns' => $layoutcolumns];

        $this->update_course_format_options($data);
    }

    /**
     * Whether this format allows to delete sections.
     *
     * Do not call this function directly, instead use {@link course_can_delete_section()}
     *
     * @param int|stdClass|section_info $section
     * @return bool
     */
    public function can_delete_section($section) {
        return true;
    }

    /**
     * Prepares the templateable object to display section name.
     *
     * @param section_info|stdClass $section
     * @param bool $linkifneeded
     * @param bool $editable
     * @param null|lang_string|string $edithint
     * @param null|lang_string|string $editlabel
     * @return core\output\inplace_editable
     */
    public function inplace_editable_render_section_name(
        $section,
        $linkifneeded = true,
        $editable = null,
        $edithint = null,
        $editlabel = null
    ) {
        if (empty($edithint)) {
            $edithint = new lang_string('editsectionname', 'format_topcoll');
        }
        if (empty($editlabel)) {
            $course = $this->get_course();
            $title = $this->get_topcoll_section_name($course, $section, false);
            $editlabel = new lang_string('newsectionname', 'format_topcoll', $title);
        }
        return parent::inplace_editable_render_section_name($section, $linkifneeded, $editable, $edithint, $editlabel);
    }

    /**
     * Returns whether this course format allows the activity to
     * have "triple visibility state" - visible always, hidden on course page but available, hidden.
     *
     * @param stdClass|cm_info $cm course module (may be null if we are displaying a form for adding a module)
     * @param stdClass|section_info $section section where this module is located or will be added to
     * @return bool
     */
    public function allow_stealth_module_visibility($cm, $section) {
        // Allow the third visibility state inside visible sections or in section 0.
        return !$section->section || $section->visible;
    }

    /**
     * Callback used in WS core_course_edit_section when teacher performs an AJAX action on a section (show/hide)
     *
     * Access to the course is already validated in the WS but the callback has to make sure
     * that particular action is allowed by checking capabilities
     *
     * Course formats should register
     *
     * @param stdClass|section_info $section
     * @param string $action
     * @param int $sr the section return
     * @return null|array|stdClass any data for the Javascript post-processor (must be json-encodeable)
     */
    public function section_action($section, $action, $sr) {
        global $PAGE;

        // Topic based course.
        $tcsettings = $this->get_settings();
        if (($tcsettings['layoutstructure'] == 1) || ($tcsettings['layoutstructure'] == 4)) {
            if ($section->section && ($action === 'setmarker' || $action === 'removemarker')) {
                // Format 'Topcoll' allows to set and remove markers in addition to common section actions.
                require_capability('moodle/course:setcurrentsection', context_course::instance($this->courseid));
                course_set_marker($this->courseid, ($action === 'setmarker') ? $section->section : 0);
                return null;
            }
        }

        // For show/hide actions call the parent method and return the new content for .section_availability element.
        $rv = parent::section_action($section, $action, $sr);
        $renderer = $PAGE->get_renderer('format_topcoll');

        if (!($section instanceof section_info)) {
            $modinfo = course_modinfo::instance($this->courseid);
            $section = $modinfo->get_section_info($section->section);
        }
        $elementclass = $this->get_output_classname('content\\section\\availability');
        $availability = new $elementclass($this, $section);

        $rv['section_availability'] = $renderer->render($availability);

        return $rv;
    }

    /**
     * Duplicate a section
     *
     * @param section_info $originalsection The section to be duplicated
     * @return section_info The new duplicated section
     * @since Moodle 4.2
     */
    public function duplicate_section(section_info $originalsection): section_info {
        $retr = parent::duplicate_section($originalsection);

        // Update 'numsections'.
        $newnumsections = $this->settings['numsections'] + 1;
        $courseformatdata = ['numsections' => $newnumsections];
        $this->update_course_format_options($courseformatdata);

        return $retr;
    }

    /**
     * Get the required javascript files for the course format.
     *
     * @return array The list of javascript files required by the course format.
     */
    public function get_required_jsfiles(): array {
        return [];
    }
}

/**
 * Override the core_output_load_template function to use our Mustache template finder.
 *
 * Info on: https://docs.moodle.org/dev/Miscellaneous_callbacks#override_webservice_execution
 *
 * @param stdClass $function Function details.
 * @param array $params Parameters
 *
 * @return boolean Success.
 */
function format_topcoll_override_webservice_execution($function, $params) {
    // Check if it's the function we want to override.
    if ($function->name === 'core_courseformat_get_state') {
        // Only one parameter of the course id.
        $courseformat = course_get_format($params[0]);
        if ($courseformat->get_format() != 'topcoll') {
            return false;
        }

        // Call our load template function in our class instead of $function->classname.
        return call_user_func_array(['format_topcoll\external\get_state', $function->methodname], $params);
    }

    return false;
}

/**
 * Implements callback inplace_editable() allowing to edit values in-place.
 *
 * @param string $itemtype
 * @param int $itemid
 * @param mixed $newvalue
 * @return \core\output\inplace_editable
 */
function format_topcoll_inplace_editable($itemtype, $itemid, $newvalue) {
    global $CFG;
    require_once($CFG->dirroot . '/course/lib.php');
    if ($itemtype === 'sectionname' || $itemtype === 'sectionnamenl') {
        global $DB;
        $section = $DB->get_record_sql(
            'SELECT s.* FROM {course_sections} s JOIN {course} c ON s.course = c.id WHERE s.id = ? AND c.format = ?',
            [$itemid, 'topcoll'],
            MUST_EXIST
        );
        return course_get_format($section->course)->inplace_editable_update_section_name($section, $itemtype, $newvalue);
    }
}

/**
 * The string that is used to describe a section of the course.
 *
 * @return string The section description.
 */
function callback_topcoll_definition() {
    return get_string('sectionname', 'format_topcoll');
}
