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
 * Collapsed Topics Course index main component.
 *
 * @module     format_topcoll/local/content
 * @class      format_topcoll/local/content
 * @copyright  2022 G J Barnard based upon work done by:
 * @copyright  2020 Ferran Recio <ferran@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import Component from 'core_courseformat/local/content';
import Config from 'core/config';
import Fragment from 'core/fragment';
import { getCurrentCourseEditor } from 'core_courseformat/courseeditor';
import inplaceeditable from 'core/inplace_editable';
import Log from 'core/log';
import Pending from 'core/pending';
import Templates from 'core/templates';
import TopcollDispatchActions from 'format_topcoll/local/content/actions';
import {setUserTopcollToggle, userSetUserToggleAll} from 'format_topcoll/util';
import * as CourseEvents from 'core_course/events';

export default class TopcollComponent extends Component {

    /**
     * The class constructor.
     *
     * The only param this method gets is a constructor with all the mandatory
     * and optional component data. Component will receive the same descriptor
     * as create method param.
     *
     * This method will call the "create" method before registering the component into
     * the reactive module. This way any component can add default selectors and events.
     *
     * @param {descriptor} descriptor data to create the object.
     */
    constructor(descriptor) {
        super(descriptor);
        const tcdata = this.getElement(this.selectors.TC_DATA);
        if (tcdata) {
            this.oneTopic = (tcdata.dataset.onetopic === 'true');
            if (tcdata.dataset.onetopictoggle === 'false') {
                this.currentTopicNum = false;
            } else {
                this.currentTopicNum = tcdata.dataset.onetopictoggle;
            }
            this.defaulttogglepersistence = (tcdata.dataset.defaulttogglepersistence === 'true');
        }
    }

    /**
     * Static method to create a component instance form the mustahce template.
     *
     * @param {string} target the DOM main element or its ID
     * @param {object} selectors optional css selector overrides
     * @param {number} sectionReturn the content section return
     * @return {Component}
     */
    static init(target, selectors, sectionReturn) {
        return new TopcollComponent({
            element: document.getElementById(target),
            reactive: getCurrentCourseEditor(),
            selectors,
            sectionReturn
        });
    }

    /**
     * Initial state ready method.
     */
    stateReady() {
        this._indexContents();

        // Toggle.
        const toogleAllClosed = this.getElement(this.selectors.TOGGLE_ALL_ClOSED);
        if (toogleAllClosed) {
            this.addEventListener(toogleAllClosed, 'click', this._toogleAllClosedToggler);
            this.addEventListener(toogleAllClosed, 'keydown', e => {
                // Close all sections when Space key is pressed on the toggle button.
                if (e.key === ' ') {
                    this._toogleAllClosedToggler(e);
                }
            });
        }

        const toogleAllOpen = this.getElement(this.selectors.TOGGLE_ALL_OPEN);
        if (toogleAllOpen) {
            this.addEventListener(toogleAllOpen, 'click', this._toogleAllOpenToggler);
            this.addEventListener(toogleAllOpen, 'keydown', e => {
                // Open all sections when Space key is pressed on the toggle button.
                if (e.key === ' ') {
                    this._toogleAllOpenToggler(e);
                }
            });
        }

        const toggles = this.getElements(this.selectors.TOGGLE);
        for (const toggle of toggles) {
            this.addEventListener(toggle, 'click', this._toogleToggler);
            this.addEventListener(toggle, 'keydown', e => {
                if (e.key === ' ') {
                    this._toogleToggler(e);
                }
            });
        }

        if (this.reactive.supportComponents) {
            // Actions are only available in edit mode.
            if (this.reactive.isEditing) {
                new TopcollDispatchActions(this);
            }

            // Mark content as state ready.
            this.element.classList.add(this.classes.STATEDREADY);
        }

        // Capture completion events.
        this.addEventListener(
            this.element,
            CourseEvents.manualCompletionToggled,
            this._completionHandler
        );

        // Capture page scroll to update page item.
        this.addEventListener(
            document,
            "scroll",
            this._scrollHandler
        );
    }

