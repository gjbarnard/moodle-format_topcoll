Introduction
------------
Topic based course format with an individual 'toggle' for each topic except 0.  This format differs from the
Accordion format in that two or more topics can be visible at the same time.

This version works with Moodle 1.9.x.

Documented on http://docs.moodle.org/19/en/Collapsed_Topics_course_format

Installation
------------
 1. Put Moodle in 'Maintenance Mode' (docs.moodle.org/en/admin/setting/maintenancemode) so that there are no users using it bar you as the
   adminstrator.
 2. Copy 'topcoll' to '/course/format/'
 3. If using a Unix based system, chmod 755 on config.php - I have not tested this but have been told that it needs to be done.
 4. In 'config.php' change the values of '$TCCFG->defaultlayoutelement' and '$TCCFG->defaultlayoutstructure' for setting the default layout and
    structure respectively for new / updating courses as desired by following the instructions contained within.
 5. In 'config.php' change the value of '$TCCFG->defaultcookieconsent' in line with your countries law on cookies.
 6. Login as an administrator and follow standard the 'plugin' update notification.  If needed, go to 'Site administration' -> 'Notifications' if
    this does not happen.
 7. If desired, edit the colours of the 'topics_collapsed.css' file - which contains instructions on how to have per theme colours.
 8. To change the arrow graphic you need to replace 'arrow_up.png' and 'arrow_down.png'.  Reuse the graphics if you want.  Created in Paint.Net.
 9. If you are not using English, please examine your langauge (or create one by copying the 'en_utf8' folder) in the 'lang' folder and translate
    the sentances I am unable to translate.
10. Put Moodle out of Maintenance Mode.

Upgrade Instructions
--------------------
1. Put Moodle in 'Maintenance Mode' so that there are no users using it bar you as the adminstrator.
2. In '/course/format/' move old 'topcoll' directory to a backup folder outside of Moodle.
3. Follow installation instructions above.
4. Put Moodle out of Maintenance Mode.

Uninstallation
--------------
1. Put Moodle in 'Maintenance Mode' so that there are no users using it bar you as the adminstrator.
2. It is recommended but not essential to change all of the courses that use the format to another.  If this is not done Moodle will pick the last format in your list of formats to use but display in 'Edit settings' of the course the first format in the list.  You can then set the desired format.
3. In '/course/format/' remove the folder 'topcoll'.
4. In the database, remove the tables 'format_topcoll_layout' and 'format_topcoll_cookie_cnsnt' along with the entry for 'format_topcoll'
   ('plugin' attribute) in the table 'config_plugins'.  If using the default prefix this will be 'mdl_format_topcoll_layout',
   'mdl_format_topcoll_cookie_cnsnt' and 'mdl_config_plugins' respectively.
5. Put Moodle out of Maintenance Mode.

Course Backup and Restore Instructions
--------------------------------------
1. Backup as you would any other course.  The layout configuration will be stored with the course settings.
2. Restore as you would any other course.  There is no option of 'Overwrite Course Configuration' so the restored course will have the default in the 'config.php' file as mentioned in the 'Installation' instructions above when restoring to an existing course.
3. Note: I believe that if you restore a Collapsed Topic's course on an installation that does not have the format then it will work and become the default course format.  However the layout data will not be stored if you install Collapsed Topic's at a later date.

UK / EU Cookie Law
------------------
On the 28th April 2012 I became aware of a new UK Cookie Law that will be in force on the 26th May 2012 and I think earlier in the EU.  In reading http://www.ico.gov.uk/for_organisations/privacy_and_electronic_communications/the_guide/cookies.aspx and the associated guidance on http://www.ico.gov.uk/for_organisations/privacy_and_electronic_communications/the_guide/~/media/documents/library/Privacy_and_electronic/Practical_application/guidance_on_the_new_cookies_regulations.ashx I considered that the cookie used for remembering the state of the toggle is 'strictly necessary' according to page nine of the guidance.  However, in reading 'http://www.international-chamber.co.uk/components/com_wordpress/wp/wp-content/uploads/2012/04/icc_uk_cookie_guide.pdf' it could be considered that it is a 'Category 3' cookie and therefore wording does need to be placed on the course before a toggle is pressed.  Therefore to support this I have created version 2.2.6 to allow you to support the law if you deem necessary as I am not a lawyer.

