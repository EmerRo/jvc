/* *
 *
 *  Highcharts variwide module
 *
 *  (c) 2010-2025 Torstein Honsi
 *
 *  License: www.highcharts.com/license
 *
 *  !!!!!!! SOURCE GETS TRANSPILED BY TYPESCRIPT. EDIT TS FILE ONLY. !!!!!!!
 *
 * */
'use strict';
import H from '../../Core/Globals.js';
var composed = H.composed;
import U from '../../Core/Utilities.js';
var addEvent = U.addEvent, pushUnique = U.pushUnique, wrap = U.wrap;
/* *
 *
 *  Functions
 *
 * */
/**
 * @private
 */
function compose(AxisClass, TickClass) {
    if (pushUnique(composed, 'Variwide')) {
        var tickProto = TickClass.prototype;
        addEvent(AxisClass, 'afterDrawCrosshair', onAxisAfterDrawCrosshair);
        addEvent(AxisClass, 'afterRender', onAxisAfterRender);
        addEvent(TickClass, 'afterGetPosition', onTickAfterGetPosition);
        tickProto.postTranslate = tickPostTranslate;
        wrap(tickProto, 'getLabelPosition', wrapTickGetLabelPosition);
    }
}
/**
 * Same width as the category (#8083)
 * @private
 */
function onAxisAfterDrawCrosshair(e) {
    var _a;
    if (this.variwide && this.cross) {
        this.cross.attr('stroke-width', (_a = e.point) === null || _a === void 0 ? void 0 : _a.crosshairWidth);
    }
}
/**
 * On a vertical axis, apply anti-collision logic to the labels.
 * @private
 */
function onAxisAfterRender() {
    var axis = this;
    if (this.variwide) {
        this.chart.labelCollectors.push(function () {
            return axis.tickPositions
                .filter(function (pos) { return !!axis.ticks[pos].label; })
                .map(function (pos, i) {
                var label = axis.ticks[pos].label;
                label.labelrank = axis.zData[i];
                return label;
            });
        });
    }
}
/**
 * @private
 */
function onTickAfterGetPosition(e) {
    var axis = this.axis, xOrY = axis.horiz ? 'x' : 'y';
    if (axis.variwide) {
        this[xOrY + 'Orig'] = e.pos[xOrY];
        this.postTranslate(e.pos, xOrY, this.pos);
    }
}
/**
 * @private
 */
function tickPostTranslate(xy, xOrY, index) {
    var axis = this.axis;
    var pos = xy[xOrY] - axis.pos;
    if (!axis.horiz) {
        pos = axis.len - pos;
    }
    pos = axis.series[0].postTranslate(index, pos);
    if (!axis.horiz) {
        pos = axis.len - pos;
    }
    xy[xOrY] = axis.pos + pos;
}
/**
 * @private
 */
function wrapTickGetLabelPosition(proceed, _x, _y, _label, horiz, 
/* eslint-disable @typescript-eslint/no-unused-vars */
_labelOptions, _tickmarkOffset, _index
/* eslint-enable @typescript-eslint/no-unused-vars */
) {
    var args = Array.prototype.slice.call(arguments, 1), xOrY = horiz ? 'x' : 'y';
    // Replace the x with the original x
    if (this.axis.variwide &&
        typeof this[xOrY + 'Orig'] === 'number') {
        args[horiz ? 0 : 1] = this[xOrY + 'Orig'];
    }
    var xy = proceed.apply(this, args);
    // Post-translate
    if (this.axis.variwide && this.axis.categories) {
        this.postTranslate(xy, xOrY, this.pos);
    }
    return xy;
}
/* *
 *
 *  Default Export
 *
 * */
var VariwideComposition = {
    compose: compose
};
export default VariwideComposition;
