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
 * code change. Full installation instructions, code adaptions and credits are included in the 'Readme.txt' file.
 *
 * @package    format_topcoll
 * @version    See the value of '$plugin->version' in version.php.
 * @copyright  &copy; 2012-onwards G J Barnard in respect to modifications of standard topics format.
 * @author     G J Barnard - gjbarnard at gmail dot com and {@link http://moodle.org/user/profile.php?id=442195}
 * @link       http://docs.moodle.org/en/Collapsed_Topics_course_format
 * @license    http://www.gnu.org/copyleft/gpl.html GNU Public License
 *
 */
defined('MOODLE_INTERNAL') || die();

require_once("HTML/QuickForm/text.php");

/**
 * HTML class for a colorpopup type element
 *
 * @author       Iain Checkland - modified from ColourPicker by Jamie Pratt [thanks]
 * @access       public
 */
class MoodleQuickForm_tccolourpopup extends HTML_QuickForm_text implements templatable {
    use templatable_form_element {
        export_for_template as export_for_template_base;
    }

    /*
     * html for help button, if empty then no help
     *
     * @var string
     */
    public $_helpbutton = '';
    public $_hiddenLabel = false;

    public function __construct($elementname = null, $elementlabel = null, $attributes = null, $options = null) {
        parent::__construct($elementname, $elementlabel, $attributes);
        /* Pretend we are a 'static' MoodleForm element so that we get the core_form/element-static template where
           we can render our own markup via core_renderer::mform_element() in lib/outputrenderers.php.
           used in combination with 'use' statement above and export_for_template() method below. */
        $this->setType('static');
    }

    public function setHiddenLabel($hiddenLabel) {
        $this->_hiddenLabel = $hiddenLabel;
    }

    public function toHtml() {
        global $PAGE;
        $id = $this->getAttribute('id');
        $PAGE->requires->js('/course/format/topcoll/js/tc_colourpopup.js');
        $PAGE->requires->js_init_call('M.util.init_tccolour_popup', array($id));
        $value = $this->getValue();
        if (!empty($value)) {
            if ($value[0] == '#') {
                $colour = substr($value, 1);
            } else if ($value[0] == '-') {
                $colour = $this->getAttribute('defaultcolour');
            } else {
                $colour = $value;
            }
        } else {
            $value = '-';
            $colour = $this->getAttribute('defaultcolour');
        }
        $content = "<input size='5' name='" . $this->getName() . "' value='" . $value . "'id='{$id}' type='text' " .
            $this->_getAttrString($this->_attributes) . " >";
        $content .= html_writer::tag('span', '&nbsp;', array(
            'id' => 'colpicked_'.$id,
            'class' => 'tccolourpopupbox',
            'tabindex' => '-1',
            'style' => 'background-color: #'.$colour.';')
        );
        $content .= html_writer::start_tag('div', array(
            'id' => 'colpick_' . $id,
            'style' => "display: none;",
            'class' => 'tccolourpopupsel form-colourpicker defaultsnext'));
        $content .= html_writer::tag('div', '', array('class' => 'admin_colourpicker clearfix'));
        $content .= html_writer::end_tag('div');
        return $content;
    }

    /**
     * Automatically generates and assigns an 'id' attribute for the element.
     *
     * Currently used to ensure that labels work on radio buttons and
     * checkboxes. Per idea of Alexander Radivanovich.
     * Overriden in moodleforms to remove qf_ prefix.
     *
     * @return void
     */
    public function _generateId() {
        static $idx = 1;

        if (!$this->getAttribute('id')) {
            $this->updateAttributes(array('id' => 'id_' . substr(md5(microtime() . $idx++), 0, 6)));
        }
    }

    /**
     * set html for help button
     *
     * @param array $help array of arguments to make a help button
     * @param string $function function name to call to get html
     */
    public function setHelpButton($helpbuttonargs, $function = 'helpbutton') {
        debugging('component setHelpButton() is not used any more, please use $mform->setHelpButton() instead');
    }

    /**
     * get html for help button
     *
     * @return  string html for help button
     */
    public function getHelpButton() {
        return $this->_helpbutton;
    }

    /**
     * Slightly different container template when frozen. Don't want to use a label tag
     * with a for attribute in that case for the element label but instead use a div.
     * Templates are defined in renderer constructor.
     *
     * @return string
     */
    public function getElementTemplateType() {
        if ($this->_flagFrozen) {
            return 'static';
        } else {
            return 'default';
        }
    }

    public function export_for_template(renderer_base $output) {
        $context = $this->export_for_template_base($output);
        $context['html'] = $this->toHtml();
        $context['staticlabel'] = false; // Not a static label!
        return $context;
    }
}
