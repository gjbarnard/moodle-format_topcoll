//  $Id: lib.js,v 1.1.2.8 2011/10/06 00:56:11 gb2048 Exp $

/**
 * Collapsed Topics Information
 *
 * @package    course/format
 * @subpackage topcoll
 * @copyright  2009-2011 @ G J Barnard.
 * @link       http://docs.moodle.org/en/Collapsed_Topics_course_format
 * @license    http://creativecommons.org/licenses/by-sa/3.0/ Creative Commons Attribution-ShareAlike 3.0 Unported (CC BY-SA 3.0)
 */
 
// Global variables 
var toggleBinaryGlobal = "10000000000000000000000000000000000000000000000000000"; // 53 possible toggles - current settings in Moodle for number of topics - 52 + 1 for topic 0.  Need 1 as Most Significant bit to allow toggle 1+ to be off.
var thesparezeros = "00000000000000000000000000"; // A constant of 26 0's to be used to pad the storage state of the toggles when converting between base 2 and 36, this is because cookies need to be compact.
var thewwwroot;  // For the toggle graphic and extra files.
var thecookiesubid; // For the cookie sub name.
var yuicookie = YAHOO.util.Cookie; // Simpler function calls.
var numToggles = 0;
var cookieExpires;
var ie7OrLess = false;
var ie = false;

// Global Time constants in milliseconds...
var aSecond = 1000;
var aMinute = 60000;
var anHour = 3600000;
var aDay = 86400000;
var aWeek = 604800000; // 7 Days.
var aMonth = 2419200000; // 4 Weeks.
var aYear = 31536000000; // 365 Days.

// Because I like the idea of private and public methods, public will have an underscore in the name.

// Initialise with the information supplied from the course format 'format.php' so we can operate.
// Args - wwwroot is the URL of the Moodle site, moodleid is the site short name (courseid 0) and courseid is the id of the current course to allow for settings for each course.
function topcoll_init(wwwroot, moodleid, courseid, cookielifetime)
{
    // Init.
    thewwwroot = wwwroot;
    thecookiesubid = moodleid + courseid;
    cookieExpires = cookielifetime; // null indicates that it is a session cookie.

    if (/MSIE (\d+\.\d+);/.test(navigator.userAgent))
    {
        // Info from: http://www.javascriptkit.com/javatutors/navigator.shtml - accessed 2nd September 2011.
        var ieversion = new Number(RegExp.$1);
        ie = true;
        //alert('Is IE ' + ieversion);
        if (ieversion <= 7)
        {
            //alert('Is IE 7');
            ie7OrLess = true;
        }
    }

    // CSS
    var cssNode = document.createElement('link');
    
    cssNode.setAttribute('rel', 'stylesheet');
    cssNode.setAttribute('type', 'text/css');
    cssNode.setAttribute('href', "" + thewwwroot + "/course/format/topcoll/js-override-topcoll.css");
    document.getElementsByTagName('head')[0].appendChild(cssNode);
}

// Set the number of toggles we have on this course so that when restoring the state we do not attempt to set something that
// no longer exists.  This can happen when the number of sections is reduced and we return to the course and reload the page
// using the data from the cookie.
function set_number_of_toggles(atoggle)
{
    numToggles = atoggle;
}

// Change the toggle binary global state as a toggle has been changed - toggle number 0 should never be switched as it is the most significant bit and represents the non-toggling topic 0.
// Args - toggleNum is an integer and toggleVal is a string which will either be "1" or "0"
function togglebinary(toggleNum, toggleVal)
{
    // Toggle num should be between 1 and 52 - see definition of toggleBinaryGlobal above.
    if ((toggleNum >=1) && (toggleNum <= 52))
    {
        // Safe to use.  So recreate the string containing the state of the toggles.
        var start = toggleBinaryGlobal.substring(0,toggleNum); // Do not need to add one to toggleNum for indexing as we are ignoring index 0 for MSB purposes.
        var end = toggleBinaryGlobal.substring(toggleNum+1); // Get the rest of the string from the position after the toggle.
        var newval = start + toggleVal + end;
        
        toggleBinaryGlobal = newval;
        save_toggles();  // We have a change so save.
    }
}

