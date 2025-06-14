define([
    'jquery', // Standard Moodle jQuery
    'core/ajax',
    'core/str',
    'core/notification'
], function($, Ajax, Str, Notification) {

    var STR_SAVING = null;
    var STR_SAVE = null;
    var STR_CANCEL = null;
    var STR_ERROR_LOADING = null;
    var STR_ERROR_SAVING = null;
    var STR_ARIA_LABEL = null;
    var STR_NOTES_HEADING = null;

    /**
     * Initializes the string fetching.
     */
    var initStrings = function() {
        var stringkeys = [
            {key: 'saving', component: 'core'},
            {key: 'save', component: 'core'},
            {key: 'cancel', component: 'core'},
            {key: 'errorloadingnotes', component: 'format_topcoll'},
            {key: 'errorsavingnotes', component: 'format_topcoll'},
            {key: 'mynotessection', component: 'format_topcoll'}, // For ARIA label of textarea, e.g., "My notes for section X"
            {key: 'personalnotesheading', component: 'format_topcoll'} // For the heading of the notes area
        ];

        return Str.get_strings(stringkeys).then(function(strings) {
            STR_SAVING = strings[0];
            STR_SAVE = strings[1];
            STR_CANCEL = strings[2];
            STR_ERROR_LOADING = strings[3];
            STR_ERROR_SAVING = strings[4];
            STR_ARIA_LABEL = strings[5]; // This will be a pattern, e.g. "My notes for %s"
            STR_NOTES_HEADING = strings[6];
            return true;
        });
    };


    /**
     * Attaches event listeners to the notes triggers.
     */
    var initEventListeners = function() {
        $('.format-topcoll-notes-trigger').off('click.format_topcoll_notes').on('click.format_topcoll_notes', function(e) {
            e.preventDefault();
            var \$trigger = $(this);
            var sectionDbId = \$trigger.data('sectiondbid');
            var courseId = \$trigger.data('courseid');
            var sectionNum = \$trigger.data('sectionnum'); // For display in UI
            var \$editorUi = $('#format-topcoll-notes-ui-' + sectionDbId);

            toggleNotesUi(\$trigger, \$editorUi, courseId, sectionDbId, sectionNum);
        });
    };

    /**
     * Toggles the visibility of the notes UI and loads content if needed.
     */
    var toggleNotesUi = function(\$trigger, \$editorUi, courseId, sectionDbId, sectionNum) {
        var isExpanded = \$trigger.attr('aria-expanded') === 'true';

        if (isExpanded) {
            \$editorUi.slideUp(function() {
                $(this).empty(); // Clear content when closing to ensure fresh load next time or save memory
            });
            \$trigger.attr('aria-expanded', 'false');
        } else {
            \$trigger.attr('aria-expanded', 'true');
            // Show loading indicator while fetching/building UI
            \$editorUi.html('<p>' + STR_SAVING + '...</p>').slideDown(); // Re-use 'saving' for 'loading'

            // Fetch notes and build UI
            fetchAndDisplayNotes(\$editorUi, courseId, sectionDbId, sectionNum, \$trigger);
        }
    };

    /**
     * Fetches notes via AJAX and populates the editor UI.
     */
    var fetchAndDisplayNotes = function(\$editorUi, courseId, sectionDbId, sectionNum, \$trigger) {
        Ajax.call([{
            methodname: 'format_topcoll_get_personal_note',
            args: {
                courseid: courseId,
                sectionid: sectionDbId
            },
            done: function(response) {
                buildNotesEditor(\$editorUi, courseId, sectionDbId, sectionNum, response.notescontent || '', \$trigger);
            },
            fail: function(ex) {
                \$editorUi.html('<p class="text-danger">' + STR_ERROR_LOADING + '</p>');
                Notification.exception(ex);
            }
        }]);
    };

    /**
     * Builds the HTML for the notes editor (textarea, buttons).
     */
    var buildNotesEditor = function(\$editorUi, courseId, sectionDbId, sectionNum, currentNotes, \$trigger) {
        \$editorUi.empty(); // Clear loading message

        // Get section title for ARIA label - ideally passed or fetched, for now use section number
        var sectionTitleText = \$trigger.closest('li.section').find('.sectionname').text().replace(/ - Toggle\$/, '').trim();
        if (!sectionTitleText) {
             sectionTitleText = "Section " + sectionNum;
        }
        var ariaLabelForTextarea = Str.sprintf(STR_ARIA_LABEL, sectionTitleText);
        var notesHeadingId = 'format-topcoll-notes-heading-' + sectionDbId;


        var \$heading = $('<h3>').attr('id', notesHeadingId).addClass('h5').text(STR_NOTES_HEADING + " " + sectionTitleText);
        var \$textarea = $('<textarea>').addClass('form-control format-topcoll-notes-textarea')
                                     .attr('rows', 5)
                                     .attr('aria-label', ariaLabelForTextarea)
                                     .attr('aria-describedby', notesHeadingId)
                                     .val(currentNotes);
        var \$saveButton = $('<button>').addClass('btn btn-primary mt-2 format-topcoll-notes-save')
                                     .text(STR_SAVE)
                                     .data({
                                         courseid: courseId,
                                         sectionid: sectionDbId,
                                         sectionnum: sectionNum
                                     });
        var \$cancelButton = $('<button>').addClass('btn btn-secondary mt-2 ml-2 format-topcoll-notes-cancel')
                                       .text(STR_CANCEL);

        \$editorUi.append(\$heading, \$textarea, \$saveButton, \$cancelButton);

        // Attach event handlers for save/cancel
        \$saveButton.on('click', function() {
            saveNotes(\$(this), \$textarea.val());
        });

        \$cancelButton.on('click', function() {
            // Simply toggle closed, which will also empty the editor
            toggleNotesUi(\$trigger, \$editorUi, courseId, sectionDbId, sectionNum);
        });
    };

    /**
     * Saves notes via AJAX.
     */
    var saveNotes = function(\$saveButton, notesContent) {
        var courseId = \$saveButton.data('courseid');
        var sectionDbId = \$saveButton.data('sectionid');
        var originalButtonText = \$saveButton.text();
        \$saveButton.text(STR_SAVING + '...').prop('disabled', true);

        Ajax.call([{
            methodname: 'format_topcoll_save_personal_note',
            args: {
                courseid: courseId,
                sectionid: sectionDbId,
                notescontent: notesContent
            },
            done: function(response) {
                if (response.status === 'success') {
                    Notification.add(Str.get_string('changessaved', 'core'), 'success');
                    // Optionally, visually indicate success or update a 'last saved' timestamp
                    // For now, just revert button
                } else {
                    Notification.add(response.message || STR_ERROR_SAVING, 'error');
                }
            },
            fail: Notification.exception,
            always: function() {
                 \$saveButton.text(originalButtonText).prop('disabled', false);
                 // Consider if the UI should close after saving. For now, it stays open.
            }
        }]);
    };

    // Public interface
    return {
        init: function() {
            initStrings().then(function() {
                initEventListeners();
            }).catch(Notification.exception);
        }
    };
});
