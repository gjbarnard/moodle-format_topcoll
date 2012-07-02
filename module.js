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

// Global variables 
var toggleBinaryGlobal = "10000000000000000000000000000000000000000000000000000"; // 53 possible toggles - current settings in Moodle for number of topics - 52 + 1 for topic 0.  Need 1 as Most Significant bit to allow toggle 1+ to be off.
var thesparezeros = "00000000000000000000000000"; // A constant of 26 0's to be used to pad the storage state of the toggles when converting between base 2 and 36, this is to be compact.
var toggleState;
var courseid;
var thewwwroot;  // For the toggle graphic.
var numToggles = 0;
var currentSection;
//var ie = false;
var mymobiletheme = false;
var ourYUI;

// Because I like the idea of private and public methods, public will have an underscore in the name.

/**
 * @namespace
 */
M.format_topcoll = M.format_topcoll || {};

/**
 * Initialise with the information supplied from the course format 'format.php' so we can operate.
 * @param {Object} Y YUI instance
 * @param {String} wwwroot the URL of the Moodle site
 * @param {Integer} thecourseid the id of the current course to allow for settings for each course.
 * @param {String} thetogglestate the current state of the toggles.
 */
M.format_topcoll.init = function(Y, wwwroot, thecourseid, thetogglestate) {
    // Init.
    ourYUI = Y;
    thewwwroot = wwwroot;
    courseid = thecourseid;
    toggleState = thetogglestate;
    //alert(toggleState);
    //alert('Init: www: ' + wwwroot + ' mooid: ' + moodleid + ' corid: ' + courseid + ' cookielt: ' + cookielifetime);
    /* if (/MSIE (\d+\.\d+);/.test(navigator.userAgent))
    {
        // Info from: http://www.javascriptkit.com/javatutors/navigator.shtml - accessed 2nd September 2011.
        var ieversion = new Number(RegExp.$1);
        ie = true;
        //alert('Is IE ' + ieversion);
    } */
    if (document.getElementById("mymobile")) {
        mymobiletheme = true;
    }
//alert(mymobiletheme);
}

M.format_topcoll.set_current_section = function (Y, theSection) {
    currentSection = theSection;
//alert(currentSection);
}

