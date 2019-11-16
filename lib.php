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
 * @version    See the value of '$plugin->version' in version.php.
 * @copyright  &copy; 2012-onwards G J Barnard in respect to modifications of standard topics format.
 * @author     G J Barnard - {@link http://moodle.org/user/profile.php?id=442195}
 * @link       http://docs.moodle.org/en/Collapsed_Topics_course_format
 * @license    http://www.gnu.org/copyleft/gpl.html GNU Public License
 *
 */
defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/course/format/lib.php'); // For format_base.

class format_topcoll extends format_base {
    // Used to determine the type of view URL to generate - parameter or anchor.
    private $coursedisplay = COURSE_DISPLAY_SINGLEPAGE;
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

        $section = optional_param('section', 0, PARAM_INT);
        if ($section) {
            $this->coursedisplay = COURSE_DISPLAY_MULTIPAGE;
        }
    }

    /**
     * Returns the format's settings and gets them if they do not exist.
     * @return type The settings as an array.
     */
    public function get_settings() {
        if (empty($this->settings) == true) {
            $this->settings = $this->get_format_options();
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
     * Gets the name for the provided section.
     *
     * @param int|stdClass $section Section object from database or just field section.section
     * @return string The section name.
     */
    public function get_section_name($section) {
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
            $thesection = new stdClass;
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
            $o .= format_string($thesection->name, true, array('context' => $context));
            if (($thesection->section != 0) && (($tcsettings['layoutstructure'] == 2) ||
                ($tcsettings['layoutstructure'] == 3) || ($tcsettings['layoutstructure'] == 5))) {
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
                    $o .= '<div class="cttoggle"> - ' .get_string('topcolltoggle', 'format_topcoll') . '</div>';
                    break;
            }
        }

        return $o;
    }

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

        switch($tcsettings['layoutstructure']) {
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
     * The URL to use for the specified course (with section)
     *
     * @param int|stdClass $section Section object from database or just field course_sections.section
     *     if omitted the course view page is returned
     * @param array $options options for view URL. At the moment core uses:
     *     'navigation' (bool) if true and section has no separate page, the function returns null
     *     'sr' (int) used by multipage formats to specify to which section to return
     * @return null|moodle_url
     */
    public function get_view_url($section, $options = array()) {
        $course = $this->get_course();
        $url = new moodle_url('/course/view.php', array('id' => $course->id));

        $sr = null;
        if (array_key_exists('sr', $options)) {
            $sr = $options['sr'];
        }
        if (is_object($section)) {
            $sectionno = $section->section;
        } else {
            $sectionno = $section;
        }
        if ($sectionno !== null) {
            if ($sr !== null) {
                if ($sr) {
                    $usercoursedisplay = COURSE_DISPLAY_MULTIPAGE;
                    $sectionno = $sr;
                } else {
                    $usercoursedisplay = COURSE_DISPLAY_SINGLEPAGE;
                }
            } else {
                $usercoursedisplay = $this->coursedisplay;
            }
            if ($sectionno != 0 && $usercoursedisplay == COURSE_DISPLAY_MULTIPAGE) {
                $url->param('section', $sectionno);
            } else {
                global $CFG;
                if (empty($CFG->linkcoursesections) && !empty($options['navigation'])) { // MDL-57412.
                    return null;
                }
                $url->set_anchor('section-' . $sectionno);
            }
        }
        return $url;
    }

    /**
     * Returns the information about the ajax support in the given source format
     *
     * The returned object's property (boolean)capable indicates that
     * the course format supports Moodle course ajax features.
     * The property (array)testedbrowsers can be used as a parameter for {@link ajaxenabled()}.
     *
     * @return stdClass
     */
    public function supports_ajax() {
        $ajaxsupport = new stdClass();
        $ajaxsupport->capable = true;
        return $ajaxsupport;
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

        $titles = array();
        $current = -1;  // MDL-33546.
        $weekformat = false;
        $tcsettings = $this->get_settings();
        if (($tcsettings['layoutstructure'] == 2) || ($tcsettings['layoutstructure'] == 3) ||
            ($tcsettings['layoutstructure'] == 5)) {
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
        return array('sectiontitles' => $titles, 'current' => $current, 'action' => 'move');
    }

    /**
     * Returns the list of blocks to be automatically added for the newly created course
     *
     * @return array of default blocks, must contain two keys BLOCK_POS_LEFT and BLOCK_POS_RIGHT
     *     each of values is an array of block names (for left and right side columns)
     */
    public function get_default_blocks() {
        return array(
            BLOCK_POS_LEFT => array(),
            BLOCK_POS_RIGHT => array('search_forums', 'news_items', 'calendar_upcoming', 'recent_activity')
        );
    }

    public function section_format_options($foreditform = false) {
        static $sectionformatoptions = false;

        if ($sectionformatoptions === false) {
            $sectionformatoptions = array(
                'donotshowdate' => array(
                    'default' => 0,
                    'type' => PARAM_INT
                )
            );
        }
        if ($foreditform && !isset($sectionformatoptions['donotshowdate']['label'])) {
            $sectionformatoptionsedit = array(
                'donotshowdate' => array(
                    'label' => new lang_string('donotshowdate', 'format_topcoll'),
                    'help' => 'donotshowdate',
                    'help_component' => 'format_topcoll',
                    'element_type' => 'checkbox'
                )
            );
            $sectionformatoptions = array_merge_recursive($sectionformatoptions, $sectionformatoptionsedit);
        }

        $tcsettings = $this->get_settings();
        if (($tcsettings['layoutstructure'] == 2) || ($tcsettings['layoutstructure'] == 3) ||
            ($tcsettings['layoutstructure'] == 5)) {
            // Weekly layout.
            return $sectionformatoptions;
        } else {
            return array();
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

        if ($courseformatoptions === false) {
            /* Note: Because 'admin_setting_configcolourpicker' in 'settings.php' needs to use a prefixing '#'
                     this needs to be stripped off here if it's there for the format's specific colour picker. */
            $defaulttgfgcolour = get_config('format_topcoll', 'defaulttgfgcolour');
            if ($defaulttgfgcolour[0] == '#') {
                $defaulttgfgcolour = substr($defaulttgfgcolour, 1);
            }
            $defaulttgfghvrcolour = get_config('format_topcoll', 'defaulttgfghvrcolour');
            if ($defaulttgfghvrcolour[0] == '#') {
                $defaulttgfghvrcolour = substr($defaulttgfghvrcolour, 1);
            }
            $defaulttgbgcolour = get_config('format_topcoll', 'defaulttgbgcolour');
            if ($defaulttgbgcolour[0] == '#') {
                $defaulttgbgcolour = substr($defaulttgbgcolour, 1);
            }
            $defaulttgbghvrcolour = get_config('format_topcoll', 'defaulttgbghvrcolour');
            if ($defaulttgbghvrcolour[0] == '#') {
                $defaulttgbghvrcolour = substr($defaulttgbghvrcolour, 1);
            }

            $courseconfig = get_config('moodlecourse');

            $courseid = $this->get_courseid();
            if ($courseid == 1) { // New course.
                 $defaultnumsections = $courseconfig->numsections;
            } else { // Existing course that may not have 'numsections' - see get_last_section().
                global $DB;
                $defaultnumsections = $DB->get_field_sql('SELECT max(section) from {course_sections}
                    WHERE course = ?', array($courseid));
            }
            $courseformatoptions = array(
                'numsections' => array(
                    'default' => $defaultnumsections,
                    'type' => PARAM_INT,
                ),
                'hiddensections' => array(
                    'default' => $courseconfig->hiddensections,
                    'type' => PARAM_INT,
                ),
                'displayinstructions' => array(
                    'default' => get_config('format_topcoll', 'defaultdisplayinstructions'),
                    'type' => PARAM_INT,
                ),
                'layoutelement' => array(
                    'default' => get_config('format_topcoll', 'defaultlayoutelement'),
                    'type' => PARAM_INT,
                ),
                'layoutstructure' => array(
                    'default' => get_config('format_topcoll', 'defaultlayoutstructure'),
                    'type' => PARAM_INT,
                ),
                'layoutcolumns' => array(
                    'default' => get_config('format_topcoll', 'defaultlayoutcolumns'),
                    'type' => PARAM_INT,
                ),
                'layoutcolumnorientation' => array(
                    'default' => get_config('format_topcoll', 'defaultlayoutcolumnorientation'),
                    'type' => PARAM_INT,
                ),
                'toggleallenabled' => array(
                    'default' => get_config('format_topcoll', 'defaulttoggleallenabled'),
                    'type' => PARAM_INT,
                ),
                'viewsinglesectionenabled' => array(
                    'default' => get_config('format_topcoll', 'defaultviewsinglesectionenabled'),
                    'type' => PARAM_INT,
                ),
                'togglealignment' => array(
                    'default' => get_config('format_topcoll', 'defaulttogglealignment'),
                    'type' => PARAM_INT,
                ),
                'toggleiconposition' => array(
                    'default' => get_config('format_topcoll', 'defaulttoggleiconposition'),
                    'type' => PARAM_INT,
                ),
                'toggleiconset' => array(
                    'default' => get_config('format_topcoll', 'defaulttoggleiconset'),
                    'type' => PARAM_ALPHA,
                ),
                'onesection' => array(
                    'default' => get_config('format_topcoll', 'defaultonesection'),
                    'type' => PARAM_INT,
                ),
                'toggleallhover' => array(
                    'default' => get_config('format_topcoll', 'defaulttoggleallhover'),
                    'type' => PARAM_INT,
                ),
                'toggleforegroundcolour' => array(
                    'default' => $defaulttgfgcolour,
                    'type' => PARAM_ALPHANUM,
                ),
                'toggleforegroundopacity' => array(
                    'default' => get_config('format_topcoll', 'defaulttgfgopacity'),
                    'type' => PARAM_RAW,
                ),
                'toggleforegroundhovercolour' => array(
                    'default' => $defaulttgfghvrcolour,
                    'type' => PARAM_ALPHANUM,
                ),
                'toggleforegroundhoveropacity' => array(
                    'default' => get_config('format_topcoll', 'defaulttgbghvropacity'),
                    'type' => PARAM_RAW,
                ),
                'togglebackgroundcolour' => array(
                    'default' => $defaulttgbgcolour,
                    'type' => PARAM_ALPHANUM,
                ),
                'togglebackgroundopacity' => array(
                    'default' => get_config('format_topcoll', 'defaulttgbgopacity'),
                    'type' => PARAM_RAW,
                ),
                'togglebackgroundhovercolour' => array(
                    'default' => $defaulttgbghvrcolour,
                    'type' => PARAM_ALPHANUM,
                ),
                'togglebackgroundhoveropacity' => array(
                    'default' => get_config('format_topcoll', 'defaulttgbghvropacity'),
                    'type' => PARAM_RAW,
                ),
                'showsectionsummary' => array(
                    'default' => get_config('format_topcoll', 'defaultshowsectionsummary'),
                    'type' => PARAM_INT,
                )
            );
        }
        if ($foreditform && !isset($courseformatoptions['displayinstructions']['label'])) {
            /* Note: Because 'admin_setting_configcolourpicker' in 'settings.php' needs to use a prefixing '#'
                     this needs to be stripped off here if it's there for the format's specific colour picker. */
            $defaulttgfgcolour = get_config('format_topcoll', 'defaulttgfgcolour');
            if ($defaulttgfgcolour[0] == '#') {
                $defaulttgfgcolour = substr($defaulttgfgcolour, 1);
            }
            $defaulttgfghvrcolour = get_config('format_topcoll', 'defaulttgfghvrcolour');
            if ($defaulttgfghvrcolour[0] == '#') {
                $defaulttgfghvrcolour = substr($defaulttgfghvrcolour, 1);
            }
            $defaulttgbgcolour = get_config('format_topcoll', 'defaulttgbgcolour');
            if ($defaulttgbgcolour[0] == '#') {
                $defaulttgbgcolour = substr($defaulttgbgcolour, 1);
            }
            $defaulttgbghvrcolour = get_config('format_topcoll', 'defaulttgbghvrcolour');
            if ($defaulttgbghvrcolour[0] == '#') {
                $defaulttgbghvrcolour = substr($defaulttgbghvrcolour, 1);
            }

            $context = $this->get_context();

            if (is_null($courseconfig)) {
                $courseconfig = get_config('moodlecourse');
            }
            $sectionmenu = array();
            for ($i = 0; $i <= $courseconfig->maxsections; $i++) {
                $sectionmenu[$i] = "$i";
            }
            $courseformatoptionsedit = array(
                'numsections' => array(
                    'label' => new lang_string('numbersections', 'format_topcoll'),
                    'element_type' => 'select',
                    'element_attributes' => array($sectionmenu),
                ),
                'hiddensections' => array(
                    'label' => new lang_string('hiddensections'),
                    'help' => 'hiddensections',
                    'help_component' => 'moodle',
                    'element_type' => 'select',
                    'element_attributes' => array(
                        array(0 => new lang_string('hiddensectionscollapsed'),
                              1 => new lang_string('hiddensectionsinvisible')
                        )
                    ),
                ),
                'displayinstructions' => array(
                    'label' => new lang_string('displayinstructions', 'format_topcoll'),
                    'help' => 'displayinstructions',
                    'help_component' => 'format_topcoll',
                    'element_type' => 'select',
                    'element_attributes' => array(
                        array(1 => new lang_string('no'),
                              2 => new lang_string('yes'))
                    )
                )
            );
            if (has_capability('format/topcoll:changelayout', $context)) {
                $courseformatoptionsedit['layoutelement'] = array(
                    'label' => new lang_string('setlayoutelements', 'format_topcoll'),
                    'help' => 'setlayoutelements',
                    'help_component' => 'format_topcoll',
                    'element_type' => 'select',
                    'element_attributes' => array( // In insertion order and not numeric for sorting purposes.
                        array(
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
                            7 => new lang_string('setlayout_no_additions', 'format_topcoll'))
                    )
                );
                $courseformatoptionsedit['layoutstructure'] = array(
                    'label' => new lang_string('setlayoutstructure', 'format_topcoll'),
                    'help' => 'setlayoutstructure',
                    'help_component' => 'format_topcoll',
                    'element_type' => 'select',
                    'element_attributes' => array(
                        array(
                            // Topic.
                            1 => new lang_string('setlayoutstructuretopic', 'format_topcoll'),
                            // Week.
                            2 => new lang_string('setlayoutstructureweek', 'format_topcoll'),
                            // Current Week First.
                            3 => new lang_string('setlayoutstructurelatweekfirst', 'format_topcoll'),
                            // Current Topic First.
                            4 => new lang_string('setlayoutstructurecurrenttopicfirst', 'format_topcoll'),
                            // Day.
                            5 => new lang_string('setlayoutstructureday', 'format_topcoll'))
                    )
                );
                $courseformatoptionsedit['layoutcolumns'] = array(
                    'label' => new lang_string('setlayoutcolumns', 'format_topcoll'),
                    'help' => 'setlayoutcolumns',
                    'help_component' => 'format_topcoll',
                    'element_type' => 'select',
                    'element_attributes' => array(
                        array(1 => new lang_string('one', 'format_topcoll'),   // Default.
                              2 => new lang_string('two', 'format_topcoll'),   // Two.
                              3 => new lang_string('three', 'format_topcoll'), // Three.
                              4 => new lang_string('four', 'format_topcoll'))  // Four.
                    )
                );
                $courseformatoptionsedit['layoutcolumnorientation'] = array(
                    'label' => new lang_string('setlayoutcolumnorientation', 'format_topcoll'),
                    'help' => 'setlayoutcolumnorientation',
                    'help_component' => 'format_topcoll',
                    'element_type' => 'select',
                    'element_attributes' => array(
                        array(1 => new lang_string('columnvertical', 'format_topcoll'),
                              2 => new lang_string('columnhorizontal', 'format_topcoll')) // Default.
                    )
                );
                $courseformatoptionsedit['toggleallenabled'] = array(
                    'label' => new lang_string('settoggleallenabled', 'format_topcoll'),
                    'help' => 'settoggleallenabled',
                    'help_component' => 'format_topcoll',
                    'element_type' => 'select',
                    'element_attributes' => array(
                        array(1 => new lang_string('no'),
                              2 => new lang_string('yes'))
                    )
                );
                $courseformatoptionsedit['viewsinglesectionenabled'] = array(
                    'label' => new lang_string('setviewsinglesectionenabled', 'format_topcoll'),
                    'help' => 'setviewsinglesectionenabled',
                    'help_component' => 'format_topcoll',
                    'element_type' => 'select',
                    'element_attributes' => array(
                        array(1 => new lang_string('no'),
                              2 => new lang_string('yes'))
                    )
                );
                $courseformatoptionsedit['toggleiconposition'] = array(
                    'label' => new lang_string('settoggleiconposition', 'format_topcoll'),
                    'help' => 'settoggleiconposition',
                    'help_component' => 'format_topcoll',
                    'element_type' => 'select',
                    'element_attributes' => array(
                        array(1 => new lang_string('left', 'format_topcoll'),   // Left.
                              2 => new lang_string('right', 'format_topcoll'))  // Right.
                    )
                );
                $courseformatoptionsedit['onesection'] = array(
                    'label' => new lang_string('onesection', 'format_topcoll'),
                    'help' => 'onesection',
                    'help_component' => 'format_topcoll',
                    'element_type' => 'select',
                    'element_attributes' => array(
                        array(1 => new lang_string('no'),
                              2 => new lang_string('yes'))
                    )
                );
                $courseformatoptionsedit['showsectionsummary'] = array(
                    'label' => new lang_string('setshowsectionsummary', 'format_topcoll'),
                    'help' => 'setshowsectionsummary',
                    'help_component' => 'format_topcoll',
                    'element_type' => 'select',
                    'element_attributes' => array(
                        array(1 => new lang_string('no'),
                              2 => new lang_string('yes'))
                    )
                );
            } else {
                $courseformatoptionsedit['layoutelement'] = array(
                    'label' => get_config('format_topcoll', 'defaultlayoutelement'), 'element_type' => 'hidden');
                $courseformatoptionsedit['layoutstructure'] = array(
                    'label' => get_config('format_topcoll', 'defaultlayoutstructure'), 'element_type' => 'hidden');
                $courseformatoptionsedit['layoutcolumns'] = array(
                    'label' => get_config('format_topcoll', 'defaultlayoutcolumns'), 'element_type' => 'hidden');
                $courseformatoptionsedit['layoutcolumnorientation'] = array(
                    'label' => get_config('format_topcoll', 'defaultlayoutcolumnorientation'), 'element_type' => 'hidden');
                $courseformatoptionsedit['toggleallenabled'] = array(
                    'label' => get_config('format_topcoll', 'defaulttoggleallenabled'), 'element_type' => 'hidden');
                $courseformatoptionsedit['viewsinglesectionenabled'] = array(
                    'label' => get_config('format_topcoll', 'defaultviewsinglesectionenabled'), 'element_type' => 'hidden');
                $courseformatoptionsedit['toggleiconposition'] = array(
                    'label' => get_config('format_topcoll', 'defaulttoggleiconposition'), 'element_type' => 'hidden');
                $courseformatoptionsedit['onesection'] = array(
                    'label' => get_config('format_topcoll', 'defaultonesection'), 'element_type' => 'hidden');
                $courseformatoptionsedit['showsectionsummary'] = array(
                    'label' => get_config('format_topcoll', 'defaultshowsectionsummary'), 'element_type' => 'hidden');
            }

            if (has_capability('format/topcoll:changetogglealignment', $context)) {
                $courseformatoptionsedit['togglealignment'] = array(
                    'label' => new lang_string('settogglealignment', 'format_topcoll'),
                    'help' => 'settogglealignment',
                    'help_component' => 'format_topcoll',
                    'element_type' => 'select',
                    'element_attributes' => array(
                        array(1 => new lang_string('left', 'format_topcoll'),   // Left.
                              2 => new lang_string('center', 'format_topcoll'), // Centre.
                              3 => new lang_string('right', 'format_topcoll'))  // Right.
                    )
                );
            } else {
                $courseformatoptionsedit['togglealignment'] = array(
                    'label' => get_config('format_topcoll', 'defaulttogglealignment'), 'element_type' => 'hidden');
            }

            if (has_capability('format/topcoll:changetoggleiconset', $context)) {
                $courseformatoptionsedit['toggleiconset'] = array(
                    'label' => new lang_string('settoggleiconset', 'format_topcoll'),
                    'help' => 'settoggleiconset',
                    'help_component' => 'format_topcoll',
                    'element_type' => 'select',
                    'element_attributes' => array(
                        array(
                            'arrow' => new lang_string('arrow', 'format_topcoll'),               // Arrow icon set.
                            'bulb' => new lang_string('bulb', 'format_topcoll'),                 // Bulb icon set.
                            'cloud' => new lang_string('cloud', 'format_topcoll'),               // Cloud icon set.
                            'eye' => new lang_string('eye', 'format_topcoll'),                   // Eye icon set.
                            'folder' => new lang_string('folder', 'format_topcoll'),             // Folder icon set.
                            'groundsignal' => new lang_string('groundsignal', 'format_topcoll'), // Ground signal set.
                            'led' => new lang_string('led', 'format_topcoll'),                   // LED icon set.
                            'point' => new lang_string('point', 'format_topcoll'),               // Point icon set.
                            'power' => new lang_string('power', 'format_topcoll'),               // Power icon set.
                            'radio' => new lang_string('radio', 'format_topcoll'),               // Radio icon set.
                            'smiley' => new lang_string('smiley', 'format_topcoll'),             // Smiley icon set.
                            'square' => new lang_string('square', 'format_topcoll'),             // Square icon set.
                            'sunmoon' => new lang_string('sunmoon', 'format_topcoll'),           // Sun / Moon icon set.
                            'switch' => new lang_string('switch', 'format_topcoll'))             // Switch icon set.
                    )
                );
                $courseformatoptionsedit['toggleallhover'] = array(
                    'label' => new lang_string('settoggleallhover', 'format_topcoll'),
                    'help' => 'settoggleallhover',
                    'help_component' => 'format_topcoll',
                    'element_type' => 'select',
                    'element_attributes' => array(
                        array(1 => new lang_string('no'),
                              2 => new lang_string('yes'))
                    )
                );
            } else {
                $courseformatoptionsedit['toggleiconset'] = array(
                    'label' => get_config('format_topcoll', 'defaulttoggleiconset'), 'element_type' => 'hidden');
                $courseformatoptionsedit['toggleallhover'] = array(
                    'label' => get_config('format_topcoll', 'defaulttoggleallhover'), 'element_type' => 'hidden');
            }

            if (has_capability('format/topcoll:changecolour', $context)) {
                $opacityvalues = array(
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
                    '1.0' => '1.0'
                );
                $courseformatoptionsedit['toggleforegroundcolour'] = array(
                    'label' => new lang_string('settoggleforegroundcolour', 'format_topcoll'),
                    'help' => 'settoggleforegroundcolour',
                    'help_component' => 'format_topcoll',
                    'element_type' => 'tccolourpopup',
                    'element_attributes' => array(
                        array('value' => $defaulttgfgcolour)
                    )
                );
                $courseformatoptionsedit['toggleforegroundopacity'] = array(
                    'label' => new lang_string('settoggleforegroundopacity', 'format_topcoll'),
                    'help' => 'settoggleforegroundopacity',
                    'help_component' => 'format_topcoll',
                    'element_type' => 'select',
                    'element_attributes' => array($opacityvalues)
                );
                $courseformatoptionsedit['toggleforegroundhovercolour'] = array(
                    'label' => new lang_string('settoggleforegroundhovercolour', 'format_topcoll'),
                    'help' => 'settoggleforegroundhovercolour',
                    'help_component' => 'format_topcoll',
                    'element_type' => 'tccolourpopup',
                    'element_attributes' => array(
                        array('value' => $defaulttgfghvrcolour)
                    )
                );
                $courseformatoptionsedit['toggleforegroundhoveropacity'] = array(
                    'label' => new lang_string('settoggleforegroundhoveropacity', 'format_topcoll'),
                    'help' => 'settoggleforegroundhoveropacity',
                    'help_component' => 'format_topcoll',
                    'element_type' => 'select',
                    'element_attributes' => array($opacityvalues)
                );
                $courseformatoptionsedit['togglebackgroundcolour'] = array(
                    'label' => new lang_string('settogglebackgroundcolour', 'format_topcoll'),
                    'help' => 'settogglebackgroundcolour',
                    'help_component' => 'format_topcoll',
                    'element_type' => 'tccolourpopup',
                    'element_attributes' => array(
                        array('value' => $defaulttgbgcolour)
                    )
                );
                $courseformatoptionsedit['togglebackgroundopacity'] = array(
                    'label' => new lang_string('settogglebackgroundopacity', 'format_topcoll'),
                    'help' => 'settogglebackgroundopacity',
                    'help_component' => 'format_topcoll',
                    'element_type' => 'select',
                    'element_attributes' => array($opacityvalues)
                );
                $courseformatoptionsedit['togglebackgroundhovercolour'] = array(
                    'label' => new lang_string('settogglebackgroundhovercolour', 'format_topcoll'),
                    'help' => 'settogglebackgroundhovercolour',
                    'help_component' => 'format_topcoll',
                    'element_type' => 'tccolourpopup',
                    'element_attributes' => array(
                        array('value' => $defaulttgbghvrcolour)
                    )
                );
                $courseformatoptionsedit['togglebackgroundhoveropacity'] = array(
                    'label' => new lang_string('settogglebackgroundhoveropacity', 'format_topcoll'),
                    'help' => 'settogglebackgroundhoveropacity',
                    'help_component' => 'format_topcoll',
                    'element_type' => 'select',
                    'element_attributes' => array($opacityvalues)
                );
            } else {
                $courseformatoptionsedit['toggleforegroundcolour'] = array(
                    'label' => $defaulttgfgcolour, 'element_type' => 'hidden');
                $courseformatoptionsedit['toggleforegroundopacity'] = array(
                    'label' => get_config('format_topcoll', 'defaulttgfgopacity'), 'element_type' => 'hidden');
                $courseformatoptionsedit['toggleforegroundhovercolour'] = array(
                    'label' => $defaulttgfghvrcolour, 'element_type' => 'hidden');
                $courseformatoptionsedit['toggleforegroundhoveropacity'] = array(
                    'label' => get_config('format_topcoll', 'defaulttgfghvropacity'), 'element_type' => 'hidden');
                $courseformatoptionsedit['togglebackgroundcolour'] = array(
                    'label' => $defaulttgbgcolour, 'element_type' => 'hidden');
                $courseformatoptionsedit['togglebackgroundopacity'] = array(
                    'label' => get_config('format_topcoll', 'defaulttgbgopacity'), 'element_type' => 'hidden');
                $courseformatoptionsedit['togglebackgroundhovercolour'] = array(
                    'label' => $defaulttgbghvrcolour, 'element_type' => 'hidden');
                $courseformatoptionsedit['togglebackgroundhoveropacity'] = array(
                    'label' => get_config('format_topcoll', 'defaulttgbghvropacity'), 'element_type' => 'hidden');
            }
            $readme = new moodle_url('/course/format/topcoll/Readme.md');
            $readme = html_writer::link($readme, 'Readme.md', array('target' => '_blank'));
            $courseformatoptionsedit['readme'] = array(
                    'label' => get_string('readme_title', 'format_topcoll'),
                    'element_type' => 'static',
                    'element_attributes' => array(get_string('readme_desc', 'format_topcoll', array('url' => $readme)))
                );
            $courseformatoptions = array_merge_recursive($courseformatoptions, $courseformatoptionsedit);
        }
        return $courseformatoptions;
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
        global $CFG, $OUTPUT, $PAGE, $USER;
        MoodleQuickForm::registerElementType('tccolourpopup', "$CFG->dirroot/course/format/topcoll/js/tc_colourpopup.php",
                                             'MoodleQuickForm_tccolourpopup');

        $elements = parent::create_edit_form_elements($mform, $forsection);

        // Increase the number of sections combo box values if the user has increased the number of sections
        // using the icon on the course page beyond course 'maxsections' or course 'maxsections' has been
        // reduced below the number of sections already set for the course on the site administration course
        // defaults page.  This is so that the number of sections is not reduced leaving unintended orphaned
        // activities / resources.
        if (!$forsection) {
            $maxsections = get_config('moodlecourse', 'maxsections');
            $numsections = $mform->getElementValue('numsections');
            $numsections = $numsections[0];
            if ($numsections > $maxsections) {
                $element = $mform->getElement('numsections');
                for ($i = $maxsections + 1; $i <= $numsections; $i++) {
                    $element->addOption("$i", $i);
                }
            }
        }

        $context = $this->get_context();

        $changelayout = has_capability('format/topcoll:changelayout', $context);
        $changecolour = has_capability('format/topcoll:changecolour', $context);
        $changetogglealignment = has_capability('format/topcoll:changetogglealignment', $context);
        $changetoggleiconset = has_capability('format/topcoll:changetoggleiconset', $context);
        $resetall = is_siteadmin($USER); // Site admins only.

        $elements[] = $mform->addElement('header', 'ctreset', get_string('ctreset', 'format_topcoll'));
        $mform->addHelpButton('ctreset', 'ctreset', 'format_topcoll', '', true);

        $bsfour = false;
        if (strcmp($PAGE->theme->name, 'boost') === 0) {
            $bsfour = true;
        } else if (!empty($PAGE->theme->parents)) {
            if (in_array('boost', $PAGE->theme->parents) === true) {
                $bsfour = true;
            }
        } else if (strcmp($PAGE->theme->name, 'foundation') === 0) {
            $bsfour = true;
        }

        $resetelements = array();
        if ($bsfour) {
            $checkboxname = get_string('resetdisplayinstructions', 'format_topcoll');
            $resetelements[] = & $mform->createElement('checkbox', 'resetdisplayinstructions', '', $checkboxname);
            $resetelements[] = & $mform->createElement('html', $OUTPUT->help_icon('resetdisplayinstructions', 'format_topcoll'));
        } else {
            $checkboxname = get_string('resetdisplayinstructions', 'format_topcoll').
                $OUTPUT->help_icon('resetdisplayinstructions', 'format_topcoll');
            $resetelements[] =& $mform->createElement('checkbox', 'resetdisplayinstructions', '', $checkboxname);
        }

        if ($changelayout) {
            if ($bsfour) {
                $checkboxname = get_string('resetlayout', 'format_topcoll');
                $resetelements[] = & $mform->createElement('checkbox', 'resetlayout', '', $checkboxname);
                $resetelements[] = & $mform->createElement('html', $OUTPUT->help_icon('resetlayout', 'format_topcoll'));
            } else {
                $checkboxname = get_string('resetlayout', 'format_topcoll').$OUTPUT->help_icon('resetlayout', 'format_topcoll');
                $resetelements[] =& $mform->createElement('checkbox', 'resetlayout', '', $checkboxname);
            }
        }

        if ($changecolour) {
            if ($bsfour) {
                $checkboxname = get_string('resetcolour', 'format_topcoll');
                $resetelements[] = & $mform->createElement('checkbox', 'resetcolour', '', $checkboxname);
                $resetelements[] = & $mform->createElement('html', $OUTPUT->help_icon('resetcolour', 'format_topcoll'));
            } else {
                $checkboxname = get_string('resetcolour', 'format_topcoll').$OUTPUT->help_icon('resetcolour', 'format_topcoll');
                $resetelements[] =& $mform->createElement('checkbox', 'resetcolour', '', $checkboxname);
            }
        }

        if ($changetogglealignment) {
            if ($bsfour) {
                $checkboxname = get_string('resettogglealignment', 'format_topcoll');
                $resetelements[] = & $mform->createElement('checkbox', 'resettogglealignment', '', $checkboxname);
                $resetelements[] = & $mform->createElement('html', $OUTPUT->help_icon('resettogglealignment', 'format_topcoll'));
            } else {
                $checkboxname = get_string('resettogglealignment', 'format_topcoll').
                    $OUTPUT->help_icon('resettogglealignment', 'format_topcoll');
                $resetelements[] =& $mform->createElement('checkbox', 'resettogglealignment', '', $checkboxname);
            }
        }

        if ($changetoggleiconset) {
            if ($bsfour) {
                $checkboxname = get_string('resettoggleiconset', 'format_topcoll');
                $resetelements[] = & $mform->createElement('checkbox', 'resettoggleiconset', '', $checkboxname);
                $resetelements[] = & $mform->createElement('html', $OUTPUT->help_icon('resettoggleiconset', 'format_topcoll'));
            } else {
                $checkboxname = get_string('resettoggleiconset', 'format_topcoll').
                    $OUTPUT->help_icon('resettoggleiconset', 'format_topcoll');
                $resetelements[] =& $mform->createElement('checkbox', 'resettoggleiconset', '', $checkboxname);
            }
        }
        $elements[] = $mform->addGroup($resetelements, 'resetgroup', get_string('resetgrp', 'format_topcoll'), null, false);

        if ($resetall) {
            $resetallelements = array();

            if ($bsfour) {
                $checkboxname = get_string('resetalldisplayinstructions', 'format_topcoll');
                $resetallelements[] = & $mform->createElement('checkbox', 'resetalldisplayinstructions', '', $checkboxname);
                $resetallelements[] = & $mform->createElement('html', $OUTPUT->help_icon('resetalldisplayinstructions', 'format_topcoll'));

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
            } else {
                $checkboxname = get_string('resetalldisplayinstructions', 'format_topcoll').
                    $OUTPUT->help_icon('resetalldisplayinstructions', 'format_topcoll');
                $resetallelements[] =& $mform->createElement('checkbox', 'resetalldisplayinstructions', '', $checkboxname);

                $checkboxname = get_string('resetalllayout', 'format_topcoll').
                    $OUTPUT->help_icon('resetalllayout', 'format_topcoll');
                $resetallelements[] =& $mform->createElement('checkbox', 'resetalllayout', '', $checkboxname);

                $checkboxname = get_string('resetallcolour', 'format_topcoll').
                    $OUTPUT->help_icon('resetallcolour', 'format_topcoll');
                $resetallelements[] =& $mform->createElement('checkbox', 'resetallcolour', '', $checkboxname);

                $checkboxname = get_string('resetalltogglealignment', 'format_topcoll').
                    $OUTPUT->help_icon('resetalltogglealignment', 'format_topcoll');
                $resetallelements[] =& $mform->createElement('checkbox', 'resetalltogglealignment', '', $checkboxname);

                $checkboxname = get_string('resetalltoggleiconset', 'format_topcoll').
                    $OUTPUT->help_icon('resetalltoggleiconset', 'format_topcoll');
                $resetallelements[] =& $mform->createElement('checkbox', 'resetalltoggleiconset', '', $checkboxname);
            }
            $elements[] = $mform->addGroup($resetallelements, 'resetallgroup',
                get_string('resetallgrp', 'format_topcoll'), null, false);
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
        $retr = array();

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
        if (preg_match('/^#?([[:xdigit:]]{3}){1,2}$/', $data)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Updates format options for a course
     *
     * In case if course format was changed to 'Collapsed Topics', we try to copy options
     * 'coursedisplay', 'numsections' and 'hiddensections' from the previous format.
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
        $resetalldisplayinstructions = false;
        $resetalllayout = false;
        $resetallcolour = false;
        $resetalltogglealignment = false;
        $resetalltoggleiconset = false;
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

        $data = (array) $data;
        if ($oldcourse !== null) {
            $oldcourse = (array) $oldcourse;
            $options = $this->course_format_options();
            foreach ($options as $key => $unused) {
                if (!array_key_exists($key, $data)) {
                    if (array_key_exists($key, $oldcourse)) {
                        $data[$key] = $oldcourse[$key];
                    } else if ($key === 'numsections') {
                        // If previous format does not have the field 'numsections'
                        // and $data['numsections'] is not set,
                        // we fill it with the maximum section number from the DB
                        $maxsection = $DB->get_field_sql('SELECT max(section) from {course_sections}
                            WHERE course = ?', array($this->courseid));
                        if ($maxsection) {
                            // If there are no sections, or just default 0-section, 'numsections' will be set to default
                            $data['numsections'] = $maxsection;
                        }
                    }
                }
            }
        }

        $changes = $this->update_format_options($data);

        if ($changes && array_key_exists('numsections', $data)) {
            // If the numsections was decreased, try to completely delete the orphaned sections (unless they are not empty).
            $numsections = (int)$data['numsections'];
            $maxsection = $DB->get_field_sql('SELECT max(section) from {course_sections}
                        WHERE course = ?', array($this->courseid));
            for ($sectionnum = $maxsection; $sectionnum > $numsections; $sectionnum--) {
                if (!$this->delete_section($sectionnum, false)) {
                    break;
                }
            }
        }

        // Now we can do the reset.
        if (($resetalldisplayinstructions) ||
            ($resetalllayout) ||
            ($resetallcolour) ||
            ($resetalltogglealignment) ||
            ($resetalltoggleiconset)) {
            $this->reset_topcoll_setting(0, $resetalldisplayinstructions, $resetalllayout, $resetallcolour,
                $resetalltogglealignment, $resetalltoggleiconset);
            $changes = true;
        } else if (($resetdisplayinstructions) ||
            ($resetlayout) ||
            ($resetcolour) ||
            ($resettogglealignment) ||
            ($resettoggleiconset)) {
            $this->reset_topcoll_setting($this->courseid, $resetdisplayinstructions, $resetlayout, $resetcolour,
                $resettogglealignment, $resettoggleiconset);
            $changes = true;
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
     * Resets the format setting to the default.
     * @param int $courseid If not 0, then a specific course to reset.
     * @param int $displayinstructions If true, reset the display instructions to the default in the settings for the format.
     * @param int $layout If true, reset the layout to the default in the settings for the format.
     * @param int $colour If true, reset the colour to the default in the settings for the format.
     * @param int $togglealignment If true, reset the toggle alignment to the default in the settings for the format.
     * @param int $toggleiconset If true, reset the toggle icon set to the default in the settings for the format.
     */
    public function reset_topcoll_setting($courseid, $displayinstructions, $layout, $colour, $togglealignment, $toggleiconset) {
        global $DB, $USER;

        $context = $this->get_context();

        $currentcourseid = 0;
        if ($courseid == 0) {
            $records = $DB->get_records('course_format_options', array('format' => $this->format), '', 'id,courseid');
        } else {
            $records = $DB->get_records('course_format_options', array('courseid' => $courseid, 'format' => $this->format),
                '', 'id,courseid');
        }

        $resetallifall = ((is_siteadmin($USER)) || ($courseid != 0)); // Will be true if reset all capability or a single course.

        $updatedata = array();
        $updatedisplayinstructions = false;
        $updatelayout = false;
        $updatetogglealignment = false;
        $updatecolour = false;
        $updatetoggleiconset = false;
        if ($displayinstructions && $resetallifall) {
            $updatedata['displayinstructions'] = get_config('format_topcoll', 'defaultdisplayinstructions');
            $updatedisplayinstructions = true;
        }
        if ($layout && has_capability('format/topcoll:changelayout', $context) && $resetallifall) {
            $updatedata['layoutelement'] = get_config('format_topcoll', 'defaultlayoutelement');
            $updatedata['layoutstructure'] = get_config('format_topcoll', 'defaultlayoutstructure');
            $updatedata['layoutcolumns'] = get_config('format_topcoll', 'defaultlayoutcolumns');
            $updatedata['layoutcolumnorientation'] = get_config('format_topcoll', 'defaultlayoutcolumnorientation');
            $updatedata['toggleallenabled'] = get_config('format_topcoll', 'defaulttoggleallenabled');
            $updatedata['viewsinglesectionenabled'] = get_config('format_topcoll', 'defaultviewsinglesectionenabled');
            $updatedata['toggleiconposition'] = get_config('format_topcoll', 'defaulttoggleiconposition');
            $updatedata['onesection'] = get_config('format_topcoll', 'defaultonesection');
            $updatedata['showsectionsummary'] = get_config('format_topcoll', 'defaultshowsectionsummary');
            $updatelayout = true;
        }
        if ($togglealignment && has_capability('format/topcoll:changetogglealignment', $context) && $resetallifall) {
            $updatedata['togglealignment'] = get_config('format_topcoll', 'defaulttogglealignment');
            $updatetogglealignment = true;
        }
        if ($colour && has_capability('format/topcoll:changecolour', $context) && $resetallifall) {
            $updatedata['toggleforegroundcolour'] = get_config('format_topcoll', 'defaulttgfgcolour');
            $updatedata['toggleforegroundopacity'] = get_config('format_topcoll', 'defaulttgfgopacity');
            $updatedata['toggleforegroundhovercolour'] = get_config('format_topcoll', 'defaulttgfghvrcolour');
            $updatedata['toggleforegroundhoveropacity'] = get_config('format_topcoll', 'defaulttgfghvropacity');
            $updatedata['togglebackgroundcolour'] = get_config('format_topcoll', 'defaulttgbgcolour');
            $updatedata['togglebackgroundopacity'] = get_config('format_topcoll', 'defaulttgbgopacity');
            $updatedata['togglebackgroundhovercolour'] = get_config('format_topcoll', 'defaulttgbghvrcolour');
            $updatedata['togglebackgroundhoveropacity'] = get_config('format_topcoll', 'defaulttgbghvropacity');
            $updatecolour = true;
        }
        if ($toggleiconset && has_capability('format/topcoll:changetoggleiconset', $context) && $resetallifall) {
            $updatedata['toggleiconset'] = get_config('format_topcoll', 'defaulttoggleiconset');
            $updatedata['toggleallhover'] = get_config('format_topcoll', 'defaulttoggleallhover');
            $updatetoggleiconset = true;
        }

        foreach ($records as $record) {
            if ($currentcourseid != $record->courseid) {
                $currentcourseid = $record->courseid; // Only do once per course.
                if (($updatedisplayinstructions) ||
                    ($updatelayout) ||
                    ($updatetogglealignment) ||
                    ($updatecolour) ||
                    ($updatetoggleiconset)) {
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
    public function restore_topcoll_setting($courseid, $layoutelement, $layoutstructure, $layoutcolumns, $tgfgcolour,
        $tgbgcolour, $tgbghvrcolour) {
        $currentcourseid = $this->courseid;  // Save for later - stack data model.
        $this->courseid = $courseid;
        // Create data array.
        $data = array(
            'layoutelement' => $layoutelement,
            'layoutstructure' => $layoutstructure,
            'layoutcolumns' => $layoutcolumns,
            'toggleforegroundcolour' => $tgfgcolour,
            'togglebackgroundcolour' => $tgbgcolour,
            'togglebackgroundhovercolour' => $tgbghvrcolour);

        $lco = get_config('format_topcoll', 'defaultlayoutcolumnorientation');
        if (empty($lco)) {
            // Upgrading from M2.3 and the defaults in 'settings.php' have not been processed at this time.
            // Defaults taken from 'settings.php'.
            $data['displayinstructions'] = get_config('format_topcoll', 'defaultdisplayinstructions');
            $data['layoutcolumnorientation'] = get_config('format_topcoll', 'defaultlayoutcolumnorientation');
            $data['toggleallenabled'] = get_config('format_topcoll', 'defaulttoggleallenabled');
            $data['viewsinglesectionenabled'] = get_config('format_topcoll', 'defaultviewsinglesectionenabled');
            $data['showsectionsummary'] = get_config('format_topcoll', 'defaultshowsectionsummary');
            $data['togglealignment'] = get_config('format_topcoll', 'defaulttogglealignment');
            $data['toggleallhover'] = get_config('format_topcoll', 'defaulttoggleallhover');
            $data['toggleiconposition'] = get_config('format_topcoll', 'defaulttoggleiconposition');
            $data['toggleiconset'] = get_config('format_topcoll', 'defaulttoggleiconset');
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
        $data = array('layoutcolumns' => $layoutcolumns);

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

    private function get_context() {
        global $SITE;

        if ($SITE->id == $this->courseid) {
            // Use the context of the page which should be the course category.
            global $PAGE;
            return $PAGE->context;
        } else {
            return context_course::instance($this->courseid);
        }
    }

    /**
     * Prepares the templateable object to display section name.
     *
     * @param \section_info|\stdClass $section
     * @param bool $linkifneeded
     * @param bool $editable
     * @param null|lang_string|string $edithint
     * @param null|lang_string|string $editlabel
     * @return \core\output\inplace_editable
     */
    public function inplace_editable_render_section_name($section, $linkifneeded = true,
                                                         $editable = null, $edithint = null, $editlabel = null) {
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
     * Indicates whether the course format supports the creation of a news forum.
     *
     * @return bool
     */
    public function supports_news() {
        return true;
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
        // Allow the third visibility state inside visible sections or in section 0, not allow in orphaned sections.
        return !$section->section || ($section->visible && $section->section <= $this->get_course()->numsections);
    }

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
        $rv['section_availability'] = $renderer->section_availability($this->get_section($section));
        return $rv;
    }
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
            array($itemid, 'topcoll'), MUST_EXIST);
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
