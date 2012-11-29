<?php

/**
 * Collapsed Topics Information
 *
 * A topic based format that solves the issue of the 'Scroll of Death' when a course has many topics. All topics
 * except zero have a toggle that displays that topic. One or more topics can be displayed at any given time.
 * Toggles are persistent on a per browser session per course basis but can be made to persist longer by a small
 * code change. Full installation instructions, code adaptions and credits are included in the 'Readme.txt' file.
 *
 * @package    course/format
 * @subpackage topcoll
 * @version    See the value of '$plugin->version' in version.php.
 * @copyright  &copy; 2012-onwards G J Barnard in respect to modifications of standard topics format.
 * @author     G J Barnard - gjbarnard at gmail dot com and {@link http://moodle.org/user/profile.php?id=442195}
 * @author     Based on code originally written by Dan Poltawski.
 * @link       http://docs.moodle.org/en/Collapsed_Topics_course_format
 * @license    http://www.gnu.org/copyleft/gpl.html GNU Public License
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.

 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.

 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */
require_once($CFG->dirroot . '/course/format/topcoll/tcconfig.php'); // For Collaped Topics defaults.
require_once($CFG->dirroot . '/course/format/lib.php'); // For format_base.

class format_topcoll extends format_base {

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
 * @param stdClass $section The section.
 * @return string The section name.
 */
    public function get_section_name($section) {
        $course = $this->get_course();
        $section = $this->get_section($section);
    // We can't add a node without any text
    if ((string) $section->name !== '') {
        return format_string($section->name, true, array('context' => context_course::instance($course->id)));
    } else if ($section->section == 0) {
        return get_string('section0name', 'format_topcoll');
    } else {
        global $tcsetting;
        if (empty($tcsetting) == true) {
            //$tcsetting = get_topcoll_setting($course->id); // CONTRIB-3378
			$tcsetting = $this->get_format_options();
			//print_r($tcsetting);
        }
        if (($tcsetting['layoutstructure'] == 1) || ($tcsetting['layoutstructure'] == 4)) {
            return get_string('sectionname', 'format_topcoll') . ' ' . $section->section;
        } else {
            $dateformat = ' ' . get_string('strftimedateshort');
            if ($tcsetting['layoutstructure'] == 5) {
                $day = format_topcoll_get_section_day($section, $course);

                $weekday = userdate($day, $dateformat);
                return $weekday;
            } else {
                $dates = format_topcoll_get_section_dates($section, $course);

                // We subtract 24 hours for display purposes.
                $dates->end = ($dates->end - 86400);

                $weekday = userdate($dates->start, $dateformat);
                $endweekday = userdate($dates->end, $dateformat);
                return $weekday . ' - ' . $endweekday;
            }
        }
    }
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
                $usercoursedisplay = $course->coursedisplay;
            }
            if ($sectionno != 0 && $usercoursedisplay == COURSE_DISPLAY_MULTIPAGE) {
                $url->param('section', $sectionno);
            } else {
                if (!empty($options['navigation'])) {
                    return null;
                }
                $url->set_anchor('section-'.$sectionno);
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
        $ajaxsupport->testedbrowsers = array('MSIE' => 8.0, 'Gecko' => 20061111, 'Opera' => 9.0, 'Safari' => 531, 'Chrome' => 6.0);
        return $ajaxsupport;
    }