// Change the toggle binary global state as a toggle has been changed - toggle number 0 should never be switched as it is the most significant bit and represents the non-toggling topic 0.
// Args - toggleNum is an integer and toggleVal is a string which will either be "1" or "0"
//        savetoggles save the toggle state - used so that all_toggles does not make multiple requests but instead one.
function togglebinary(toggleNum, toggleVal, savetoggles)
{
    // Toggle num should be between 1 and 52 - see definition of toggleBinaryGlobal above.
    if ((toggleNum >=1) && (toggleNum <= 52))
    {
        //alert("togglebinary tbg:" + toggleBinaryGlobal);
        
        // Safe to use.
        var start = toggleBinaryGlobal.substring(0,toggleNum);
        var end = toggleBinaryGlobal.substring(toggleNum+1);
        //var newval = start + toggleVal + end;
        toggleBinaryGlobal = start + toggleVal + end;
        
        //toggleBinaryGlobal = newval;

        //alert("togglebinary toggleNum:" + toggleNum + " st:" + start + " ed:" + end + " tv:" + toggleVal + " tbg:" + toggleBinaryGlobal);

        if (savetoggles == true) 
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
    if(document.getElementById)
    {
        //if (ie == true)
        //{
        //var displaySetting = "table-row";
        //}
        //else
        //{
        var displaySetting = "block";
        //}
        
        //alert("toggleexacttopic tdisp:" + target.style.display + " disp:" + displaySetting + " ton:" + toggleNum + " rl:" + reloading);

        //var sectoggle = document.getElementById("toggle-" + toggleNum);
        if (target.style.display == displaySetting)
        {
            target.style.display = "none";

            if (mymobiletheme == true) {
                image.className = image.className.replace(/\b opencps\b/,''); //remove the class name
                image.style.backgroundImage = "url(" + thewwwroot + "/course/format/topcoll/images/arrow_down.png)";  // Temporary until MyMobile is fixed - MDL-33115.
            } else {
                image.style.backgroundImage = "url(" + thewwwroot + "/course/format/topcoll/images/arrow_down.png)";
            }

            // Save the toggle!
            if (reloading == false) {
                togglebinary(toggleNum,"0",savetoggles);
            }
        }
        else
        {
            target.style.display = displaySetting;

            if (mymobiletheme == true) {
                image.className += " opencps";  //add the class name
                image.style.backgroundImage = "url(" + thewwwroot + "/course/format/topcoll/images/arrow_up.png)";  // Temporary until MyMobile is fixed - MDL-33115.
            } else {
                image.style.backgroundImage = "url(" + thewwwroot + "/course/format/topcoll/images/arrow_up.png)";
            }

            // Save the toggle!
            if (reloading == false) togglebinary(toggleNum,"1",savetoggles);
        }
    }
}

// Called by the html code created by format.php on the actual course page.
// Args - toggler the tag that initiated the call, toggleNum the number of the toggle for which toggler is a part of - see format.php.
function toggle_topic(toggler,toggleNum)
{
    //alert('toggle_topic ' + toggleNum);
    if(document.getElementById)
    {
        imageSwitch = toggler;
        targetElement = toggler.parentNode.nextSibling; // Called from <a> in a <div> so find the next <div>.

        toggleexacttopic(targetElement,imageSwitch,toggleNum,false,true);
    }
}

// Current maximum number of topics is 52, but as the converstion utilises integers which are 32 bit signed, this must be broken into two string segments for the
// process to work.  Therefore each 6 character base 36 string will represent 26 characters for part 1 and 27 for part 2 in base 2.
// This is all required to save cookie space, so instead of using 53 bytes (characters) per course, only 12 are used.
// Convert from a base 36 string to a base 2 string - effectively a private function.
// Args - thirtysix - a 12 character string representing a base 36 number.
function to2baseString(thirtysix)
{
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
    M.util.set_user_preference('topcoll_toggle_'+courseid , value);
}

// 'Private' version of reload_toggles
function reloadToggles()
{
    if (toggleState != null)
    {
        toggleBinaryGlobal = to2baseString(toggleState);
    }
    else
    {
        // Reset to default.
        toggleBinaryGlobal = "10000000000000000000000000000000000000000000000000000";
    }
    //alert("Bongo3 " + toggleState + " " + numToggles + " " + toggleBinaryGlobal);
    
    //alert(toggleBinaryGlobal);
    for (var theToggle = 1; theToggle <= numToggles; theToggle++)
    {
        if ((theToggle <= numToggles) && ((toggleBinaryGlobal.charAt(theToggle) == "1") || (theToggle == currentSection))) // Array index 0 is never tested - MSB thing.
        {
            //alert(theToggle);
            var target = document.getElementById("toggledsection-"+theToggle);
            var image = document.getElementById("toggle-" + theToggle);
            if ((target != null) && (image != null))
            {
                toggleexacttopic(target,image.firstChild,theToggle,true,false);
            }
        //alert("Bongo4 " + theToggle);
        }
    }    
}

// Toggle persistence functions
// Reload the toggles - called from an onload event handler setup at the bottom of format.php
// aToggle sets the number of toggles we have on this course so that when restoring the state we do not attempt to set something that
// no longer exists.  This can happen when the number of sections is reduced and we return to the course and reload the page
// using the data from the cookie.
M.format_topcoll.reload_toggles = function (Y, aToggle) {
    //alert('reload_toggles(' + aToggle + ')');
    numToggles = aToggle;
    
    Y.use('node-base', function(daYUI) {
        daYUI.on("domready", reloadToggles);
    });
}

// Save the toggles - called from togglebinary and allToggle.
function save_toggles()
{
    savetogglestate(to36baseString(toggleBinaryGlobal));
}

// Functions that turn on or off all toggles.
// Alter the state of the toggles.  Where 'state' needs to be true for open and false for close.
function allToggle(state)
{
    var displaySetting;

    if (state == false)
    {
        // All on to set off!
        //if (ie == false)
        //{
        displaySetting = "block";
    //}
    //else
    //{
    //  displaySetting = "table-row";
    //}
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
        if ((target != null) && (image != null))
        {
            target.style.display = displaySetting;
            toggleexacttopic(target,image.firstChild,theToggle,false,false);
        }
    //alert(theToggle);
    }
    // Now save the state of the toggles for efficiency...
    save_toggles();
}

// Open all toggles.
function all_opened()
{
    allToggle(true);
}

// Close all toggles.
function all_closed()
{
    allToggle(false);
}