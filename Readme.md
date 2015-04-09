Introduction
============
Topic based course format with an individual 'toggle' for each topic except 0.

If you find an issue with the format, please see the 'Reporting Issues' section below.

Required version of Moodle
==========================
This version works with Moodle version 2014111000.00 release 2.8 (Build: 20141110) and above within the 2.8 branch until the
next release.

Please ensure that your hardware and software complies with 'Requirements' in 'Installing Moodle' on
'docs.moodle.org/28/en/Installing_Moodle'.

Downloads and documentation
===========================
The primary source for downloading this branch of the format is https://moodle.org/plugins/view.php?plugin=format_topcoll
with 'Select Moodle version:' set at 'Moodle 2.8'.

The secondary source is a tagged version with the v2.8 prefix on https://github.com/gjb2048/moodle-format_topcoll/tags

If you download from the development area - https://github.com/gjb2048/moodle-format_topcoll/tree/MOODLE_27 - consider that
the code is unstable and not for use in production environments.  This is because I develop the next version in stages
and use GitHub as a means of backup.  Therefore the code is not finished, subject to alteration and requires testing.

Documented on http://docs.moodle.org/28/en/Collapsed_Topics_course_format

Bespoke changes
===============
Would you like a bespoke Collapsed Topics? Contact me via www.gjbarnard.co.uk/contact/ for a competitive quote.

Free software
=============
The Collapsed Topics format is 'free' software under the terms of the GNU GPLv3 License, please see 'COPYING.txt'.

It can be obtained for free from the links in 'Downloads and documentation' above.

You have all the rights granted to you by the GPLv3 license.  If you are unsure about anything, then the
FAQ - http://www.gnu.org/licenses/gpl-faq.html - is a good place to look.

If you reuse any of the code then I kindly ask that you make reference to the format.

If you make improvements or bug fixes then I would appreciate if you would send them back to me by forking from
https://github.com/gjb2048/moodle-format_topcoll and doing a 'Pull Request' so that the rest of the
Moodle community benefits.

Supporting Collapsed Topics development
=======================================
If you find Collapsed Topics useful and beneficial, please consider donating by:

PayPal - Please contact me via my 'Moodle profile' (above) for details as I am an individual and therefore am unable to have 'donation' / 'buy me now' buttons under their terms.

Flattr - https://flattr.com/profile/gjb2048

I develop and maintain for free and any donations to assist me in this endeavour are appreciated.

New features for this Moodle 2.8 version
========================================
 1. Same features as version 2.7.1.5 in Moodle 2.7.

Installation
============
 1. Ensure you have the version of Moodle as stated above in 'Required version of Moodle'.  This is essential as the
    format relies on underlying core code that is out of my control.
 2. If upgrading from Moodle 1.9, 2.0 or 2.1, please see 'Upgrading from Moodle 1.9, 2.0 or 2.1' below.
 3. If upgrading from Moodle 2.2, please see 'Upgrading from Moodle 2.2' below.
 4. If upgrading from Moodle 2.3, please see 'Upgrade Instructions' below.
 5. Put Moodle in 'Maintenance Mode' (docs.moodle.org/en/admin/setting/maintenancemode) so that there are no 
    users using it bar you as the administrator - if you have not already done so.
 6. Copy 'topcoll' to '/course/format/' if you have not already done so.
 7. In 'Site Administration -> Plugins -> Course formats -> Collapsed Topics' change the values of 'defaultlayoutelement',
    'defaultlayoutstructure' and 'defaultlayoutcolumns' for setting the default layout, structure and columns respectively
    for new / updating courses as desired by following the instructions contained within.
 8. In 'Site Administration -> Plugins -> Course formats -> Collapsed Topics' change the values of 'defaulttgfgcolour',
    'defaulttgbgcolour' and 'defaulttgbghvrcolour' for setting the default toggle colours.
 9. In 'Site Administration -> Plugins -> Course formats -> Collapsed Topics' turn off toggle persistence if desired by
    changing 'defaulttogglepersistence' as indicated.
10. In 'Site Administration -> Plugins -> Course formats -> Collapsed Topics' set the default toggle alignment by changing
    'defaulttogglealignment' as indicated.
11. Login as an administrator and follow standard the 'plugin' update notification.  If needed, go to
    'Site administration' -> 'Notifications' if this does not happen.
12.  To change the arrow graphic please see 'Icon Sets' below.
13.  Put Moodle out of Maintenance Mode.

Upgrade Instructions
====================
1. Ensure you have the version of Moodle as stated above in 'Required version of Moodle'.  This is essential as the
   format relies on underlying core code that is out of my control.
2. If upgrading from Moodle 1.9, 2.0, 2.1 or 2.2 please read the appropriate sections below.
3. Put Moodle in 'Maintenance Mode' so that there are no users using it bar you as the administrator.
4. In '/course/format/' move old 'topcoll' directory to a backup folder outside of Moodle.
5. Follow installation instructions above.
6. If automatic 'Purge all caches' appears not to work by lack of display etc. then perform a manual 'Purge all caches'
   under 'Home -> Site administration -> Development -> Purge all caches'.
7. Put Moodle out of Maintenance Mode.

Upgrading from Moodle 1.9, 2.0 or 2.1
-------------------------------------
Moodle 2.4 requires that Moodle 2.2 is installed to upgrade from, so therefore Moodle 2.2 is an intermediate step.
So:
1. Put Moodle in 'Maintenance Mode' so that there are no users using it bar you as the administrator.
2. In '/course/format/' move old 'topcoll' directory to a backup folder outside of Moodle.
3. Do not copy in the new version of 'topcoll' yet!  As this will cause the upgrade to fail.
4. Upgrade to Moodle 2.2 first - http://docs.moodle.org/22/en/Upgrading_to_Moodle_2.2.
5. After you have installed Moodle 2.2, now upgrade to Moodle 2.4 with this new topcoll -
   http://docs.moodle.org/24/en/Upgrading_to_Moodle_2.4 - but before initiating the upgrade you can copy the
   new (i.e. this) 'topcoll' folder to '/course/format'.
6. Now follow 'Upgrading from Moodle 2.2' below please.
INFO: Having no 'topcoll' folder in '/course/format' is fine as the courses that use it are not accessed and
      both the old and new versions will confuse an intermediate 2.2 version and cause it's installation to fail.

Upgrading from Moodle 2.2
-------------------------
1.    Put Moodle in 'Maintenance Mode' so that there are no users using it bar you as the administrator - if you have
      not already done so.
2.    In '/course/format/' move old 'topcoll' directory to a backup folder outside of Moodle - if you have not
      already done so.
3.    Copy this new 'topcoll' folder to '/course/format/'.
4.    Upgrade to Moodle 2.4 by being logged in as 'admin' and clicking on 'Home'.  If you have previously upgraded but
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
   layout it previously had or the default in the 'config.php' file as mentioned in the 'Installation'
   instructions above depending on the situation.
3. Note: I believe that if you restore a Collapsed Topic's course on an installation that does not have the
         format then it will work and become the default course format.  However the layout data will not be
         stored if you install Collapsed Topic's at a later date.

Remembered Toggle State Information
===================================
The state of the toggles are remembered beyond the session on a per user per course basis though the employment
of a user preference.  This functionality is now built in from previous versions.  You do not need to do anything.

Icon Sets
=========
Icon sets allow you to choose what is the most appropriate set of icons to use for a given courses demographic.  They
are set on a per course basis but with all the functionality of the other settings in respect to a default and resetting
the current or all courses.

If you want to change what icon represents which state / action, then edit 'styles.css' and change the selectors with
the 'background' attribute with a 'toggle-...' type class within them.  There are selectors for both the 'toggles' and
the 'toggle all' functionality.  For example:

    body.jsenabled .course-content ul.ctopics li.section .content .toggle-arrow a.toggle_closed {
        background-image: url([[pix:format_topcoll|arrow_right]]);
    }

    #toggle-all .content .toggle-arrow h4 a.off {
        background-image: url([[pix:format_topcoll|arrow_down]]); 
    }

