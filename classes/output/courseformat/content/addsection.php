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
 * A trait to contain all of the deprecated methods, but implemented using the new components.  Thus easing migration.
 * Note: 'courseformat' property is set in the using class constructor.
 *
 * @package    format_topcoll
 * @version    See the value of '$plugin->version' in version.php.
 * @copyright  &copy; 2022-onwards G J Barnard in respect to modifications of core code.
 * @copyright  &copy; 2020 Ferran Recio <ferran@moodle.com>
 * @author     G J Barnard - {@link http://moodle.org/user/profile.php?id=442195}
 * @link       http://docs.moodle.org/en/Collapsed_Topics_course_format
 * @license    http://www.gnu.org/copyleft/gpl.html GNU Public License
 *
 */

namespace format_topcoll\output\courseformat\content;

use core_courseformat\output\local\content\addsection as addsection_base;
use core_courseformat\base as course_format;

use moodle_url;
use stdClass;

/**
 * Class to render a course add section buttons.
 *
 * @package    format_topcoll
 * @copyright  &copy; 2022-onwards G J Barnard in respect to modifications of core code.
 * @copyright  &copy; 2020 Ferran Recio <ferran@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class addsection extends addsection_base {

    /** @var int The current section number. */
    protected $thissectionno;

    /**
     * Constructor.
     *
     * @param course_format $format the course format.
     * @param int $thissectionno The current section number.
     */
    public function __construct(course_format $format, $thissectionno = 0) {
        parent::__construct($format);
        $this->thissectionno = $thissectionno;
    }

    /**
     * Export this data so it can be used as the context for a mustache template.
     *
     * @param renderer_base $output typically, the renderer that's calling this function
     * @return stdClass data context for a mustache template
     */
    public function export_for_template(\renderer_base $output): stdClass {
        if (!$this->thissectionno) {
            return parent::export_for_template($output);
        }

        $format = $this->format;
        $course = $format->get_course();

        $lastsection = $format->get_last_section_number();
        $maxsections = $format->get_max_sections();

        $data = new stdClass();

        // If no editor must be displayed, just return an empty structure.
        if (!$format->show_editor()) {
            return $data;
        }

        // Component based formats handle add section button in the frontend.
        $show = ($lastsection < $maxsections) || $format->supports_components();

        if ($show) {
            $params = ['courseid' => $course->id, 'insertsection' => $this->thissectionno + 1, 'sesskey' => sesskey()];

            $data->addsections = (object) [
                'url' => new moodle_url('/course/changenumsections.php', $params),
                'title' => get_string('addsection', 'format_topcoll'),
                'newsection' => $maxsections - $lastsection,
            ];
        }

        if (count((array)$data)) {
            $data->showaddsection = true;
        }

        return $data;
    }
}
