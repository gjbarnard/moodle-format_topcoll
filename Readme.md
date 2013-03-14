Introduction
============
Topic based course format with an individual 'toggle' for each topic except 0.

If you find an issue with the format, please see the 'Reporting Issues' section below.

Required version of Moodle
==========================
This version works with Moodle version 2012062504.01 release 2.3.4+ (Build: 20130118) and above until the next release.

Download and documentation
==========================
The primary source for downloading this branch of the format is https://moodle.org/plugins/view.php?plugin=format_topcoll
with 'Select Moodle version:' set at 'Moodle 2.3'.

The secondary source is a tagged version with the v2.3 prefix on https://github.com/gjb2048/moodle-format_topcoll/tags

If you download from the development area - https://github.com/gjb2048/moodle-format_topcoll/tree/MOODLE_23 - consider that
the code is unstable and not for use in production environments.  This is because I develop the next version in stages
and use GitHub as a means of backup.  Therefore the code is not finished, subject to alteration and requires testing.

Documented on http://docs.moodle.org/23/en/Collapsed_Topics_course_format

Supporting Collapsed Topics development
=======================================
If you find Collapsed Topics useful and beneficial, please consider donating to its development through the following
PayPal link:

[PayPal donate](https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=6KEALTXATCXVE)

I develop and maintain for free and any donations to assist me in this endeavour are appreciated.

Previous versions and required version of Moodle
================================================
If this version does not work with your version of Moodle because it requires a newer version of Moodle, then
please download an older version from https://moodle.org/plugins/pluginversions.php?plugin=format_topcoll using
this table as a guide:

CT version - Moodle version
V2.3.9.8     2.3.4+, version 2012062504.01 (Build: 20130118).
V2.3.9.7     2.3.2+, version 2012062502.05 (Build: 20121005).
V2.3.8.1     2.3.1+, version 2012062501.09 (Build: 20120809).
V2.3.7       2.3+,   version 2012062500.01 (Build: 20120701).

New features for this Moodle 2.3.1 version
==========================================
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
============
 1. Ensure you have the version of Moodle as stated above in 'Required version of Moodle'.  This is essential as the
    format relies on underlying core code that is out of my control.
 2. If upgrading from a previous version of Moodle please see 'Upgrading from Moodle 1.9, 2.0, 2.1' and
    'Upgrading from Moodle 2.2.x' below.
 3. Put Moodle in 'Maintenance Mode' (docs.moodle.org/en/admin/setting/maintenancemode) so that there are no 
    users using it bar you as the administrator - if you have not already done so.
 4. Copy 'topcoll' to '/course/format/' if you have not already done so.
 5. If using a Unix based system, chmod 755 on config.php - I have not tested this but have been told that it
    needs to be done.
 6. In 'tcconfig.php' change the values of '$TCCFG->defaultlayoutelement', '$TCCFG->defaultlayoutstructure' and
    '$TCCFG->defaultlayoutcolumns' for setting the default layout, structure and columns respectively for
    new / updating courses as desired by following the instructions contained within.
 7. In 'config.php' change the values of '$TCCFG->defaulttgfgcolour', '$TCCFG->defaulttgbgcolour' and
    '$TCCFG->defaulttgbghvrcolour' for setting the default toggle colours.
 8. Login as an administrator and follow standard the 'plugin' update notification.  If needed, go to
    'Site administration' -> 'Notifications' if this does not happen.
 9. If desired, edit the colours of the 'styles.css' - which contains instructions on how to have per theme colours.
10. To change the arrow graphic you need to replace 'arrow_up.png' and 'arrow_down.png' in the 'pix' folder.  Or override the
    css by using the selectors for the various images, override the 'background' attribute:

     body.jsenabled .course-content ul.ctopics li.section .content .toggle a.toggle_open - For the 'up' arrow in the toggle - original is 24px.
     body.jsenabled .course-content ul.ctopics li.section .content .toggle a.toggle_closed - For the 'down' arrow in the toggle - original is 24px.
     .course-content ul.ctopics li.section .content .toggle a.toggle_closed - For the 'up' arrow in the toggle when JavaScript is disabled and the toggles default to open.
     #toggle-all .content .sectionbody h4 a.on - For the 'open all sections' image - original is 24px.
     #toggle-all .content .sectionbody h4 a.off - For the 'closed all sections' image - original is 24px.
     #tc-set-settings - For the 'settings' image.

     If in doubt, please consult 'styles.css' in the format.