    /**
     * Return the component watchers.
     *
     * @returns {Array} of watchers
     */
    getWatchers() {
        // Section return is a global page variable but most formats define it just before start printing
        // the course content. This is the reason why we define this page setting here.
        this.reactive.sectionReturn = this.sectionReturn;

        // Check if the course format is compatible with reactive components.
        if (!this.reactive.supportComponents) {
            return [];
        }
        return [
            // State changes that require to reload some course modules.
            {watch: `cm.visible:updated`, handler: this._reloadCm},
            {watch: `cm.stealth:updated`, handler: this._reloadCm},
            {watch: `cm.sectionid:updated`, handler: this._reloadCm},
            {watch: `cm.indent:updated`, handler: this._reloadCm},
            {watch: `cm.groupmode:updated`, handler: this._reloadCm},
            {watch: `cm.name:updated`, handler: this._refreshCmName},
            // Update section number and title.
            {watch: `section.number:updated`, handler: this._refreshSectionNumber},
            {watch: `section.title:updated`, handler: this._refreshSectionTitle},
            //{watch: `section:updated`, handler: this._refreshTCSection},
            // Sections and cm sorting.
            {watch: `transaction:start`, handler: this._startProcessing},
            {watch: `course.sectionlist:updated`, handler: this._refreshCourseSectionlist},
            {watch: `section.cmlist:updated`, handler: this._refreshSectionCmlist},
            // Section visibility.
            {watch: `section.visible:updated`, handler: this._reloadSection},
            // Reindex sections and cms.
            {watch: `state:updated`, handler: this._indexContents},
        ];
    }

    _refreshTCSection({element}) {
        Log.debug(element.id);
    }

    /**
     * Update a course section when the section number changes.
     *
     * The courseActions module used for most course section tools still depends on css classes and
     * section numbers (not id). To prevent inconsistencies when a section is moved, we need to refresh
     * the
     *
     * Course formats can override the section title rendering so the frontend depends heavily on backend
     * rendering. Luckily in edit mode we can trigger a title update using the inplace_editable module.
     *
     * @param {Object} param
     * @param {Object} param.element details the update details.
     */
    _refreshSectionNumber({element}) {
        Log.debug('_refreshSectionNumber ' + element.id);

        // Find the element.
        const target = this.getElement(this.selectors.SECTION, element.id);
        if (!target) {
            // Job done. Nothing to refresh.
            return;
        }

        if (target.classList.contains('delegated-section')) {
            // Update section numbers in all data, css and YUI attributes.
            target.id = `section-${element.number}`;
            // YUI uses section number as section id in data-sectionid, in principle if a format use components
            // don't need this sectionid attribute anymore, but we keep the compatibility in case some plugin
            // use it for legacy purposes.
            target.dataset.sectionid = element.number;
            // The data-number is the attribute used by components to store the section number.
            target.dataset.number = element.number;

            // Update title and title inplace editable, if any.
            const inplace = inplaceeditable.getInplaceEditable(target.querySelector(this.selectors.SECTION_ITEM));
            if (inplace) {
                // The course content HTML can be modified at any moment, so the function need to do some checkings
                // to make sure the inplace editable still represents the same itemid.
                const currentvalue = inplace.getValue();
                const currentitemid = inplace.getItemId();
                // Unnamed sections must be recalculated.
                if (inplace.getValue() === '') {
                    // The value to send can be an empty value if it is a default name.
                    if (currentitemid == element.id && (currentvalue != element.rawtitle || element.rawtitle == '')) {
                        inplace.setValue(element.rawtitle);
                    }
                }
            }
        } else {
            // Normal section.
            // As the number has changed then we need to regenerate the whole section.
            this._reloadSection({
                element: element,
            });
        }
    }

