/* *
 *
 *  (c) 2010-2025 Torstein Honsi
 *
 *  License: www.highcharts.com/license
 *
 *  !!!!!!! SOURCE GETS TRANSPILED BY TYPESCRIPT. EDIT TS FILE ONLY. !!!!!!!
 *
 * */
'use strict';
import H from '../Core/Globals.js';
var deg2rad = H.deg2rad;
import Series from '../Core/Series/Series.js';
import U from '../Core/Utilities.js';
var fireEvent = U.fireEvent, isNumber = U.isNumber, pick = U.pick, relativeLength = U.relativeLength;
/**
 * @private
 */
var CenteredUtilities;
(function (CenteredUtilities) {
    /* *
     *
     *  Declarations
     *
     * */
    /* *
     *
     *  Functions
     *
     * */
    /* eslint-disable valid-jsdoc */
    /**
     * Get the center of the pie based on the size and center options relative
     * to the plot area. Borrowed by the polar and gauge series types.
     *
     * @private
     * @function Highcharts.CenteredSeriesMixin.getCenter
     */
    function getCenter() {
        var options = this.options, chart = this.chart, slicingRoom = 2 * (options.slicedOffset || 0), plotWidth = chart.plotWidth - 2 * slicingRoom, plotHeight = chart.plotHeight - 2 * slicingRoom, centerOption = options.center, smallestSize = Math.min(plotWidth, plotHeight), thickness = options.thickness;
        var handleSlicingRoom, size = options.size, innerSize = options.innerSize || 0, i, value;
        if (typeof size === 'string') {
            size = parseFloat(size);
        }
        if (typeof innerSize === 'string') {
            innerSize = parseFloat(innerSize);
        }
        var positions = [
            pick(centerOption === null || centerOption === void 0 ? void 0 : centerOption[0], '50%'),
            pick(centerOption === null || centerOption === void 0 ? void 0 : centerOption[1], '50%'),
            // Prevent from negative values
            pick(size && size < 0 ? void 0 : options.size, '100%'),
            pick(innerSize && innerSize < 0 ? void 0 : options.innerSize || 0, '0%')
        ];
        // No need for inner size in angular (gauges) series but still required
        // for pie series
        if (chart.angular && !(this instanceof Series)) {
            positions[3] = 0;
        }
        for (i = 0; i < 4; ++i) {
            value = positions[i];
            handleSlicingRoom = i < 2 || (i === 2 && /%$/.test(value));
            // I == 0: centerX, relative to width
            // i == 1: centerY, relative to height
            // i == 2: size, relative to smallestSize
            // i == 3: innerSize, relative to size
            positions[i] = relativeLength(value, [plotWidth, plotHeight, smallestSize, positions[2]][i]) + (handleSlicingRoom ? slicingRoom : 0);
        }
        // Inner size cannot be larger than size (#3632)
        if (positions[3] > positions[2]) {
            positions[3] = positions[2];
        }
        // Thickness overrides innerSize, need to be less than pie size (#6647)
        if (isNumber(thickness) &&
            thickness * 2 < positions[2] && thickness > 0) {
            positions[3] = positions[2] - thickness * 2;
        }
        fireEvent(this, 'afterGetCenter', { positions: positions });
        return positions;
    }
    CenteredUtilities.getCenter = getCenter;
    /**
     * GetStartAndEndRadians - Calculates start and end angles in radians.
     * Used in series types such as pie and sunburst.
     *
     * @private
     * @function Highcharts.CenteredSeriesMixin.getStartAndEndRadians
     *
     * @param {number} [start]
     *        Start angle in degrees.
     *
     * @param {number} [end]
     *        Start angle in degrees.
     *
     * @return {Highcharts.RadianAngles}
     *         Returns an object containing start and end angles as radians.
     */
    function getStartAndEndRadians(start, end) {
        var startAngle = isNumber(start) ? start : 0, // Must be a number
        endAngle = ((isNumber(end) && // Must be a number
            end > startAngle && // Must be larger than the start angle
            // difference must be less than 360 degrees
            (end - startAngle) < 360) ?
            end :
            startAngle + 360), correction = -90;
        return {
            start: deg2rad * (startAngle + correction),
            end: deg2rad * (endAngle + correction)
        };
    }
    CenteredUtilities.getStartAndEndRadians = getStartAndEndRadians;
})(CenteredUtilities || (CenteredUtilities = {}));
/* *
 *
 *  Default Export
 *
 * */
export default CenteredUtilities;
/* *
 *
 *  API Declarations
 *
 * */
/**
 * @private
 * @interface Highcharts.RadianAngles
 */ /**
* @name Highcharts.RadianAngles#end
* @type {number}
*/ /**
* @name Highcharts.RadianAngles#start
* @type {number}
*/
''; // Keeps doclets above in JS file
