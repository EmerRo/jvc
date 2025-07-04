/* *
 *
 *  Popup generator for Stock tools
 *
 *  (c) 2009-2025 Sebastian Bochan
 *
 *  License: www.highcharts.com/license
 *
 *  !!!!!!! SOURCE GETS TRANSPILED BY TYPESCRIPT. EDIT TS FILE ONLY. !!!!!!!
 *
 * */
'use strict';
import H from '../../../Core/Globals.js';
var doc = H.doc;
import U from '../../../Core/Utilities.js';
var addEvent = U.addEvent, createElement = U.createElement;
/* *
 *
 *  Functions
 *
 * */
/**
 * Create tab content
 * @private
 * @return {HTMLDOMElement} - created HTML tab-content element
 */
function addContentItem() {
    var popupDiv = this.container;
    return createElement('div', {
        // #12100
        className: 'highcharts-tab-item-content highcharts-no-mousewheel'
    }, void 0, popupDiv);
}
/**
 * Create tab menu item
 * @private
 * @param {string} tabName
 * `add` or `edit`
 * @param {number} [disableTab]
 * Disable tab when 0
 * @return {Highcharts.HTMLDOMElement}
 * Created HTML tab-menu element
 */
function addMenuItem(tabName, disableTab) {
    var popupDiv = this.container, lang = this.lang;
    var className = 'highcharts-tab-item';
    if (disableTab === 0) {
        className += ' highcharts-tab-disabled';
    }
    // Tab 1
    var menuItem = createElement('button', {
        className: className
    }, void 0, popupDiv);
    menuItem.appendChild(doc.createTextNode(lang[tabName + 'Button'] || tabName));
    menuItem.setAttribute('highcharts-data-tab-type', tabName);
    return menuItem;
}
/**
 * Set all tabs as invisible.
 * @private
 */
function deselectAll() {
    var popupDiv = this.container, tabs = popupDiv
        .querySelectorAll('.highcharts-tab-item'), tabsContent = popupDiv
        .querySelectorAll('.highcharts-tab-item-content');
    for (var i = 0; i < tabs.length; i++) {
        tabs[i].classList.remove('highcharts-tab-item-active');
        tabsContent[i].classList.remove('highcharts-tab-item-show');
    }
}
/**
 * Init tabs. Create tab menu items, tabs containers
 * @private
 * @param {Highcharts.Chart} chart
 * Reference to current chart
 */
function init(chart) {
    if (!chart) {
        return;
    }
    var indicatorsCount = this.indicators.getAmount.call(chart);
    // Create menu items
    var firstTab = addMenuItem.call(this, 'add'); // Run by default
    addMenuItem.call(this, 'edit', indicatorsCount);
    // Create tabs containers
    addContentItem.call(this);
    addContentItem.call(this);
    switchTabs.call(this, indicatorsCount);
    // Activate first tab
    selectTab.call(this, firstTab, 0);
}
/**
 * Set tab as visible
 * @private
 * @param {globals.Element} - current tab
 * @param {number} - Index of tab in menu
 */
function selectTab(tab, index) {
    var allTabs = this.container
        .querySelectorAll('.highcharts-tab-item-content');
    tab.className += ' highcharts-tab-item-active';
    allTabs[index].className += ' highcharts-tab-item-show';
}
/**
 * Add click event to each tab
 * @private
 * @param {number} disableTab
 * Disable tab when 0
 */
function switchTabs(disableTab) {
    var popup = this, popupDiv = this.container, tabs = popupDiv.querySelectorAll('.highcharts-tab-item');
    tabs.forEach(function (tab, i) {
        if (disableTab === 0 &&
            tab.getAttribute('highcharts-data-tab-type') === 'edit') {
            return;
        }
        ['click', 'touchstart'].forEach(function (eventName) {
            addEvent(tab, eventName, function () {
                // Reset class on other elements
                deselectAll.call(popup);
                selectTab.call(popup, this, i);
            });
        });
    });
}
/* *
 *
 *  Default Export
 *
 * */
var PopupTabs = {
    init: init
};
export default PopupTabs;
