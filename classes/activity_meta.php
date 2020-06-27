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
 * @copyright  &copy; 2020-onwards G J Barnard in respect to modifications of Adaptable activity related meta data,
 *             see below.
 * @author     G J Barnard - {@link http://moodle.org/user/profile.php?id=442195}
 * @link       http://docs.moodle.org/en/Collapsed_Topics_course_format
 * @license    http://www.gnu.org/copyleft/gpl.html GNU Public License
 *
 */

/**
 * Activity-related meta data.
 *
 * This defines the activity_meta class that is used to store information such as submission status,
 * due dates etc.
 *
 * @copyright 2018 Manoj Solanki (Coventry University)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 */

namespace format_topcoll;

defined('MOODLE_INTERNAL') || die();

/**
 * Activity meta data.
 *
 * @copyright Copyright (c) 2015 Moodlerooms Inc. (http://www.moodlerooms.com)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class activity_meta {

    use null_object;

    // Strings.
    /**
     * @var string $submittedstr - string to use when submitted
     */
    public $submittedstr;
    /**
     * @var string $notsubmittedstr - string to use when not submitted
     */
    public $notsubmittedstr;
    /**
     * @var string $submitstrkey - language string key
     */
    public $submitstrkey;
    /**
     * @var string $draftstr - string for draft status
     */
    public $draftstr;
    /**
     * @var string $reopenedstr - string for reopened status
     */
    public $reopenedstr;

    // General meta data.
    /**
     * @var int $timeopen - unix time stamp for time open
     */
    public $timeopen;
    /**
     * @var int $timeclose - unix time stamp for time closes
     */
    public $timeclose;

    /**
     * @var int $extension - unix time stamp for extended due dates.
     */
    public $extension;

    /**
     * @var bool $isteacher - true if meta data is intended for teacher
     */
    public $isteacher = false;
    /**
     * @var bool $submissionnotrequired - true if a submission is not required
     */
    public $submissionnotrequired = false;

    // Student meta data.
    /**
     * @var bool $submitted - true if submission has been made
     */
    public $submitted = false; // Consider collapsing this variable + draft variable into one 'status' variable?
    /**
     * @var bool $draft - true if activity submission is in draft status
     */
    public $draft = false;
    /**
     * @var bool $reopened - true if reopened
     */
    public $reopened = false;
    /**
     * @var int $timesubmitted - unix time stamp for time submitted
     */
    public $timesubmitted;
    /**
     * @var bool $grade - has the submission been graded
     */
    public $grade = false;
    /**
     * @var bool $overdue - is the submission overdue
     */
    public $overdue = false;

    // Teacher meta data.
    /**
     * @var int $numsubmissions - number of submissions
     */
    public $numsubmissions = 0;
    /**
     * @var int $numrequiregrading - number of submissions requiring grading
     */
    public $numrequiregrading = 0;
}