The format creates and stores a cookie 'mdl_cf_topcoll' on the users computer.  This is a session cookie (unless changed to a persistent one in 'Remembered Toggle State Instructions' below).  The cookie consists of one or more strings of the format 'SiteshortnameCourseid=Data'.  The 'Data' is a base 36 encoded 53 bit string to represent the state of the toggles.  There are 52 bits, one for each toggle, because of the maximum number of sections that can be defined in the course settings.  The 53'rd bit is always '1' so that preceeding '0's are not eleminated in the conversion process.  Base 36 encoding is used purely for compression purposes as cookies have limited storage space and use of base 16 'hexidecimal' would be a larger string.  I consider that the statement in the guidance 'where such storage or access is strictly necessary for the provision of an information society service requested by the subscriber or user' on page nine applies because each bit is used to set the state of the toggle thus being open or closed.  When the user clicks on a toggle, it's state is remembered so that when they return / refresh the course the toggles are as they left them.  That's it, they are not transmitted or used for any other purpose.  Therefore in 'requesting' to use the course and the format it is 'essential to provide the service requested by the user' and the cookie is not stored until a toggle is clicked upon.  But it could also be said of 'Category 3' where it says 'These cookies are used to remember customer selections that change the way the site behaves or looks.' in the 'icc_uk_cookie_guide.pdf' - hence the implementation of the functionality.

Cookie Consent Information
--------------------------
The state of consent for using cookies is stored in the table 'format_topcoll_cookie_cnsnt' in the database.  When a user first accesses any 'Collapsed Topics' course an entry is made for them.  The 'id' field is a standard sequence as defined for all Moodle tables.  The 'userid' attribute is the standard Moodle user id value.  The 'cookieconsent' attribute can have one of three values, 1 - Display message (Default), 2 - Use cookie and 3 - Don't use cookie.  If you need to perform a 'reset' on all or one of the users as indicated in the consent message for whatever reason, then this is the table to alter.  It is quite safe to remove one or more records whilst the system is running.  Once a user makes a selection, then this decision will apply to all 'Collapsed Topics' courses.  There is no current code to delete an entry when a user is deleted - I'm not yet sure of how to do this.

It is worth noting that the 'format_topcoll_cookie_cnsnt' table is not backed up by the format's backup code as that pertains to course backups and this is a system thing.

You can turn on '$TCCFG->defaultcookieconsent' in 'config.php' at any time even when the system is being used by users.

Remembered Toggle State Instructions
------------------------------------
To have the state of the toggles be remembered beyond the session for a user (stored as a cookie in the user's 
web browser local storage area), edit format.php and find the following at the towards the top...

<script type="text/javascript">
//<![CDATA[
    topcoll_init('<?php echo $CFG->wwwroot ?>',
                 '<?php echo preg_replace("/[^A-Za-z0-9]/", "", $SITE->shortname) ?>',
                 '<?php echo $course->id ?>',
                 null); <!-- Expiring Cookie Initialisation - replace 'null' with your chosen duration. -->
//]]>
</script>

The word to change is 'null' which says to create a 'session cookie' for the toggle state.  There are several
predefined durations available: 'aSecond', 'aMinute', 'anHour', 'aDay', 'aWeek', 'aMonth' and 'aYear'.  For
example a remembered state of a week would be:

