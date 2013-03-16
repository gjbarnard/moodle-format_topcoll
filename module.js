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

// Global variables 
var toggleBinaryGlobal = "10000000000000000000000000000000000000000000000000000"; // 53 possible toggles - current settings in Moodle for number of topics - 52 + 1 for topic 0.  Need 1 as Most Significant bit to allow toggle 1+ to be off.
var thesparezeros = "00000000000000000000000000"; // A constant of 26 0's to be used to pad the storage state of the toggles when converting between base 2 and 36, this is to be compact.
var toggleState;
var courseid;
var numToggles = 0;
var togglePersistence = 1; // Toggle persistence - 1 = on, 0 = off.
var ourYUI;

/**
 * @namespace
 */
M.format_topcoll = M.format_topcoll || {};

/**
 * Initialise with the information supplied from the course format 'format.php' so we can operate.
 * @param {Object} Y YUI instance
 * @param {Integer} thecourseid the id of the current course to allow for settings for each course.
 * @param {String} thetogglestate the current state of the toggles.
 * @param {Integer} noOfToggles The number of toggles.
 * @param {Integer} theTogglePersistence Persistence on (1) or off (0).
 */
M.format_topcoll.init = function(Y, thecourseid, thetogglestate, noOfToggles, theTogglePersistence) {
    "use strict";
    // Init.
    ourYUI = Y;
    courseid = thecourseid;
    toggleState = thetogglestate;
    numToggles = noOfToggles;
    togglePersistence = theTogglePersistence;

    if (toggleState !== null)
    {
        toggleBinaryGlobal = to2baseString(toggleState);
    }
    else
    {
        // Reset to default.
        toggleBinaryGlobal = "10000000000000000000000000000000000000000000000000000";
    }

    // Info on http://yuilibrary.com/yui/docs/event/
    // Event handlers for the toggles.
    for (var theToggle = 1; theToggle <= numToggles; theToggle++)
    {
        var toggler = document.getElementById("toggle-" + theToggle); // Need the DOM element not the YUI one for manipulation purposes.
        if (toggler !== null)
        {
            var instance = new CollapsedTopicsToggler(toggler,theToggle);
            Y.one("#toggle-" + theToggle).on('click', instance.handleClick, instance);
        }
    }
    
    // Event handlers for all opened / closed.
    var allopen = Y.one("#toggles-all-opened");
    if (allopen) {
        allopen.on('click',function(e){
            e.preventDefault();
            all_opened();
        });
    }
    var allclosed = Y.one("#toggles-all-closed");
    if (allclosed) {
        allclosed.on('click',function(e){
            e.preventDefault();
            all_closed();
        });
    }
};

// Info on http://pivotallabs.com/users/pjaros/blog/articles/1368-javascript-constructors-prototypes-and-the-new-keyword
var CollapsedTopicsToggler = function CollapsedTopicsToggler(toggler,toggleNum)
{
    "use strict";
    this.toggler = toggler;
    this.toggleNum = toggleNum;
};

// Info on http://yuilibrary.com/yui/docs/event/
CollapsedTopicsToggler.prototype = {
    handleClick: function (e) {
        "use strict";
        e.preventDefault();
        toggle_topic(this.toggler,this.toggleNum);
    }
};

// Change the toggle binary global state as a toggle has been changed - toggle number 0 should never be switched as it is the most significant bit and represents the non-toggling topic 0.
// Args - toggleNum is an integer and toggleVal is a string which will either be "1" or "0"
//        savetoggles save the toggle state - used so that all_toggles does not make multiple requests but instead one.
function togglebinary(toggleNum, toggleVal, savetoggles)
{
    "use strict";
    // Toggle num should be between 1 and 52 - see definition of toggleBinaryGlobal above.
    if ((toggleNum >=1) && (toggleNum <= 52))
    {
        // Safe to use.
        var start = toggleBinaryGlobal.substring(0,toggleNum);
        var end = toggleBinaryGlobal.substring(toggleNum+1);
        toggleBinaryGlobal = start + toggleVal + end;

        if (savetoggles === true) 
        {
            save_toggles();
        }
    }
}

// Toggle functions
// Args - target is the list item element in the DOM to be toggled.
//        image is the img tag element in the DOM to be changed.
//        toggleNum is the toggle number to change.
//        reloading is a boolean that states if the function is called from reload_toggles() so that we do not have to resave what we already know - ohh for default argument values.
//        savetoggles save the toggle state - used so that all_toggles does not make multiple requests but instead one.
function toggleexacttopic(target,image,toggleNum,reloading,savetoggles)  // Toggle the target and change the image.
{
    "use strict";
    if(document.getElementById)
    {
        var displaySetting = "block";

        if (target.style.display === displaySetting)
        {
            target.style.display = "none";

            image.className = image.className.replace('toggle_open','toggle_closed');

            // Save the toggle!
            if (reloading === false) {
                togglebinary(toggleNum,"0",savetoggles);
            }
        }
        else
        {
            target.style.display = displaySetting;

            image.className = image.className.replace('toggle_closed','toggle_open');

            // Save the toggle!
            if (reloading === false) {
                togglebinary(toggleNum,"1",savetoggles);
            }
        }
    }
}

