// Javascript functions for course format

M.course = M.course || {};

M.course.format = M.course.format || {};

/**
 * Get sections config for this format
 *
 * The section structure is:
 * <table class="topics">
 *  <tbody class"section">
 *    <td class="sectionbody">...</td>
 *    <td class="sectionbody">...</td>
 *  </tbody>
 *   ...
 * </table>
 *
 * @return {object} section list configuration
 */
M.course.format.get_config = function() {
    return {
        container_node : 'table',
        container_class : 'topics',
        section_wrapper_node : 'tbody',
        section_wrapper_class : 'section',
        section_node : 'tr',
        section_class : 'sectionbody'
    };
}

/**
 * Swap section
 *
 * @param {YUI} Y YUI3 instance
 * @param {string} node1 node to swap to
 * @param {string} node2 node to swap with
 * @return {NodeList} section list
 */
M.course.format.swap_sections = function(Y, node1, node2) {
    var CSS = {
        COURSECONTENT : 'course-content',
        LEFT : 'left',
        SECTIONADDMENUS : 'section_add_menus',
        CPS : 'cps'
    };

    var sectionlist = Y.Node.all('.'+CSS.COURSECONTENT+' '+M.course.format.get_section_wrapper(Y));
    // Swap left block
    sectionlist.item(node1).one('.'+CSS.LEFT).swap(sectionlist.item(node2).one('.'+CSS.LEFT));
    // Swap menus
    sectionlist.item(node1).one('.'+CSS.SECTIONADDMENUS).swap(sectionlist.item(node2).one('.'+CSS.SECTIONADDMENUS));
    // Swap toggles
    sectionlist.item(node1).one('.'+CSS.CPS).swap(sectionlist.item(node2).one('.'+CSS.CPS));
}
