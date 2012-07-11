Introduction
------------
Topic based course format with an individual 'toggle' for each topic except 0.

This version works with Moodle 2.3.1 Build: 20120706 and above until the next release.

Documented on http://docs.moodle.org/23/en/Collapsed_Topics_course_format

New features for this Moodle 2.3.1 version
------------------------------------------
1. One to four columns which can be set on the Collapsed Topics settings form (one column for MyMobile users 
   regardless of this setting).
2. Persistence now uses user preferences on the server which facilitates remembrance beyond the session and
   removal of the evil cookie.
3. Administrators can now reset the layout and colours of all Collapsed Topics courses via the settings form.
4. New 'Days' structure which has each section as a day.  The first section is the day of the start date.
5. Removed the use of tables for layout and now using more conventional div's and unordered lists which should
   be better for theme compatibility.
6. A slight reworking to operate with the MyMobile theme - a few issues to resolve, please see 'Known Issues' below.
7. When the course layout setting is "Show one section per page" in the course settings then the toggles are not
   displayed as each section just contains a link to the section with the content.  But when editing toggles are
   shown as the section contains the content.  The column functionality is implemented in both instances.

Installation
------------
 1. If upgrading from a previous version of Moodle please see 'Upgrading from Moodle 1.9, 2.0, 2.1' and
    'Upgrading from Moodle 2.2.x' below.
 2. Put Moodle in 'Maintenance Mode' (docs.moodle.org/en/admin/setting/maintenancemode) so that there are no 
    users using it bar you as the administrator - if you have not already done so.
 3. Copy 'topcoll' to '/course/format/' if you have not already done so.
 4. If using a Unix based system, chmod 755 on config.php - I have not tested this but have been told that it
    needs to be done.
 5. In 'config.php' change the values of '$TCCFG->defaultlayoutelement', '$TCCFG->defaultlayoutstructure' and
    '$TCCFG->defaultlayoutcolumns' for setting the default layout, structure and columns respectively for
	new / updating courses as desired by following the instructions contained within.
 6. In 'config.php' change the values of '$TCCFG->defaulttgfgcolour', '$TCCFG->defaulttgbgcolour' and
    '$TCCFG->defaulttgbghvrcolour' for setting the default toggle colours.
 7. Login as an administrator and follow standard the 'plugin' update notification.  If needed, go to
    'Site administration' -> 'Notifications' if this does not happen.
 8.  If desired, edit the colours of the 'styles.css' - which contains instructions on how to have per theme colours.
 9.  To change the arrow graphic you need to replace 'arrow_up.png' and 'arrow_down.png'.  Reuse the graphics
     if you want.  Created in Paint.Net.
10.  Put Moodle out of Maintenance Mode.

Upgrade Instructions
--------------------
1. If upgrading from a previous version of Moodle please see 'Upgrading from Moodle 1.9, 2.0 or 2.1' and
   'Upgrading from Moodle 2.2' below.
2. Put Moodle in 'Maintenance Mode' so that there are no users using it bar you as the administrator.
3. In '/course/format/' move old 'topcoll' directory to a backup folder outside of Moodle.
4. If you have previously installed a development, beta or release candidate of version 2.3.7 you need to
   perform step 4 in 'Uninstallation' below.
5. Follow installation instructions above.
6. Put Moodle out of Maintenance Mode.

Upgrading from Moodle 1.9, 2.0 or 2.1
-------------------------------------
Moodle 2.3.1 requires that Moodle 2.2 is installed to upgrade from, so therefore Moodle 2.2 is an intermediate step.
So:
1. Put Moodle in 'Maintenance Mode' so that there are no users using it bar you as the administrator.
2. In '/course/format/' move old 'topcoll' directory to a backup folder outside of Moodle.
3. Do not copy in the new version of 'topcoll' yet!  As this will cause the upgrade to fail.
4. Upgrade to Moodle 2.2 first - http://docs.moodle.org/22/en/Upgrading_to_Moodle_2.2.
5. After you have installed Moodle 2.2, now upgrade to Moodle 2.3.1 with this new topcoll - 
   http://docs.moodle.org/23/en/Upgrading_to_Moodle_2.3 - but before initiating the upgrade you can copy the
   new (i.e. this) 'topcoll' folder to '/course/format'.
6. Now follow 'Upgrading from Moodle 2.2.x' below please.
INFO: Having no 'topcoll' folder in '/course/format' is fine as the courses that use it are not accessed and
      both the old and new versions will confuse an intermediate 2.2 version and cause it's installation to fail.

Upgrading from Moodle 2.2.x
---------------------------
1.    First ensure you start with release 2.3.7.1 of Collapsed Topics (available from the plugins database) of the
      11th July 2012 or above.
2.    Put Moodle in 'Maintenance Mode' so that there are no users using it bar you as the administrator - if you have
      not already done so.
3.    In '/course/format/' move old 'topcoll' directory to a backup folder outside of Moodle - if you have not
      already done so.
