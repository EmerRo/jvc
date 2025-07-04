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
import AreaRangePoint from './AreaRangePoint.js';
import H from '../../Core/Globals.js';
var noop = H.noop;
import SeriesRegistry from '../../Core/Series/SeriesRegistry.js';
var _a = SeriesRegistry.seriesTypes, AreaSeries = _a.area, areaProto = _a.area.prototype, columnProto = _a.column.prototype;
import U from '../../Core/Utilities.js';
var addEvent = U.addEvent, defined = U.defined, extend = U.extend, isArray = U.isArray, isNumber = U.isNumber, pick = U.pick, merge = U.merge;
/* *
 *
 *  Constants
 *
 * */
/**
 * The area range series is a carteseian series with higher and lower values for
 * each point along an X axis, where the area between the values is shaded.
 *
 * @sample {highcharts} highcharts/demo/arearange/
 *         Area range chart
 * @sample {highstock} stock/demo/arearange/
 *         Area range chart
 *
 * @extends      plotOptions.area
 * @product      highcharts highstock
 * @excluding    stack, stacking
 * @requires     highcharts-more
 * @optionparent plotOptions.arearange
 *
 * @private
 */
var areaRangeSeriesOptions = {
    /**
     * @see [fillColor](#plotOptions.arearange.fillColor)
     * @see [fillOpacity](#plotOptions.arearange.fillOpacity)
     *
     * @apioption plotOptions.arearange.color
     */
    /**
     * @default   low
     * @apioption plotOptions.arearange.colorKey
     */
    /**
     * @see [color](#plotOptions.arearange.color)
     * @see [fillOpacity](#plotOptions.arearange.fillOpacity)
     *
     * @apioption plotOptions.arearange.fillColor
     */
    /**
     * @see [color](#plotOptions.arearange.color)
     * @see [fillColor](#plotOptions.arearange.fillColor)
     *
     * @default   {highcharts} 0.75
     * @default   {highstock} 0.75
     * @apioption plotOptions.arearange.fillOpacity
     */
    /**
     * Whether to apply a drop shadow to the graph line. Since 2.3 the
     * shadow can be an object configuration containing `color`, `offsetX`,
     * `offsetY`, `opacity` and `width`.
     *
     * @type      {boolean|Highcharts.ShadowOptionsObject}
     * @product   highcharts
     * @apioption plotOptions.arearange.shadow
     */
    /**
     * Pixel width of the arearange graph line.
     *
     * @since 2.3.0
     *
     * @private
     */
    lineWidth: 1,
    /**
     * @type {number|null}
     */
    threshold: null,
    tooltip: {
        pointFormat: '<span style="color:{series.color}">\u25CF</span> ' +
            '{series.name}: <b>{point.low}</b> - <b>{point.high}</b><br/>'
    },
    /**
     * Whether the whole area or just the line should respond to mouseover
     * tooltips and other mouse or touch events.
     *
     * @since 2.3.0
     *
     * @private
     */
    trackByArea: true,
    /**
     * Extended data labels for range series types. Range series data
     * labels use no `x` and `y` options. Instead, they have `xLow`,
     * `xHigh`, `yLow` and `yHigh` options to allow the higher and lower
     * data label sets individually.
     *
     * @declare Highcharts.SeriesAreaRangeDataLabelsOptionsObject
     * @exclude x, y
     * @since   2.3.0
     * @product highcharts highstock
     *
     * @private
     */
    dataLabels: {
        align: void 0,
        verticalAlign: void 0,
        /**
         * X offset of the lower data labels relative to the point value.
         *
         * @sample highcharts/plotoptions/arearange-datalabels/
         *         Data labels on range series
         * @sample highcharts/plotoptions/arearange-datalabels/
         *         Data labels on range series
         */
        xLow: 0,
        /**
         * X offset of the higher data labels relative to the point value.
         *
         * @sample highcharts/plotoptions/arearange-datalabels/
         *         Data labels on range series
         */
        xHigh: 0,
        /**
         * Y offset of the lower data labels relative to the point value.
         *
         * @sample highcharts/plotoptions/arearange-datalabels/
         *         Data labels on range series
         */
        yLow: 0,
        /**
         * Y offset of the higher data labels relative to the point value.
         *
         * @sample highcharts/plotoptions/arearange-datalabels/
         *         Data labels on range series
         */
        yHigh: 0
    }
};
/* *
 *
 *  Class
 *
 * */
