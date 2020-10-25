@format @format_topcoll
Feature: Toggle
  In order to see and hide the sections
  As a student
  I need to be able to open and close toggled sections.

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email            |
      | dennis   | Dennis    | Topcoll  | dennis@topcoll.com |
    And the following "courses" exist:
      | fullname | shortname | format  | numsections |
      | CollTop  | CT        | topcoll | 2           |
    And the following "course enrolments" exist:
      | user     | course | role    |
      | dennis   | CT     | student |
    And I log in as "dennis"
    And I am on "CollTop" course homepage

  @javascript
  Scenario: Open a toggle
    When I click on "Section 1 - Toggle" "text"
    Then "#toggledsection-1" "css_element" should be visible
    And "#toggledsection-2" "css_element" should not be visible

  @javascript
  Scenario: Close a toggle
    When I click on "Open all" "text"
    And I click on "Section 1 - Toggle" "text"
    Then "#toggledsection-1" "css_element" should not be visible
    And "#toggledsection-2" "css_element" should be visible
