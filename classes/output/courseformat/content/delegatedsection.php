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
 * Contains the Collapsed Topics format delegated section course format output class.
 *
 * @package    format_topcoll
 * @copyright  2024 Mikel Martín <mikel@moodle.com>
 * @copyright  &copy; 2025-onwards G J Barnard in respect to modifications of core code.
 * @author     G J Barnard - {@link https://moodle.org/user/profile.php?id=442195}
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace format_topcoll\output\courseformat\content;

use core_courseformat\base as course_format;
use core_courseformat\output\local\content\delegatedsection as delegatedsection_base;
use section_info;

/**
 * Class to render a delegated section.
 *
 * @package   format_topcoll
 * @copyright  &copy; 2025-onwards G J Barnard in respect to modifications of core code.
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class delegatedsection extends delegatedsection_base {
    /**
     * Constructor.
     *
     * @param course_format $format the course format
     * @param section_info $section the section info
     */
    public function __construct(course_format $format, section_info $section) {
        parent::__construct($format, $section);
        if (!empty($section->component)) {
            $this->isstealth = false;
        }
    }

    /**
     * Get the name of the template to use for this templatable.
     *
     * @param renderer_base $renderer The renderer requesting the template name.
     * @return string.
     */
    public function get_template_name(\renderer_base $renderer): string {
        return 'format_topcoll/local/content/delegatedsection';
    }
}
