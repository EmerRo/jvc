/* *
 *
 *  Highcharts funnel3d series module
 *
 *  (c) 2010-2025 Highsoft AS
 *
 *  Author: Kacper Madej
 *
 *  License: www.highcharts.com/license
 *
 *  !!!!!!! SOURCE GETS TRANSPILED BY TYPESCRIPT. EDIT TS FILE ONLY. !!!!!!!
 *
 * */
'use strict';
var __extends = (this && this.__extends) || (function () {
    var extendStatics = function (d, b) {
        extendStatics = Object.setPrototypeOf ||
            ({ __proto__: [] } instanceof Array && function (d, b) { d.__proto__ = b; }) ||
            function (d, b) { for (var p in b) if (Object.prototype.hasOwnProperty.call(b, p)) d[p] = b[p]; };
        return extendStatics(d, b);
    };
    return function (d, b) {
        if (typeof b !== "function" && b !== null)
            throw new TypeError("Class extends value " + String(b) + " is not a constructor or null");
        extendStatics(d, b);
        function __() { this.constructor = d; }
        d.prototype = b === null ? Object.create(b) : (__.prototype = b.prototype, new __());
    };
})();
import Funnel3DComposition from './Funnel3DComposition.js';
import Funnel3DSeriesDefaults from './Funnel3DSeriesDefaults.js';
import Funnel3DPoint from './Funnel3DPoint.js';
import H from '../../Core/Globals.js';
var noop = H.noop;
import Math3D from '../../Core/Math3D.js';
var perspective = Math3D.perspective;
import SeriesRegistry from '../../Core/Series/SeriesRegistry.js';
var Series = SeriesRegistry.series, ColumnSeries = SeriesRegistry.seriesTypes.column;
import U from '../../Core/Utilities.js';
var extend = U.extend, merge = U.merge, pick = U.pick, relativeLength = U.relativeLength;
/* *
 *
 *  Class
 *
 * */
/**
 * The funnel3d series type.
 *
 * @private
 * @class
 * @name Highcharts.seriesTypes.funnel3d
 * @augments seriesTypes.column
 * @requires highcharts-3d
 * @requires modules/cylinder
 * @requires modules/funnel3d
 */
