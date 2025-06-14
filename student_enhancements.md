## Student-Focused Enhancements for Collapsed Topics Plugin

This document outlines three proposed student-focused enhancements for the Collapsed Topics Moodle plugin.

### 1. Topic Progress Indicator

*   **Description:** For each topic/section, display a visual indicator (e.g., a circular progress bar next to the topic title or a subtle background fill progression) representing the student's completion of activities within that topic. The progress should be calculated based on the number of completable activities within the topic and how many of those the student has marked as complete or has had automatically marked as complete.
*   **Student Benefit:**
    *   Provides immediate visual feedback on their progress through the course content on a per-topic basis.
    *   Helps students easily identify which topics they have started, are part-way through, or have completed.
    *   Can increase motivation by showing tangible progress as they complete activities.
    *   Allows students to better plan their studies by seeing how much is left in a particular topic.
*   **Potential Implementation Considerations:**
    *   Leverage Moodle's activity completion system (both student-marked and automatic completion).
    *   Requires querying the completion status for all activities within a section for the current user.
    *   The calculation would be (completed activities in topic / total completable activities in topic) * 100%.
    *   Careful consideration for performance, especially on courses with many topics and activities. Data might need to be cached or calculated efficiently.
    *   The visual indicator should be clear but not distracting, and accessible (e.g., provide text equivalents for screen readers).
    *   A course setting could allow teachers to enable or disable this feature.

### 2. "What's New?" Highlight

*   **Description:** Automatically highlight topics or specific activities within topics that have been recently added or updated by the teacher since the student's last course access or a specified timeframe. This could be a small, unobtrusive icon (e.g., a "new" badge or a dot) next to the topic title or activity, or a subtle change in background color for the new/updated item.
*   **Student Benefit:**
    *   Enables students to quickly identify new materials, announcements, or changes without having to scan the entire course page or remember what was there previously.
    *   Saves students time and ensures they don't miss important updates or newly added resources/activities.
    *   Particularly useful in courses that are actively being developed or have frequent updates.
*   **Potential Implementation Considerations:**
    *   Need to track the 'last viewed' timestamp for each student for the course or for individual sections/activities. Moodle's log data or user preferences could potentially be used.
    *   Alternatively, or in addition, track the creation/modification dates of course modules and section summaries.
    *   A "new" status would be determined by comparing the content modification date with the student's last view date or a fixed period (e.g., "new for 7 days").
    *   The visual highlight should be distinct but not overly distracting and should be accessible (e.g., ARIA attributes for "new" status).
    *   A mechanism for students to "dismiss" the "new" highlight might be useful, either individually or globally for the course.
    *   Teachers might need a setting to control the sensitivity or duration of the "new" highlight.

### 3. Personal Topic Notes/Reminders

*   **Description:** Allow students to add short, private text notes or reminders to each topic/section. These notes would be stored per student, per topic and would only be visible to the student who created them. A small icon or link within each topic's header could allow students to view, add, or edit their personal notes for that topic.
*   **Student Benefit:**
    *   Helps students organize their thoughts, jot down key takeaways, or list questions they have about a specific topic.
    *   Allows students to create personalized reminders for assignments, study points, or areas they need to revisit within a topic.
    *   Enhances the learning process by making the course material more interactive and tailored to individual student needs.
    *   Notes can serve as a quick reference when reviewing material before exams.
*   **Potential Implementation Considerations:**
    *   This would likely require a new custom database table to store the notes (e.g., `format_topcoll_student_notes` with fields for `id`, `courseid`, `sectionid`, `userid`, `notes_content`, `timemodified`).
    *   A simple text area (perhaps with basic rich text editing) would be needed for inputting and displaying the notes. This could be within a modal popup or an inline expandable area.
    *   An icon (e.g., a notepad or sticky note icon) in the section header could trigger the display/editing of notes. The icon could change appearance if notes exist for that topic for the student.
    *   Ensure that these notes are backed up and restored with user data if possible, or clearly communicate if they are not.
    *   Consider privacy implications and ensure notes are strictly private to the student.
    *   The feature could be enabled/disabled at the site or course level by an administrator or teacher.
