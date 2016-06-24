Introduction
============
Topic based course format with an individual 'toggle' for each topic except 0.

If you find an issue with the format, please see the 'Reporting Issues' section below.

[![Build Status](https://travis-ci.org/gjb2048/moodle-format_topcoll.svg?branch=master)](https://travis-ci.org/gjb2048/moodle-format_topcoll)

Required version of Moodle
==========================
This version works with Moodle 3.1 version 2016052300.00 (Build: 20160523) and above within the 3.1 branch until the
next release.

Please ensure that your hardware and software complies with 'Requirements' in 'Installing Moodle' on
'docs.moodle.org/31/en/Installing_Moodle'.

Downloads and documentation
===========================
The primary source for downloading this branch of the format is https://moodle.org/plugins/view.php?plugin=format_topcoll
with 'Select Moodle version:' set at 'Moodle 3.1'.

The secondary source is a tagged version with the v3.1 prefix on https://github.com/gjb2048/moodle-format_topcoll/tags

If you download from the development area - https://github.com/gjb2048/moodle-format_topcoll/ - consider that
the code is unstable and not for use in production environments.  This is because I develop the next version in stages
and use GitHub as a means of backup.  Therefore the code is not finished, subject to alteration and requires testing.

Documented on http://docs.moodle.org/31/en/Collapsed_Topics_course_format

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

Sponsorships
============
Collapsed Topics is provided to you for free, and if you want to express your gratitude for using this format, please consider
sponsoring by:

PayPal - Please contact me via my 'Moodle profile' (above) for details as I am an individual and therefore am unable to have
'buy me now' buttons under their terms.

Flattr - https://flattr.com/profile/gjb2048

Sponsorships may allow me to provide you with more or better features in less time.

Sponsors
========
Sponsorships gratefully received with thanks from:
Emerogork: Central Connecticut State University, USA

New features for this Moodle 3.1 version
========================================
 1. Features as version 3.0.2.2 in Moodle 3.0 and section name editing as implemented in core by MDL-51802.

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

    body.jsenabled .course-content ul.ctopics li.section .content .toggle-arrow span.toggle_closed {
        background-image: url([[pix:format_topcoll|arrow_right]]);
    }

    #toggle-all .content .toggle-arrow h4 span.off {
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
4.  If you get HTTP 403 errors on the browsers console for the 'settopcollpref.php' then check that the permissions within the
    'topcoll' folder are 755 for folders and 644 for files.  Ref: https://moodle.org/mod/forum/discuss.php?d=329620.

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
See Changes.md.

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
