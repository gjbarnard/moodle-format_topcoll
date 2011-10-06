$Id: Readme.txt,v 1.25 2011/10/06 00:55:38 gb2048 Exp $

Introduction
------------
Topic based course format with an individual 'toggle' for each topic except 0.  This format differs from the
Accordion format in that two or more topics can be visible at the same time.

This version works with Moodle 2.0+.  And should be fine with 2.1 and 2.2.

Documented on http://docs.moodle.org/20/en/Collapsed_Topics_course_format

Installation
------------
1. Copy 'topcoll' to /course/formats/
2. If using a Unix based system, chmod 755 on config.php - I have not tested this but have been told that it needs to be done.
3. If desired, edit the colours of the topics_collapsed.css - which contains instructions on how to have per theme colours.
4. To change the arrow graphic you need to replace arrow_up.png and arrow_down.png.  Reuse the graphics
   if you want.  Created in Paint.Net.

Upgrade Instructions
--------------------
1. Put Moodle in Maintenance Mode so that there are no users using it bar you as the adminstrator.
2. In /course/formats/ move old 'topcoll' directory to a backup folder outside of Moodle.
3. Follow installation instructions above.
4. Put Moodle out of Maintenance Mode.

Remembered Toggle State Instructions
------------------------------------
To have the state of the toggles be remembered beyond the session for a user (stored as a cookie in the user's 
web browser local storage area), edit format.php and find the following at the towards the top...

    $PAGE->requires->js_function_call('topcoll_init',
                                      array($CFG->wwwroot,
                                            preg_replace("/[^A-Za-z0-9]/", "", $SITE->shortname),
                                            $course->id,
                                            null)); // Expiring Cookie Initialisation - replace 'null' with your chosen duration.

Millisecond values for standard durations are:
a Second = 1000
a Minute = 60000
an Hour = 3600000
a Day = 86400000
a Week = 604800000 is 7 Days.
a Month = 2419200000 is 4 Weeks.
a Year = 31536000000 is 365 Days.

The word to change is 'null' which says to create a 'session cookie' for the toggle state.  Set the time in milliseconds in the
future.  For example a remembered state of a week would be:

    $PAGE->requires->js_function_call('topcoll_init',
                                      array($CFG->wwwroot,
                                            preg_replace("/[^A-Za-z0-9]/", "", $SITE->shortname),
                                            $course->id,
                                            604800000)); // Expiring Cookie Initialisation - replace 'null' with your chosen duration.

You can combine the durations together and perform mathematical operations, for example, to have a
duration in the future of one day 38 minutes and 30 seconds you would have:

    $PAGE->requires->js_function_call('topcoll_init',
                                      array($CFG->wwwroot,
                                            preg_replace("/[^A-Za-z0-9]/", "", $SITE->shortname),
                                            $course->id,
                                            88710000)); // Expiring Cookie Initialisation - replace 'null' with your chosen duration.

Calculated by 'a Day' + ('a Minute' * 38) + ('a Second' * 30) = 86400000 + (60000 * 38) + (1000 * 30) = 88710000

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
    the up and down arrows, edit lib.php and remove "'MSIE' => 6.0," from:
    "$ajaxsupport->testedbrowsers = array('MSIE' => 6.0, 'Gecko' => 20061111, 'Opera' => 9.0, 'Safari' => 531, 'Chrome' => 6.0);"
    And if possible, please let me know, my Moodle.org profile is 'http://moodle.org/user/profile.php?id=442195'.

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
  11th June 2010 - CONTRIB-1471 as a result of  MDL-22647 - Changes to Moodle 2.0 callbacks in lib.php.
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
  
25th October 2010 - CONTRIB-1471 - Removal of redundant Javascript Code.

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
1. Use ordered lists / divs instead of tables to fall in line with current web design theory.  Older versions of
   'certain' browsers causing issues in making this happen.
2. Smoother animated toggle action.

G J Barnard - BSc(Hons)(Sndw), MBCS, CEng, CITP, PGCE - 6th October 2011.