    /**
     * Custom action after section has been moved in AJAX mode
     *
     * Used in course/rest.php
     *
     * @return array This will be passed in ajax respose
     */
    function ajax_section_move() {
        global $PAGE;
        $titles = array();
        $course = $this->get_course();
        $modinfo = get_fast_modinfo($course);
        $renderer = $this->get_renderer($PAGE);
        if ($renderer && ($sections = $modinfo->get_section_info_all())) {
            foreach ($sections as $number => $section) {
                $titles[$number] = $renderer->section_title($section, $course);
            }
        }
        return array('sectiontitles' => $titles, 'action' => 'move');
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

    /**
     * Definitions of the additional options that this course format uses for course
     *
     * Collapsed Topics format uses the following options (until extras are migrated):
     * - coursedisplay
     * - numsections
     * - hiddensections
     *
     * @param bool $foreditform
     * @return array of options
     */
    public function course_format_options($foreditform = false) {
        static $courseformatoptions = false;
		global $TCCFG;
		//$currentoptions = $this->get_format_options();
		//print_object($currentoptions);
		//print_object($this);
		//if (!empty($this->formatoptions)) {
		//print_object($this->formatoptions[0]);
		//}
        if ($courseformatoptions === false) {
            $courseconfig = get_config('moodlecourse');
            $courseformatoptions = array(
                'numsections' => array(
                    'default' => $courseconfig->numsections,
                    'type' => PARAM_INT,
                ),
                'hiddensections' => array(
                    'default' => $courseconfig->hiddensections,
                    'type' => PARAM_INT,
                ),
                'coursedisplay' => array(
                    'default' => $courseconfig->coursedisplay,
                    'type' => PARAM_INT,
                ),
                'layoutelement' => array(
                    'default' => $TCCFG->defaultlayoutelement,
                    'type' => PARAM_INT,
                ),
                'layoutstructure' => array(
                    'default' => $TCCFG->defaultlayoutstructure,
                    'type' => PARAM_INT,
                ),
                'layoutcolumns' => array(
                    'default' => $TCCFG->defaultlayoutcolumns,
                    'type' => PARAM_INT,
                ),
                'toggleforegroundcolour' => array(
                    'default' => $TCCFG->defaulttgfgcolour,
                    'type' => PARAM_ALPHANUM,
                ),
                'togglebackgroundcolour' => array(
                    'default' => $TCCFG->defaulttgbgcolour,
                    'type' => PARAM_ALPHANUM,
                ),
                'togglebackgroundhovercolour' => array(
                    'default' => $TCCFG->defaulttgbghvrcolour,
                    'type' => PARAM_ALPHANUM,
                ),
            );
        }
        if ($foreditform && !isset($courseformatoptions['coursedisplay']['label'])) {
		    global $USER;
            $courseconfig = get_config('moodlecourse');
            $sectionmenu = array();
            for ($i = 0; $i <= $courseconfig->maxsections; $i++) {
                $sectionmenu[$i] = "$i";
            }
            $courseformatoptionsedit = array(
                'numsections' => array(
                    'label' => new lang_string('numbersections','format_topcoll'),
                    'element_type' => 'select',
                    'element_attributes' => array($sectionmenu),
                ),
                'hiddensections' => array(
                    'label' => new lang_string('hiddensections'),
                    'help' => 'hiddensections',
                    'help_component' => 'moodle',
                    'element_type' => 'select',
                    'element_attributes' => array(
                        array(
                            0 => new lang_string('hiddensectionscollapsed'),
                            1 => new lang_string('hiddensectionsinvisible')
                        )
                    ),
                ),
                'coursedisplay' => array(
                    'label' => new lang_string('coursedisplay'),
                    'element_type' => 'select',
                    'element_attributes' => array(
                        array(
                            COURSE_DISPLAY_SINGLEPAGE => new lang_string('coursedisplay_single'),
                            COURSE_DISPLAY_MULTIPAGE => new lang_string('coursedisplay_multi')
                        )
                    ),
                    'help' => 'coursedisplay',
                    'help_component' => 'moodle',
                ),
				'layoutelement' => array(
                    'label' => new lang_string('setlayoutelements', 'format_topcoll'),
                    'help' => 'setlayoutelements',
                    'help_component' => 'format_topcoll',
                    'element_type' => 'select',
                    'element_attributes' => array(
                array(1 => new lang_string('setlayout_default', 'format_topcoll'), // Default.
                    2 => new lang_string('setlayout_no_toggle_section_x', 'format_topcoll'), // No 'Topic x' / 'Week x'.
                    3 => new lang_string('setlayout_no_section_no', 'format_topcoll'), // No section number.
                    4 => new lang_string('setlayout_no_toggle_section_x_section_no', 'format_topcoll'), // No 'Topic x' / 'Week x' and no section number.
                    5 => new lang_string('setlayout_no_toggle_word', 'format_topcoll'), // No 'Toggle' word.
                    6 => new lang_string('setlayout_no_toggle_word_toggle_section_x', 'format_topcoll'), // No 'Toggle' word and no 'Topic x' / 'Week x'.
                    7 => new lang_string('setlayout_no_toggle_word_toggle_section_x_section_no', 'format_topcoll')) // No 'Toggle' word, no 'Topic x' / 'Week x'  and no section number.
                                            )
                ),
				'layoutstructure' => array(
                    'label' => new lang_string('setlayoutstructure', 'format_topcoll'),
                    'help' => 'setlayoutstructure',
                    'help_component' => 'format_topcoll',
                    'element_type' => 'select',
                    'element_attributes' => array(
              array(1 => get_string('setlayoutstructuretopic', 'format_topcoll'), // Topic
                    2 => get_string('setlayoutstructureweek', 'format_topcoll'), // Week   
                    3 => get_string('setlayoutstructurelatweekfirst', 'format_topcoll'), // Latest Week First 
                    4 => get_string('setlayoutstructurecurrenttopicfirst', 'format_topcoll'), // Current Topic First
                    5 => get_string('setlayoutstructureday', 'format_topcoll'))                // Day                                            ),
                                            )
                ),
				'layoutcolumns' => array(
                    'label' => new lang_string('setlayoutcolumns', 'format_topcoll'),
                    'help' => 'setlayoutcolumns',
                    'help_component' => 'format_topcoll',
                    'element_type' => 'select',
                    'element_attributes' => array(
                array(1 => get_string('one', 'format_topcoll'), // Default
                    2 => get_string('two', 'format_topcoll'), // Two   
                    3 => get_string('three', 'format_topcoll'), // Three
                    4 => get_string('four', 'format_topcoll')) // Four
					)
                                            ),					
				/*'resetlayout' => array(
                    'label' => new lang_string('resetlayout', 'format_topcoll'),
                    'help' => 'resetlayout',
                    'help_component' => 'format_topcoll',
                    'element_type' => 'checkbox',
                    'element_attributes' => array(
                                            )
                 ),*/											
				'toggleforegroundcolour' => array(
                    'label' => new lang_string('settoggleforegroundcolour', 'format_topcoll'),
                    'help' => 'settoggleforegroundcolour',
                    'help_component' => 'format_topcoll',
                    'element_type' => 'tccolourpopup',
                    'element_attributes' => array(
                array('tabindex' => -1, 'value' => $TCCFG->defaulttgfgcolour)
                                            )
                ),
				'togglebackgroundcolour' => array(
                    'label' => new lang_string('settogglebackgroundcolour', 'format_topcoll'),
                    'help' => 'settogglebackgroundcolour',
                    'help_component' => 'format_topcoll',
                    'element_type' => 'tccolourpopup',
                    'element_attributes' => array(
                array('tabindex' => -1, 'value' => $TCCFG->defaulttgbgcolour)
                                            )
                ),
				'togglebackgroundhovercolour' => array(
                    'label' => new lang_string('settogglebackgroundhovercolour', 'format_topcoll'),
                    'help' => 'settogglebackgroundhovercolour',
                    'help_component' => 'format_topcoll',
                    'element_type' => 'tccolourpopup',
                    'element_attributes' => array(
                array('tabindex' => -1, 'value' => $TCCFG->defaulttgbghvrcolour)
                                            )
                )/*,
				'resetcolour' => array(
                    'label' => new lang_string('resetcolour', 'format_topcoll'),
                    'help' => 'resetcolour',
                    'help_component' => 'format_topcoll',
                    'element_type' => 'checkbox',
                    'element_attributes' => array(
                                            )
                 )*/									
            );
			/*if (is_siteadmin($USER)) {
			   $courseformatoptionsadmin = array (
				'resetalllayout' => array(
                    'label' => new lang_string('resetalllayout', 'format_topcoll'),
                    'help' => 'resetalllayout',
                    'help_component' => 'format_topcoll',
                    'element_type' => 'checkbox',
                    'element_attributes' => array(
                                            )
                 ),											
				'resetallcolour' => array(
                    'label' => new lang_string('resetallcolour', 'format_topcoll'),
                    'help' => 'resetallcolour',
                    'help_component' => 'format_topcoll',
                    'element_type' => 'checkbox',
                    'element_attributes' => array(
                                            )
                 )											
			   );
			   $courseformatoptionsedit = array_merge_recursive($courseformatoptionsedit, $courseformatoptionsadmin);
			}*/
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
	    global $CFG;
        MoodleQuickForm::registerElementType('tccolourpopup', "$CFG->dirroot/course/format/topcoll/js/tc_colourpopup.php", 'MoodleQuickForm_tccolourpopup');
	    /*if ($forsection == false) {
	    global $CFG, $USER;
		
        //$mform->insertElementBefore($mform->createElement('header', 'setlayout', get_string('setlayout', 'format_topcoll')),'numsections');
        $mform->addElement('header', 'ctreset', get_string('ctreset', 'format_topcoll'));
        $mform->addHelpButton('ctreset', 'ctreset', 'format_topcoll', '', true);
        //$mform->insertElementBefore($mform->removeElement('setlayout', false),'numsections');
        //print_object($mform);
		$mform->addElement('checkbox', 'resetlayout', get_string('resetlayout', 'format_topcoll'), false);
		$mform->setAdvanced('resetlayout');
		//$mform->insertElementBefore($mform->createElement('checkbox', 'resetlayout', get_string('resetlayout', 'format_topcoll'), false),'numsections');
		//$mform->insertElementBefore($mform->removeElement('resetlayout', false),'numsections');
        $mform->addHelpButton('resetlayout', 'resetlayout', 'format_topcoll', '', true);

		$mform->addElement('checkbox', 'resetcolour', get_string('resetcolour', 'format_topcoll'), false);
        $mform->addHelpButton('resetcolour', 'resetcolour', 'format_topcoll', '', true);
		$mform->setAdvanced('resetcolour');

        //$user = $DB->get_record('user', array('id'=>$USER->id));
        if (is_siteadmin($USER)) {
            $mform->addElement('checkbox', 'resetalllayout', get_string('resetalllayout', 'format_topcoll'), false);
            $mform->addHelpButton('resetalllayout', 'resetalllayout', 'format_topcoll', '', true);
		$mform->setAdvanced('resetalllayout');

            $mform->addElement('checkbox', 'resetallcolour', get_string('resetallcolour', 'format_topcoll'), false);
            $mform->addHelpButton('resetallcolour', 'resetallcolour', 'format_topcoll', '', true);
		$mform->setAdvanced('resetallcolour');
        }
		}*/
		
	    $elements = parent::create_edit_form_elements($mform, $forsection);

		//print_r($elements);
		
		return $elements;
    }
	
    /**
     * Hijack the format options to reset certain things.
     *
     * @param array $data array of ("fieldname"=>value) of submitted data
     * @param array $files array of uploaded files "element_name"=>tmp_file_path
     * @param array $errors errors already discovered in edit form validation
     * @return array of "element_name"=>"error_description" if there are errors,
     *         or an empty array if everything is OK.
     *         Do not repeat errors from $errors param here
     */
    public function edit_form_validation($data, $files, $errors) {
	    //global $TCCFG;
	    //print_object($data);
		/*if (isset($data['id'])) { // Course Id.
		    $courseid = $data['id'];
		} else {
		   // Error?
		   $courseid = 0;
		}*/
	
	    //print_r($data);
	
	    /*if ((isset($data['resetlayout']) == true) && (isset($data['resetcolour']) == true)) {
		    $data['layoutelement'] = $TCCFG->defaultlayoutelement;
		    $data['layoutstructure'] = $TCCFG->defaultlayoutstructure;
		    $data['layoutcolumns'] = $TCCFG->defaultlayoutcolumns;
		    $data['toggleforegroundcolour'] = $TCCFG->defaulttgfgcolour;
		    $data['togglebackgroundcolour'] = $TCCFG->defaulttgbgcolour;
		    $data['togglebackgroundhovercolour'] = $TCCFG->defaulttgbghvrcolour;
            //$this->reset_topcoll_setting($courseid, true, true);
        } else if (isset($data['resetlayout']) == true) {
		    $data['layoutelement'] = $TCCFG->defaultlayoutelement;
		    $data['layoutstructure'] = $TCCFG->defaultlayoutstructure;
		    $data['layoutcolumns'] = $TCCFG->defaultlayoutcolumns;
            //$this->reset_topcoll_setting($courseid, true, false);
        } else if (isset($data['resetcolour']) == true) {
		    $data['toggleforegroundcolour'] = $TCCFG->defaulttgfgcolour;
		    $data['togglebackgroundcolour'] = $TCCFG->defaulttgbgcolour;
		    $data['togglebackgroundhovercolour'] = $TCCFG->defaulttgbghvrcolour;
            //$this->reset_topcoll_setting($courseid, false, true);
        }

		if ((isset($data['resetalllayout']) == true) && (isset($data['resetallcolour']) == true)) {
            $this->reset_topcoll_setting(true, true);
        } else if (isset($data['resetalllayout']) == true) {
            $this->reset_topcoll_setting(true, false);
        } else if (isset($data['resetallcolour']) == true) {
            $this->reset_topcoll_setting(false, true);
        }*/
	
        return array();
    }

    /**
     * Updates format options for a course
     *
     * In case if course format was changed to 'Collapsed Topics', we try to copy options
     * 'coursedisplay', 'numsections' and 'hiddensections' from the previous format.
     * If previous course format did not have 'numsections' option, we populate it with the
     * current number of sections
     *
     * @param stdClass|array $data return value from {@link moodleform::get_data()} or array with data
     * @param stdClass $oldcourse if this function is called from {@link update_course()}
     *     this object contains information about the course before update
     * @return bool whether there were any changes to the options values
     */
    public function update_course_format_options($data, $oldcourse = null) {
        if ($oldcourse !== null) {
            $data = (array)$data;
            $oldcourse = (array)$oldcourse;
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
        return $this->update_format_options($data);
    }

    /**
     * Is the section passed in the current section?
     *
     * @param stdClass $section The course_section entry from the DB
     * @return bool true if the section is current
     */
    public function is_section_current($section) {
        global $tcsetting;
        if (($tcsetting['layoutstructure'] == 2) || ($tcsetting['layoutstructure'] == 3)) {
            if ($section->section < 1) {
                return false;
            }

            $timenow = time();
            $dates = format_topcoll_get_section_dates($section, $this->get_course());

            return (($timenow >= $dates->start) && ($timenow < $dates->end));
        } else if ($tcsetting['layoutstructure'] == 5) {
            if ($section->section < 1) {
                return false;
            }

            $timenow = time();
            $day = format_topcoll_get_section_day($section, $this->get_course());
            $onedayseconds = 86400;
            return (($timenow >= $day) && ($timenow < ($day + $onedayseconds)));
        } else {
            return parent::is_section_current($section);
        }
    }

/**
 * Return the start and end date of the passed section.
 *
 * @param stdClass $section The course_section entry from the DB.
 * @param stdClass $course The course entry from DB.
 * @return stdClass property start for startdate, property end for enddate.
 */
private function format_topcoll_get_section_dates($section, $course) {
    $oneweekseconds = 604800;
    // Hack alert. We add 2 hours to avoid possible DST problems. (e.g. we go into daylight
    // savings and the date changes.
    $startdate = $course->startdate + 7200;

    $dates = new stdClass();
    $dates->start = $startdate + ($oneweekseconds * ($section->section - 1));
    $dates->end = $dates->start + $oneweekseconds;

    return $dates;
}

/**
 * Return the date of the passed section.
 *
 * @param stdClass $section The course_section entry from the DB.
 * @param stdClass $course The course entry from DB.
 * @return stdClass property date.
 */
private function format_topcoll_get_section_day($section, $course) {
    $onedayseconds = 86400;
    // Hack alert. We add 2 hours to avoid possible DST problems. (e.g. we go into daylight
    // savings and the date changes.
    $startdate = $course->startdate + 7200;

    $day = $startdate + ($onedayseconds * ($section->section - 1));

    return $day;
}

/**
 * Resets the format setting to the default for all courses that use Collapsed Topics.
 * CONTRIB-3652
 * @param int $courseid If not 0, then a specific course to reset.
 * @param int $layout If true, reset the layout to the default in tcconfig.php.
 * @param int $colour If true, reset the colour to the default in tcconfig.php.
 */
public function reset_topcoll_setting($thiscourse, $layout, $colour) {
    global $DB;
    global $TCCFG;

	if ($thiscourse == 0) {
    $records = $DB->get_records('course_format_options', array('format' => $this->format));//, '', 'name,id,value');
	} else {
	        $cached = array();
			$allformatoptions = $this->course_format_options();
        foreach ($allformatoptions as $key => $option) {
            $defaultoptions[$key] = null;
            if (array_key_exists('default', $option)) {
                $defaultoptions[$key] = $option['default'];
            }
            $cached[$key] = !empty($option['cache']);
			}
    $records = $DB->get_records('course_format_options', array('courseid' => $thiscourse,'format' => $this->format));//, '', 'name,id,value');
   }
	//print($thiscourse);
	//print_r($records);
    //print_object($records);
	$changed = $needrebuild = false;
	
    foreach ($records as $record) {
        if ($layout) {
		    if ($record->name == 'layoutelement') {
            $record->value = $TCCFG->defaultlayoutelement;
                    $changed = true;
					if ($thiscourse != null) {
                    $needrebuild = $needrebuild || $cached['layoutelement'];
					}					
			}
		    if ($record->name == 'layoutstructure') {
            $record->value = $TCCFG->defaultlayoutstructure;
                    $changed = true;
					if ($thiscourse != null) {
                    $needrebuild = $needrebuild || $cached['layoutstructure'];
					}					
			}
		    if ($record->name == 'layoutcolumns') {
            $record->value = $TCCFG->defaultlayoutcolumns;
                    $changed = true;
					if ($thiscourse != null) {
                    $needrebuild = $needrebuild || $cached['layoutcolumns'];
					}					
			}
        }
        if ($colour) {
		    if ($record->name == 'toggleforegroundcolour') {
            $record->value = $TCCFG->defaulttgfgcolour;
                    $changed = true;
                    $needrebuild = $needrebuild || $cached[$key];			
			}
		    if ($record->name == 'togglebackgroundcolour') {
            $record->value = $TCCFG->defaulttgbgcolour;
                    $changed = true;
                    $needrebuild = $needrebuild || $cached[$key];			
			}
		    if ($record->name == 'togglebackgroundhovercolour') {
            $record->value = $TCCFG->defaulttgbghvrcolour;
                    $changed = true;
                    $needrebuild = $needrebuild || $cached[$key];			
			}
        }
		if ($changed == true) {		
					//print('A record:');
			    //print_object($record);
        $DB->update_record('course_format_options', $record);
		                    //$DB->set_field('course_format_options', 'value',
                            //$record->value, array('id' => $record->id));
		}
    }
        if ($needrebuild) {
		// TODO for all.
            rebuild_course_cache($this->courseid, true);
        }
        if ($changed) {
            // reset internal caches
                $this->course = false;
            unset($this->formatoptions[0]);
        }
    //$records = $DB->get_records('format_topcoll_settings');
    //print_object($records);
}


}

/**
 * Used to display the course structure for a course where format=Collapsed Topics
 *
 * This is called automatically by {@link load_course()} if the current course
 * format = Collapsed Topics.
 *
 * @param navigation_node $navigation The course node.
 * @param array $path An array of keys to the course node.
 * @param stdClass $course The course we are loading the section for.
 */
function callback_topcoll_load_content(&$navigation, $course, $coursenode) {
    return $navigation->load_generic_course_sections($course, $coursenode, 'topcoll');
}

/**
 * The string that is used to describe a section of the course.
 *
 * @return string The section description.
 */
function callback_topcoll_definition() {
    return get_string('sectionname', 'format_topcoll');
}

/**
 * Deletes the user preference entries for the given course upon course deletion.
 * CONTRIB-3520.
 */
function format_topcoll_delete_course($courseid) {
    global $DB;

    $DB->delete_records("user_preferences", array("name" => 'topcoll_toggle_' . $courseid));
}