4.    Copy this new 'topcoll' folder to '/course/format/'.
5.    Upgrade to Moodle 2.3.1 by being logged in as admin and clicking on 'Home'.  If you have previously upgraded but
      'topcoll' was an old version and the upgrade failed, this should still work.
5.    Follow installation instructions above.
6.    Put Moodle out of Maintenance Mode.
NOTE: If the automated upgrade fails for which can be seen by getting errors when using a Collapsed Topics course,
      then please follow this.
      Please carry on if a table / field has been removed / changed / already exists as it should still work - this 
	  is to cope with the different possible scenarios.  These instructions are written with the MySQL database in
	  mind, however should work with other database engines but the types should be compared with other tables in 
	  the database to get an idea of what they should be.  If possible please kindly feedback to me any additional
	  information you discover so I can update these instructions - contact details at the very bottom.
	  The table prefix i.e, 'mdl_' is not stated in the instructions but ensure you know what yours is and use
	  it with the table names.
1.    In your database:
2.1   Rename the table 'format_topcoll_layout' to 'format_topcoll_settings'.
2.2   With the table 'format_topcoll_settings' change all integer types to signed if using a MySQL database.
2.3   If the table 'format_topcoll_settings' does not exist, then create it and add the following fields 
      in this order:
2.3.1 'id' of type 'BIGINT(10)' type, not null, auto increment, no zero fill with a null default value - the same 
       as any other 'id' field in the other tables.  Make it the primary key.
2.3.2 'courseid' of type 'BIGINT(10)' type, not null, no auto increment, no zero fill with a null default value - the
      same as the 'course' field in the 'course_sections' table bar the default value.
2.3.3 'layoutelement' of type 'TINYINT(2)' type, not null, no auto increment, no zero fill with a default value
      of '1'.
2.3.4 'layoutstructure' of type 'TINYINT(1)' type, not null, no auto increment, no zero fill with a default value
      of '1'.
2.4   With the table 'format_topcoll_settings' append three new fields of 'VARCHAR(6)' type, not null, called
      'tgfgcolour', 'tgbgcolour' and 'tgbghvrcolour' in that order with the default values of '000000', 'e2e2f2'
      and 'eeeeff' respectively.
2.5   With the table 'format_topcoll_settings' append a new field 'layoutcolumns' after the 'layoutstructure' field
      and with identical size, type and attributes.  The default is '1'. i.e:
2.5.1 'layoutcolumns' of type 'TINYINT(1)' type, not null, no auto increment, no zero fill with a default value
      of '1'.
2.6   Drop the table 'format_topcoll_cookie_cnsnt'.

Upgrading from a beta or release candidate version of Collapsed Topics for Moodle 2.3
-------------------------------------------------------------------------------------
1. Please perform step 4 of uninstallation instructions below.
2. Drop the table 'format_topcoll_cookie_cnsnt' if it exists.
3. Follow installation instructions above.

Uninstallation
--------------
1. Put Moodle in 'Maintenance Mode' so that there are no users using it bar you as the administrator.
2. It is recommended but not essential to change all of the courses that use the format to another.  If this is
   not done Moodle will pick the last format in your list of formats to use but display in 'Edit settings' of the
   course the first format in the list.  You can then set the desired format.
3. In '/course/format/' remove the folder 'topcoll'.
4. In the database, remove the table 'format_topcoll_settings' along with the entry for 'format_topcoll'
   ('plugin' attribute) in the table 'config_plugins'.  If using the default prefix this will be
   'mdl_format_topcoll_settings' and 'mdl_config_plugins' respectively.
5. Put Moodle out of Maintenance Mode.

Course Backup and Restore Instructions
--------------------------------------
1. Backup as you would any other course.  The layout configuration will be stored with the course settings.
2. Restore as you would any other course.  If you are offered the option of 'Overwrite Course Configuration'
   you must say 'Yes' to have thelayout configuration restored otherwise the restored course will retain the
   layout it previously had or the default in the 'config.php' file as mentioned in the 'Installation'
   instructions above depending on the situation.
3. Note: I believe that if you restore a Collapsed Topic's course on an installation that does not have the
         format then it will work and become the default course format.  However the layout data will not be
		 stored if you install Collapsed Topic's at a later date.


Remembered Toggle State Information
-----------------------------------
The state of the toggles are remembered beyond the session on a per user per course basis though the employment of a user preference.  This functionality is now built in from previous versions.  You do not need to do anything.

Known Issues
------------
1.  If you get toggle text issues in languages other than English please ensure you have the latest version of Moodle installed.  More
    information on http://moodle.org/mod/forum/discuss.php?d=184150.
2.  The MyMobile theme is not quite as implemented as the previous versions but does work, please see http://tracker.moodle.org/browse/MDL-33115.
    Also has a tendency not to reload the toggle state or set the arrow icon on first load.  A page refresh fixes it - something to do with the inclusion
	of a '#' in the url.  I have contacted 'John Stabinger' on 'MDL-33115' and he will be looking into it.
3.  Importing a Moodle 1.9 course does not currently work, please see CONTRIB-3552 which depends on MDL-32205 - as
    a workaround, please select the 'Topics' format first in 1.9, backup and restore then select the Collapsed Topics
	course format in the course settings.  You will have to reset your decisions on structure etc.

