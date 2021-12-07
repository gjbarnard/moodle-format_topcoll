@format @format_topcoll @javascript
Feature: Toggle
  In order to see and hide the sections
  As a student
  I need to be able to open and close toggled sections.

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email              |
      | dennis   | Dennis    | Topcoll  | dennis@topcoll.com |
    And the following "courses" exist:
      | fullname | shortname | format  | numsections |
      | CollTop  | CT        | topcoll | 3           |
    And the following "course enrolments" exist:
      | user     | course | role    |
      | dennis   | CT     | student |
    And the following config values are set as admin:
      | config                | value | plugin         |
      | defaultuserpreference | 0     | format_topcoll |
    And I am on the "CT" "Course" page logged in as "dennis"

  Scenario: Open a toggle
    When I click on "Section 1 - Toggle" "text"
    Then "#toggledsection-1" "css_element" should be visible
    And "#toggledsection-2" "css_element" should not be visible

  Scenario: Close a toggle
    When I click on "Open all" "text"
    And I click on "Section 1 - Toggle" "text"
    Then "#toggledsection-1" "css_element" should not be visible
    And "#toggledsection-2" "css_element" should be visible

  Scenario: Open all toggles
    When I click on "Open all" "text"
    Then "#toggledsection-1" "css_element" should be visible
    And "#toggledsection-2" "css_element" should be visible

  Scenario: Close all toggles
    When I click on "Open all" "text"
    And I click on "Close all" "text"
    Then "#toggledsection-1" "css_element" should not be visible
    And "#toggledsection-2" "css_element" should not be visible

  Scenario: Toggle open after reloading the page
    When I click on "Section 1 - Toggle" "text"
    And I reload the page
    Then "#toggledsection-1" "css_element" should be visible
    And "#toggledsection-2" "css_element" should not be visible
