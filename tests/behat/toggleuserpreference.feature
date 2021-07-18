@format @format_topcoll @javascript
Feature: Toggle user preference
  To give educators the option
  As an Administrator
  I can set the toggles to be closed or open on first access.

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email              |
      | dennis   | Dennis    | Topcoll  | dennis@topcoll.com |
    And the following "courses" exist:
      | fullname | shortname | format  | numsections |
      | CollTop  | CT        | topcoll | 2           |
    And the following "course enrolments" exist:
      | user     | course | role    |
      | dennis   | CT     | student |

  Scenario: Toggle closed on loading the page
    Given the following config values are set as admin:
      | config                | value | plugin         |
      | defaultuserpreference | 0     | format_topcoll |
    When I am on the "CT" "Course" page logged in as "dennis"
    Then "#toggledsection-1" "css_element" should not be visible
    And "#toggledsection-2" "css_element" should not be visible

  Scenario: Toggle open on loading the page
    Given the following config values are set as admin:
      | config                | value | plugin         |
      | defaultuserpreference | 1     | format_topcoll |
    When I am on the "CT" "Course" page logged in as "dennis"
    Then "#toggledsection-1" "css_element" should be visible
    And "#toggledsection-2" "css_element" should be visible
