Introduction
------------
Topic based course format with an individual 'toggle' for each topic except 0.  This format differs from the
Accordion format in that two or more topics can be visible at the same time.

Documented on http://docs.moodle.org/en/Collapsed_Topics_course_format

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

References
----------
.Net Magazine Issue 186 - Article on Collapsed Tables by Craig Grannell -
 http://www.netmag.co.uk/zine/latest-issue/issue-186

Craig Grannell - http://www.snubcommunications.com/

Accordion Format - Initiated the thought - http://moodle.org/mod/forum/discuss.php?d=44773 & 
                                           http://www.moodleman.net/archives/47

Paint.Net - http://www.getpaint.net/

JavaScript: The Definitive Guide - David Flanagan - O'Reilly - ISBN: 978-0-596-10199-2

Moodle Tracker - http://tracker.moodle.org/

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

25th August 2009 - Version 1.3.1 - Fully comment code for future reference.
  Additionally there are now three branches, HEAD, MOODLE_19_STABLE and MOODLE_18_STABLE - this is the 1.8 branch.
  Please see the documentation on http://docs.moodle.org/en/Collapsed_Topics_course_format

26th August 2009 - Version 1.3.2 - Moodle Tracker CONTRIB-1494  
  1. Versioning now based upon changes documented in Moodle tracker - tracker.moodle.org
  2. As the toogle contains the name of the section there is no need to display it again whilst editing.
  
5th April 2010 - Version 1.3.3 - Moodle Tracker CONTRIB-1825, CONTRIB-1952 & CONTRIB-1954
  1. CONTRIB-1825 - Allowing the 'Show topic x' functionality can be confusing in this course format to users
     as it is not always apparent how to turn it off.
  2. CONTRIB-1952 - Having an apostrophy in the site shortname causes the format to fail.
  3. CONTRIB-1954 - Reloading of the toggles by using JavaScript DOM events not working for the function reload_toggles,
     but instead the function was being called at the end of the page regardless of the readiness state of the DOM.

9th April 2010 - Version 1.3.4 - Moodle Tracker CONTRIB-1973
  1. Added CSS styles in topics_collapsed.css to have changeable toggle styles along with instructions on how
     to have them on a per theme basis.
  2. Tidied up format.php and sorted an incorrect comment.
  3. Updated the above installation instructions.
  
11th September 2010 - Version 1.3.5 - Moodle Tracker CONTRIB-2355
  1. Added the ability to remove 'topic x' and the section number from being displayed.  To do this, open up
     format.php in a text editor - preferably with line numbers displayed - such as Notepad++ - and read the 
	 instructions on lines 252 and 262.    

25th October 2011 - Version 1.3.6 - Transitioned to GitHub.com (https://github.com/gjb2048/moodle-format_topcoll)
                                    for reference and legacy support.

Thanks
------
I would like to thank Anthony Borrow - arborrow@jesuits.net & anthony@moodle.org - for his invaluable input.

For the Peristence upgrade I would like to thank all those who contributed to the developer forum -
http://moodle.org/mod/forum/discuss.php?d=124264 - Frank Ralf, Matt Gibson, Howard Miller and Tim Hunt.  And
indeed all those who have worked on the developer documentation - http://docs.moodle.org/en/Javascript_FAQ.

Michael de Raadt for CONTRIB-1945 & 1946 which sparked fixes in CONTRIB-1952 & CONTRIB-1954

Desired Enhancements
--------------------
1.  Smoother animated toggle action.
2.  Peristence beyond the session.

G J Barnard - BSc(Hons)(Sndw), MBCS, CEng, CITP, PGCE - 25th October 2011.