<script type="text/javascript">
//<![CDATA[
    topcoll_init('<?php echo $CFG->wwwroot ?>',
                 '<?php echo preg_replace("/[^A-Za-z0-9]/", "", $SITE->shortname) ?>',
                 '<?php echo $course->id ?>',
                 aWeek); <!-- Expiring Cookie Initialisation - replace 'null' with your chosen duration. -->
//]]>
</script>

You can combine the durations together and perform mathematical operations, for example, to have a
duration in the future of one day 38 minutes and 30 seconds you would have:

<script type="text/javascript">
//<![CDATA[
    topcoll_init('<?php echo $CFG->wwwroot ?>',
                 '<?php echo preg_replace("/[^A-Za-z0-9]/", "", $SITE->shortname) ?>',
                 '<?php echo $course->id ?>',
                 aDay + (aMinute * 38) + (aSecond * 30)); <!-- Expiring Cookie Initialisation - replace 'null' with your chosen duration. -->
//]]>
</script>

To revert back to session cookies, simply put back the word 'null'.

NOTE: The client's browser must support the persistent storage of cookies in the user's profile for this to work.  I realise that
      some configured systems do not allow this and therefore this mechanism will not work.  However, I anticipate that setting
      an expiring cookie will be fine as it will simply be deleted in environments where they are removed on log out, but will have
      use when the user is at home and remotely logs in.

Known Issues
------------

1.  If you get toggle text issues in languages other than English please ensure you have the latest version of Moodle installed.  More
    information on http://moodle.org/mod/forum/discuss.php?d=184150.
2.  AJAX drag and drop appears not to be working in IE 9 for me, but is in compatibility mode (IE 7) and same issue with the standard
    topics format too.  Hence I consider it to be either an issue with my system or Moodle Core.  If you experience it and wish to use
    the up and down arrows, edit ajax.php and remove "'MSIE' => 6.0," from:
    "$CFG->ajaxtestedbrowsers = array('MSIE' => 6.0, 'Gecko' => 20061111, 'Opera' => 9.0, 'Safari' => 531, 'Chrome' => 6.0);"
    And if possible, please let me know, my Moodle.org profile is 'http://moodle.org/user/profile.php?id=442195'.

Version Information
-------------------
21st February 2009 - Version 0.1

1st March 2009 - Version 0.2

2nd March 2009 - Version 1.0

3rd March 2009 - Version 1.1.  Moodle Tracker CONTRIB-1081
  Adjusted the Topic Toggle to make the topic summary standout more.

28th June 2009 - Version 1.2 - Persistence - tested on Moodle 1.9.5.  Moodle Tracker CONTRIB-1363
  1. Persistence is session based on a per user per course basis.
  2. Cookies must be enabled for it to work.
  3. I need to tidy up the code and remove the development comments.
  4. I would like to slightly alter the binary string to be an array.
  5. I would like to make the lib.js functions a part of a class for future proofing.
  6. Sort out page refresh event so that it works instead of saving the cookie every time a toggle is toggled.
  
15th July 2009 - Version 1.3 - Visual tidy up and Javascript file reduction! Moodle Tracker CONTRIB-1471 / CONTRIB-1423
  1. Moved the prefix words of 'Topic x' to the right hand side of the toggle when the summary exists.
  2. Compressed the lib.js into lib_min.js for faster loading using YUICompressor - original source still available.
  3. Moved as much as possible into css so that the files can be cached by the web browser and less transmitted in
     terms of HTML.
  4. Sorted out the way the topic table is constructed in terms of column widths to be more robust on different
     web browsers.  Tested on a Vista PC with: FireFox 3.5, IE 8.0.6001.18783 in both normal and compatibility
     modes, Google Chrome 2.0.172.33, Safari 4.0 (530.17) and Opera 9.64 build 10487.

21st August 2009 - Version 1.3.1 - Fully comment code for future reference.  Moodle Tracker CONTRIB-1486
  Additionally there are now two branches, HEAD and MOODLE_19_STABLE - this is the 1.9 branch.
  Please see the documentation on http://docs.moodle.org/en/Collapsed_Topics_course_format