Version Information
-------------------
21st February 2009 - Version 0.1

1st March 2009 - Version 0.2

2nd March 2009 - Version 1.0

3rd March 2009 - Version 1.1
  Adjusted the Topic Toggle to make the topic summary standout more.

28th June 2009 - Version 1.2 - Persistence - tested on Moodle 1.9.5.
  1. Persistence is session based on a per user per course basis.
  2. Cookies must be enabled for it to work.
  3. I need to tidy up the code and remove the development comments.
  4. I would like to slightly alter the binary string to be an array.
  5. I would like to make the lib.js functions a part of a class for future proofing.
  6. Sort out page refresh event so that it works instead of saving the cookie every time a toggle is toggled.
  
15th July 2009 - Version 1.3 - Visual tidy up and Javascript file reduction!
  1. Moved the prefix words of 'Topic x' to the right hand side of the toggle when the summary exists.
  2. Compressed the lib.js into lib_min.js for faster loading using YUICompressor - original source still available.
  3. Moved as much as possible into css so that the files can be cached by the web browser and less transmitted in
     terms of HTML.
  4. Sorted out the way the topic table is constructed in terms of column widths to be more robust on different
     web browsers.  Tested on a Vista PC with: FireFox 3.5, IE 8.0.6001.18783 in both normal and compatibility
     modes, Google Chrome 2.0.172.33, Safari 4.0 (530.17) and Opera 9.64 build 10487.

16th July 2009 - Moodle 2.0 Development Version
  This is now the 2.0 development version under the HEAD CVS Tag.
  
Development Notes:  
21st August 2009 -
  1. Fully comment code for future reference.
  2. Please see the documentation on http://docs.moodle.org/en/Collapsed_Topics_course_format

24th August 2009 -
  1. Removed duplication in section name.
  2. Tidied up format.php to be XHTML strict in line with http://docs.moodle.org/en/Development:JavaScript_guidelines -
     but I will need to revisit this at the end of development to tidy up any unintentional introduced issues &
     adapt to have a non-javascript functionality where all the contents of the toggles are shown and the toggles do
     not exist.
  3. Converted to using the Page Requirements Manager ($PAGE) as much as possible for JavaScript.
  
23rd January 2010 - Moodle Tracler CONTRIB-1756
  1. Put instructions in the CSS file 'topics_collapsed.css' on how you can define theme based toggle colours.
  2. Redesigned the arrow to be more 'modern'.

16th February 2010 - Moodle Tracker CONTRIB-1825
  1. Removed the capability to 'Show topic x' unless editing as confusing to users.
  2. Removed redundant 'aToggle' as existing $course->numsections already contained the correct figure
     and counting toggles that are displayed causes an issue when in 'Show topic x' mode as the toggle
     number does not match the display number for the specific element.
  3. Removed redundant calls to 'get_context_instance(CONTEXT_COURSE, $course->id)' as result already
     stored in $context variable towards the top - so use in more places.
     
5th April 2010 - Moodle Tracker CONTRIB-1952 & CONTRIB-1954
  1. CONTRIB-1952 - Having an apostrophy in the site shortname causes the format to fail.
  2. CONTRIB-1954 - Reloading of the toggles by using JavaScript DOM events not working for the function reload_toggles,
     but instead the function was being called at the end of the page regardless of the readiness state of the DOM.       

31st July 2010 - Summary of developments towards release version as I keep pace with Moodle 2.0 changes:
  13th April 2010 - CONTRIB-1471 - Changes as a result of MDL-15252, MDL-21693 & MDL-22056.
  24th April 2010 - CONTRIB-1471 - Fixed section jump when in 'Show only topic x' mode.
  31st May 2010 - CONTRIB-1471 - thanks to Skodak in 1.120 of format.php in the topics format - summaryformat attribute in section class.
  11th June 2010 - CONTRIB-1471 as a result of  MDL-22647 - Changes to Moodle 2.0 call-backs in lib.php.
  3rd July 2010 - CONTRIB-1471 as a result of MDL-20475 & MDL-22950.
  30th July 2010 - CONTRIB-1471 as a result of MDL-20628 and CONTRIB-2111 - in essence, sections now have a name attribute, so this can be
                   used for the topic name instead of the section summary - far better.
                   
12th September 2010 - Moodle Tracker CONTRIB-2355 & CONTRIB-1471
  1. CONTRIB-2355 - Added the ability to remove 'topic x' and the section number from being displayed.  To do this, open up
     format.php in a text editor - preferably with line numbers displayed - such as Notepad++ - and read the 
     instructions on lines 216 and 226.
  2. CONTRIB-1471 - Changes as a result of MDL-14679. 
  
24th September 2010 - CONTRIB-1471 - Changes as a result of MDL-24321 - changed object to stdClass.

17th October 2010 - CONTRIB-1471 - Changes as a result of MDL-14679, MDL-20366 and MDL-24316.
  1. Removed the requirement of needing js-override-topcoll.css - to make things simpler.
  2. Tidied up some of the JavaScript to be slightly more efficient.
  