/**
 * The AreaRange series type.
 *
 * @private
 * @class
 * @name Highcharts.seriesTypes.arearange
 *
 * @augments Highcharts.Series
 */
var AreaRangeSeries = /** @class */ (function (_super) {
    __extends(AreaRangeSeries, _super);
    function AreaRangeSeries() {
        return _super !== null && _super.apply(this, arguments) || this;
    }
    /* *
     *
     *  Functions
     *
     * */
    AreaRangeSeries.prototype.toYData = function (point) {
        return [point.low, point.high];
    };
    /**
     * Translate a point's plotHigh from the internal angle and radius measures
     * to true plotHigh coordinates. This is an addition of the toXY method
     * found in Polar.js, because it runs too early for arearanges to be
     * considered (#3419).
     * @private
     */
    AreaRangeSeries.prototype.highToXY = function (point) {
        // Find the polar plotX and plotY
        var chart = this.chart, xy = this.xAxis.postTranslate(point.rectPlotX || 0, this.yAxis.len - (point.plotHigh || 0));
        point.plotHighX = xy.x - chart.plotLeft;
        point.plotHigh = xy.y - chart.plotTop;
        point.plotLowX = point.plotX;
    };
    /**
     * Extend the line series' getSegmentPath method by applying the segment
     * path to both lower and higher values of the range.
     * @private
     */
    AreaRangeSeries.prototype.getGraphPath = function (points) {
        var highPoints = [], highAreaPoints = [], getGraphPath = areaProto.getGraphPath, options = this.options, polar = this.chart.polar, connectEnds = polar && options.connectEnds !== false, connectNulls = options.connectNulls;
        var i, point, pointShim, step = options.step;
        points = points || this.points;
        // Create the top line and the top part of the area fill. The area fill
        // compensates for null points by drawing down to the lower graph,
        // moving across the null gap and starting again at the lower graph.
        i = points.length;
        while (i--) {
            point = points[i];
            // Support for polar
            var highAreaPoint = polar ? {
                plotX: point.rectPlotX,
                plotY: point.yBottom,
                doCurve: false // #5186, gaps in areasplinerange fill
            } : {
                plotX: point.plotX,
                plotY: point.plotY,
                doCurve: false // #5186, gaps in areasplinerange fill
            };
            if (!point.isNull &&
                !connectEnds &&
                !connectNulls &&
                (!points[i + 1] || points[i + 1].isNull)) {
                highAreaPoints.push(highAreaPoint);
            }
            pointShim = {
                polarPlotY: point.polarPlotY,
                rectPlotX: point.rectPlotX,
                yBottom: point.yBottom,
                // `plotHighX` is for polar charts
                plotX: pick(point.plotHighX, point.plotX),
                plotY: point.plotHigh,
                isNull: point.isNull
            };
            highAreaPoints.push(pointShim);
            highPoints.push(pointShim);
            if (!point.isNull &&
                !connectEnds &&
                !connectNulls &&
                (!points[i - 1] || points[i - 1].isNull)) {
                highAreaPoints.push(highAreaPoint);
            }
        }
        // Get the paths
        var lowerPath = getGraphPath.call(this, points);
        if (step) {
            if (step === true) {
                step = 'left';
            }
            options.step = {
                left: 'right',
                center: 'center',
                right: 'left'
            }[step]; // Swap for reading in getGraphPath
        }
        var higherPath = getGraphPath.call(this, highPoints);
        var higherAreaPath = getGraphPath.call(this, highAreaPoints);
        options.step = step;
        // Create a line on both top and bottom of the range
        var linePath = [].concat(lowerPath, higherPath);
        // For the area path, we need to change the 'move' statement into
        // 'lineTo'
        if (!this.chart.polar &&
            higherAreaPath[0] &&
            higherAreaPath[0][0] === 'M') {
            // This probably doesn't work for spline
            higherAreaPath[0] = [
                'L',
                higherAreaPath[0][1],
                higherAreaPath[0][2]
            ];
        }
        this.graphPath = linePath;
        this.areaPath = lowerPath.concat(higherAreaPath);
        // Prepare for sideways animation
        linePath.isArea = true;
        linePath.xMap = lowerPath.xMap;
        this.areaPath.xMap = lowerPath.xMap;
        return linePath;
    };
    /**
     * Extend the basic drawDataLabels method by running it for both lower and
     * higher values.
     * @private
     */
    AreaRangeSeries.prototype.drawDataLabels = function () {
        var _a, _b;
        var data = this.points, length = data.length, originalDataLabels = [], dataLabelOptions = this.options.dataLabels, inverted = this.chart.inverted;
        var i, point, up, upperDataLabelOptions, lowerDataLabelOptions;
        if (dataLabelOptions) {
            // Split into upper and lower options. If data labels is an array,
            // the first element is the upper label, the second is the lower.
            //
            // TODO: We want to change this and allow multiple labels for both
            // upper and lower values in the future - introducing some options
            // for which point value to use as Y for the dataLabel, so that this
            // could be handled in Series.drawDataLabels. This would also
            // improve performance since we now have to loop over all the points
            // multiple times to work around the data label logic.
            if (isArray(dataLabelOptions)) {
                upperDataLabelOptions = dataLabelOptions[0] || {
                    enabled: false
                };
                lowerDataLabelOptions = dataLabelOptions[1] || {
                    enabled: false
                };
            }
            else {
                // Make copies
                upperDataLabelOptions = extend({}, dataLabelOptions);
                upperDataLabelOptions.x = dataLabelOptions.xHigh;
                upperDataLabelOptions.y = dataLabelOptions.yHigh;
                lowerDataLabelOptions = extend({}, dataLabelOptions);
                lowerDataLabelOptions.x = dataLabelOptions.xLow;
                lowerDataLabelOptions.y = dataLabelOptions.yLow;
            }
            // Draw upper labels
            if (upperDataLabelOptions.enabled || ((_a = this.hasDataLabels) === null || _a === void 0 ? void 0 : _a.call(this))) {
                // Set preliminary values for plotY and dataLabel
                // and draw the upper labels
                i = length;
                while (i--) {
                    point = data[i];
                    if (point) {
                        var _c = point.plotHigh, plotHigh = _c === void 0 ? 0 : _c, _d = point.plotLow, plotLow = _d === void 0 ? 0 : _d;
                        up = upperDataLabelOptions.inside ?
                            plotHigh < plotLow :
                            plotHigh > plotLow;
                        point.y = point.high;
                        point._plotY = point.plotY;
                        point.plotY = plotHigh;
                        // Store original data labels and set preliminary label
                        // objects to be picked up in the uber method
                        originalDataLabels[i] = point.dataLabel;
                        point.dataLabel = point.dataLabelUpper;
                        // Set the default offset
                        point.below = up;
                        if (inverted) {
                            if (!upperDataLabelOptions.align) {
                                upperDataLabelOptions.align = up ?
                                    'right' : 'left';
                            }
                        }
                        else {
                            if (!upperDataLabelOptions.verticalAlign) {
                                upperDataLabelOptions.verticalAlign = up ?
                                    'top' :
                                    'bottom';
                            }
                        }
                    }
                }
                this.options.dataLabels = upperDataLabelOptions;
                if (areaProto.drawDataLabels) {
                    // #1209:
                    areaProto.drawDataLabels.apply(this, arguments);
                }
                // Reset state after the upper labels were created. Move
                // it to point.dataLabelUpper and reassign the originals.
                // We do this here to support not drawing a lower label.
                i = length;
                while (i--) {
                    point = data[i];
                    if (point) {
                        point.dataLabelUpper = point.dataLabel;
                        point.dataLabel = originalDataLabels[i];
                        delete point.dataLabels;
                        point.y = point.low;
                        point.plotY = point._plotY;
                    }
                }
            }
            // Draw lower labels
            if (lowerDataLabelOptions.enabled || ((_b = this.hasDataLabels) === null || _b === void 0 ? void 0 : _b.call(this))) {
                i = length;
                while (i--) {
                    point = data[i];
                    if (point) {
                        var _e = point.plotHigh, plotHigh = _e === void 0 ? 0 : _e, _f = point.plotLow, plotLow = _f === void 0 ? 0 : _f;
                        up = lowerDataLabelOptions.inside ?
                            plotHigh < plotLow :
                            plotHigh > plotLow;
                        // Set the default offset
                        point.below = !up;
                        if (inverted) {
                            if (!lowerDataLabelOptions.align) {
                                lowerDataLabelOptions.align = up ?
                                    'left' : 'right';
                            }
                        }
                        else {
                            if (!lowerDataLabelOptions.verticalAlign) {
                                lowerDataLabelOptions.verticalAlign = up ?
                                    'bottom' :
                                    'top';
                            }
                        }
                    }
                }
                this.options.dataLabels = lowerDataLabelOptions;
                if (areaProto.drawDataLabels) {
                    areaProto.drawDataLabels.apply(this, arguments);
                }
            }
            // Merge upper and lower into point.dataLabels for later destroying
            if (upperDataLabelOptions.enabled) {
                i = length;
                while (i--) {
                    point = data[i];
                    if (point) {
                        point.dataLabels = [
                            point.dataLabelUpper,
                            point.dataLabel
                        ].filter(function (label) {
                            return !!label;
                        });
                    }
                }
            }
            // Reset options
            this.options.dataLabels = dataLabelOptions;
        }
    };
    AreaRangeSeries.prototype.alignDataLabel = function () {
        columnProto.alignDataLabel.apply(this, arguments);
    };
    AreaRangeSeries.prototype.modifyMarkerSettings = function () {
        var series = this, originalMarkerSettings = {
            marker: series.options.marker,
            symbol: series.symbol
        };
        if (series.options.lowMarker) {
            var _a = series.options, marker = _a.marker, lowMarker = _a.lowMarker;
            series.options.marker = merge(marker, lowMarker);
            if (lowMarker.symbol) {
                series.symbol = lowMarker.symbol;
            }
        }
        return originalMarkerSettings;
    };
    AreaRangeSeries.prototype.restoreMarkerSettings = function (originalSettings) {
        var series = this;
        series.options.marker = originalSettings.marker;
        series.symbol = originalSettings.symbol;
    };
    AreaRangeSeries.prototype.drawPoints = function () {
        var series = this, pointLength = series.points.length;
        var i, point;
        var originalSettings = series.modifyMarkerSettings();
        // Draw bottom points
        areaProto.drawPoints.apply(series, arguments);
        // Restore previous state
        series.restoreMarkerSettings(originalSettings);
        // Prepare drawing top points
        i = 0;
        while (i < pointLength) {
            point = series.points[i];
            /**
             * Array for multiple SVG graphics representing the point in the
             * chart. Only used in cases where the point can not be represented
             * by a single graphic.
             *
             * @see Highcharts.Point#graphic
             *
             * @name Highcharts.Point#graphics
             * @type {Array<Highcharts.SVGElement>|undefined}
             */
            point.graphics = point.graphics || [];
            // Save original props to be overridden by temporary props for top
            // points
            point.origProps = {
                plotY: point.plotY,
                plotX: point.plotX,
                isInside: point.isInside,
                negative: point.negative,
                zone: point.zone,
                y: point.y
            };
            if (point.graphic || point.graphics[0]) {
                point.graphics[0] = point.graphic;
            }
            point.graphic = point.graphics[1];
            point.plotY = point.plotHigh;
            if (defined(point.plotHighX)) {
                point.plotX = point.plotHighX;
            }
            point.y = pick(point.high, point.origProps.y); // #15523
            point.negative = point.y < (series.options.threshold || 0);
            if (series.zones.length) {
                point.zone = point.getZone();
            }
            if (!series.chart.polar) {
                point.isInside = point.isTopInside = (typeof point.plotY !== 'undefined' &&
                    point.plotY >= 0 &&
                    point.plotY <= series.yAxis.len && // #3519
                    point.plotX >= 0 &&
                    point.plotX <= series.xAxis.len);
            }
            i++;
        }
        // Draw top points
        areaProto.drawPoints.apply(series, arguments);
        // Reset top points preliminary modifications
        i = 0;
        while (i < pointLength) {
            point = series.points[i];
            point.graphics = point.graphics || [];
            if (point.graphic || point.graphics[1]) {
                point.graphics[1] = point.graphic;
            }
            point.graphic = point.graphics[0];
            if (point.origProps) {
                extend(point, point.origProps);
                delete point.origProps;
            }
            i++;
        }
    };
    AreaRangeSeries.prototype.hasMarkerChanged = function (options, oldOptions) {
        var lowMarker = options.lowMarker, oldMarker = oldOptions.lowMarker || {};
        return (lowMarker && (lowMarker.enabled === false ||
            oldMarker.symbol !== lowMarker.symbol || // #10870, #15946
            oldMarker.height !== lowMarker.height || // #16274
            oldMarker.width !== lowMarker.width // #16274
        )) || _super.prototype.hasMarkerChanged.call(this, options, oldOptions);
    };
    /**
     *
     *  Static Properties
     *
     */
    AreaRangeSeries.defaultOptions = merge(AreaSeries.defaultOptions, areaRangeSeriesOptions);
    return AreaRangeSeries;
}(AreaSeries));
addEvent(AreaRangeSeries, 'afterTranslate', function () {
    // Set plotLow and plotHigh
    var _this = this;
    // Rules out lollipop, but lollipop should not inherit range series in the
    // first place
    if (this.pointArrayMap.join(',') === 'low,high') {
        this.points.forEach(function (point) {
            var high = point.high, plotY = point.plotY;
            if (point.isNull) {
                point.plotY = void 0;
            }
            else {
                point.plotLow = plotY;
                // Calculate plotHigh value based on each yAxis scale (#15752)
                point.plotHigh = isNumber(high) ? _this.yAxis.translate(_this.dataModify ?
                    _this.dataModify.modifyValue(high) : high, false, true, void 0, true) : void 0;
                if (_this.dataModify) {
                    point.yBottom = point.plotHigh;
                }
            }
        });
    }
}, { order: 0 });
addEvent(AreaRangeSeries, 'afterTranslate', function () {
    var _this = this;
    this.points.forEach(function (point) {
        // Postprocessing after the PolarComposition's afterTranslate
        if (_this.chart.polar) {
            _this.highToXY(point);
            point.plotLow = point.plotY;
            point.tooltipPos = [
                ((point.plotHighX || 0) + (point.plotLowX || 0)) / 2,
                ((point.plotHigh || 0) + (point.plotLow || 0)) / 2
            ];
            // Put the tooltip in the middle of the range
        }
        else {
            var tooltipPos = point.pos(false, point.plotLow), posHigh = point.pos(false, point.plotHigh);
            if (tooltipPos && posHigh) {
                tooltipPos[0] = (tooltipPos[0] + posHigh[0]) / 2;
                tooltipPos[1] = (tooltipPos[1] + posHigh[1]) / 2;
            }
            point.tooltipPos = tooltipPos;
        }
    });
}, { order: 3 });
extend(AreaRangeSeries.prototype, {
    deferTranslatePolar: true,
    pointArrayMap: ['low', 'high'],
    pointClass: AreaRangePoint,
    pointValKey: 'low',
    setStackedPoints: noop
});
SeriesRegistry.registerSeriesType('arearange', AreaRangeSeries);
/* *
 *
 *  Default Export
 *
 * */
export default AreaRangeSeries;
