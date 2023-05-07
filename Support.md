The Collapsed Topics format story
=================================
The concept of 'Collapsed Topics' arose from a need to segment a multi-unit course I was teaching, whereby a student would only
need to look at the material for a given unit at any given time.  So instead of the administrative complications of meta
courses, and as there was no group or cohort functionality in Moodle at the time, I thought that given my experience I could
potentially write some code as a plugin that would solve the problem and that of the 'Scroll of Death' that my students were
currently experiencing.  I then read in .Net Magazine (now no longer being published) issue 186, March 2009, an article entitled
'Collapsed Tables' by Craig Grannell (of Snub Communications) and thought that was the way to address the issue, so I contacted him
and received permission to reuse the idea.  And so in Moodle 1.9 I set to work creating the first version of 'Collapsed Topics' using
'cookies' to store the state of the toggles, as I'd as yet at the time not learned how to use a server based solution.  Having
completed the first version, I backported it to Moodle 1.8.  Then the complexities of the EU Cookie Law assisted the momentum
towards a server side solution that didn't use 'Cookies', and since then with the 'GDPR' regulations the format does now implement the
'[Privacy API](https://docs.moodle.org/dev/Privacy_API)' as the toggle states pertain to an identifiable user.

There has now been a version of the format for every major version of Moodle ever since the first, gradually being improved and
enhanced over the years in addition to coping with the API changes.

If you'd like to sponsor, get support or fund improvements, then please do get in touch via:

- gjbarnard | Gmail dt com address.
- GitHub | Please outline your issue / improvement on '[GitHub](https://github.com/gjb2048/moodle-format_topcoll/issues)'.
- @gjbarnard | '[Twitter](https://twitter.com/gjbarnard)'.

Sponsors
========
Sponsorships gratefully received with thanks from:
Emerogork: Central Connecticut State University, USA

Open source software
====================
Collapsed Topics is licensed under the [GNU GPLv3](https://www.gnu.org/licenses/gpl-3.0.en.html) License it comes with NO support,
please see 'COPYING.txt'. If you would like support from me then I'm happy to provide it for a fee (please see my contact details
below).  Otherwise, the Moodle '[Courses and course formats](https://moodle.org/mod/forum/view.php?id=47)' forum is an excellent place
to ask questions.

Collapsed Topics can be obtained from:

* [Moodle.org](https://moodle.org/plugins/view.php?plugin=format_topcoll).
* [GitHub](https://github.com/gjb2048/moodle-format_topcoll/releases).

You have all the rights granted to you by the GPLv3 license.  If you are unsure about anything, then the
FAQ - [GPL FAQ](https://www.gnu.org/licenses/gpl-faq.html) - is a good place to look.

If you reuse any of the code then I kindly ask that you make reference to the format.

If you make improvements or bug fixes then I would appreciate if you would send them back to me by forking from
[GitHub](https://github.com/gjb2048/moodle-format_topcoll) and doing a 'Pull Request' so that the rest of the Moodle community
benefits.

Required version of Moodle
==========================
This version works with Moodle 4.2 version 2023042400.00 (Build: 20230424) and above within the MOODLE_402_STABLE branch until the
next release.

Please ensure that your hardware and software complies with 'Requirements' in '[Installing Moodle](https://docs.moodle.org/402/en/Installing_Moodle)'.

Reporting issues
================
Before reporting an issue, please ensure that you are running the current version for the major release of Moodle you are using.  It
is essential that you are operating the required version of Moodle as stated above, this is because the format relies on core functionality
that is out of its control.

If you think you've discovered a genuine bug with the format then please look at the Moodle Course and course formats forum first to see if it
has already been repoted.  Secondly, look at [GitHub](https://github.com/gjb2048/moodle-format_topcoll/issues), and thirdly [Moodle Tracker](https://tracker.moodle.org/issues/?jql=project+%3D+CONTRIB+AND+component+%3D+%22Course+format%3A+Topcoll%22).

I operate a policy that I will fix all genuine issues in 'my' (not other developers of the format) code, when fully described and
replicatable.

It is essential that you provide as much information as possible, the critical information being the contents of the format's
version.php file / or the top of the 'Information' settings tab.  Other version information such as specific Moodle version,
theme name and version also helps.  A screen shot can be really useful in visualising the issue along with any files you
consider to be relevant.

You can use either the '[Course and course formats forum](https://moodle.org/mod/forum/view.php?id=47)' or '[GitHub](https://github.com/gjb2048/moodle-format_topcoll/issues)'.

Developed and maintained by
===========================
G J Barnard MSc. BSc(Hons)(Sndw). MBCS. CEng. CITP. PGCE.

- Moodle profile | [Moodle.org](https://moodle.org/user/profile.php?id=442195)
- @gjbarnard     | [Twitter](https://twitter.com/gjbarnard)
- Web profile    | [About.me](https://about.me/gjbarnard)
- Website        | [Website](https://gjbarnard.co.uk)
