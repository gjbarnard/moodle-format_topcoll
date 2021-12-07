@format @format_topcoll @javascript
Feature: Toggle persistance off
  To improve performace
  As an Administrator
  I can turn off the persistance.

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

  Scenario: Toggle closed after reloading the page when they were open
    Given the following config values are set as admin:
      | config                   | value | plugin         |
      | defaulttogglepersistence | 0     | format_topcoll |
    When I am on the "CT" "Course" page logged in as "dennis"
    And I click on "Section 1 - Toggle" "text"
    And I click on "Section 2 - Toggle" "text"
    And I reload the page
    Then "#toggledsection-1" "css_element" should not be visible
    And "#toggledsection-2" "css_element" should not be visible

  Scenario: Toggle open after reloading the page when they were closed
    Given the following config values are set as admin:
      | config                   | value | plugin         |
      | defaulttogglepersistence | 1     | format_topcoll |
    When I am on the "CT" "Course" page logged in as "dennis"
    And I click on "Section 1 - Toggle" "text"
    And I click on "Section 2 - Toggle" "text"
    And I reload the page
    Then "#toggledsection-1" "css_element" should be visible
    And "#toggledsection-2" "css_element" should be visible
