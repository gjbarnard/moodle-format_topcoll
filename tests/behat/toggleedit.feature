@format @format_topcoll @javascript
Feature: Toggle
  In order to see and hide the sections
  As a student
  I need to be able to open and close toggled sections.

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email                    |
      | dennis   | Dennis    | Topcoll  | dennis@topcoll.localhost |
    And the following "courses" exist:
      | fullname | shortname | format  | numsections |
      | CollTop  | CT        | topcoll | 3           |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | dennis   | CT     | editingteacher |
    And the following config values are set as admin:
      | config                | value | plugin         |
      | defaultuserpreference | 0     | format_topcoll |
    And I am on the "CT" "Course" page logged in as "dennis"
    And I turn editing mode on

  Scenario: Open a toggle editing
    When I click on "Section 1" "text" in the ".toggle" "css_element"
    Then "#toggledsection-1" "css_element" should be visible
    And "#toggledsection-2" "css_element" should not be visible

  Scenario: Close a toggle editing
    When I click on "Open all" "text"
    And I click on "Section 1" "text" in the ".toggle" "css_element"
    Then "#toggledsection-1" "css_element" should not be visible
    And "#toggledsection-2" "css_element" should be visible
