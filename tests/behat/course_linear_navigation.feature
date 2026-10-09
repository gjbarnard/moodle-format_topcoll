@format @format_topcoll
Feature: Collapsed topics supports course linear navigation
  In order to navigate through the course activities in a linear way
  As an administrator
  I need the custom sections format to support course linear navigation when I enable it site-wide

  @javascript
  Scenario Outline: The site setting controls linear navigation for the custom sections format
    Given the following "users" exist:
      | username | firstname | lastname |
      | s1       | Student   | 1        |
    And the following config values are set as admin:
      | enablelinearnav | <value> | format_topcoll |
    And the following "courses" exist:
      | fullname | shortname | format |
      | Course 1 | C1        | topcoll |
    And the following "course enrolments" exist:
      | user | course | role    |
      | s1   | C1     | student |
    And the following "activities" exist:
      | activity | name  | course |
      | page     | Page1 | C1     |
    When I am on the "Page1" "page activity" page logged in as "s1"
    Then the course linear navigation <shouldbevisible>

    Examples:
      | value | shouldbevisible       |
      | 1     | should be visible     |
      | 0     | should not be visible |
