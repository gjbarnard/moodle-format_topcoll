<?php

require_once("HTML/QuickForm/text.php");

/**
 * HTML class for a colorpopup type element
 *
 * @author       Iain Checkland - modified from ColourPicker by Jamie Pratt [thanks]
 * @access       public
 */
class MoodleQuickForm_tccolourpopup extends HTML_QuickForm_text {

    /**
     * html for help button, if empty then no help
     *
     * @var string
     */
    var $_helpbutton = '';
    var $_hiddenLabel = false;

    function MoodleQuickForm_tccolourpopup($elementName = null, $elementLabel = null, $attributes = null, $options = null) {
        global $CFG;
        parent::HTML_QuickForm_text($elementName, $elementLabel, $attributes);
    }

    function setHiddenLabel($hiddenLabel) {
        $this->_hiddenLabel = $hiddenLabel;
    }

    function toHtml() {
        global $CFG, $COURSE, $USER, $PAGE, $OUTPUT;
        $id = $this->getAttribute('id');
        $PAGE->requires->js('/course/format/topcoll/js/tc_colourpopup.js');
        $PAGE->requires->js_init_call('M.util.init_tccolour_popup', array($id));
        $content = "<input size='8' name='" . $this->getName() . "' value='" . $this->getValue() . "' 
                        id='{$id}' type='text' " . $this->_getAttrString($this->_attributes) . " >";
        $content .= html_writer::tag('span', '&nbsp;', array('id' => 'colpicked_' . $id, 'tabindex' => '-1', 'style' => 'background-color:#' . $this->getValue() . ';cursor:pointer;margin:0px;padding: 0 8px;border:1px solid black'));
        $content .= html_writer::start_tag('div', array('id' => 'colpick_' . $id, 'style' => "display:none;position:absolute;z-index:500;",
                    'class' => 'form-colourpicker defaultsnext'));
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
     * @access private
     * @return void
     */
    function _generateId() {
        static $idx = 1;

        if (!$this->getAttribute('id')) {
            $this->updateAttributes(array('id' => 'id_' . substr(md5(microtime() . $idx++), 0, 6)));
        }
    }

// end func _generateId
    /**
     * set html for help button
     *
     * @access   public
     * @param array $help array of arguments to make a help button
     * @param string $function function name to call to get html
     */
    function setHelpButton($helpbuttonargs, $function = 'helpbutton') {
        debugging('component setHelpButton() is not used any more, please use $mform->setHelpButton() instead');
    }

    /**
     * get html for help button
     *
     * @access   public
     * @return  string html for help button
     */
    function getHelpButton() {
        return $this->_helpbutton;
    }

    /**
     * Slightly different container template when frozen. Don't want to use a label tag
     * with a for attribute in that case for the element label but instead use a div.
     * Templates are defined in renderer constructor.
     *
     * @return string
     */
    function getElementTemplateType() {
        if ($this->_flagFrozen) {
            return 'static';
        } else {
            return 'default';
        }
    }

}

?>