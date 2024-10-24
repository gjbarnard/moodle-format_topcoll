// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Collapsed Topics Course state actions dispatcher.
 *
 * @module     format_topcoll/local/content/actions
 * @class      format_topcoll/local/content/actions
 * @copyright  2024 G J Barnard based upon work done by:
 * @copyright  2021 Ferran Recio <ferran@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import BaseActions from 'core_courseformat/local/content/actions';

export default class extends BaseActions {

    /**
     * Constructor hook.
     */
    create() {
        super.create();
        this.selectors.ADDSECTION = ".increase-sections";
    }

    /**
     * Disable all add sections actions.
     *
     * @param {boolean} locked the new locked value.
     */
    _setAddSectionLocked(locked) {
        const courseAddSection = this.getElement(this.selectors.COURSEADDSECTION);
        if (courseAddSection) {
            const addSection = courseAddSection.querySelector(this.selectors.ADDSECTION);
            if (addSection) {
                addSection.classList.toggle(this.classes.DISPLAYNONE, locked);
            }
            const noMoreSections = courseAddSection.querySelector(this.selectors.MAXSECTIONSWARNING);
            noMoreSections.classList.toggle(this.classes.DISPLAYNONE, !locked);
        }
    }
}