// Toggle functions
// Args - target is the table row element in the DOM to be toggled.
//            image is the img tag element in the DOM to be changed.
//            toggleNum is the toggle number to change.
//            reloading is a boolean that states if the function is called from reload_toggles() so that we do not have to resave what we already know - ohh for default argument values.
function toggleexacttopic(target,image,toggleNum,reloading)  // Toggle the target tr and change the image which is the a tag within the td of the tr above target
{
    // It is possible that 'target' and 'image' can be null if they are not found on the page.
    // This can happen when they are not displayed by php when only one topic is shown with the 'Show only topic x' functionality, if
    // the logic at the end of format.php is broken and reload_toggles is called.
    if((document.getElementById) && ((target != null) && (image !=null)))
    {
        if (ie == true)
        {
            var displaySetting = "block";  // IE is always different from the rest!
        }
        else
        {
            var displaySetting = "table-row";
        }

        if (target.style.display == displaySetting)
        {
            target.style.display = "none";
            if (ie7OrLess == true)
            {
                target.className += " collapsed_topic";  //add the class name
                //alert('Added class name');
            }
            image.style.backgroundImage = "url(" + thewwwroot + "/course/format/topcoll/arrow_down.png)";
            // Save the toggle!
            if (reloading == false)    togglebinary(toggleNum,"0");
        }
        else
        {
            target.style.display = displaySetting;
            if (ie7OrLess == true)
            {
                target.className = target.className.replace(/\b collapsed_topic\b/,'') //remove the class name
                //alert('Removed class name');
            }
            image.style.backgroundImage = "url(" + thewwwroot + "/course/format/topcoll/arrow_up.png)";
            // Save the toggle!
            if (reloading == false) togglebinary(toggleNum,"1");
        }
    }
}

// Called by the html code created by format.php on the actual course page.
// Args - toggler the tag that initiated the call, toggleNum the number of the toggle for which toggler is a part of - see format.php.
function toggle_topic(toggler,toggleNum)
{
    if(document.getElementById)
    {
        imageSwitch = toggler;
        targetElement = toggler.parentNode.parentNode.nextSibling; // Called from a <td> inside a <tr> so find the next <tr>.

        if(targetElement.className == undefined)
        {
            targetElement = toggler.parentNode.parentNode.nextSibling.nextSibling; // If not found, try the next.
        }
        toggleexacttopic(targetElement,imageSwitch,toggleNum,false);
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

// Cookie Monster
// Args - value to save to the cookie
function savetopcollcookie(value)
{
    // Using Sub cookies, so, name, moodleid/courseid, value.
    if (cookieExpires == null)
    {
        // Session Cookie...
        yuicookie.setSub("mdl_cf_topcoll",thecookiesubid,value);    
        // This is not a Moodle table but in fact the cookies name.
    }
    else
    {
        // Expiring Cookie...
        var newDate = new Date();
        newDate.setTime(newDate.getTime() + cookieExpires);
        yuicookie.setSub("mdl_cf_topcoll",thecookiesubid,value, { expires: newDate });
        // This is not a Moodle table but in fact the cookies name.
    }
}

// Get the cookie - yum.
function restoretopcollcookie()
{
    return yuicookie.getSub("mdl_cf_topcoll",thecookiesubid); // Returns null if cookie does not exist.
}

// Toggle persistence functions
// Reload the toggles - called from an onload event handler setup at the bottom of format.php
function reload_toggles()
{
    // Get the cookie if there!
    var storedval = restoretopcollcookie();
    if (storedval != null)
    {
        toggleBinaryGlobal = to2baseString(storedval);
    }
    
    for (var theToggle = 1; theToggle <= numToggles; theToggle++)
    {
        if ((theToggle <= numToggles) && (toggleBinaryGlobal.charAt(theToggle) == "1")) // Array index 0 is never tested - MSB thing.
        {
            toggleexacttopic(document.getElementById("section-"+theToggle),document.getElementById("sectionatag-" + theToggle),theToggle,true);
        }
    }
}

// Show a specific topic - used when in 'Show topic x' mode.
function show_topic(theTopic)
{
    toggleexacttopic(document.getElementById("section-"+theTopic),document.getElementById("sectionatag-" + theTopic),theTopic,true);
}

// Save the toggles - called from togglebinary and an the unload event handler at the bottom of format.php which does not work for a refresh even though it should!
function save_toggles()
{
    savetopcollcookie(to36baseString(toggleBinaryGlobal));
}

// Functions that turn on or off all toggles.
// Alter the state of the toggles.  Where 'state' needs to be true for open and false for close.
function allToggle(state)
{
    var target;
    var displaySetting;

    if (state == false)
    {
         // All on to set off!
        if (ie == true)
        {
            displaySetting = "block"; // IE is always different from the rest!
        }
        else
        {
            displaySetting = "table-row";
        }
    }
    else
    {
        // Set all off to set on.
        displaySetting = "none";
    }

    for (var theToggle = 1; theToggle <= numToggles; theToggle++)
    {
        target = document.getElementById("section-"+theToggle);
        target.style.display = displaySetting;
        toggleexacttopic(target,document.getElementById("sectionatag-" + theToggle),theToggle,false);
    }
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