11.  Put Moodle out of Maintenance Mode.

Upgrade Instructions
====================
1. Ensure you have the version of Moodle as stated above in 'Required version of Moodle'.  This is essential as the
   format relies on underlying core code that is out of my control.
2. If upgrading from a previous version of Moodle please see 'Upgrading from Moodle 1.9, 2.0 or 2.1' and
   'Upgrading from Moodle 2.2' below.
3. Put Moodle in 'Maintenance Mode' so that there are no users using it bar you as the administrator.
4. In '/course/format/' move old 'topcoll' directory to a backup folder outside of Moodle.
5. If you have previously installed a development, beta or release candidate of version 2.3.7 you need to
   perform step 4 in 'Uninstallation' below.
6. Follow installation instructions above.
7. Perform a 'Purge all caches' under 'Home -> Site administration -> Development -> Purge all caches'.
8. Put Moodle out of Maintenance Mode.

Upgrading from Moodle 1.9, 2.0 or 2.1
=====================================
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
===========================
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
=====================================================================================
1. Please perform step 4 of uninstallation instructions below.
2. Drop the table 'format_topcoll_cookie_cnsnt' if it exists.
3. Follow installation instructions above.

Uninstallation
==============
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
======================================
1. Backup as you would any other course.  The layout configuration will be stored with the course settings.
2. Restore as you would any other course.  If you are offered the option of 'Overwrite Course Configuration'
   you must say 'Yes' to have the layout configuration restored otherwise the restored course will retain the
   layout it previously had or the default in the 'tcconfig.php' file as mentioned in the 'Installation'
   instructions above depending on the situation.
3. Note: I believe that if you restore a Collapsed Topic's course on an installation that does not have the
         format then it will work and become the default course format.  However the layout data will not be
         stored if you install Collapsed Topic's at a later date.


Remembered Toggle State Information
===================================
The state of the toggles are remembered beyond the session on a per user per course basis though the employment of a user preference.  This functionality is now built in from previous versions.  You do not need to do anything.

Known Issues
============
1.  If you get toggle text issues in languages other than English please ensure you have the latest version of Moodle installed.  More
    information on http://moodle.org/mod/forum/discuss.php?d=184150.
2.  The MyMobile theme is not quite as implemented as the previous versions but does work, please see http://tracker.moodle.org/browse/MDL-33115.
    If your version does not have MDL-38422 implemented, then please follow these instructions:

At the bottom of 'general.php':

            </div>
        </div><!-- ends page -->

        <!-- empty divs with info for the JS to use -->
        <div id="<?php echo sesskey(); ?>" class="mobilesession"></div>
        <div id="<?php p($CFG->wwwroot); ?>" class="mobilesiteurl"></div>
        <div id="<?php echo $dtheme;?>" class="datatheme"></div>
        <div id="<?php echo $dthemeb;?>" class="datathemeb"></div>
        <div id="page-footer"><!-- empty page footer needed by moodle yui for embeds --></div>
        <!-- end js divs -->

        <?php echo $OUTPUT->standard_end_of_body_html() ?>
    </body>

to:

            </div>

            <!-- empty divs with info for the JS to use -->
            <div id="<?php echo sesskey(); ?>" class="mobilesession"></div>
            <div id="<?php p($CFG->wwwroot); ?>" class="mobilesiteurl"></div>
            <div id="<?php echo $dtheme;?>" class="datatheme"></div>
            <div id="<?php echo $dthemeb;?>" class="datathemeb"></div>
            <div id="page-footer"><!-- empty page footer needed by moodle yui for embeds --></div>
            <!-- end js divs -->

        <?php echo $OUTPUT->standard_end_of_body_html() ?>
        </div><!-- ends page -->
    </body>

