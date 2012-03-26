/**
 * Collapsed Topics Information
 *
 * A topic based format that solves the issue of the 'Scroll of Death' when a course has many topics. All topics
 * except zero have a toggle that displays that topic. One or more topics can be displayed at any given time.
 * Toggles are persistent on a per browser session per course basis but can be made to persist longer by a small
 * code change. Full installation instructions, code adaptions and credits are included in the 'Readme.txt' file.
 *
 * @package    course/format
 * @subpackage topcoll
 * @version    See the value of '$plugin->version' in version.php.
 * @copyright  &copy; 2009-onwards G J Barnard in respect to modifications of standard topics format.
 * @author     G J Barnard - gjbarnard at gmail dot com and {@link http://moodle.org/user/profile.php?id=442195}
 * @link       http://docs.moodle.org/en/Collapsed_Topics_course_format
 * @license    http://www.gnu.org/copyleft/gpl.html GNU Public License
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.

 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.

 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */
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
