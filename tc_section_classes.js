// Replaces by overriding the original function in /lib/ajax/section_classes.js
section_class.prototype.swap_with_section = function(sectionIn)
{
    var tmpStore = null;

    var thisIndex = main.get_section_index(this);
    var targetIndex = main.get_section_index(sectionIn);
    if (thisIndex == -1) {
        // source must exist
        return;
    }
    if (targetIndex == -1) {
        // target must exist
        return;
    }

    main.sections[targetIndex] = this;
    main.sections[thisIndex] = sectionIn;

    this.changeId(targetIndex);
    sectionIn.changeId(thisIndex);

    if (this.debug) {
        YAHOO.log("Swapping "+this.getEl().id+" with "+sectionIn.getEl().id);
    }
    // Swap the sections.
    YAHOO.util.DDM.swapNode(this.getEl(), sectionIn.getEl());
    // This is the additional line that swaps the section underneath the toggle as well as the toggle itself (above line).
    // But the 'Topic x' does not change until page refresh.
    YAHOO.util.DDM.swapNode(this.getEl().previousSibling, sectionIn.getEl().previousSibling);

    // Sections contain forms to add new resources/activities. These forms
    // have not been updated to reflect the new positions of the sections that
    // we have swapped. Let's swap the two sections' forms around.
    if (this.getEl().getElementsByTagName('form')[0].parentNode
            && sectionIn.getEl().getElementsByTagName('form')[0].parentNode) {

        YAHOO.util.DDM.swapNode(this.getEl().getElementsByTagName('form')[0].parentNode,
                sectionIn.getEl().getElementsByTagName('form')[0].parentNode);
    } else {
        YAHOO.log("Swapping sections: form not present in one or both sections", "warn");
    }
};
