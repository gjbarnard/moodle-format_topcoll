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
 * @copyright  &copy; 2009-onwards G J Barnard in respect to modifications of standard topics format.
 * @author     G J Barnard - gjbarnard at gmail dot com and {@link http://moodle.org/user/profile.php?id=442195}
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

// Cleaned through use of http://jshint.com/.
/**
 * @namespace
 */
M.format_topcoll = M.format_topcoll || {};

// Namespace variables 
M.format_topcoll.toggleBinaryGlobal = "10000000000000000000000000000000000000000000000000000"; // 53 possible toggles - current settings in Moodle for number of topics - 52 + 1 for topic 0.  Need 1 as Most Significant bit to allow toggle 1+ to be off.
M.format_topcoll.thesparezeros = "00000000000000000000000000"; // A constant of 26 0's to be used to pad the storage state of the toggles when converting between base 2 and 36, this is to be compact.
M.format_topcoll.courseid;
M.format_topcoll.togglePersistence = 1; // Toggle persistence - 1 = on, 0 = off.
M.format_topcoll.ourYUI;

/**
 * Initialise with the information supplied from the course format 'format.php' so we can operate.
 * @param {Object} Y YUI instance
 * @param {String} theCourseId the id of the current course to allow for settings for each course.
 * @param {String} theToggleState the current state of the toggles.
 * @param {Integer} theTogglePersistence Persistence on (1) or off (0).
 * @param {Integer} theDefaultTogglePersistence Persistence all open (1) or all closed (0) when thetogglestate is null.
 */
M.format_topcoll.init = function(Y, theCourseId, theToggleState, theTogglePersistence, theDefaultTogglePersistence) {
    "use strict";
    // Init.
    this.ourYUI = Y;
    this.courseid = theCourseId;
    this.togglePersistence = theTogglePersistence;

    if (theToggleState !== null) {
        this.toggleBinaryGlobal = this.to2baseString(theToggleState);
    } else {
        // Reset to default.
        if (theDefaultTogglePersistence == 0) {
            this.toggleBinaryGlobal = "10000000000000000000000000000000000000000000000000000";
        } else {
            this.toggleBinaryGlobal = "11111111111111111111111111111111111111111111111111111";
        }
    }

    // Info on http://yuilibrary.com/yui/docs/event/delegation.html
    // Delegated event handler for the toggles.
    // Inspiration thanks to Ben Kelada.
    // Code help thanks to the guru Andrew Nicols.
    Y.delegate('click', this.toggleClick, Y.config.doc, 'ul.ctopics .toggle', this);

    // Event handlers for all opened / closed.
    var allopen = Y.one("#toggles-all-opened");
    if (allopen) {
        allopen.on('click', this.allOpenClick);
    }
    var allclosed = Y.one("#toggles-all-closed");
    if (allclosed) {
        allclosed.on('click', this.allCloseClick);
    }
};

M.format_topcoll.toggleClick = function(e) {
    var toggleIndex = parseInt(e.currentTarget.get('id').replace("toggle-", ""));
    e.preventDefault();
    this.toggle_topic(e.currentTarget, toggleIndex);
};

M.format_topcoll.allOpenClick = function(e) {
    e.preventDefault();
    M.format_topcoll.ourYUI.all(".toggledsection").show().setStyle('display', 'block');
    M.format_topcoll.ourYUI.all(".toggle a").addClass('toggle_open').removeClass('toggle_closed');
    M.format_topcoll.toggleBinaryGlobal = "11111111111111111111111111111111111111111111111111111";
    M.format_topcoll.save_toggles();
};

M.format_topcoll.allCloseClick = function(e) {
    e.preventDefault();
    M.format_topcoll.ourYUI.all(".toggledsection").hide();
    M.format_topcoll.ourYUI.all(".toggle a").addClass('toggle_closed').removeClass('toggle_open');
    M.format_topcoll.toggleBinaryGlobal = "10000000000000000000000000000000000000000000000000000";
    M.format_topcoll.save_toggles();
};

// Toggle functions
// Change the toggle binary global state as a toggle has been changed - toggle number 0 should never be switched as it is the most significant bit and represents the non-toggling topic 0.
// Args - toggleNum is an integer and toggleVal is a string which will either be "1" or "0"
//        savetoggles save the toggle state - used so that all_toggles does not make multiple requests but instead one.
M.format_topcoll.togglebinary = function(toggleNum, toggleVal, savetoggles) {
    "use strict";
    // Toggle num should be between 1 and 52 - see definition of toggleBinaryGlobal above.
    if ((toggleNum >= 1) && (toggleNum <= 52)) {
        // Safe to use.
        var start = this.toggleBinaryGlobal.substring(0,toggleNum);
        var end = this.toggleBinaryGlobal.substring(toggleNum+1);
        this.toggleBinaryGlobal = start + toggleVal + end;

        if (savetoggles === true) {
            this.save_toggles();
        }
    }
};

// Args - targetNode that initiated the call, toggleNum the number of the toggle.
M.format_topcoll.toggle_topic = function(targetNode, toggleNum) {
    "use strict";
    var targetLink = targetNode.one('a');
    if (!targetLink.hasClass('toggle_open')) {
        targetLink.addClass('toggle_open').removeClass('toggle_closed');
        targetNode.next('.toggledsection').show().setStyle('display', 'block');
        this.togglebinary(toggleNum, "1", true);
    } else {
        targetLink.addClass('toggle_closed').removeClass('toggle_open');
        targetNode.next('.toggledsection').hide();
        this.togglebinary(toggleNum, "0", true);
    }
};

// Current maximum number of topics is 52, but as the converstion utilises integers which are 32 bit signed, this must be broken into two string segments for the
// process to work.  Therefore each 6 character base 36 string will represent 26 characters for part 1 and 27 for part 2 in base 2.
// This is all required to save cookie space, so instead of using 53 bytes (characters) per course, only 12 are used.
// Convert from a base 36 string to a base 2 string - effectively a private function.
// Args - thirtysix - a 12 character string representing a base 36 number.
M.format_topcoll.to2baseString = function(thirtysix) {
    "use strict";
    // Break apart the string because integers are signed 32 bit and therefore can only store 31 bits, therefore a 53 bit number will cause overflow / carry with loss of resolution.
    var firstpart = parseInt(thirtysix.substring(0,6),36);
    var secondpart = parseInt(thirtysix.substring(6,12),36);
    var fps = firstpart.toString(2);
    var sps = secondpart.toString(2);
    
    // Add in preceding 0's if base 2 sub strings are not long enough
    if (fps.length < 26) {
        // Need to PAD.
        fps = this.thesparezeros.substring(0,(26 - fps.length)) + fps;
    }
    if (sps.length < 27) {
        // Need to PAD.
        sps = this.thesparezeros.substring(0,(27 - sps.length)) + sps;
    }
    
    return fps + sps;
};

// Convert from a base 2 string to a base 36 string - effectively a private function.
// Args - two - a 52 character string representing a base 2 number.
M.format_topcoll.to36baseString = function(two) {
    "use strict";
    // Break apart the string because integers are signed 32 bit and therefore can only store 31 bits, therefore a 52 bit number will cause overflow / carry with loss of resolution.
    var firstpart = parseInt(two.substring(0,26),2);
    var secondpart = parseInt(two.substring(26,53),2);
    var fps = firstpart.toString(36);
    var sps = secondpart.toString(36);

    // Add in preceding 0's if base 36 sub strings are not long enough
    if (fps.length < 6) {
        // Need to PAD.
        fps = this.thesparezeros.substring(0,(6 - fps.length)) + fps;
    }
    if (sps.length < 6) {
        // Need to PAD.
        sps = this.thesparezeros.substring(0,(6 - sps.length)) + sps;
    }

    return fps + sps;
};

// Save the toggles - called from togglebinary and allToggle.
// AJAX call to server to save the state of the toggles for this course for the current user if on.
M.format_topcoll.save_toggles = function() {
    "use strict";
    if (this.togglePersistence == 1) { // Toggle persistence - 1 = on, 0 = off.
        M.util.set_user_preference('topcoll_toggle_'+this.courseid , this.to36baseString(this.toggleBinaryGlobal));
    }
};