25th October 2010 - CONTRIB-1471 - Removal of redundant JavaScript Code.

6th November 2010 - CONTRIB-1471 - Changes as follows:
  1. ajax.php changed to add more browser support as a result of MDL-22528.
  2. format.php changed in light of MDL-24680, MDL-24895, MDL-24927.
  3. Fixed edit icon showing even when not in edit mode.  A big thank you to Peeush Bajpai - http://moodle.org/user/profile.php?id=1127356 - 
     for spotting this and suggesting the fix.
  4. Added Dutch language.  Thanks to Pieter Wolters - http://moodle.org/user/profile.php?id=537037 - for this.
  
12th November 2010 - CONTRIB-1471 & CONTRIB-2497 - Changes as a result of MDL-25072:
  1. Movement of ajax capable stating 'code' from ajax.php to lib.php.
  2. As a consequence, ajax.php removed.
  3. Added German, French, Spanish (Spain, Mexico and International), Italian, Polish, Portuguese (Brazil too) 
     and Welsh.  I used Google Translate! If inaccurate, please let me know!
  4. Added the string 'topcolltogglewidth' to the relevant language file and amended format.php so that
     the word 'Topic' when translated fits within the toggle.

20th November 2010 - CONTRIB-1471 - Changes as follows:
  1. In format.php added completionlib.php include as a result of MDL-24698.
  2. In lib.php fixed non-functioning code added as a result of MDL-22647 which means that the navigation block will
     correctly display the right wording for the section names: 'General' for section 0, 'Topic' for other sections
     unless they have names defined by the user on the course, in which case they will be displayed.  Language
     changes of the 12th November will give translations for 'General' and 'Topic'.

Released Moodle 2.0 version.  Treat as completed and out of development.
25th November 2010 - CONTRIB-1471 - Changes as follows:
  1. As Moodle 2.0 was released on the 24th November now using lib_min.js.
  2. Tidied up and removed any development code / styles that was not being used.
  3. Sorted out topic spacing for Internet Explorer 7 and below.  This also has the side effect bonus of not allowing
     section content to appear above the toggle when the toggle is open and closed with the mouse - reload is not affected.
     This only affects Internet Explorer 7-, other web browsers work as expected.
  4. Removed &nbsp; when no summary as putting in spacing that was pointless and made the section look odd.
  5. Removed redundant $timenow = time() line as not used.  Strangely this is in the topic format's format.php - MDL-25417 raised.

12th March 2011 - Version 1.1 - Moodle Tracker CONTRIB-2747
  1. Make the toggle state last beyond the user session if desired.
  2. Changes made for MDL-25927 & MDL-23939.
  3. Because of 'displaysection' logic issue introduced with MDL-23939, I've decided to allow the showing of a single topic
     regardless of being in editing mode or not.  I think that the improved functionality of showing the topic fully when in
     'single topic' mode will be fine.

9th May 2011 - Version 1.2 - Moodle Tracker CONTRIB-2925
  1. Convert all language files to UTF-8 encoding.
  
12th May 2011 - Version 1.2.1 - Fixed typo with this readme in expiring cookie duration example.

30th May 2011 - Version 1.2.2 - Moodle Tracker CONTRIB-2963
  1. Added in copyright and contact information.
  
9th June 2011 - Version 1.2.3 - Moodle Tracker CONTRIB-2975 - Unfinished.
  1. AJAX support temporarily withdrawn due to issue with moving sections and the toggle title not following.
     Complex to resolve.