If you would like your own icon set, either replace the icons in the 'pix' folder, deduce how the code works or better
still create new icons yourself and ask me to add them to the release.  If you do the latter then the icons must be your
own for which you grant the same GPL licence as [Moodle](http://www.gnu.org/copyleft/gpl.html) or provide direct evidence
of the originator under the same licence.  The icons must be 24x24 pixels with a transparent background.

Known Issues
============
1.  If you get toggle text issues in languages other than English please ensure you have the latest version of Moodle installed.
    More information on http://moodle.org/mod/forum/discuss.php?d=184150.
2.  Importing a Moodle 1.9 course does not currently work, please see CONTRIB-3552 which depends on MDL-32205 - as
    a workaround, please select the 'Topics' format first in 1.9, backup and restore then select the Collapsed Topics
    course format in the course settings.  You will have to reset your decisions on structure etc.
3.  Sometimes when restoring a course, it is accessed for the first time and a toggle is clicked a 'Error updating user
    preference 'topcoll_toggle_x'' (where 'x' is the course id as shown in the URL 'id=x') can occur.  I'm not completely sure
    why this is happening as the 'user_preference_allow_ajax_update' call in 'format.php' should establish that the user
    preference can be set.  Could be a page cache thing as the 'init' code is getting the course id unlike an issue I'm
    currently experiencing with the MyMobile theme - MDL-33115.  The work around is to refresh the page.  Having altered some
    of the event handing code to operate after page load, I'm hoping that this has now been resolved, please let me know
    if you encounter it.

Reporting Issues
================
Before reporting an issue, please ensure that you are running the latest version for your release of Moodle.  The primary
release area is located on https://moodle.org/plugins/view.php?plugin=format_topcoll.  It is also essential that you are
operating the required version of Moodle as stated at the top - this is because the format relies on core functionality that
is out of its control.

All Collapsed Topics does is integrate with the course page and control it's layout, therefore what may appear to be an issue
with the format is in fact to do with a theme or core component.  Please be confident that it is an issue with Collapsed Topics
but if in doubt, ask.

I operate a policy that we will fix all genuine issues for free (this only applies to the code as supplied from the sources listed
in 'Downloads and documentation' above.  Any changes / improvements you make are not covered and invalidate this policy for all of
the code).  Improvements are at my discretion.  I am happy to make bespoke customisations / improvements for a negotiated fee.  I
will endeavour to respond to all requests for support as quickly as possible, if you require a faster service then offering payment
for the service will expedite the response.

When reporting an issue you can post in the course format's forum on Moodle.org (currently 'moodle.org/mod/forum/view.php?id=47'), 
on Moodle tracker 'tracker.moodle.org' ensuring that you chose the 'Non-core contributed modules' and 'Course Format: Topcoll'
for the component or contact me direct (details at the bottom).

It is essential that you provide as much information as possible, the critical information being the contents of the format's 
version.php file.  Other version information such as specific Moodle version, theme name and version also helps.  A screen shot
can be really useful in visualising the issue along with any files you consider to be relevant.

Version Information
===================
Version 2.8.2.2
  1.  Added print media styles.
  2.  Version information to no longer have the date as will work better.

27th January 2015 Version 2.8.2.1
  1.  Refix issue #4 - No block drag and drop icon when blockeditingmenu is false - activity editing menu no longer shows.

23rd January 2015 Version 2.8.2
  1.  New 'Do not show date' feature gratefully funded by 'GAC Corporate Academy, GAC HQ, Dubai, UAE (http://www.gacacademy.com)'.
  2.  Fix issue #14 - No block drag and drop icon when blockeditingmenu is false.

11th December 2014 Version 2.8.1.1
  1.  New 'Show section summary when collapsed' feature gratefully funded by 'Te Rito Maioha Early Childhood New Zealand - https://ecnz.ac.nz'.
  2.  Fix CONTRIB-5472.
  3.  Integrated 'Use core required_param for toggle parameters.': https://github.com/gjb2048/moodle-format_topcoll/pull/12
      "This work was made possible through funding from Te Rito Maioha Early Childhood New Zealand".
      Improves on work instigated in CONTRIB-5211 and related to MDL-46754.
  4.  Improved HTTP protocol handling in 'settopcollpref.php'.

16th November 2014 Version 2.8.1
  1.  Stable version for Moodle 2.8.

10th November 2014 Version 2.8.0.1 - Release Candidate
  1.  Release candidate for Moodle 2.8 - NOT for production servers.

20th September 2014 Version 2.7.1.5
  1.  Fixed issue where a debug message was being written to the PHP error log.
  2.  When toggle persistence is off then the state of the toggles on page load will depend on the default user preference setting.
  3.  Added the ability to set the size of the toggle icon site wide as: Small = 16px, Medium = 24px and Large = 32px.
  4.  Added the ability to set each corner of the toggle border radius site wide between 0.0 and 4.0em in increments of 0.1.

19th August 2014 Version 2.7.1.4
  1.  Fixed issue #11: Moodle notice - https://github.com/gjb2048/moodle-format_topcoll/issues/11.

18th August 2014 Version 2.7.1.3
  1.  Fixed a few typos.
  2.  Fixed CONTRIB-5211: Section 5 does not remain expanded when browsing away and back.

17th July 2014 Version 2.7.1.2
  1.  Slight tweak to css to tidy things up when editing.
  2.  Patch for IE8(!!!) kindly supplied by Mathew Gancarz - https://moodle.org/user/profile.php?id=1471695.  To fix an issue
      where the activities / resources were still being displayed even after the toggle had closed.  If you are still on IE8,
      then please see this: http://www.microsoft.com/en-gb/security/pc-security/updates.aspx?linkId=8591289.
  3.  Fixed being able to manipulate sections via left and right content areas on tablets.  Thanks to Rick Jerz for reporting this
      on https://moodle.org/mod/forum/discuss.php?d=263739.

12th June 2014 Version 2.7.1.1
  1.  Fixed toggle name word break: https://moodle.org/mod/forum/discuss.php?d=261388.
  2.  Added toggle foreground colour hover.

20th May 2014 Version 2.7.1 - Stable.
  1.  Stable release for M2.7.
  2.  Fixed CONTRIB-5073 - invisible section causes an error with "topcoll" format.

22nd April 2014 Version 2.7.0.1 - BETA
  1.  First beta version for Moodle 2.7beta.

17th April 2014 Version 2.6.1.5
  1.  Fixed CONTRIB-4099 with the arrangement of the editing icon and associated functionality such that a better solution is
      applied for the specific nature of the format.

28th March 2014 Version 2.6.1.4
  1.  Fixed slight issue with mobile / tablet display issue of toggle all and instructions.
  2.  Fixed issue with Bootstrap 3 breaking the layout of hidden sections when >= 2 columns.
  3.  Fixed hidden section when "Hidden sections are shown in collapsed form." mode breaks columns.
  4.  Fixed slight mobile / tablet display issues as shown on https://moodle.org/mod/forum/discuss.php?d=256093.
  5.  Optimised 'print_multiple_section_page' in 'renderer.php' by using a 'break' instead of a 'continue' when the number of
      sections is exceeded.

20th February 2014 Version 2.6.1.3
  1.  Refactoring for the 'Elegance' theme: https://github.com/moodleman/moodle-theme_elegance.

16th January 2014 Version 2.6.1.2
  1.  Fixed Essential theme overriding toggle text colour - see: https://moodle.org/mod/forum/discuss.php?d=251951.
  2.  Fixed bullet point styles - see: https://moodle.org/mod/forum/discuss.php?d=251944.
  3.  Refactored 'print_single_section_page()' in 'renderer.php' to call parent version of method and thus reduce
      code duplication and maintenance.
  4.  Removed duplicate section title when editing.

18th December 2013 Version 2.6.1.1
  1.  Fixed issue reported by Graham Woodsford whereby teachers could not create Collapsed Topics courses.  This is because the
      validation method 'edit_form_validation' in 'lib.php' was failing the values passed to it.  These happened to be the
      hidden label values from 'course_format_options' which were being used because the 'Course creator' role that teachers
      have before becoming an 'editingteacher' role as defined in 'db/access.php' does not allow the teacher to have the
      the 'format/topcoll:changelayout', 'format/topcoll:changecolour', 'format/topcoll:changetogglealignment' and
      'format/topcoll:changetoggleiconset' capabilities.  This also implies that the values of the other settings are wrong,
      which in fact they are, causing courses to be created (after fixing the colour settings for 'edit_form_validation') with
      odd values and not the defaults resulting in no icon set etc.  And therefore needing to go back to edit the course settings.

      Ok, this now leads on to a dilemma.  Currently the course creator role does not have the CT capabilities listed above.  If
      they were added to 'access.php' then the role would have them (existing CT admins would have to add manually).  Then the
      teacher would see all the options when first creating a course as they do whilst editing.  However, this means that if you
      wish to restrict the teacher from changing things as is the purpose of the capabilities in the first place, then you have
      to remove the capability in both the 'coursecreator' and 'editingteacher' roles.  This is because by default 'coursecreator'
      is above 'editingteacher' and once enrolled on the course after having created it, the teacher has both.  This makes things
      a bit complex and to be honest not that admin friendly.  Therefore to keep things simple in what is in reality an event
      that is rare, I have decided not to add the capabilities to the 'coursecreator' role.  This is additionally based on the
      presumed work-flow of a teacher where they create the course using the defaults, look at it and then decide what to change
      in the settings.  The fix as it stands will facilitate this.

18th November 2013 Version 2.6.1
Change by G J Barnard
  1.  Fixed slight issue with lack of prefixing '#' for colour settings in default settings.

14th November 2013 Version 2.6.0.1
  1.  Initial BETA code for Moodle 2.6.

14th November 2013 Version 2.5.3.5
  1.  Changes for 'Accessibility' based upon MDL-41252.
  2.  Fully implemented MDL-39542.
  3.  Slight tweak to colour pop up code such that default settings courses don't have a prefixing '#'.
  4.  Implemented validation on colours as an implied result of CONTRIB-4736.  Thanks to Kirill Astashov for this.
  5.  Fixed sections not being aligned at the top when more than one column and with a vertical column orientation.
  6.  Fixed updating from Moodle 2.3 for existing courses issue - CONTRIB-4743.  Thanks to Kirill Astashov for this.

24th October 2013 Version 2.5.3.4
  1.  Fixed reset toggle instructions not working when only thing reset.
  2.  Fixed reset logic as was updating course format options when should not have done even though there would have been no effect.
  3.  Slight optimisation to 'renderer.php' for getting 'format_topcoll' object when already have it.
  4.  Slight optimisation to getting the strings for the current section 'light bulb'.

2nd October 2013 Version 2.5.3.3
  1.  Added: Bulb, Cloud, Eye, LED, Radio, Smiley, Square, Sun / Moon and Switch icon sets as a result of remembering about:
      https://moodle.org/mod/forum/discuss.php?d=220142.
  2.  Added instructions on how to use the toggles from a suggestion by Guido Rößling on Learn Moodle.
  3.  Added setting to turn on (default) / off the instructions at the course and site default level with 'Reset' and 'Reset all'
      capability.
  4.  Worked out how to get the reset options on the course settings page in-line in groups.

19th August 2013 Version 2.5.3.2
  1.  Fixed issue with the 'float: left' CSS style when used to ensure that the columns were displayed correctly in the
      'vertical' column orientation.  The fix is to use 'display: inline-block' instead but this does not work in IE7, so as
      it does in IE8+ and other browsers I'm going to have to go with it.  Thanks to Ed Przyzycki for reporting this.

27th July 2013 - Version 2.5.3.1
  1.  Fixed issue with dates being shown on section zero with temporal structures.  Thanks to Michael Turico for reporting this.

9th July 2013 - Version 2.5.3
  1.  Added the ability to set the position of the toggle as either left or right on a per course basis with
      a default setting.  You need the 'changelayout' capability to be able to set this.
  2.  Gratefully crowd funded on Moodle Garage -> http://www.moodlegarage.com/projects/collapsed-topics-left-to-right/.

26th June 2013 - Version 2.5.2.2
  1.  Fixed issue with 'Notice: String offset cast occurred in togglelib.php on line 68' when running on PHP 5.4.  Thanks
      to Halldór Kristjánsson (https://moodle.org/user/profile.php?id=1611408) for reporting this.

24th June 2013 - Version 2.5.2.1
  1.  Fixed CONTRIB-4436 with a 'blocker' bug on V2.5.2 below with backups failing (do not ever install this version unless you
      want to test your disaster recovery procedures).  Thanks to Mike Turico for reporting it.  Note to self and all, using html
      tags in the 'get_section_name()' method in 'lib.php' will break backups and trash your database.

23rd June 2013 - Version 2.5.2
  1.  Fixed issue with sections not showing their contents in editing mode when open all has been used, then they are individually
      closed and reopened.  Thanks to Marc Hermon for reporting this.
  2.  Added small icon which shows up when updating.
  3.  Ensure the correct arrow is used when not using JavaScript.
  4.  Radically changed the toggle persistence storage mechanism to be based on a base 64 system using the following subset of ASCII:
      ":;<=>?@ABCDEFGHIJKLMNOPQRSTUVWXYZ[\]^_`abcdefghijklmnopqrstuvwxy".  This is more efficient than the actual Base64 system of:
      "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/" because there is no complex conversion from the character to
      to the value it represents.  I also decided not to include "01" as that makes detection of the old mechanism simple for upgrade
      purposes.
      This was done to support courses with sections greater than fifty two.  Currently there is no upper limit bar what your machine
      is capable of serving.  The length of the toggle persistence data increases and decreases automatically in responce to the number
      of sections.  There are six sections per digit.
  5.  Finally fixed having the 'toggle' word on toggles and not on the navigation menu with AJAX drag and drop working - CONTRIB-4403.
  6.  Implemented MDL-33546.
  7.  Fixed size of toggles altering when using AJAX drag and drop.
  8.  Ran the code through the infamous 'Code Checker' version 2013060600, release 2.2.7 and cleared as much as possible.
  9.  Note:  Once you upgrade to this version and beyond then going back will mean loss of the user preferences as you will need to
             remove all 'topcoll_toggle_x' rows from the 'user_preferences' table first.

6th June 2013 Version 2.5.1.1
  1.  Implemented MDL-39764 to fix maxsections < numsections issue.
  2.  Reversed the order of the history in this file for easy reading.
  3.  Cleaned up some of the CSS.
  4.  Changes to 'renderer.php' because of MDL-21097.

14th May 2013 Version 2.5.1 - Stable
  1.  First stable version for Moodle 2.5 stable.

12th May 2013 - Version 2.5.0.6 - Beta
  1.  Changes for MDL-39542.

9th May 2013 - Version 2.5.0.5 - Beta
  1.  Fixed coding fault with resetting introduced in capabilities change.
  2.  Fixed coding fault with language string in layout settings.

8th May 2013 - Version 2.5.0.4 - Beta
  1.  Fixed "When in 'Show one section per page' mode and the column orientation is set to 'Horizontal' the sections on the main
      page do not fill their correct width.  This is due to the use of the 'section_summary()' method which needs to be changed
      within the format to set the calculated width on the 'li' tag." because the core fix I submitted on MDL-39099 has now
      been integrated.  Thus requiring version 2013050200.00 2.5beta+ (Build: 20130502).
  2.  Changed the layout descriptions to be more 'positive' in nature.  Should be backwards compatible in terms of languages. From
      a suggestion by Guido Hornig.
  3.  Added automatic 'Purge all caches' when upgrading.  If this appears not to work by lack of display etc. then perform a
      manual 'Purge all caches' under 'Home -> Site administration -> Development -> Purge all caches'.

29th April 2013 - Version 2.5.0.3 - Beta
  1.  Fixed non-referenced member variable bug which showed up as 'undefined' but should have been a reference error in testing.
  2.  Please perform a 'Purge all caches' under 'Home -> Site administration -> Development -> Purge all caches' when upgrading.

27th April 2013 - Version 2.5.0.2 - Beta
  1.  Thanks to ideas from Ben Kelada and help from Andrew Nicols / Tim Hunt, I have made the event handing toggle functions more efficient.
  2.  Fixed an obscure bug with '$defaultuserpreference' in 'format.php' not being parsed to 'M.format_topcoll.init' in 'module.js'.
  3.  Removed '.jumpmenu' from styles.css because of MDL-38907.
  4.  Please perform a 'Purge all caches' under 'Home -> Site administration -> Development -> Purge all caches' when upgrading.

12th April 2013 - Version 2.5.0.1 - Beta
  1.  First 'Beta' release for Moodle 2.5 Beta.
  2.  Note: Date in version file (2013041500) is greater than actual date code released publically to facilitate updates to Moodle 2.4 version.
  3.  Please perform a 'Purge all caches' under 'Home -> Site administration -> Development -> Purge all caches' when upgrading.

11th April 2013 - Version 2.4.4
  1.  Implemented the administrator setting for the format so that the default state of the toggles can be set to
      'all closed' or 'all opened' for new users.  Thanks to Jamie Burgess (https://moodle.org/user/profile.php?id=1489185) for the idea.
  2.  Realised that Tablets have more space, so allow two columns even when two or more are set.
  3.  Please perform a 'Purge all caches' under 'Home -> Site administration -> Development -> Purge all caches' when upgrading.

16th March 2013 - Version 2.4.3.1.1
  1.  Fixed toggle peristence issue caused by code checking the code and not realising the implications of '==='.
      Thanks to Marc Hermon for reporting this.
  2.  Implemented round toggle borders to reduce the harshness and integrate with jQueryMobile themes in line with Moodle 2.3 version.
  3.  Please perform a 'Purge all caches' under 'Home -> Site administration -> Development -> Purge all caches' when upgrading.

14th March 2013 - Version 2.4.3.1
  1.  Improved mobile and tablet theme detection and support.
  2.  Added 'Downloads and documentation' to this readme to clarify the download locations.
  3.  Cleaned JavaScript through use of http://jshint.com/.
  4.  Added 'Previous versions and required version of Moodle' to this guide.
  5.  Implemented MDL-37901.
  6.  Implemented MDL-37976.
  7.  Moved 'float: left' to styles.css for Henrik Thorn - CONTRIB-4198.
  8.  Improvements for MDL-34917.
  9.  Please perform a 'Purge all caches' under 'Home -> Site administration -> Development -> Purge all caches' when upgrading.

8th February 2013 - Version 2.4.3
  1.  Taking inspiration from the IEC 5009 standard standby symbol as described on http://en.wikipedia.org/wiki/Power_symbol and a
      suggestion with the + / - symbols by Ben Kelada on CONTRIB-4098.  I have used the 'standby' concept as Moodle is on
      and waiting for input.
  2.  I created the icons in Paint.Net and are released under the same GPL licence as the rest of Collapsed Topics and indeed
      Moodle.
  3.  Implemented 'Icon sets' such that the user can choose what set of icons they wish to use without complex code changes.  I am
      hoping that this will spark more 'sets' to incorporated in the main release from users.
  4.  Added the ability to control if the toggle all icons will change when hovered over, for Rick Jerz.
  5.  Moved all 'tcconfig.php' default functionalty to 'Site Administration -> Plugins -> Course formats -> Collapsed Topics'
      so that defaults can be changed by the administrator from within Moodle without resorting to code changes.
  6.  Added capabilities 'format/topcoll:changelayout', 'format/topcoll:changecolour', 'format/topcoll:changetogglealignment'
      and 'format/topcoll:changetoggleiconset' to editing teachers and managers such that site administrators can choose to
      disable functionality through roles if they wish.  In order for this to work the version number must be updated.
  7.  Code cleaned with ['code-checker'](https://moodle.org/plugins/view.php?plugin=local_codechecker) - not finished yet
      - no functional changes.
  8.  Added toggle icons to the selection boxes of the edit settings and plugin settings.  Does not work with Chrome - known
      browser issue.
  9.  Changed this readme to ['Markdown' format](http://en.wikipedia.org/wiki/Markdown).
 10.  Please perform a 'Purge all caches' under 'Home -> Site administration -> Development -> Purge all caches'.

23rd January 2013 - Version 2.4.2.1
  1.  Further tweaks for toggle line height and to make work in IE9 with and without IE7 mode.
  2.  Please perform a 'Purge all caches' under 'Home -> Site administration -> Development -> Purge all caches' when upgrading.
  
22nd January 2013 - Version 2.4.2
  1.  Added ability to control the orientation of columns when more than one is used.  You can now choose between
      having the sections number down the page (vertical) or left to right (horizontal).  Default is horizontal.  This
      is from a suggestion on CONTRIB-4098 by Michele Turre.  The MyMobile theme only uses one column regardless of the number
      of columns setting.
  2.  Fixed section number not updating in the left part of a section when using AJAX drag and drop moving.
  3.  Implemented and adjusted CSS for CONTRIB-4106 to have consistent section name styles.
  4.  Tweaked no JavaScript operation such that the 'Toggle all' functionality is hidden.
  5.  Implemented removal of css float for MyMobile theme for CONTRIB-4108.
  6.  Fixed issue with JavaScript in 'module.js' breaking with 0 or 1 sections causing the 'Add an activity or resource' to fail.
  7.  Changes to 'renderer.php' because of MDL-36095 hence requiring Moodle version 2012120301.02 release 2.4.1+ (Build: 20130118)
      and above.
  8.  Tweaked for the MyMobile theme but point '2' on 'Known issues' still occurring - any help appreciated. 
  9.  Please perform a 'Purge all caches' under 'Home -> Site administration -> Development -> Purge all caches' when upgrading.

11th January 2013 - Version 2.4.1.7 - Further improvements inspired by CONTRIB-4098.
  1.  Changed 'Latest Week' to 'Current Week' to be less confusing.
  2.  Added 'Reporting Issues' to this file.
  3.  Please perform a 'Purge all caches' under 'Home -> Site administration -> Development -> Purge all caches' when upgrading.

10th January 2013 - Version 2.4.1.6 - Improvements inspired by CONTRIB-4098 - Thanks to Michele Turre and Rick Jerz.
  1.  Changed the direction of the up arrow in line with the navigation block.
  2.  Refactored the global constant structure in tcconfig.php to be a class with constants, thus removing the 'globalness'.
  3.  Added ability to determine the alignment of the toggle text, left, centre or right.
  4.  Fixed version year which was still stuck at 2012 - please ensure you use this version when upgrading rather than
      a previous 2013 release.
  5.  If upgrading, please perform a 'Purge all caches' under 'Home -> Site administration -> Development -> Purge all caches'.

5th January 2013 - Version 2.4.1.5
  1.  In applying versions 2.4.1.2 and 2.4.1.4 to the Moodle 2.3 version I considered that when a section had
      a name that the date should be after and not before.  Thereby being more aesthetically pleasing.

3rd January 2013 - Version 2.4.1.4
  1.  Fixed unexpected issue when the number of sections is '0'.  Thanks to 'Aylwin Cal' for reporting this.

2nd January 2013 - Version 2.4.1.3
  1.  Fixed unexpected issue with changes made to 'get_section_name()' in lib.php in version 2.4.1.2 caused
      course backup to fail.  This was due to the inclusion of a 'br' tag to make the section name and date
      look effective.  I have refactored to apply the 'br' tag formatting in renderer.php instead.  The
      down side of this being the navigation window does not contain the date when a section name is set.
      Apart from doing a lot of string splitting in renderer.php to insert the 'br' tag in the right place,
      there is no other way of solving this - and I consider for efficiency and clutter that the date should
      be omitted in this circumstance.

31st December 2012 - Version 2.4.1.2
  1.  Fixed missing date text in week / day based structures that were in 2.2 versions and below.  Thanks
      to Michael Turico for informing me of this.
  2.  Moved edit section icon to the right of the toggle as it was not click-able on the toggle itself.
  3.  Changed format.js to have better results when moving sections - I hope.

19th December 2012 - Version 2.4.1.1
  1.  Minor refactor to remove redundant parameter on 'section_nav_selection()' in 'renderer.php'.

17th December 2012 - Version 2.4.1 - Stable
  1.  Tested completely fix for CONTRIB-4065.
  2.  Re-factored to remove global '$tcsettings' and place in 'lib.php' so code is more OO.
  3.  Code now considered stable.

12th December 2012 - Version 2.4.0.6 - Beta - Do not install on production sites.
  1.  Fix for CONTRIB-4065.

8th December 2012 - Version 2.4.0.5 - Beta - Do not install on production sites.
  1.  Changes for CONTRIB-4018 so that the toggles are not click-able until after the page has loaded, thus
      preventing JavaScript errors during page load.
  2.  If upgrading, please perform a 'Purge all caches' under 'Home -> Site administration -> Development -> Purge all caches'.
  3.  Ensure the toggle word is not appended to section zero.

4th December 2012 - Version 2.4.0.4 - Beta - Do not install on production sites.
  1.  Adjusted logic of optional postfixed 'Toggle' word because of 'drag and drop section name' issue.
  2.  Moved reset code to custom group box on course editing form thanks to Marina Glancy on MDL-35218.
  3.  Added the course display option as a default in 'tcconfig.php' so that all new CT courses are set to this value.
  4.  Reset now incorporates the course display option to put it back to the default.
  5.  Removed old reset form, icon and CSS.

3rd December 2012 - Version 2.4.0.3 - Beta - Do not install on production sites.
  1.  Fixed drag and drop section name issue.
  2.  Implemented a few suggestions by Marina Glancy on MDL-35218.
  3.  Updated required version to first stable release.

1st December 2012 - Version 2.4.0.2 - Beta - Do not install on production sites.
  1.  Beta version for Moodle 2.4 with one new known issue 'When moving sections around in editing mode the toggle name
      and section summary do not update until page refresh.'
  2.  Do not install on a production site.
  3.  Feedback appreciated though the course format forum (https://moodle.org/mod/forum/view.php?id=47) or
      Moodle messaging (moodle.org/user/profile.php?id=442195).
  4.  Using the colour picker for the toggle colours no longer requires a prefixing hash for the hexadecimal number.
  5.  Three new language strings added: 'numbersections', 'ctreset' and 'ctreset_help' to the English file, others to follow.
  6.  I have tested using Zend Server 5.6.0 Community Edition (MySQL DB) on Windows 7 with Chrome 23.0.1271.95:
      - Upgrading from Moodle 2.2 and 2.3.
      - Restoring 2.2 and 2.3 courses.
      - Resetting to defaults for the 'current course' and 'all courses' as an administrator.
      - Course backup and restore.
      - Toggle persistence on and off.
      - AJAX section move (with section name & summary caveat).
      - Course layout and colour settings.
      - Deleting the course.
      - Moving from the 'topics' format.
      - One section per page setting.
      - Invalid number of columns in the database, both low and high.
      However, this is not exhaustive, therefore if you are able to test on different environments and upgrades
      from older versions of Moodle, then that would be helpful.
  7.  I am currently deliberating on the issues raised on [CONTRIB-4018](http://tracker.moodle.org/browse/CONTRIB-4018) which
      apply to this version too.  If you have any thoughts / solutions, please comment on the tracker, thank you.

2nd August 2012+ - Version 2.4.0.1 - Do not install on production sites.
  1.  Development for Moodle 2.4.
  2.  Major changes for course formats refactoring - MDL-35218 - mainly to do with moving the settings into the course
      settings.

9th November 2012 - Version 2.3.9.3
  1.  Fixed issue with wrong text colour being used for the current right section text.  Had to use 'left' side selector
      for getting the correct text colour on the right for the current section.  This is because the selector
      '.course-content .current .left' defines the colour in the theme and therefore any CT specific 'right' implementation
      would not work for all themes.
  2.  Tweaked CSS for 'Anomaly', 'Afterburner', 'MyMobile' and 'Rocket' themes.

23rd October 2012 - Version 2.3.9.2
  1.  Fixed issue with wrong colour being used for current section background.
      Thanks to [Rick Jerz](https://moodle.org/user/profile.php?id=520965) for reporting this.

18th October 2012 - Version 2.3.9.1
  1. Fixed potential issue when the course is first accessed and there is no user preference.
  2. Identified that sometimes when restoring a course, it is accessed for the first time and a toggle is clicked a 'Error
     updating user preference 'topcoll_toggle_x'' (where 'x' is the course id as shown in the URL 'id=x') can occur.  I'm not
     completely sure why this is happening as the 'user_preference_allow_ajax_update' call in 'format.php' should establish that
     the user preference can be set.  Could be a page cache thing as the 'init' code is getting the course id unlike an issue
     I'm currently experiencing with the MyMobile theme - MDL-33115.  The work around is to refresh the page.

17th October 2012 - Version 2.3.9
  1. Idea posed on https://moodle.org/mod/forum/discuss.php?d=213138 (implemented in 2.3.2 first as it is currently the main
     development branch), led to the thought that the code could now be optimised to set the toggle state at the server end as
     that is where the persistence is now stored.  So to speed things up this version should reduce page load times by about 0.4
     of a second.  This has been achieved by setting the state of the toggle when writing out the HTML at the server end instead
     of making all toggles initially closed and then getting the client side JavaScript to open them as required.  Until the
     move to server side persistence this would not have been possible.

7th  October 2012 - Version 2.3.8.2  1. Changes to 'renderer.php' because of MDL-31976 and MDL-35276 - thus requiring
     Moodle 2.3.2+, version 2012062502.05 (Build: 20121005).

10th September 2012 - Version 2.3.8.1
  1. Fixed 'Warning: Illegal string offset 'defaultblocks' in ...\topcoll\config.php on line 39' issue when
     operating with developer level debugging messages under PHP 5.4.3.  This was due to 'config.php's inclusion in 'lib.php'
     with a 'require_once' function call.  Somehow Moodle core must include this file in another way.  Therefore collapsed
     topics specific settings have been placed in a new file 'tcconfig.php' and all files changed to reflect this.
     Thanks to [Paul Nijbakker](http://moodle.org/user/profile.php?id=10036) for spotting this issue.

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
 10. Cherry picked Luiggi's change
     https://github.com/luiggisanso/moodle-format_topcoll/commit/9bd818f5a4efb347aef4f5154ea2930526552bfc
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
 12. Checked operation in 'MyMobile' theme, all seems good except bottom left and right navigation links in 'One section per
     page' mode.  HTML is identical to that of 'Topics' format bar difference classes higher up the document object model to
     distinguish 'Collapsed Topics' from 'Topics'.  Hopefully will be resolved when MDL-33115 implemented.

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

11th July 2012 - Version 2.3.7.1
  1. Updated french lanugage file thanks to Luiggi Sansonetti.
  2. Fixed an issue with section zero summary not showing - thanks [Chris Adams](http://moodle.org/mod/forum/discuss.php?d=206423)
  3. Attempted automated upgrade in 'upgrade.php' to cope with issues users are experiencing.  Altered upgrade from
     Moodle 1.9, 2.0, 2.1 and 2.2 instructions to reflect this.  Version control for older versions less than Moodle 2.3
     needs to follow a 'branching date' strategy for this to work properly -
     http://moodle.org/mod/forum/discuss.php?d=206647#p901061.  This was sparked by CONTRIB-3765.
  4. Tidied up and clarified the instructions for upgrading.

3rd July 2012 - Version 2.3.7 Stable - Completion of CONTRIB-3652 development - rewrite for Moodle 2.3.
  1. Test and tidy up code.
  2. Placed check and correction for columns out of range 1-4 in renderer.php.
  3. Cope with backups from Moodle 2.0, 2.1 and 2.2.
  4. Cope when sections are not shown in column calculations.
  5. Test with MyMobile to understand underlying issue.

29th June 2012 - Version 2.3.7rc5 - CONTRIB-3652 development - rewrite for Moodle 2.3
  1. Test and tidy up code.

28th June 2012 - Version 2.3.7rc4 - CONTRIB-3652 development - rewrite for Moodle 2.3
  1. Corrected an issue in 'renderer.php' for the overridden method 'print_multiple_section_page()' so that section 0 has a name
     displayed if there is one - see http://moodle.org/mod/forum/discuss.php?d=205724.
  2. Ensured that only one column is present when using the MyMobile theme regardless of setting.
  3. Made work to a greater extent with the MyMobile theme - not quite as the theme intends as all changes within CT.
  4. Tidied up left and right sides to be language specific when not editing for variations in the words 'Topic' and 'Week'.
  5. Optimised open and close all toggles such that persistence is now only one AJAX call to update the user preferences instead
     of one per section.

27th June 2012 - Version 2.3.7rc3 - CONTRIB-3652 development - rewrite for Moodle 2.3
  1. Put layout columns into backup and restore code.
  2. Tidy up instructions in this readme.
  3. A few slight alterations for the MyMobile theme - MDL-33115.
  
26th June 2012 - Version 2.3.7rc2 - CONTRIB-3652 development - rewrite for Moodle 2.3
  1. Removed line that was related to the cookie functionality - thanks Hartmut Scherer and Kyle Smith on 
     http://moodle.org/mod/forum/discuss.php?d=204705.
  2. Removed cookie consent code from lib.php.
  3. To keep things clean for what will be a fresh install for all I have decided to remove the update code in update.php,
     so if you have previously installed a beta version please kindly follow step 4 of the 'Uninstallation Instructions' above
     after updating your code but before clicking on 'Notifications' to 'upgrade'.
  4. Request from Kyle Smith to implement the functionality of being able to reset to defaults for all Collapsed Topics courses.
     I have made this for 'admins' only.
  5. Added in multi-column functionality as a layout setting.  Default in config.php.  Can have one to four columns.

24th June 2012 - Version 2.3.7rc - CONTRIB-3652 development - rewrite for Moodle 2.3
  1. Removed cookie functionality in favour of user preferences via AJAX - see MDL-17084.
  2. Updated instructions above to reflect changes.
  3. Tidied up code and removed redundant files in this branch.

12th June 2012 - Version 2.3.7beta - CONTRIB-3652 development - rewrite for Moodle 2.3
  1. Completed bulk of code development, now 'Beta' version for testing.

3rd June 2012 - Version 2.3.7dev - CONTRIB-3652 development - rewrite for Moodle 2.3
  1. Started rewrite of Collapsed Topics for Moodle 2.3 as course formats now use a completely new renderer system introduced
     in MDL-32508.
  2. This branch now in 'Alpha' for stability as existing code does not work and reapplying old code in a progressive manner.

31st May 2012 - Version 2.3.6.3 - CONTRIB-3682
  1. Fixed issue with students not being able to perform cookie consent because of incorrect application of requiring the
     capability of course update.
  2. Code change done in line with other versions but format not working with development version.

20th May 2012 - Version 2.3.6.2.1 - CONTRIB-3655
  1. Changes in module.js for MyMobile theme.
  
14th May 2012 - Version 2.3.6.2
  1. Fixed slight issue with version number causing 'Site Administration -> Plugins -> Plugin Overview' to fail, please
     see 'http://moodle.org/mod/forum/discuss.php?d=202578'.

3rd May 2012 - Version 2.3.6.1
  1. Reverted back to unsigned data types in database due to error with MSSQL database code probably in core, but not essential
     change at this point in time - see http://moodle.org/mod/forum/discuss.php?d=201460.
  2. Updated French translation thanks to Luiggi Sansonetti.

1st May 2012 - Version 2.3.6 - CONTRIB-3624
  1. Implemented code to facilitate the ability to confirm with the user that it is ok to place the cookie 'mdl_cf_topcoll' on
     their computer.  This fucntionality can be switched on / off through the changing of `$TCCFG->defaultcookieconsent` in the
     format's 'config.php'.  This functionality exists because I believe that the cookie is a 'Category 3' cookie in line with
     the forthcoming UK EU Cookie Law - please see 'UK / EU Cookie Law' at the top of this file.
  2. Fixed - Changing the language on the 'Settings' form produces an invalid Moodle URL.
  3. Fixed - Toggles are open and sections displayed when JavaScript is turned off in the user's browser.
  4. A few fixes to changes made in version 2.2.5 where I had renamed table 'format_topcoll_layout' to 'format_topcoll_settings'
     in the code.
  5. Created a `$TCCFG` object in the 'config.php' file to solve the 'globals' issue in 'lib.php'.

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

21st March 2012 - Version 2.3.4.2
  1. Received an updated version of 'format_topcoll.php' from Luiggi Sansonetti for the French translation - Merci :).

17th March 2012 - Version 2.3.4.1
  1. Tried with restorelib.php in the root folder for importing Moodle 1.9 courses and did not work.  So for tidiness, moved the
     Moodle 1.9 backup and restore code to backup/moodle1 folder.
  2. So please note that restoring Moodle 1.9 courses in this course format will not retain the structure settings and will
     default to the values in 'config.php'.  I hope to investigate and either fix or have this fixed.
  3. Release '2012030100.02' of Moodle 2.3dev converted all tables to have signed integers in the function
     'upgrade_mysql_fix_unsigned_columns()' in '/lib/db/upgradelib.php' called from 'upgrade.php' in the same folder.  This
     included 'format_topcoll_layout' because of the code was written.  This made it very difficult for me to create an
     effective upgrade in my own 'upgrade.php' because I would be converting what had already been converted if the format was
     installed and you were updating Moodle 2.3dev but if you install for the first time, the code has been written as such to
     have signed fields.  Therefore if you have previously installed this format for Moodle 2.3, please remove the table
     'format_topcoll_layout' from your database before upgrading.  This is not quite brilliant, but I consider reasonable for
     this development version at this stage.
  4. Implemented the change in 'format.php' introduced by MDL-31255, therefore you now require Moodle 2.3 version
     '2012031500.00'.

15th March 2012 - Version 2.3.4 - CONTRIB-3520 - Stable.
  1. Completed files for 1.9 and placed in the root folder of the format in the hope that they are executed by the upgrade
     restoring code as they are in the Moodle 1.9 version of this issue.  I think it is a Moodle core coding issue that they are
     not called in Moodle 2.x+ when importing a Moodle 1.9 course backup - need to investigate.
  2. Translated the words 'Topic' and 'Week' in all language files so that the toggle bar is correct in all structures.  If you
     are a native speaker I would appreciate translation of the rest as Google Translate is not so good with long sentences.
  3. Added backup and restore instructions to this file.

14th March 2012 - Version 2.3.4 - BETA - CONTRIB-3520.
  1. Added backup and restore functionality.  If required when restoring a course 'Overwrite course configuration' needs to be
     'Yes' to set the structure and elements correctly.
  2. Added the function 'format_topcoll_delete_course' in 'lib.php' which will remove the entry in the 'format_topcoll_layout'
     table for the course when it is deleted.
  3. Added language strings to the language files that were missing previous changes.  Still in English at the moment in the
     hope a native speaker will translate them for me.  I intend to translate the basics like 'Topic' and 'Week' though before
     release in line with what was already there.

2nd March 2012 - Version 2.3.3.1
  1. Minor fix to ensure consistent use of $coursecontext and not $context.

29th February 2012 - Version 2.3.3 - Release Candidate 4
  1. Updated Spanish language files thanks to Carlos Sánchez Martín.
  2. Added setting default layout and structure to installation instructions.
  3. Decided to have '$formcourselayoutstrutures' out of config.php to prevent possible future user error.
  4. Spotted a minor issue with changing language whilst on the 'Set Layout' form.  Added to known issues as very minor and rare
     as almost certainly the user will not have changed language on this form but would have done so beforehand.
  5. Fixed duplicate entry issue in 'course_sections' table when the default structure is 'Current Topic First' and a new course
     is created.

1st March 2012 - Version 2.3.3 - Stable
  1. Integrated Git Branch CONTRIB-3378 into stable branch master.
  2. NOTE: If you have previously installed a Beta or Release Candidate please drop the table 'format_topcoll_layout' before use.
  3. Removed redundant lib.js and lib_min.js in this branch.

28th February 2012 - Version 2.3.3 - Release Candidate 3
  1. Tidied up 'module.js' to be more efficient in using the YUI instance given.
  2. Updated installation and toggle state instructions. 
  3. Added uninstall procedure in the unlikely event that you need it.

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

25th February 2012 - Version 2.3.3 - Release Candidate 1
  1. Added help information to the drop down options on the set layout form.
  2. Tidied up to be consistent and use less words where required.
  3. In format.php changed from depreciated `js_function_call()` to `js_init_call()`.
  4. If you have previously installed a beta version you will need to drop the table 'format_topcoll_layout' in the database.
  5. If you are a native speaker of a language other than English, I would be grateful of a translation of the new language
     strings in 'lang/en/format_topcoll.php' under the comment 'Layout enhancement - Moodle Tracker CONTRIB-3378'.  Please
     message me using the details in my Moodle profile 'http://moodle.org/user/profile.php?id=442195'.

18th February 2012 - Version 2.3.3 - BETA 8
  1. CONTRIB-3225 - Added screen reader capability using 'h3' tags, the same as the standard Topics format.

15th February 2012 - Version 2.3.3 - BETA 7
  1. Added strings for MDL-26105 in format_topcoll.php.
  2. Used non-depreciated 'create_table' method in 'upgrade.php'.
  3. Finally worked out how to ensure that the 'Settings Block' displays the course and not front page administration by using
     `require_login($course)`.

12th February 2012 - Version 2.3.3 - BETA 6
  1. Fixed CONTRIB-3283 in lib.js (and hence lib_min.js) for when you are in display only 'Section x' mode and the number of
     sections is reduced, you go back to the course with a section number for you in the database that no longer exists and the
     'Jump to...' drop down box does not work.  Leading to having to change the database or the value of 'ctopics' in the URL to
     that of a valid one.
  2. Added 'callback_topcoll_get_section_url' in 'lib.php' for MDL-26477.
  3. Corrected slight mistake with version number.

11th February 2012 - Version 2.3.3 - BETA 5
  1. Implemented the capability to have different 'structures' thereby encapsulating the 'Collapsed Weeks' and 'Latest First'
     formats into this one.
  2. If you have previously installed this development, you need to drop the table 'format_topcoll_layout' in your database to
     upgrade as I do not wish to have a complicated upgrade.php in the db folder at this stage whilst development continues.
  3. As a consequence of some changes, the Spanish translation now needs fixing, sorry Carlos.

8th February 2012 - Version 2.3.3 - BETA 4
  1. A big thank you to [Andrew Nicols](http://moodle.org/user/view.php?id=268794) for his contribution on the developer forum
     http://moodle.org/mod/forum/discuss.php?d=195293.
  2. Implemented the fixes and suggestions to tidy up the code as specified by Andrew above.
  3. Implemented Spanish translations thanks to [Carlos Sánchez Martín](http://moodle.org/user/profile.php?id=743362).

5th February 2012 - Version 2.3.3 - BETA 3
  1. A big thank you to [Carlos Sánchez Martín](http://moodle.org/user/profile.php?id=743362) spotting issues in set_layout.php.
  2. Fixed issues in set_layout.php.
  3. Tidied up code to remove debug statements and development code.
  4. Created icon for setting the layout instead of words.
  5. Made strings in the English language file for the layout options and 'Set layout format'.  Others to follow.
  6. Raised CONTRIB-3378 to document the development.

4th February 2012 - Version 2.3.3 - BETA 2
  1. A big thank you to [Carlos Sánchez Martín](http://moodle.org/user/profile.php?id=743362) for his help in discovering the
     install.xml bug.
  2. Fixed issue with install.xml file, gained knowledge on uninstallation for the note below:

2nd February 2012 - Version 2.3.3 - BETA
  1. Added capability for layouts with persistence in the database.

23rd January 2012 - Version 2.3.2
  1. Sorted out UTF-8 BOM issue, see MDL-31343.
  2. Added Russian translation, thanks to [Pavel Evgenjevich Timoshenko](http://moodle.org/user/profile.php?id=1322784).

9th January 2012 - Version 2.3.1.1.2
  1. Corrected licence to be correct one used by Moodle Plugins - thanks to {Tim Hunt](http://moodle.org/user/profile.php?id=93821).

3rd January 2012 - Version 2.3.1.1.1 - Moodle Tracker MDL-30632
  1. Use consistent edit section icon.

9th December 2011 - Version 2.3.1.1 - Moodle Tracker CONTRIB-3295
  1. Fixed issue of the web browser miscaluating the width of the content in 'editing' mode so that the sections
     are less than 100%.

8th December 2011 - Version 2.2.1 - Moodle Tracker CONTRIB-2497
  1. Updated Brazilian translation thanks to [Tarcísio Nunes](http://moodle.org/user/profile.php?id=1149633).
  2. Changed version to relate to Moodle version, so this is for Moodle 2.2.

11th October 2011 - Version 1.3.1 - Branched from Moodle 2.0.x version.
  1. Updated version.php to be fully populated.
  2. MDL-29188 - Formatting of section name.  Causing Moodle 2.1.x branch of Collapsed Topics.

6th October 2011 - Version 1.3 - Moodle Tracker CONTRIB-2975, CONTRIB-3189 and CONTRIB-3190.
  1. CONTRIB-2975 - AJAX support reinstated after working out a way of swapping the content as well as the toggle.  Solution
                    sparked off by [Amanda Doughty](http://tracker.moodle.org/secure/ViewProfile.jspa?name=amanda.doughty).
  2. CONTRIB-3189 - Reported by Benn Cass that text in IE8- does not hide when the toggle is closed, solution suggested
                    by [Mark Ward](http://moodle.org/user/profile.php?id=489101) - please see
                    http://moodle.org/mod/forum/discuss.php?d=183875.
  3. CONTRIB-3190 - In realising that to make CONTRIB-2975 easier to use I suggested 'Toggle all' functionality and the
                    community said it was a good idea with no negative comments, please see
                    http://moodle.org/mod/forum/discuss.php?d=176806.

9th June 2011 - Version 1.2.3 - Moodle Tracker CONTRIB-2975 - Unfinished.
  1. AJAX support temporarily withdrawn due to issue with moving sections and the toggle title not following.
     Complex to resolve.

30th May 2011 - Version 1.2.2 - Moodle Tracker CONTRIB-2963
  1. Added in copyright and contact information.
  
12th May 2011 - Version 1.2.1 - Fixed typo with this readme in expiring cookie duration example.

9th May 2011 - Version 1.2 - Moodle Tracker CONTRIB-2925
  1. Convert all language files to UTF-8 encoding.
  
12th March 2011 - Version 1.1 - Moodle Tracker CONTRIB-2747
  1. Make the toggle state last beyond the user session if desired.
  2. Changes made for MDL-25927 & MDL-23939.
  3. Because of 'displaysection' logic issue introduced with MDL-23939, I've decided to allow the showing of a single topic
     regardless of being in editing mode or not.  I think that the improved functionality of showing the topic fully when in
     'single topic' mode will be fine.

Released Moodle 2.0 version.  Treat as completed and out of development.
25th November 2010 - CONTRIB-1471 - Changes as follows:
  1. As Moodle 2.0 was released on the 24th November now using lib_min.js.
  2. Tidied up and removed any development code / styles that was not being used.
  3. Sorted out topic spacing for Internet Explorer 7 and below.  This also has the side effect bonus of not allowing
     section content to appear above the toggle when the toggle is open and closed with the mouse - reload is not affected.
     This only affects Internet Explorer 7-, other web browsers work as expected.
  4. Removed &nbsp; when no summary as putting in spacing that was pointless and made the section look odd.
  5. Removed redundant $timenow = time() line as not used.  Strangely this is in the topic format's format.php - MDL-25417 raised.

20th November 2010 - CONTRIB-1471 - Changes as follows:
  1. In format.php added completionlib.php include as a result of MDL-24698.
  2. In lib.php fixed non-functioning code added as a result of MDL-22647 which means that the navigation block will
     correctly display the right wording for the section names: 'General' for section 0, 'Topic' for other sections
     unless they have names defined by the user on the course, in which case they will be displayed.  Language
     changes of the 12th November will give translations for 'General' and 'Topic'.

12th November 2010 - CONTRIB-1471 & CONTRIB-2497 - Changes as a result of MDL-25072:
  1. Movement of ajax capable stating 'code' from ajax.php to lib.php.
  2. As a consequence, ajax.php removed.
  3. Added German, French, Spanish (Spain, Mexico and International), Italian, Polish, Portuguese (Brazil too) 
     and Welsh.  I used Google Translate! If inaccurate, please let me know!
  4. Added the string 'topcolltogglewidth' to the relevant language file and amended format.php so that
     the word 'Topic' when translated fits within the toggle.

6th November 2010 - CONTRIB-1471 - Changes as follows:
  1. ajax.php changed to add more browser support as a result of MDL-22528.
  2. format.php changed in light of MDL-24680, MDL-24895, MDL-24927.
  3. Fixed edit icon showing even when not in edit mode.  A big thank you to [Peeush Bajpai]
     (http://moodle.org/user/profile.php?id=1127356) - for spotting this and suggesting the fix.
  4. Added Dutch language.  Thanks to [Pieter Wolters](http://moodle.org/user/profile.php?id=537037) for this.
  
25th October 2010 - CONTRIB-1471 - Removal of redundant JavaScript Code.

17th October 2010 - CONTRIB-1471 - Changes as a result of MDL-14679, MDL-20366 and MDL-24316.
  1. Removed the requirement of needing js-override-topcoll.css - to make things simpler.
  2. Tidied up some of the JavaScript to be slightly more efficient.
  
24th September 2010 - CONTRIB-1471 - Changes as a result of MDL-24321 - changed object to stdClass.

12th September 2010 - Moodle Tracker CONTRIB-2355 & CONTRIB-1471
  1. CONTRIB-2355 - Added the ability to remove 'topic x' and the section number from being displayed.  To do this, open up
     format.php in a text editor - preferably with line numbers displayed - such as Notepad++ - and read the 
     instructions on lines 216 and 226.
  2. CONTRIB-1471 - Changes as a result of MDL-14679. 
  
31st July 2010 - Summary of developments towards release version as I keep pace with Moodle 2.0 changes:
  13th April 2010 - CONTRIB-1471 - Changes as a result of MDL-15252, MDL-21693 & MDL-22056.
  24th April 2010 - CONTRIB-1471 - Fixed section jump when in 'Show only topic x' mode.
  31st May   2010 - CONTRIB-1471 - thanks to Skodak in 1.120 of format.php in the topics format - 'summaryformat' attribute in
                                   section class.
  11th June  2010 - CONTRIB-1471 as a result of  MDL-22647 - Changes to Moodle 2.0 call-backs in lib.php.
  3rd  July  2010 - CONTRIB-1471 as a result of MDL-20475 & MDL-22950.
  30th July  2010 - CONTRIB-1471 as a result of MDL-20628 and CONTRIB-2111 - in essence, sections now have a name attribute, so
                                 this can be used for the topic name instead of the section summary - far better.
                   
5th April 2010 - Moodle Tracker CONTRIB-1952 & CONTRIB-1954
  1. CONTRIB-1952 - Having an apostrophy in the site shortname causes the format to fail.
  2. CONTRIB-1954 - Reloading of the toggles by using JavaScript DOM events not working for the function reload_toggles,
     but instead the function was being called at the end of the page regardless of the readiness state of the DOM.       

16th February 2010 - Moodle Tracker CONTRIB-1825
  1. Removed the capability to 'Show topic x' unless editing as confusing to users.
  2. Removed redundant 'aToggle' as existing `$course->numsections` already contained the correct figure
     and counting toggles that are displayed causes an issue when in 'Show topic x' mode as the toggle
     number does not match the display number for the specific element.
  3. Removed redundant calls to `get_context_instance(CONTEXT_COURSE, $course->id)` as result already
     stored in $context variable towards the top - so use in more places.
     
23rd January 2010 - Moodle Tracler CONTRIB-1756
  1. Put instructions in the CSS file 'topics_collapsed.css' on how you can define theme based toggle colours.
  2. Redesigned the arrow to be more 'modern'.

24th August 2009 -
  1. Removed duplication in section name.
  2. Tidied up format.php to be XHTML strict in line with http://docs.moodle.org/en/Development:JavaScript_guidelines -
     but I will need to revisit this at the end of development to tidy up any unintentional introduced issues &
     adapt to have a non-javascript functionality where all the contents of the toggles are shown and the toggles do
     not exist.
  3. Converted to using the Page Requirements Manager ($PAGE) as much as possible for JavaScript.
  
Development Notes:  
21st August 2009 -
  1. Fully comment code for future reference.
  2. Please see the documentation on http://docs.moodle.org/en/Collapsed_Topics_course_format

16th July 2009 - Moodle 2.0 Development Version
  This is now the 2.0 development version under the HEAD CVS Tag.
  
15th July 2009 - Version 1.3 - Visual tidy up and Javascript file reduction!
  1. Moved the prefix words of 'Topic x' to the right hand side of the toggle when the summary exists.
  2. Compressed the lib.js into lib_min.js for faster loading using YUICompressor - original source still available.
  3. Moved as much as possible into css so that the files can be cached by the web browser and less transmitted in
     terms of HTML.
  4. Sorted out the way the topic table is constructed in terms of column widths to be more robust on different
     web browsers.  Tested on a Vista PC with: FireFox 3.5, IE 8.0.6001.18783 in both normal and compatibility
     modes, Google Chrome 2.0.172.33, Safari 4.0 (530.17) and Opera 9.64 build 10487.

28th June 2009 - Version 1.2 - Persistence - tested on Moodle 1.9.5.
  1. Persistence is session based on a per user per course basis.
  2. Cookies must be enabled for it to work.
  3. I need to tidy up the code and remove the development comments.
  4. I would like to slightly alter the binary string to be an array.
  5. I would like to make the lib.js functions a part of a class for future proofing.
  6. Sort out page refresh event so that it works instead of saving the cookie every time a toggle is toggled.
  
3rd March 2009 - Version 1.1
  Adjusted the Topic Toggle to make the topic summary standout more.

2nd March 2009 - Version 1.0

1st March 2009 - Version 0.2

21st February 2009 - Version 0.1

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
2. Toggle saving only when the user closes the window / moves to another course.

Me
==
G J Barnard MSc. BSc(Hons)(Sndw). MBCS. CEng. CITP. PGCE.
Moodle profile: http://moodle.org/user/profile.php?id=442195.
Web profile   : http://about.me/gjbarnard