var Funnel3DSeries = /** @class */ (function (_super) {
    __extends(Funnel3DSeries, _super);
    function Funnel3DSeries() {
        return _super !== null && _super.apply(this, arguments) || this;
    }
    /* *
     *
     *  Functions
     *
     * */
    /**
     * @private
     */
    Funnel3DSeries.prototype.alignDataLabel = function (point, _dataLabel, options) {
        var series = this, dlBoxRaw = point.dlBoxRaw, inverted = series.chart.inverted, below = point.plotY > pick(series.translatedThreshold, series.yAxis.len), inside = pick(options.inside, !!series.options.stacking), dlBox = {
            x: dlBoxRaw.x,
            y: dlBoxRaw.y,
            height: 0
        };
        options.align = pick(options.align, !inverted || inside ? 'center' : below ? 'right' : 'left');
        options.verticalAlign = pick(options.verticalAlign, inverted || inside ? 'middle' : below ? 'top' : 'bottom');
        if (options.verticalAlign !== 'top') {
            dlBox.y += dlBoxRaw.bottom /
                (options.verticalAlign === 'bottom' ? 1 : 2);
        }
        dlBox.width = series.getWidthAt(dlBox.y);
        if (series.options.reversed) {
            dlBox.width = dlBoxRaw.fullWidth - dlBox.width;
        }
        if (inside) {
            dlBox.x -= dlBox.width / 2;
        }
        else {
            // Swap for inside
            if (options.align === 'left') {
                options.align = 'right';
                dlBox.x -= dlBox.width * 1.5;
            }
            else if (options.align === 'right') {
                options.align = 'left';
                dlBox.x += dlBox.width / 2;
            }
            else {
                dlBox.x -= dlBox.width / 2;
            }
        }
        point.dlBox = dlBox;
        ColumnSeries.prototype.alignDataLabel.apply(series, arguments);
    };
    /**
     * Override default axis options with series required options for axes.
     * @private
     */
    Funnel3DSeries.prototype.bindAxes = function () {
        Series.prototype.bindAxes.apply(this, arguments);
        extend(this.xAxis.options, {
            gridLineWidth: 0,
            lineWidth: 0,
            title: void 0,
            tickPositions: []
        });
        merge(true, this.yAxis.options, {
            gridLineWidth: 0,
            title: void 0,
            labels: {
                enabled: false
            }
        });
    };
    /**
     * @private
     */
    Funnel3DSeries.prototype.translate = function () {
        Series.prototype.translate.apply(this, arguments);
        var series = this, chart = series.chart, options = series.options, reversed = options.reversed, ignoreHiddenPoint = options.ignoreHiddenPoint, plotWidth = chart.plotWidth, plotHeight = chart.plotHeight, center = options.center, centerX = relativeLength(center[0], plotWidth), centerY = relativeLength(center[1], plotHeight), width = relativeLength(options.width, plotWidth), height = relativeLength(options.height, plotHeight), neckWidth = relativeLength(options.neckWidth, plotWidth), neckHeight = relativeLength(options.neckHeight, plotHeight), neckY = (centerY - height / 2) + height - neckHeight, points = series.points;
        var sum = 0, cumulative = 0, // Start at top
        tempWidth, getWidthAt, fraction, tooltipPos, 
        //
        y1, y3, y5, 
        //
        h, shapeArgs; // @todo: Type it. It's an extended SVGAttributes.
        // Return the width at a specific y coordinate
        series.getWidthAt = getWidthAt = function (y) {
            var top = (centerY - height / 2);
            return (y > neckY || height === neckHeight) ?
                neckWidth :
                neckWidth + (width - neckWidth) *
                    (1 - (y - top) / (height - neckHeight));
        };
        // Expose
        series.center = [centerX, centerY, height];
        series.centerX = centerX;
        /*
            * Individual point coordinate naming:
            *
            *  _________centerX,y1________
            *  \                         /
            *   \                       /
            *    \                     /
            *     \                   /
            *      \                 /
            *        ___centerX,y3___
            *
            * Additional for the base of the neck:
            *
            *       |               |
            *       |               |
            *       |               |
            *        ___centerX,y5___
            */
        // get the total sum
        for (var _i = 0, points_1 = points; _i < points_1.length; _i++) {
            var point = points_1[_i];
            if (!ignoreHiddenPoint || point.visible !== false) {
                sum += point.y;
            }
        }
        for (var _a = 0, points_2 = points; _a < points_2.length; _a++) {
            var point = points_2[_a];
            // Set start and end positions
            y5 = null;
            fraction = sum ? point.y / sum : 0;
            y1 = centerY - height / 2 + cumulative * height;
            y3 = y1 + fraction * height;
            tempWidth = getWidthAt(y1);
            h = y3 - y1;
            shapeArgs = {
                // For fill setter
                gradientForSides: pick(point.options.gradientForSides, options.gradientForSides),
                x: centerX,
                y: y1,
                height: h,
                width: tempWidth,
                z: 1,
                top: {
                    width: tempWidth
                }
            };
            tempWidth = getWidthAt(y3);
            shapeArgs.bottom = {
                fraction: fraction,
                width: tempWidth
            };
            // The entire point is within the neck
            if (y1 >= neckY) {
                shapeArgs.isCylinder = true;
            }
            else if (y3 > neckY) {
                // The base of the neck
                y5 = y3;
                tempWidth = getWidthAt(neckY);
                y3 = neckY;
                shapeArgs.bottom.width = tempWidth;
                shapeArgs.middle = {
                    fraction: h ? (neckY - y1) / h : 0,
                    width: tempWidth
                };
            }
            if (reversed) {
                shapeArgs.y = y1 = centerY + height / 2 -
                    (cumulative + fraction) * height;
                if (shapeArgs.middle) {
                    shapeArgs.middle.fraction = 1 -
                        (h ? shapeArgs.middle.fraction : 0);
                }
                tempWidth = shapeArgs.width;
                shapeArgs.width = shapeArgs.bottom.width;
                shapeArgs.bottom.width = tempWidth;
            }
            point.shapeArgs = extend(point.shapeArgs, shapeArgs);
            // For tooltips and data labels context
            point.percentage = fraction * 100;
            point.plotX = centerX;
            if (reversed) {
                point.plotY = centerY + height / 2 -
                    (cumulative + fraction / 2) * height;
            }
            else {
                point.plotY = (y1 + (y5 || y3)) / 2;
            }
            // Placement of tooltips and data labels in 3D
            tooltipPos = perspective([{
                    x: centerX,
                    y: point.plotY,
                    z: reversed ?
                        -(width - getWidthAt(point.plotY)) / 2 :
                        -(getWidthAt(point.plotY)) / 2
                }], chart, true)[0];
            point.tooltipPos = [tooltipPos.x, tooltipPos.y];
            // Base to be used when alignment options are known
            point.dlBoxRaw = {
                x: centerX,
                width: getWidthAt(point.plotY),
                y: y1,
                bottom: shapeArgs.height || 0,
                fullWidth: width
            };
            if (!ignoreHiddenPoint || point.visible !== false) {
                cumulative += fraction;
            }
        }
    };
    /* *
     *
     *  Static Properties
     *
     * */
    Funnel3DSeries.compose = Funnel3DComposition.compose;
    Funnel3DSeries.defaultOptions = merge(ColumnSeries.defaultOptions, Funnel3DSeriesDefaults);
    return Funnel3DSeries;
}(ColumnSeries));
extend(Funnel3DSeries.prototype, {
    pointClass: Funnel3DPoint,
    translate3dShapes: noop
});
SeriesRegistry.registerSeriesType('funnel3d', Funnel3DSeries);
/* *
 *
 *  Default Export
 *
 * */
export default Funnel3DSeries;