24th August 2009 - Version 1.3.2 - Moodle Tracker CONTRIB-1494
  1. Removed duplication in section name.
  2. Tidied up format.php to be XHTML strict in line with http://docs.moodle.org/en/Development:JavaScript_guidelines
  
23rd January 2010 - Version 1.3.3 - Moodle Tracler CONTRIB-1756
  1. Put instructions in the CSS file 'topics_collapsed.css' on how you can define theme based toggle colours.
  2. Redesigned the arrow to be more 'modern'.

16th February 2010 - Version 1.3.4 - Moodle Tracker CONTRIB-1825
  1. Removed the capability to 'Show topic x' unless editing as confusing to users.
  2. Removed redundant 'aToggle' as existing $course->numsections already contained the correct figure
     and counting toggles that are displayed causes an issue when in 'Show topic x' mode as the toggle
     number does not match the display number for the specific element.
  3. Removed redundant calls to 'get_context_instance(CONTEXT_COURSE, $course->id)' as result already
     stored in $context variable towards the top - so use in more places.

5th April 2010 - Version 1.3.5 - Moodle Tracker CONTRIB-1952 & CONTRIB-1954
  1. CONTRIB-1952 - Having an apostrophy in the site shortname causes the format to fail.
  2. CONTRIB-1954 - Reloading of the toggles by using JavaScript DOM events not working for the function reload_toggles,
     but instead the function was being called at the end of the page regardless of the readiness state of the DOM.

9th April 2010 - Version 1.3.6 - Moodle Tracker CONTRIB-1973
  1. Tidied up format.php, made the fetching of topic and toggle names more efficient and sorted an incorrect comment.
  2. Tidied up this file.
  
11th September 2010 - Version 1.3.7 - Moodle Tracker CONTRIB-2355
  1. Added the ability to remove 'topic x' and the section number from being displayed.  To do this, open up
     format.php in a text editor - preferably with line numbers displayed - such as Notepad++ - and read the 
     instructions on lines 239 and 249.

6th November 2010 - Version 1.3.8 - Moodle Tracker CONTRIB-2497
  1. Added Dutch language.  Thanks to Pieter Wolters - http://moodle.org/user/profile.php?id=537037 - for this.
  2. Added German, French, Spanish (Spain, Mexico and International), Italian, Polish, Portuguese (Brazil too) 
     and Welsh.  I used Google Translate! If inaccurate, please let me know!

7th November 2010 - Version 1.3.9 - Moodle Tracker CONTRIB-2497
  1. Added the string 'topcolltogglewidth' to the relevant language file and amended format.php so that
     the word 'Topic' when translated fits within the toggle.

12th November 2010 - Version 1.3.9.1 - Moodle Tracker CONTRIB-2497
  1. Fixed issue with missing semi-colon in language file that appeared not to affect Moodle 1.9 as it
     did with the Moodle 2.0 version, but corrected anyway as a PHP syntax bug. 

14th January 2011 - Version 1.4 - Moodle Tracker CONTRIB-2660
  1. Removed redundant call to 'time()' thus was wasting processor time. 

12th March 2011 - Version 1.5 - Moodle Tracker CONTRIB-2747
  1. Make the toggle state last beyond the user session if desired.
  2. Added id of "sectionblock-0" / "sectionblock-'.$section.'" for the left side, see MDL-18232.
  3. Added class of "icon topicall" for right side expansion icons when editing, see MDL-20757.
  
16th March 2011 - Version 1.5.1 - Moodle Tracker CONTRIB-2747
  1. Quick fix for Internet Explorer as it does not understand the Javascript 'const' keyword!

30th May 2011 - Version 1.5.2 - Moodle Tracker CONTRIB-2963
  1. Added in copyright and contact information.

