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
 * Setting that displays information.  Based on admin_setting_description in adminlib.php.
 *
 * @copyright  &copy; 2022-onwards G J Barnard.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.
 */
class admin_setting_information extends \admin_setting {

    /** @var int The branch this is for. */
    protected $mbranch;

    /**
     * Not a setting, just information.
     *
     * @param string $name Setting name.
     * @param string $visiblename Setting name on the device.
     * @param string $description Setting description on the device.
     * @param string $mbranch The branch this is for.
     */
    public function __construct($name, $visiblename, $description, $mbranch) {
        $this->nosave = true;
        $this->mbranch = $mbranch;
        return parent::__construct($name, $visiblename, $description, '');
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

        $formats = \core_plugin_manager::instance()->get_present_plugins('format');
        if (!empty($formats['topcoll'])) {
            $plugininfo = $formats['topcoll'];
        } else {
            $plugininfo = \core_plugin_manager::instance()->get_plugin_info('format_topcoll');
            $plugininfo->version = $plugininfo->versiondisk;
        }

        $classes[] = 'fa fa-heart';
        $attributes = array();
        $attributes['aria-hidden'] = 'true';
        $attributes['class'] = 'fa fa-heart';
        $attributes['title'] = get_string('love', 'format_topcoll');
        $content = \html_writer::tag('span', $attributes['title'], array('class' => 'sr-only'));
        $content = \html_writer::tag('span', $content, $attributes);
        $context['versioninfo'] = get_string('versioninfo', 'format_topcoll',
            array(
                'moodle' => $CFG->release,
                'release' => $plugininfo->release,
                'version' => $plugininfo->version,
                'love' => $content
            )
        );

        if (!empty($plugininfo->maturity)) {
            switch ($plugininfo->maturity) {
                case MATURITY_ALPHA:
                    $context['maturity'] = get_string('versionalpha', 'format_topcoll');
                    $context['maturityalert'] = 'danger';
                break;
                case MATURITY_BETA:
                    $context['maturity'] = get_string('versionbeta', 'format_topcoll');
                    $context['maturityalert'] = 'danger';
                break;
                case MATURITY_RC:
                    $context['maturity'] = get_string('versionrc', 'format_topcoll');
                    $context['maturityalert'] = 'warning';
                break;
                case MATURITY_STABLE:
                    $context['maturity'] = get_string('versionstable', 'format_topcoll');
                    $context['maturityalert'] = 'info';
                break;
            }
        }

        if ($CFG->branch != $this->mbranch) {
            $context['versioncheck'] = 'Release '.$plugininfo->release.', version '.$plugininfo->version;
            $context['versioncheck'] .= ' is incompatible with Moodle '.$CFG->release;
            $context['versioncheck'] .= ', please get the correct version from ';
            $context['versioncheck'] .= '<a href="https://moodle.org/plugins/format_topcoll" target="_blank">Moodle.org</a>.  ';
            $context['versioncheck'] .= 'If none is available, then please consider supporting the format by funding it.  ';
            $context['versioncheck'] .= 'Please contact me via \'gjbarnard at gmail dot com\' or my ';
            $context['versioncheck'] .= '<a href="https://moodle.org/user/profile.php?id=442195">Moodle dot org profile</a>.  ';
            $context['versioncheck'] .= 'This is my <a href="http://about.me/gjbarnard">\'Web profile\'</a> if you want ';
            $context['versioncheck'] .= 'to know more about me.';
        }

        return $OUTPUT->render_from_template('format_topcoll/ct_admin_setting_information', $context);
    }
}