// Called by the html code created by format.php on the actual course page.
// Args - toggler the tag that initiated the call, toggleNum the number of the toggle for which toggler is a part of - see format.php.
function toggle_topic(toggler,toggleNum)
{
    "use strict";
    var imageSwitch = toggler.firstChild; // The image is on the <a> so now that 'toggler' is the <div> container, we need to get it.
    var targetElement = toggler.nextSibling; // Event hander on the <div> containing the <a> so find the next <div>.

    toggleexacttopic(targetElement,imageSwitch,toggleNum,false,true);
}

// Current maximum number of topics is 52, but as the converstion utilises integers which are 32 bit signed, this must be broken into two string segments for the
// process to work.  Therefore each 6 character base 36 string will represent 26 characters for part 1 and 27 for part 2 in base 2.
// This is all required to save cookie space, so instead of using 53 bytes (characters) per course, only 12 are used.
// Convert from a base 36 string to a base 2 string - effectively a private function.
// Args - thirtysix - a 12 character string representing a base 36 number.
function to2baseString(thirtysix)
{
    "use strict";
    // Break apart the string because integers are signed 32 bit and therefore can only store 31 bits, therefore a 53 bit number will cause overflow / carry with loss of resolution.
    var firstpart = parseInt(thirtysix.substring(0,6),36);
    var secondpart = parseInt(thirtysix.substring(6,12),36);
    var fps = firstpart.toString(2);
    var sps = secondpart.toString(2);
    
    // Add in preceding 0's if base 2 sub strings are not long enough
    if (fps.length < 26)
    {
        // Need to PAD.
        fps = thesparezeros.substring(0,(26 - fps.length)) + fps;
    }
    if (sps.length < 27)
    {
        // Need to PAD.
        sps = thesparezeros.substring(0,(27 - sps.length)) + sps;
    }
    
    return fps + sps;
}

// Convert from a base 2 string to a base 36 string - effectively a private function.
// Args - two - a 52 character string representing a base 2 number.
function to36baseString(two)
{
    "use strict";
    // Break apart the string because integers are signed 32 bit and therefore can only store 31 bits, therefore a 52 bit number will cause overflow / carry with loss of resolution.
    var firstpart = parseInt(two.substring(0,26),2);
    var secondpart = parseInt(two.substring(26,53),2);
    var fps = firstpart.toString(36);
    var sps = secondpart.toString(36);

    // Add in preceding 0's if base 36 sub strings are not long enough
    if (fps.length < 6)
    {
        // Need to PAD.
        fps = thesparezeros.substring(0,(6 - fps.length)) + fps;
    }
    if (sps.length < 6)
    {
        // Need to PAD.
        sps = thesparezeros.substring(0,(6 - sps.length)) + sps;
    }

    return fps + sps;
}

// AJAX call to server to save the state of the toggles for this course for the current user.
// Args - value is the base 36 state of the toggles.
function savetogglestate(value)
{
    "use strict";
    if (togglePersistence == 1) // Toggle persistence - 1 = on, 0 = off.
    {
        M.util.set_user_preference('topcoll_toggle_'+courseid , value);
    }
}

// Save the toggles - called from togglebinary and allToggle.
function save_toggles()
{
    "use strict";
    savetogglestate(to36baseString(toggleBinaryGlobal));
}

// Functions that turn on or off all toggles.
// Alter the state of the toggles.  Where 'state' needs to be true for open and false for close.
function allToggle(state)
{
    "use strict";
    var displaySetting;

    if (state === false)
    {
        displaySetting = "block";
    }
    else
    {
        // Set all off to set on.
        displaySetting = "none";
    }

    for (var theToggle = 1; theToggle <= numToggles; theToggle++)
    {
        var target = document.getElementById("toggledsection-"+theToggle);
        var image = document.getElementById("toggle-" + theToggle);
        if ((target !== null) && (image !== null))
        {
            target.style.display = displaySetting;
            toggleexacttopic(target,image.firstChild,theToggle,false,false);
        }
    }
    // Now save the state of the toggles for efficiency...
    save_toggles();
}

// Open all toggles.
function all_opened()
{
    "use strict";
    allToggle(true);
}

// Close all toggles.
function all_closed()
{
    "use strict";
    allToggle(false);
}