In 'embedded.php':

        <?php if ($mypagetype == 'mod-chat-gui_ajax-index') { ?>
        <div data-role="page" id="chatpage" data-fullscreen="true" data-title="<?php p($SITE->shortname) ?>">
            <?php echo $OUTPUT->main_content(); ?>
            <input type="button" value="back" data-role="none" id="chatback" onClick="history.back()">
        </div>
        <?php } else { ?>
        <div id="content2" data-role="page" data-title="<?php p($SITE->shortname) ?>" data-theme="<?php echo $datatheme;?>">
            <div data-role="header" data-theme="<?php echo $datatheme;?>">
                <h1><?php echo $PAGE->heading ?>&nbsp;</h1>
                <?php if ($mypagetype != "help") { ?>
                    <a class="ui-btn-right" data-ajax="false" data-icon="home" href="<?php p($CFG->wwwroot) ?>" data-iconpos="notext"><?php p(get_string('home')); ?></a>
                <?php } ?>
            </div>
            <div data-role="content" class="mymobilecontent" data-theme="<?php echo $databodytheme;?>">
                <?php echo $OUTPUT->main_content(); ?>
            </div>
        </div>
        <?php } ?>
        <!-- START OF FOOTER -->
        <?php echo $OUTPUT->standard_end_of_body_html() ?>
    </body>

to:

        <?php if ($mypagetype == 'mod-chat-gui_ajax-index') { ?>
        <div data-role="page" id="chatpage" data-fullscreen="true" data-title="<?php p($SITE->shortname) ?>">
            <?php echo $OUTPUT->main_content(); ?>
            <input type="button" value="back" data-role="none" id="chatback" onClick="history.back()">
        <?php } else { ?>
        <div id="content2" data-role="page" data-title="<?php p($SITE->shortname) ?>" data-theme="<?php echo $datatheme;?>">
            <div data-role="header" data-theme="<?php echo $datatheme;?>">
                <h1><?php echo $PAGE->heading ?>&nbsp;</h1>
                <?php if ($mypagetype != "help") { ?>
                    <a class="ui-btn-right" data-ajax="false" data-icon="home" href="<?php p($CFG->wwwroot) ?>" data-iconpos="notext"><?php p(get_string('home')); ?></a>
                <?php } ?>
            </div>
            <div data-role="content" class="mymobilecontent" data-theme="<?php echo $databodytheme;?>">
                <?php echo $OUTPUT->main_content(); ?>
            </div>
            <?php } ?>
            <!-- START OF FOOTER -->
            <?php echo $OUTPUT->standard_end_of_body_html() ?>
        </div>
    </body>

3.  Importing a Moodle 1.9 course does not currently work, please see CONTRIB-3552 which depends on MDL-32205 - as
    a workaround, please select the 'Topics' format first in 1.9, backup and restore then select the Collapsed Topics
    course format in the course settings.  You will have to reset your decisions on structure etc.
4.  Sometimes when restoring a course, it is accessed for the first time and a toggle is clicked a 'Error updating user preference
    'topcoll_toggle_x'' (where 'x' is the course id as shown in the URL 'id=x') can occur.  I'm not completely sure why this is happening
    as the 'user_preference_allow_ajax_update' call in 'format.php' should establish that the user preference can be set.  Could be a page cache
    thing as the 'init' code is getting the course id unlike an issue I'm currently experiencing with the MyMobile theme - MDL-33115.  The
    work around is to refresh the page.

Reporting Issues
================
Before reporting an issue, please ensure that you are running the latest version for your release of Moodle.  The primary
release area is located on https://moodle.org/plugins/view.php?plugin=format_topcoll.  It is also essential that you are
operating the required version of Moodle as stated at the top - this is because the format relies on core functionality that
is out of its control.

All Collapsed Topics does is integrate with the course page and control it's layout, therefore what may appear to be an issue
with the format is in fact to do with a theme or core component.  Please be confident that it is an issue with Collapsed Topics
but if in doubt, ask.

I operate a policy that I will fix all genuine issues for free.  Improvements are at my discretion.  I am happy to make bespoke
customisations / improvements for a negotiated fee. 

