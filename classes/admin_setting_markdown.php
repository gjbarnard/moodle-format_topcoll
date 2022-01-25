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
 * @copyright  &copy; 2022-onwards G J Barnard based upon work done by Marina Glancy.
 * @author     G J Barnard - {@link http://moodle.org/user/profile.php?id=442195}
 * @link       http://docs.moodle.org/en/Collapsed_Topics_course_format
 * @license    http://www.gnu.org/copyleft/gpl.html GNU Public License
 *
 */

namespace format_topcoll;

/**
 * Setting that displays markdown files.  Based on admin_setting_description in adminlib.php.
 *
 * @copyright  &copy; 2022-onwards G J Barnard.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.
 */
class admin_setting_markdown extends \admin_setting {

    /** @var string Filename */
    private $filename;

    /**
     * Not a setting, just markup.
     *
     * @param string $name Setting name.
     * @param string $visiblename Setting name on the device.
     * @param string $description Setting description on the device.
     * @param string $filename The file to show.
     */
    public function __construct($name, $visiblename, $description, $filename) {
        $this->nosave = true;
        $this->filename = $filename;
        parent::__construct($name, $visiblename, $description, '');
    }

    /**
     * Always returns true.
     *
     * @return bool Always returns true.
     */
    public function get_setting() {
        return true;
    }

    /**
     * Always returns true.
     *
     * @return bool Always returns true.
     */
    public function get_defaultsetting() {
        return true;
    }

    /**
     * Never write settings
     *
     * @param mixed $data Gets converted to str for comparison against yes value.
     * @return string Always returns an empty string.
     */
    public function write_setting($data) {
        // Do not write any setting.
        return '';
    }

    /**
     * Returns an HTML string
     *
     * @param string $data
     * @param string $query
     * @return string Returns an HTML string
     */
    public function output_html($data, $query='') {
        global $CFG, $OUTPUT;

        $context = new \stdClass();
        $context->title = $this->visiblename;
        $context->description = $this->description;

        if (file_exists("{$CFG->dirroot}/course/format/topcoll/{$this->filename}")) {
            $filecontents = file_get_contents($CFG->dirroot.'/course/format/topcoll/'.$this->filename);
        } else {
            $filecontents = 'Collapsed Topics format admin_setting_markdown -> file not found: '.$this->filename;
        }
        $context->markdown = format_text($filecontents, FORMAT_MARKDOWN);

        return $OUTPUT->render_from_template('format_topcoll/ct_admin_setting_markdown', $context);
    }
}