    /**
     * Reload a course section contents.
     *
     * Section HTML is still strongly backend dependant.
     * Some changes require to get a new version of the section.
     *
     * @param {details} param0 the watcher details
     * @param {object} param0.element the state object
     */
    _reloadSection({element}) {
        Log.debug('_reloadSection ' + element.id);
        const pendingReload = new Pending(`courseformat/content:reloadSection_${element.id}`);
        const sectionitem = this.getElement(this.selectors.SECTION, element.id);
        if (sectionitem) {
            // Cancel any pending reload because the section will reload cms too.
            for (const cmId of element.cmlist) {
                this._cancelDebouncedReloadCm(cmId);
            }
            const promise = Fragment.loadFragment(
                'core_courseformat',
                'section',
                Config.courseContextId,
                {
                    id: element.id,
                    courseid: Config.courseId,
                    sr: this.reactive.sectionReturn ?? null,
                }
            );
            promise.then((html, js) => {
                Log.debug('_reloadSection promise reply eid: ' + element.id);
                Templates.replaceNode(sectionitem, html, js);
                this._indexContents();

                const container = this.getElement(this.selectors.COURSE_SECTIONLIST);
                const toggle = container.querySelector('[data-id="' + element.id + '"] ' + this.selectors.TOGGLE);
                Log.debug('toggle id ' + toggle.id + ' parent li ' + toggle.parentElement.parentElement.id +
                    ' ' + toggle.parentElement.parentElement.dataset.id);
                if (toggle !== null) {
                    Log.debug('toggle exists ' + toggle.id);
                    this.addEventListener(toggle, 'click', this._toogleToggler);
                    this.addEventListener(toggle, 'keydown', e => {
                        // Open all sections when Space key is pressed on the toggle button.
                        if (e.key === ' ') {
                            this._toogleToggler(e);
                        }
                    });
                }

                pendingReload.resolve();
            }).catch(() => {
                Log.debug('_reloadSection promise fail ' + element.id);
                pendingReload.resolve();
            });
        } else {
            Log.debug('_reloadSection no section item ' + element.id);
        }
    }

    /**
     * Handle the close all toggles button.
     *
     * @param {Event} event the triggered event
     */
    _toogleAllClosedToggler(event) {
        event.preventDefault();

        const toggles = this.getElements(this.selectors.TOGGLE + ' .the_toggle');
        for (const toggle of toggles) {
            toggle.classList.add('toggle_closed');
            toggle.classList.remove('toggle_open');
        }
        const toggledsections = this.getElements(this.selectors.TOGGLED_SECTION);
        for (const toggledsection of toggledsections) {
            toggledsection.classList.remove('sectionopen');
        }

        if (this.defaulttogglepersistence === true) {
            userSetUserToggleAll(Config.courseId, false);
        }
    }

    /**
     * Handle the open all toggles button.
     *
     * @param {Event} event the triggered event
     */
    _toogleAllOpenToggler(event) {
        event.preventDefault();

        const toggles = this.getElements(this.selectors.TOGGLE + ' .the_toggle');
        for (const toggle of toggles) {
            toggle.classList.add('toggle_open');
            toggle.classList.remove('toggle_closed');
        }
        const toggledsections = this.getElements(this.selectors.TOGGLED_SECTION);
        for (const toggledsection of toggledsections) {
            toggledsection.classList.add('sectionopen');
        }

        if (this.defaulttogglepersistence === true) {
            userSetUserToggleAll(Config.courseId, true);
        }
    }