When reporting an issue you can post in the course format's forum on Moodle.org (currently 'moodle.org/mod/forum/view.php?id=47'), 
on Moodle tracker 'tracker.moodle.org' ensuring that you chose the 'Non-core contributed modules' and 'Course Format: Topcoll'
for the component or contact me direct (details at the bottom).

It is essential that you provide as much information as possible, the critical information being the contents of the format's 
version.php file.  Other version information such as specific Moodle version, theme name and version also helps.  A screen shot
can be really useful in visualising the issue along with any files you consider to be relevant.

Version Information
===================
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
  3. Fixed edit icon showing even when not in edit mode.  A big thank you to [Peeush Bajpai]
     (http://moodle.org/user/profile.php?id=1127356) - for spotting this and suggesting the fix.
  4. Added Dutch language.  Thanks to [Pieter Wolters](http://moodle.org/user/profile.php?id=537037) for this.
  
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
  1. CONTRIB-2975 - AJAX support reinstated after working out a way of swapping the content as well as the toggle.  Solution
                    sparked off by [Amanda Doughty](http://tracker.moodle.org/secure/ViewProfile.jspa?name=amanda.doughty).
  2. CONTRIB-3189 - Reported by Benn Cass that text in IE8- does not hide when the toggle is closed, solution suggested
                    by [Mark Ward](http://moodle.org/user/profile.php?id=489101) - please see
                    http://moodle.org/mod/forum/discuss.php?d=183875.
  3. CONTRIB-3190 - In realising that to make CONTRIB-2975 easier to use I suggested 'Toggle all' functionality and the
                    community said it was a good idea with no negative comments, please see (http://moodle.org/mod/forum/discuss.php?d=176806).

11th October 2011 - Version 1.3.1 - Branched from Moodle 2.0.x version.
  1. Updated version.php to be fully populated.
  2. MDL-29188 - Formatting of section name.  Causing Moodle 2.1.x branch of Collapsed Topics.

8th December 2011 - Version 2.2.1 - Moodle Tracker CONTRIB-2497
  1. Updated Brazilian translation thanks to [Tarcísio Nunes](http://moodle.org/user/profile.php?id=1149633).
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
  2. Added Russian translation, thanks to [Pavel Evgenjevich Timoshenko](http://moodle.org/user/profile.php?id=1322784).

2nd February 2012 - Version 2.3.3 - BETA
  1. Added capability for layouts with persistence in the database.

4th February 2012 - Version 2.3.3 - BETA 2
  1. A big thank you to [Carlos Sánchez Martín](http://moodle.org/user/profile.php?id=743362) for his help in discovering the
     install.xml bug.
  2. Fixed issue with install.xml file, gained knowledge on uninstallation for the note below:

5th February 2012 - Version 2.3.3 - BETA 3
  1. A big thank you to [Carlos Sánchez Martín](http://moodle.org/user/profile.php?id=743362) spotting issues in set_layout.php.
  2. Fixed issues in set_layout.php.
  3. Tidied up code to remove debug statements and development code.
  4. Created icon for setting the layout instead of words.
  5. Made strings in the English language file for the layout options and 'Set layout format'.  Others to follow.
  6. Raised CONTRIB-3378 to document the development.

8th February 2012 - Version 2.3.3 - BETA 4
  1. A big thank you to [Andrew Nicols](http://moodle.org/user/view.php?id=268794) for his contribution on the developer forum
     http://moodle.org/mod/forum/discuss.php?d=195293.
  2. Implemented the fixes and suggestions to tidy up the code as specified by Andrew above.
  3. Implemented Spanish translations thanks to [Carlos Sánchez Martín](http://moodle.org/user/profile.php?id=743362).

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
  1. Added 'Current Topic First' as a new structure as suggested by ['Hartmut Scherer']
     (http://moodle.org/user/view.php?id=441502) on discussion 'Collapsed Topics with Custom Layouts'
     (http://moodle.org/mod/forum/discuss.php?d=195292).
  2. Fixed an issue in moving to js_init_call() in RC 1 and then followed the
     ['JavaScript guidelines'](http://docs.moodle.org/dev/JavaScript_guidelines) and
     ['How to include javascript file in a new course format?'](http://moodle.org/mod/forum/discuss.php?d=169124)
     to understand how to transition to using 'module.js' correctly.  Still going to include 'tc_section_classes_min.js'
     using the old way until I can figure out how to do this the new way.
  3. 'lib.js' and 'lib_min.js' will remain for reference until I backport the code to the Moodle 1.9 version which does not
     follow the changes in '2' and work out how to merge in Git and not have those files removed in that branch.
  4. In 'Show only section x' mode the 'Open / Close all toggles' option is not shown as not really appropriate.
  5. Topic structure now opens current section by default in the same way as the weekly ones.
  6. Changed name of 'Latest First' to 'Latest Week First' to be clearer.

NOTE: If uninstallation fails, drop the table 'format_topcoll_layout' and the entry for 'format_topcoll' in the table
      'config_plugins' where tables are with the prefix you use, the default being 'mdl_'.  Then delete the installation folder
      and replace with the current stable version.

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

26th April 2012 - Version 2.3.5 - CONTRIB-3529 - As suggested by [Leonie Vos](http://moodle.org/user/profile.php?id=1435066).
   1. Added the ability to set the colour attributes of the toggle.
   2. Added the ability to reset the layout and colour attributes back to the defaults as defined in the 'config.php' file.
   3. Thank you to 'Nadav Kavalerchik' for pointing out on MDL-23320 how this can be done by modifying the colour picker code
      implemented by 'Iain Checkland' in his Quick Structure block
      'https://github.com/drcheckers/moodle-block_quickstructure/tree/master/blocks/quickstructure', and to 'Matthew Cannings'
      on MDL-23320 for the colour validation rule.
   4. Moved JavaScript code into its own folder 'js' for neatness.
   5. Renamed 'format_topcoll_layout' table to 'format_topcoll_settings' so that it is a better representation of what it
      stores.  Restores from previous versions should work.  Raised MDL-32650 as cannot rename the comment for the renamed
      table in upgrading installations.
   6. Added an American English translation (en_us) because of the incorporation of the word 'colour'.  More information on
      'http://en.wikipedia.org/wiki/American_and_British_English_spelling_differences'.  I may have not got everything correct!
   7. Added an English Pirate translation (en_ar) upon discovery of the 'Pirate' treasure language pack mee hearties :).
   8. Additional language strings have been placed in the language files, where I have been unable to translate them they are in
      English, if you are able to translate them into your own language I would appreciate the translation, please contact me
      via Moodle - http://moodle.org/user/profile.php?id=442195.
   9. Minor tweaks to format.php for showing the correct wording over icons when in a 'weeks' structure.
  10. Discovered a minor issue with hovering over the light bulb when in a week based structure and using AJAX that it describes
      'topics' and not 'weeks', raised a point on MDL-31052 for this.  Not sure how to fix yet as it is in the initialisation
      code of 'section_classes.js' and overloading does not seem to work.

1st May 2012 - Version 2.3.6 - CONTRIB-3624
  1. Implemented code to facilitate the ability to confirm with the user that it is ok to place the cookie 'mdl_cf_topcoll' on their computer.  This functionality can be switched on / off through the changing of '$TCCFG->defaultcookieconsent' in the format's 'config.php'.  This functionality exists because I believe that the cookie is a 'Category 3' cookie in line with the forthcoming UK EU Cookie Law - please see 'UK / EU Cookie Law' at the top of this file.
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
  2. Fixed an issue with section zero summary not showing - thanks [Chris Adams](http://moodle.org/mod/forum/discuss.php?d=206423)
  3. Attempted automated upgrade in 'upgrade.php' to cope with issues users are experiencing.  Altered upgrade from
     Moodle 1.9, 2.0, 2.1 and 2.2 instructions to reflect this.  Version control for older versions less than Moodle 2.3
     needs to follow a 'branching date' strategy for this to work properly -
     http://moodle.org/mod/forum/discuss.php?d=206647#p901061.  This was sparked by CONTRIB-3765.
  4. Tidied up and clarified the instructions for upgrading.

1st August 2012 - Version 2.3.7.2
  1. Changes to 'renderer.php' because of MDL-33767.
  2. Tidied up some of the logic in 'renderer.php'.
  3. Made 'format.php' more adaptable to old style section 'x' only urls.
  4. Made inclusion of 'config.php' in 'lib.php' more precise.
  5. Removed 'callback_topcoll_get_section_url' in 'lib.php' because it is no longer required by
     'load_generic_course_sections' in '/lib/navigationlib.php'.
  6. Added 'currentsection' string to '/lang/en/format_topcoll.php' - thanks to [Carlos Kiyan Tsunami](http://moodle.org/mod/forum/discuss.php?d=208066).
  7. Shrunk the settings icon to 75% of the original size so that it is not so 'in your face' and added
     instructions on the left.  The instructions are in the 'en' langauge file as the 'formatsettingsinformation'
     string for translation.
 
3rd September 2012 - Version 2.3.8
  1. Changes to 'renderer.php' because of MDL-28207 - thus requiring Moodle 2.3.1 2012062501.09 (Build: 20120809).
  2. Implemented MDL-34798 which I reported for AJAX section moving.
  3. Integrated CONTRIB-3827 to fix proliferation of CSS styles across other course formats.
  4. Change to 'format.php' because of MDL-34829.
  5. Sorted wording of 'light bulb' when editing.
  6. Integrated CONTRIB-3825 to fix upgrade issue when converting a non-MySQL the database.
  7. Implemented MDL-34858 which I reported as a section zero default name issue.
  8. Implemented MDL-34917 which I reported as an improvement.  Code is slightly different, feedback appreciated.
  9. Make toggle titles bold and change 'all toggles' to 'all sections', from comments made on MDL-35048.
 10. Cherry picked Luiggi's change https://github.com/luiggisanso/moodle-format_topcoll/commit/9bd818f5a4efb347aef4f5154ea2930526552bfc
 11. Figured out how to use 'pix:' for URL's in css for the format, so have changed so that the images are now controlled by css classes.  This
     means that it is now possible to override them in your theme in css.  The following is the selectors for the various images, override
     the 'background' attribute:

     `body.jsenabled .course-content ul.ctopics li.section .content .toggle a.toggle_open` - For the 'up' arrow in the toggle - original is 24px.
     `body.jsenabled .course-content ul.ctopics li.section .content .toggle a.toggle_closed` - For the 'down' arrow in the toggle - original is 24px.
     `.course-content ul.ctopics li.section .content .toggle a.toggle_closed` - For the 'up' arrow in the toggle when JavaScript is disabled and the toggles default to open.
     `#toggle-all .content .sectionbody h4 a.on` - For the 'open all sections' image - original is 24px.
     `#toggle-all .content .sectionbody h4 a.off` - For the 'closed all sections' image - original is 24px.
     `#tc-set-settings` - For the 'settings' image.

     If in doubt, please consult 'styles.css' in the format.
 12. Checked operation in 'MyMobile' theme, all seems good except bottom left and right navigation links in 'One section per page' mode.  HTML is
     identical to that of 'Topics' format bar difference classes higher up the document object model to distinguish 'Collapsed Topics' from 'Topics'.
     Hopefully will be resolved when MDL-33115 implemented.

10th September 2012 - Version 2.3.8.1
  1. Fixed 'Warning: Illegal string offset 'defaultblocks' in ...\topcoll\config.php on line 39' issue when
     operating with developer level debugging messages under PHP 5.4.3.  This was due to 'config.php's inclusion in 'lib.php'
     with a 'require_once' function call.  Somehow Moodle core must include this file in another way.  Therefore collapsed topics
     specific settings have been placed in a new file 'tcconfig.php' and all files changed to reflect this.
     Thanks to [Paul Nijbakker](http://moodle.org/user/profile.php?id=10036) for spotting this issue.

7th October 2012 - Version 2.3.8.2
  1. Changes to 'renderer.php' because of MDL-31976 and MDL-35276 - thus requiring Moodle 2.3.2+, version 2012062502.05 (Build: 20121005).

17th October 2012 - Version 2.3.9
  1. Idea posed on https://moodle.org/mod/forum/discuss.php?d=213138 (implemented in 2.3.2 first as it is currently the main development branch),
     led to the thought that the code could now be optimised to set the toggle state at the server end as that is where the persistence is now
     stored.  So to speed things up this version should reduce page load times by about 0.4 of a second.  This has been achieved by setting the
     state of the toggle when writing out the HTML at the server end instead of making all toggles initially closed and then getting the client
     side JavaScript to open them as required.  Until the move to server side persistence this would not have been possible.

18th October 2012 - Version 2.3.9.1
  1. Fixed potenial issue when the course is first accessed and there is no user preference.
  2. Identified that sometimes when restoring a course, it is accessed for the first time and a toggle is clicked a 'Error updating user preference
     'topcoll_toggle_x'' (where 'x' is the course id as shown in the URL 'id=x') can occur.  I'm not completely sure why this is happening
     as the 'user_preference_allow_ajax_update' call in 'format.php' should establish that the user preference can be set.  Could be a page cache
     thing as the 'init' code is getting the course id unlike an issue I'm currently experiencing with the MyMobile theme - MDL-33115.  The
     work around is to refresh the page.

23rd October 2012 - Version 2.3.9.2
  1.  Fixed issue with wrong colour being used for current section background.
      Thanks to [Rick Jerz](https://moodle.org/user/profile.php?id=520965) for reporting this.

9th November 2012 - Version 2.3.9.3
  1.  Fixed issue with wrong text colour being used for the current right section text.  Had to use 'left' side selector for getting the correct text
      colour on the right for the current section.  This is because the selector '.course-content .current .left' defines the colour in the theme and
      therefore any CT specific 'right' implementation would not work for all themes.
  2.  Tweaked CSS for 'Anomaly', 'Afterburner', 'MyMobile' and 'Rocket' themes.

5th January 2013 - Version 2.3.9.4
  1.  Fixed missing date text in week / day based structures that were in 2.2 versions and below.  Thanks
      to Michael Turico for informing me of this.
  2.  Moved edit section icon to the right of the toggle in line with 2.4 version.
  3.  Changed format.js to have better results when moving sections - I hope.
  4.  Fixed unexpected issue when the number of sections is '0'.  Thanks to 'Aylwin Cal' for reporting this.
  5.  Decided that when a section had a name that the date should be after and not before.  Thereby being more aesthetically pleasing.
  6.  Changes for CONTRIB-4018 so that the toggles are not click-able until after the page has loaded, thus
      preventing JavaScript errors during page load.
  7.  Added ability to turn on / off toggle persistence in the tcconfig.php file.
  8.  If upgrading, please perform a 'Purge all caches' under 'Home -> Site administration -> Development -> Purge all caches'.

7th January 2013 - Version 2.3.9.5
  1.  Fixed layout issue in renderer.php when user screen reader setting is enabled.  Thanks to Garry Edmonds for
      reporting this.

9th January 2013 - Version 2.3.9.6
  1.  Fixed slight logic issue in renderer.php to have section names always show regardless of section number in both
      many sections per page and one section per page modes when using a screen reader.  Thanks to Michele Turre for
      reporting this.

11th January 2013 - Version 2.3.9.7 - CONTRIB-4098
  1.  Changed the edit settings to a simpler edit and icon line within the 'toggle all' area.
  2.  Changed 'Latest Week' to 'Current Week' to be less confusing.
  3.  Changed the direction of the up arrow to be a right arrow in line with the navigation block.
  4.  Added 'Reporting Issues' to this file.
  5.  Please perform a 'Purge all caches' under 'Home -> Site administration -> Development -> Purge all caches' when upgrading.

21st January 2013 - Version 2.3.9.8
  1.  Fixed issue with JavaScript in 'module.js' breaking with 0 or 1 sections causing the 'Add an activity or resource' to fail.
  2.  Changes to 'renderer.php' because of MDL-36095 hence requiring Moodle version 2012062504.01 release 2.3.4+ (Build: 20130118) and above.
  3.  Please perform a 'Purge all caches' under 'Home -> Site administration -> Development -> Purge all caches' when upgrading.

14th March 2013 - Version 2.3.9.9
  1.  Improved mobile and tablet theme detection and support.
  2.  Fixed 'float' issue for jQueryMobile themes as reported in CONTRIB-4108.
  3.  Implemented round toggle borders to reduce the harshness and integrate with jQueryMobile themes.
  4.  Changed this readme to ['Markdown' format](http://en.wikipedia.org/wiki/Markdown).
  5.  Added 'Download and documentation' to this readme to clarify download locations.
  6.  Cleaned JavaScript through use of http://jshint.com/.
  7.  Added 'Previous versions and required version of Moodle' to this guide.
  8.  Changed 'Open / Close all sections' to 'Open all / Close all' as per 2.4 version.
  9.  Tidied up lang files as Moodle.org now does automatic translation.
 10.  Added mobile toggle data hidden div tag for future use.
 11.  Implemented MDL-37901.
 12.  Improved MDL-34917 code.
 13.  Implemented CONTRIB-4198.
 14.  Please perform a 'Purge all caches' under 'Home -> Site administration -> Development -> Purge all caches' when upgrading.

Thanks
======
I would like to thank Anthony Borrow - arborrow@jesuits.net & anthony@moodle.org - for his invaluable input.

Craig Grannell of Snub Communications who wrote the article on Collapsed Tables in .Net Magazine Issue 186 from whom
the original code is based and concept used with his permission.

For the persistence upgrade I would like to thank all those who contributed to the
[developer forum](http://moodle.org/mod/forum/discuss.php?d=124264) - Frank Ralf, Matt Gibson, Howard Miller and Tim Hunt.  And
indeed all those who have worked on the developer documentation - http://docs.moodle.org/en/Javascript_FAQ.

Michael de Raadt for CONTRIB-1945 & 1946 which sparked fixes in CONTRIB-1952 & CONTRIB-1954.

[Amanda Doughty](http://moodle.org/user/profile.php?id=1062329) for her contribution in solving the AJAX move problem.

[Mark Ward](http://moodle.org/user/profile.php?id=489101) for his contribution solving the IE8- display problem.

[Pieter Wolters](http://moodle.org/user/profile.php?id=537037) - for the Dutch translation.

[Tarcísio Nunes](http://moodle.org/user/profile.php?id=1149633) - for the Brazilian translation.

[Pavel Evgenjevich Timoshenko](http://moodle.org/user/profile.php?id=1322784) - for the Russian translation.

All of the developers of the [Grid Course format](https://github.com/PukunuiAustralia/moodle-courseformat_grid) for showing how
the database can be used with a course format.

[Carlos Sánchez Martín](http://moodle.org/user/profile.php?id=743362) for his assistance on CONTRIB-3378 and the
Spanish translation.

[Andrew Nicols](http://moodle.org/user/view.php?id=268794) for his assistance on CONTRIB-3378.

[Hartmut Scherer](http://moodle.org/user/view.php?id=441502) for suggesting the 'Current Topic First' structure and testing the
Moodle 2.2 code on discussion [Collapsed Topics with Custom Layouts](http://moodle.org/mod/forum/discuss.php?d=195292).

[Luiggi Sansonetti](http://moodle.org/user/profile.php?id=1297063) for the French translation.

References
==========
.Net Magazine Issue 186 - Article on Collapsed Tables by Craig Grannell -
 http://www.netmag.co.uk/zine/latest-issue/issue-186

Craig Grannell - http://www.snubcommunications.com/

Accordion Format - Initiated the thought - http://moodle.org/mod/forum/discuss.php?d=44773 & 
                                           http://www.moodleman.net/archives/47

Paint.Net - http://www.getpaint.net/

JavaScript: The Definitive Guide - David Flanagan - O'Reilly - ISBN: 978-0-596-10199-2

Desired Enhancements
====================
1. Smoother animated toggle action.

G J Barnard MSc. BSc(Hons)(Sndw). MBCS. CEng. CITP. PGCE. - 14th March 2013.
Moodle profile: moodle.org/user/profile.php?id=442195.
Web profile   : about.me/gjbarnard