6th October 2011 - Version 1.3 - Moodle Tracker CONTRIB-2975, CONTRIB-3189 and CONTRIB-3190.
  1. CONTRIB-2975 - AJAX support reinstated after working out a way of swapping the content as well as the toggle.  Solution sparked off by
                    Amanda Doughty (http://tracker.moodle.org/secure/ViewProfile.jspa?name=amanda.doughty).
  2. CONTRIB-3189 - Reported by Benn Cass that text in IE8- does not hide when the toggle is closed, solution suggested
                    by Mark Ward (http://moodle.org/user/profile.php?id=489101) - please see http://moodle.org/mod/forum/discuss.php?d=183875.
  3. CONTRIB-3190 - In realising that to make CONTRIB-2975 easier to use I suggested 'Toggle all' functionality and the
                    community said it was a good idea with no negative comments, please see (http://moodle.org/mod/forum/discuss.php?d=176806).

11th October 2011 - Version 1.3.1 - Branched from Moodle 2.0.x version.
  1. Updated version.php to be fully populated.
  2. MDL-29188 - Formatting of section name.  Causing Moodle 2.1.x branch of Collapsed Topics.

8th December 2011 - Version 2.2.1 - Moodle Tracker CONTRIB-2497
  1. Updated Brazilian translation thanks to Tarcísio Nunes (http://moodle.org/user/profile.php?id=1149633).
  2. Changed version to relate to Moodle version, so this is for Moodle 2.2.

9th December 2011 - Version 2.3.1.1 - Moodle Tracker CONTRIB-3295
  1. Fixed issue of the web browser miscaluating the width of the content in 'editing' mode so that the sections
     are less than 100%.

3rd January 2012 - Version 2.3.1.1.1 - Moodle Tracker MDL-30632
  1. Use consistent edit section icon.

9th January 2012 - Version 2.3.1.1.2
  1. Corrected licence to be correct one used by Moodle Plugins - thanks to Tim Hunt (http://moodle.org/user/profile.php?id=93821).

23rd January 2012 - Version 2.3.2
  1. Sorted out UTF-8 BOM issue, see MDL-31343.
  2. Added Russian translation, thanks to Pavel Evgenjevich Timoshenko (http://moodle.org/user/profile.php?id=1322784).

2nd February 2012 - Version 2.3.3 - BETA
  1. Added capability for layouts with persistence in the database.

4th February 2012 - Version 2.3.3 - BETA 2
  1. A big thank you to Carlos Sánchez Martín (http://moodle.org/user/profile.php?id=743362) for his help in discovering the install.xml bug.
  2. Fixed issue with install.xml file, gained knowledge on uninstallation for the note below:

5th February 2012 - Version 2.3.3 - BETA 3
  1. A big thank you to Carlos Sánchez Martín (http://moodle.org/user/profile.php?id=743362) spotting issues in set_layout.php.
  2. Fixed issues in set_layout.php.
  3. Tidied up code to remove debug statements and development code.
  4. Created icon for setting the layout instead of words.
  5. Made strings in the English language file for the layout options and 'Set layout format'.  Others to follow.
  6. Raised CONTRIB-3378 to document the development.

8th February 2012 - Version 2.3.3 - BETA 4
  1. A big thank you to Andrew Nicols (http://moodle.org/user/view.php?id=268794) for his contribution on the developer forum (http://moodle.org/mod/forum/discuss.php?d=195293).
  2. Implemented the fixes and suggestions to tidy up the code as specified by Andrew above.
  3. Implemented Spanish translations thanks to Carlos Sánchez Martín (http://moodle.org/user/profile.php?id=743362).

11th February 2012 - Version 2.3.3 - BETA 5
  1. Implemented the capability to have different 'structures' thereby encapsulating the 'Collapsed Weeks' and 'Latest First' formats into this one.
  2. If you have previously installed this development, you need to drop the table 'format_topcoll_layout' in your database to upgrade as I do
     not wish to have a complicated upgrade.php in the db folder at this stage whilst development continues.
  3. As a consequence of some changes, the Spanish translation now needs fixing, sorry Carlos.

12th February 2012 - Version 2.3.3 - BETA 6
  1. Fixed CONTRIB-3283 in lib.js (and hence lib_min.js) for when you are in display only 'Section x' mode and the number of sections is reduced, you go back to the course with a section number for you in the database that no longer exists and the 'Jump to...' drop down box does not work.  Leading to having to change the database or the value of 'ctopics' in the URL to that od a valid one.
  2. Added 'callback_topcoll_get_section_url' in 'lib.php' for MDL-26477.
  3. Corrected slight mistake with version number.

15th February 2012 - Version 2.3.3 - BETA 7
  1. Added strings for MDL-26105 in format_topcoll.php.
  2. Used non-depreciated 'create_table' method in 'upgrade.php'.
  3. Finally worked out how to ensure that the 'Settings Block' displays the course and not front page administration by using 'require_login($course)'.

18th February 2012 - Version 2.3.3 - BETA 8
  1. CONTRIB-3225 - Added screen reader capability using 'h3' tags, the same as the standard Topics format.

25th February 2012 - Version 2.3.3 - Release Candidate 1
  1. Added help information to the drop down options on the set layout form.
  2. Tidied up to be consistent and use less words where required.
  3. In format.php changed from depreciated js_function_call() to js_init_call().
  4. If you have previously installed a beta version you will need to drop the table 'format_topcoll_layout' in the database.
  5. If you are a native speaker of a language other than English, I would be grateful of a translation of the new language strings in 'lang/en/format_topcoll.php' under the comment 'Layout enhancement - Moodle Tracker CONTRIB-3378'.  Please message me using the details in my Moodle profile 'http://moodle.org/user/profile.php?id=442195'.

28th February 2012 - Version 2.3.3 - Release Candidate 2
  1. Added 'Current Topic First' as a new structure as suggested by 'Hartmut Scherer' (http://moodle.org/user/view.php?id=441502) on discussion '
Collapsed Topics with Custom Layouts' (http://moodle.org/mod/forum/discuss.php?d=195292).
  2. Fixed an issue in moving to js_init_call() in RC 1 and then followed the 'JavaScript guidelines' (http://docs.moodle.org/dev/JavaScript_guidelines) and 'How to include javascript file in a new course format?' (http://moodle.org/mod/forum/discuss.php?d=169124) to understand how to transition to using 'module.js' correctly.  Still going to include 'tc_section_classes_min.js' using the old way until I can figure out how to do this the new way.
  3. 'lib.js' and 'lib_min.js' will remain for reference until I backport the code to the Moodle 1.9 version which does not follow the changes in '2' and work out how to merge in Git and not have those files removed in that branch.
  4. In 'Show only section x' mode the 'Open / Close all toggles' option is not shown as not really appropriate.
  5. Topic structure now opens current section by default in the same way as the weekly ones.
  6. Changed name of 'Latest First' to 'Latest Week First' to be clearer.

NOTE: If uninstallation fails, drop the table 'format_topcoll_layout' and the entry for 'format_topcoll' in the table 'config_plugins' where tables are with the prefix you use, the default being 'mdl_'.  Then delete the installation folder and replace with the current stable version.

28th February 2012 - Version 2.3.3 - Release Candidate 3
  1. Tidied up 'module.js' to be more efficient in using the YUI instance given.
  2. Updated installation and toggle state instructions. 
  3. Added uninstall procedure in the unlikely event that you need it.

29th February 2012 - Version 2.3.3 - Release Candidate 4
  1. Updated Spanish language files thanks to Carlos Sánchez Martín.
  2. Added setting default layout and structure to installation instructions.
  3. Decided to have '$formcourselayoutstrutures' out of config.php to prevent possible future user error.
  4. Spotted a minor issue with changing language whilst on the 'Set Layout' form.  Added to known issues as very minor and rare as almost certainly the user will not have changed language on this form but would have done so beforehand.
  5. Fixed duplicate entry issue in 'course_sections' table when the default structure is 'Current Topic First' and a new course is created.

1st March 2012 - Version 2.3.3 - Stable
  1. Integrated Git Branch CONTRIB-3378 into stable branch master.
  2. NOTE: If you have previously installed a Beta or Release Candidate please drop the table 'format_topcoll_layout' before use.
  3. Removed redundant lib.js and lib_min.js in this branch.

2nd March 2012 - Version 2.3.3.1
  1. Minor fix to ensure consistent use of $coursecontext and not $context.

14th March 2012 - Version 2.3.4 - BETA - CONTRIB-3520.
  1. Added backup and restore functionality.  If required when restoring a course 'Overwrite course configuration' needs to be 'Yes' to set the structure and elements correctly.
  2. Added the function 'format_topcoll_delete_course' in 'lib.php' which will remove the entry in the 'format_topcoll_layout' table for the course when it is deleted.
  3. Added language strings to the language files that were missing previous changes.  Still in English at the moment in the hope a native speaker will translate them for me.  I intend to translate the basics like 'Topic' and 'Week' though before release in line with what was already there.

15th March 2012 - Version 2.3.4 - CONTRIB-3520 - Stable.
  1. Completed files for 1.9 and placed in the root folder of the format in the hope that they are executed by the upgrade restoring code as they are in the Moodle 1.9 version of this issue.  I think it is a Moodle core coding issue that they are not called in Moodle 2.x+ when importing a Moodle 1.9 course backup - need to investigate.
  2. Translated the words 'Topic' and 'Week' in all language files so that the toggle bar is correct in all structures.  If you are a native speaker I would appreciate translation of the rest as Google Translate is not so good with long sentences.
  3. Added backup and restore instructions to this file.

17th March 2012 - Version 2.3.4.1
  1. Tried with restorelib.php in the root folder for importing Moodle 1.9 courses and did not work.  So for tidiness, moved the Moodle 1.9 backup and restore code to backup/moodle1 folder.
  2. So please note that restoring Moodle 1.9 courses in this course format will not retain the structure settings and will default to the values in 'config.php'.  I hope to investigate and either fix or have this fixed.
  3. Release '2012030100.02' of Moodle 2.3dev converted all tables to have signed integers in the function 'upgrade_mysql_fix_unsigned_columns()' in '/lib/db/upgradelib.php' called from 'upgrade.php' in the same folder.  This included 'format_topcoll_layout' because of the code was written.  This made it very difficult for me to create an effective upgrade in my own 'upgrade.php' because I would be converting what had already been converted if the format was installed and you were updating Moodle 2.3dev but if you install for the first time, the code has been written as such to have signed fields.  Therefore if you have previously installed this format for Moodle 2.3, please remove the table 'format_topcoll_layout' from your database before upgrading.  This is not quite brilliant, but I consider reasonable for this development version at this stage.
  4. Implemented the change in 'format.php' introduced by MDL-31255, therefore you now require Moodle 2.3 version '2012031500.00'.

21st March 2012 - Version 2.3.4.2
  1. Received an updated version of 'format_topcoll.php' from Luiggi Sansonetti for the French translation - Merci :).

26th April 2012 - Version 2.3.5 - CONTRIB-3529 - As suggested by Leonie Vos (http://moodle.org/user/profile.php?id=1435066).
   1. Added the ability to set the colour attributes of the toggle.
   2. Added the ability to reset the layout and colour attributes back to the defaults as defined in the 'config.php' file.
   3. Thank you to 'Nadav Kavalerchik' for pointing out on MDL-23320 how this can be done by modifying the colour picker code implemented by 'Iain Checkland' in his Quick Structure block 'https://github.com/drcheckers/moodle-block_quickstructure/tree/master/blocks/quickstructure', and to 'Matthew Cannings' on MDL-23320 for the colour validation rule.
   4. Moved JavaScript code into its own folder 'js' for neatness.
   5. Renamed 'format_topcoll_layout' table to 'format_topcoll_settings' so that it is a better representation of what it stores.  Restores from previous versions should work.  Raised MDL-32650 as cannot rename the comment for the renamed table in upgrading installations.
   6. Added an American English translation (en_us) because of the incorporation of the word 'colour'.  More information on 'http://en.wikipedia.org/wiki/American_and_British_English_spelling_differences'.  I may have not got everything correct!
   7. Added an English Pirate translation (en_ar) upon discovery of the 'Pirate' treasure language pack mee hearties :).
   8. Additional language strings have been placed in the language files, where I have been unable to translate them they are in English, if you are able to translate them into your own language I would appreciate the translation, please contact me via Moodle - http://moodle.org/user/profile.php?id=442195.
   9. Minor tweaks to format.php for showing the correct wording over icons when in a 'weeks' structure.
  10. Discovered a minor issue with hovering over the light bulb when in a week based structure and using AJAX that it describes 'topics' and not 'weeks', raised a point on MDL-31052 for this.  Not sure how to fix yet as it is in the initialisation code of 'section_classes.js' and overloading does not seem to work.

1st May 2012 - Version 2.3.6 - CONTRIB-3624
  1. Implemented code to facilitate the ability to confirm with the user that it is ok to place the cookie 'mdl_cf_topcoll' on their computer.  This fucntionality can be switched on / off through the changing of '$TCCFG->defaultcookieconsent' in the format's 'config.php'.  This functionality exists because I believe that the cookie is a 'Category 3' cookie in line with the forthcoming UK EU Cookie Law - please see 'UK / EU Cookie Law' at the top of this file.
  2. Fixed - Changing the language on the 'Settings' form produces an invalid Moodle URL.
  3. Fixed - Toggles are open and sections displayed when JavaScript is turned off in the user's browser.
  4. A few fixes to changes made in version 2.2.5 where I had renamed table 'format_topcoll_layout' to 'format_topcoll_settings' in the code.
  5. Created a '$TCCFG' object in the 'config.php' file to solve the 'globals' issue in 'lib.php'.

3rd May 2012 - Version 2.3.6.1
  1. Reverted back to unsigned data types in database due to error with MSSQL database code probably in core, but not essential change at
     this point in time - see http://moodle.org/mod/forum/discuss.php?d=201460.
  2. Updated French translation thanks to Luiggi Sansonetti.

14th May 2012 - Version 2.3.6.2
  1. Fixed slight issue with version number causing 'Site Administration -> Plugins -> Plugin Overview' to fail, please see 'http://moodle.org/mod/forum/discuss.php?d=202578'.

20th May 2012 - Version 2.3.6.2.1 - CONTRIB-3655
  1. Changes in module.js for MyMobile theme.
  
31st May 2012 - Version 2.3.6.3 - CONTRIB-3682
  1. Fixed issue with students not being able to perform cookie consent because of incorrect application of requiring the capability of course update.
  2. Code change done in line with other versions but format not working with development version.

3rd June 2012 - Version 2.3.7dev - CONTRIB-3652 development - rewrite for Moodle 2.3
  1. Started rewrite of Collapsed Topics for Moodle 2.3 as course formats now use a completely new renderer system introduced in MDL-32508.
  2. This branch now in 'Alpha' for stability as existing code does not work and reapplying old code in a progressive manner.

12th June 2012 - Version 2.3.7beta - CONTRIB-3652 development - rewrite for Moodle 2.3
  1. Completed bulk of code development, now 'Beta' version for testing.

24th June 2012 - Version 2.3.7rc - CONTRIB-3652 development - rewrite for Moodle 2.3
  1. Removed cookie functionality in favour of user preferences via AJAX - see MDL-17084.
  2. Updated instructions above to reflect changes.
  3. Tidied up code and removed redundant files in this branch.

26th June 2012 - Version 2.3.7rc2 - CONTRIB-3652 development - rewrite for Moodle 2.3
  1. Removed line that was related to the cookie functionality - thanks Hartmut Scherer and Kyle Smith on http://moodle.org/mod/forum/discuss.php?d=204705.
  2. Removed cookie consent code from lib.php.
  3. To keep things clean for what will be a fresh install for all I have decided to remove the update code in update.php,
     so if you have previously installed a beta version please kindly follow step 4 of the 'Uninstallation Instructions' above
	 after updating your code but before clicking on 'Notifications' to 'upgrade'.
  4. Request from Kyle Smith to implement the functionality of being able to reset to defaults for all Collapsed Topics courses.  I have made this for admins only.
  5. Added in multi-column functionality as a layout setting.  Default in config.php.  Can have one to four columns.

27th June 2012 - Version 2.3.7rc3 - CONTRIB-3652 development - rewrite for Moodle 2.3
  1. Put layout columns into backup and restore code.
  2. Tidy up instructions in this readme.
  3. A few slight alterations for the MyMobile theme - MDL-33115.
  
28th June 2012 - Version 2.3.7rc4 - CONTRIB-3652 development - rewrite for Moodle 2.3
  1. Corrected an issue in 'renderer.php' for the overridden method 'print_multiple_section_page()' so that section 0 has a name displayed if there
     is one - see http://moodle.org/mod/forum/discuss.php?d=205724.
  2. Ensured that only one column is present when using the MyMobile theme regardless of setting.
  3. Made work to a greater extent with the MyMobile theme - not quite as the theme intends as all changes within CT.
  4. Tidied up left and right sides to be language specific when not editing for variations in the words 'Topic' and 'Week'.
  5. Optimised open and close all toggles such that persistence is now only one AJAX call to update the user preferences instead of one per section.

29th June 2012 - Version 2.3.7rc5 - CONTRIB-3652 development - rewrite for Moodle 2.3
  1. Test and tidy up code.

3rd July 2012 - Version 2.3.7 Stable - Completion of CONTRIB-3652 development - rewrite for Moodle 2.3.
  1. Test and tidy up code.
  2. Placed check and correction for columns out of range 1-4 in renderer.php.
  3. Cope with backups from Moodle 2.0, 2.1 and 2.2.
  4. Cope when sections are not shown in column calculations.
  5. Test with MyMobile to understand underlying issue.

11th July 2012 - Version 2.3.7.1
  1. Updated french lanugage file thanks to Luiggi Sansonetti.
  2. Fixed an issue with section zero summary not showing - thanks Chris Adams on http://moodle.org/mod/forum/discuss.php?d=206423
  3. Attempted automated upgrade in 'upgrade.php' to cope with issues users are experiencing.  Altered upgrade from
     Moodle 1.9, 2.0, 2.1 and 2.2 instructions to reflect this.  Version control for older versions less than Moodle 2.3
	 needs to follow a 'branching date' strategy for this to work properly (http://moodle.org/mod/forum/discuss.php?d=206647#p901061).
	 This was sparked by CONTRIB-3765.
  4. Tidied up and clarified the instructions for upgrading.

Thanks
------
I would like to thank Anthony Borrow - arborrow@jesuits.net & anthony@moodle.org - for his invaluable input.

Craig Grannell of Snub Communications who wrote the article on Collapsed Tables in .Net Magazine Issue 186 from whom
the original code is based and concept used with his permission.

For the Peristence upgrade I would like to thank all those who contributed to the developer forum -
http://moodle.org/mod/forum/discuss.php?d=124264 - Frank Ralf, Matt Gibson, Howard Miller and Tim Hunt.  And
indeed all those who have worked on the developer documentation - http://docs.moodle.org/en/Javascript_FAQ.

Michael de Raadt for CONTRIB-1945 & 1946 which sparked fixes in CONTRIB-1952 & CONTRIB-1954.

Amanda Doughty (http://moodle.org/user/profile.php?id=1062329) for her contribution in solving the AJAX move problem.

Mark Ward (http://moodle.org/user/profile.php?id=489101) for his contribution solving the IE8- display problem.

Pieter Wolters (http://moodle.org/user/profile.php?id=537037) - for the Dutch translation.

Tarcísio Nunes (http://moodle.org/user/profile.php?id=1149633) - for the Brazilian translation.

Pavel Evgenjevich Timoshenko (http://moodle.org/user/profile.php?id=1322784) - for the Russian translation.

All of the developers of the Grid Course format (https://github.com/PukunuiAustralia/moodle-courseformat_grid) for showing how the database can be used with a course format.

Carlos Sánchez Martín (http://moodle.org/user/profile.php?id=743362) for his assistance on CONTRIB-3378 and the Spanish translation.

Andrew Nicols (http://moodle.org/user/view.php?id=268794) for his assistance on CONTRIB-3378.

Hartmut Scherer (http://moodle.org/user/view.php?id=441502) for suggesting the 'Current Topic First' structure and testing the Moodle 2.2 code on discussion 'Collapsed Topics with Custom Layouts' (http://moodle.org/mod/forum/discuss.php?d=195292).

Luiggi Sansonetti (http://moodle.org/user/profile.php?id=1297063) for the French translation.

References
----------
.Net Magazine Issue 186 - Article on Collapsed Tables by Craig Grannell -
 http://www.netmag.co.uk/zine/latest-issue/issue-186

Craig Grannell - http://www.snubcommunications.com/

Accordion Format - Initiated the thought - http://moodle.org/mod/forum/discuss.php?d=44773 & 
                                           http://www.moodleman.net/archives/47

Paint.Net - http://www.getpaint.net/

JavaScript: The Definitive Guide - David Flanagan - O'Reilly - ISBN: 978-0-596-10199-2

Desired Enhancements
--------------------
1. Smoother animated toggle action.

G J Barnard MSc. BSc(Hons)(Sndw). MBCS. CEng. CITP. PGCE. - 11th July 2012.
Moodle profile: moodle.org/user/profile.php?id=442195.
Web profile   : about.me/gjbarnard