9th June 2011 - Version 1.5.3 - Moodle Tracker CONTRIB-2975 - Unfinished.
  1. AJAX support temporarily withdrawn due to issue with moving sections and the toggle title not following.
     Complex to resolve.

6th October 2011 - Version 1.6 - Moodle Tracker CONTRIB-2975, CONTRIB-3189 and CONTRIB-3190.
  1. CONTRIB-2975 - AJAX support reinstated after working out a way of swapping the content as well as the toggle.  Solution sparked off by
                    Amanda Doughty (http://tracker.moodle.org/secure/ViewProfile.jspa?name=amanda.doughty).
  2. CONTRIB-3189 - Reported by Benn Cass that text in IE8- does not hide when the toggle is closed, solution suggested
                    by Mark Ward (http://moodle.org/user/profile.php?id=489101) - please see http://moodle.org/mod/forum/discuss.php?d=183875.
  3. CONTRIB-3190 - In realising that to make CONTRIB-2975 easier to use I suggested 'Toggle all' functionality and the
                    community said it was a good idea with no negative comments, please see (http://moodle.org/mod/forum/discuss.php?d=176806).

11th October 2011 - Updated version.php to be fully populated.

8th December 2011 - Version 1.9.6.1 - Moodle Tracker CONTRIB-2497
  1. Updated Brazilian translation thanks to Tarcísio Nunes (http://moodle.org/user/profile.php?id=1149633).
  2. Changed version to relate to Moodle version, so this is for Moodle 1.9.

9th December 2011 - Version 1.9.6.2 - Moodle Tracker CONTRIB-3295
  1. Fixed issue of the web browser miscaluating the width of the content in 'editing' mode so that the sections
     are less than 100%.

10th January 2012 - Version 1.9.6.2.1
  1. Corrected licence to be correct one used by Moodle Plugins - thanks to Tim Hunt (http://moodle.org/user/profile.php?id=93821).

23rd January 2012 - Version 1.9.7
  1. Sorted out UTF-8 BOM issue, see MDL-31343.
  2. Added Russian translation, thanks to Pavel Evgenjevich Timoshenko (http://moodle.org/user/profile.php?id=1322784).

2nd March 2012 - Version 1.9.8
  1. Integrated in CONTRIB-3378 (Multiple Layouts) and associated CONTRIB-3283 (Current Section) CONTRIB-3225 (Screen Reader) from the Moodle 2.2
     branch.
  2. Still need to get help buttons working on the 'set layout form'.
  3. NOTE: This is the first integration of CONTRIB-3378 which requires further testing.  If you encounter an issue please revert back to version
     1.9.7 using the uninstallation and then installation instructions above.  And if you could let me know that would be a bonus :).

3rd March 2012 - Version 1.9.8.1
  1. Help buttons on the 'set_layout' form now working in English and Spanish.  If you need your language, copy the folder 'help' from 'en_utf8'
     into your language folder and edit the files contained within.
  2. General tidy up of 'set_layout' form and code.
  3. Fixed issue in lib.js and hence lib_min.js because the arrow images had moved.
  4. Optimised format.php to avoid logical duplicate code.

17th March 2012 - Version 1.9.9
  1. Integrated CONTRIB-3520 such that the structure is saved in the course backup file and restored except when restoring to an existing course as
     Moodle 1.9 does not have the 'Overwrite course configuration' on course merge as Moodle 2+ does.
  2. Remove the entry for the course in the table 'format_topcoll_layout' when the course is deleted by adding
     'topcoll_course_format_delete_course' to 'lib.php'.
  3. Added language strings to the language files that were missing previous changes.  Still in English at the moment in the hope a native speaker
     will translate them for me as Google Translate is not so good with long sentences.  Translated the words 'Topic' and 'Week' in all language
     files so that the toggle bar is correct in all structures.
  4. Added the 'help' folder for each language in English to every language that does not have them.  Translation appreciated.
  5. Added backup and restore instructions to this file.

2nd May 2012 - Version 1.9.10 - CONTRIB-3624
  1. Implemented code to facilitate the ability to confirm with the user that it is ok to place the cookie 'mdl_cf_topcoll' on their computer.
     This fucntionality can be switched on / off through the changing of '$TCCFG->defaultcookieconsent' in the format's 'config.php'.  This
     functionality exists because I believe that the cookie is a 'Category 3' cookie in line with the forthcoming UK EU Cookie Law - please
     see 'UK / EU Cookie Law' at the top of this file.
  2. Created a '$TCCFG' object in the 'config.php' file to solve the 'globals' issue in 'lib.php'.
  3. Added the ability to reset the layout attributes back to the defaults as defined in the 'config.php' file.

31st May 2012 - Version 1.9.10.1 - CONTRIB-3682
  1. Fixed issue with students not being able to perform cookie consent because of incorrect application of requiring the capability of course update.

Thanks
------
I would like to thank Anthony Borrow - arborrow@jesuits.net & anthony@moodle.org - for his invaluable input.

For the Peristence upgrade I would like to thank all those who contributed to the developer forum -
http://moodle.org/mod/forum/discuss.php?d=124264 - Frank Ralf, Matt Gibson, Howard Miller and Tim Hunt.  And
indeed all those who have worked on the developer documentation - http://docs.moodle.org/en/Javascript_FAQ.

Michael de Raadt for CONTRIB-1945 & 1946 which sparked fixes in CONTRIB-1952 & CONTRIB-1954

Amanda Doughty (http://moodle.org/user/profile.php?id=1062329) for her contribution in solving the AJAX move problem.

Mark Ward (http://moodle.org/user/profile.php?id=489101) for his contribution solving the IE8- display problem.

Pieter Wolters (http://moodle.org/user/profile.php?id=537037) - for the Dutch translation.

Tarcísio Nunes (http://moodle.org/user/profile.php?id=1149633) - for the Brazilian translation.

Pavel Evgenjevich Timoshenko (http://moodle.org/user/profile.php?id=1322784) - for the Russian translation.

All of the developers of the Grid Course format (https://github.com/PukunuiAustralia/moodle-courseformat_grid) for showing how the database can be used with a course format.

Carlos Sánchez Martín (http://moodle.org/user/profile.php?id=743362) for his assistance on CONTRIB-3378 and the Spanish translation.

Andrew Nicols (http://moodle.org/user/view.php?id=268794) for his assistance on CONTRIB-3378.

Hartmut Scherer (http://moodle.org/user/view.php?id=441502) for suggesting the 'Current Topic First' structure and testing the Moodle 2.2 code on discussion 'Collapsed Topics with Custom Layouts' (http://moodle.org/mod/forum/discuss.php?d=195292).

References
----------
.Net Magazine Issue 186 - Article on Collapsed Tables by Craig Grannell -
 http://www.netmag.co.uk/zine/latest-issue/issue-186

Craig Grannell - http://www.snubcommunications.com/

Accordion Format - Initiated the thought - http://moodle.org/mod/forum/discuss.php?d=44773 & 
                                           http://www.moodleman.net/archives/47

Paint.Net - http://www.getpaint.net/

JavaScript: The Definitive Guide - David Flanagan - O'Reilly - ISBN: 978-0-596-10199-2

Integrated 'Cookie' icon from http://www.iconfinder.com/icondetails/6279/128/cake_cookie_icon - The rest of the 'tc_logo_cookie.png' image is my own work.

Desired Enhancements
--------------------
1. Use ordered lists / divs instead of tables to fall in line with current web design theory.  Older versions of
   'certain' browsers causing issues in making this happen.
2. Smoother animated toggle action.

G J Barnard MSc. BSc(Hons)(Sndw). MBCS. CEng. CITP. PGCE. - 31st May 2012.
Moodle profile: moodle.org/user/profile.php?id=442195.
Web profile   : about.me/gjbarnard