    /**
     * Handle the toggler.
     *
     * @param {Event} event the triggered event
     */
    _toogleToggler(event) {
        Log.debug('_toogleToggler');
        if (this.reactive.isEditing) {
            const parentClasses = event.target.parentElement.classList;
            if ((parentClasses.contains('quickediticon')) || (parentClasses.contains('inplaceeditable'))) {
                return;
            }
        }

        event.preventDefault();
        const toggle = event.target.closest(this.selectors.TOGGLE);
        const toggleNum = parseInt(toggle.getAttribute('id').replace("toggle-", ""));
        Log.debug('_toogleToggler: ' + toggleNum);

        if (this.oneTopic === true) {
            if ((this.currentTopicNum !== false) && (this.currentTopicNum != toggleNum)) {
                const currentTargetParent = this.getElement('#toggle-' + this.currentTopicNum).parentElement;
                const currentToggle = currentTargetParent.querySelector('.the_toggle');
                currentToggle.classList.add('toggle_closed');
                currentToggle.classList.remove('toggle_open');
                currentToggle.setAttribute('aria-expanded', 'false');

                const currentSection = currentTargetParent.querySelector(this.selectors.TOGGLED_SECTION);
                currentSection.classList.remove('sectionopen');

                if (this.defaulttogglepersistence === true) {
                    setUserTopcollToggle(Config.courseId, this.currentTopicNum, false);
                }
                this.currentTopicNum = false;
            }
        }

        const target = toggle.querySelector('.the_toggle');
        const targetSection = toggle.parentElement.querySelector(this.selectors.TOGGLED_SECTION);
        var state;
        if (target.classList.contains('toggle_closed')) {
            target.classList.add('toggle_open');
            target.classList.remove('toggle_closed');
            target.setAttribute('aria-expanded', 'true');
            targetSection.classList.add('sectionopen');
            if (this.oneTopic === true) {
                this.currentTopicNum = toggleNum;
            }
            state = true;
        } else {
            target.classList.add('toggle_closed');
            target.classList.remove('toggle_open');
            target.setAttribute('aria-expanded', 'false');
            targetSection.classList.remove('sectionopen');
            if (this.oneTopic === true) {
                this.currentTopicNum = false;
            }
            state = false;
        }
        if (this.defaulttogglepersistence === true) {
            setUserTopcollToggle(Config.courseId, toggleNum, state);
        }
    }

    /**
     * Refresh the section list.
     *
     * @param {Object} param
     * @param {Object} param.state the full state object.
     */
    _refreshCourseSectionlist({state}) {
        // If we have a section return means we only show a single section so no need to fix order.
        if (this.reactive.sectionReturn !== null) {
            return;
        }
        const sectionlist = this.reactive.getExporter().listedSectionIds(state);
        const listparent = this.getElement(this.selectors.COURSE_SECTIONLIST);
        // For now section cannot be created at a frontend level.
        const createSection = this._createSectionItem.bind(this);
        if (listparent) {
            this._fixTopcollSectionOrder(listparent, sectionlist, this.selectors.SECTION, this.dettachedSections, createSection);
        }
    }

    /**
     * Fix/reorder the section or cms order.
     *
     * @param {Element} container the HTML element to reorder.
     * @param {Array} neworder an array with the ids order
     * @param {string} selector the element selector
     * @param {Object} dettachedelements a list of dettached elements
     * @param {function} createMethod method to create missing elements
     */
    async _fixTopcollSectionOrder(container, neworder, selector, dettachedelements, createMethod) {
        if (container === undefined) {
            return;
        }

        // Empty lists should not be visible.
        if (!neworder.length) {
            container.classList.add('hidden');
            container.innerHTML = '';
            return;
        }

        // Grant the list is visible (in case it was empty).
        container.classList.remove('hidden');

        // Move the elements in order at the beginning of the list.
        neworder.forEach((itemid, index) => {
            let item = this.getElement(selector, itemid) ?? dettachedelements[itemid] ?? createMethod(container, itemid);
            if (!item) {
                // Missing elements cannot be sorted.
                return;
            }
            let itemno = this.getElement('#tcnoid-'+itemid);
            if (itemno) {
                itemno.textContent = index + 1; // Update the section number in the 'left' part.
            }
            // Get the current elemnt at that position.
            const currentitem = container.children[index];
            if (!currentitem) {
                container.append(item);
                return;
            }
            if (currentitem !== item) {
                container.insertBefore(item, currentitem);
            }
        });

        // Dndupload add a fake element we need to keep.
        let dndFakeActivity;

        // Remove the remaining elements.
        while (container.children.length > neworder.length) {
            const lastchild = container.lastChild;
            if (lastchild?.classList?.contains('dndupload-preview')) {
                dndFakeActivity = lastchild;
            } else {
                dettachedelements[lastchild?.dataset?.id ?? 0] = lastchild;
            }
            container.removeChild(lastchild);
        }
        // Restore dndupload fake element.
        if (dndFakeActivity) {
            container.append(dndFakeActivity);
        }
    }
}
