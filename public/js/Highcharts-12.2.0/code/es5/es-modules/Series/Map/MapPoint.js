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
var __assign = (this && this.__assign) || function () {
    __assign = Object.assign || function(t) {
        for (var s, i = 1, n = arguments.length; i < n; i++) {
            s = arguments[i];
            for (var p in s) if (Object.prototype.hasOwnProperty.call(s, p))
                t[p] = s[p];
        }
        return t;
    };
    return __assign.apply(this, arguments);
};
import ColorMapComposition from '../ColorMapComposition.js';
import MU from '../../Maps/MapUtilities.js';
var boundsFromPath = MU.boundsFromPath;
import SeriesRegistry from '../../Core/Series/SeriesRegistry.js';
var ScatterPoint = SeriesRegistry.seriesTypes.scatter.prototype.pointClass;
import U from '../../Core/Utilities.js';
var extend = U.extend, isNumber = U.isNumber, pick = U.pick;
/* *
 *
 *  Class
 *
 * */
var MapPoint = /** @class */ (function (_super) {
    __extends(MapPoint, _super);
    function MapPoint() {
        return _super !== null && _super.apply(this, arguments) || this;
    }
    /* *
     *
     *  Static Functions
     *
     * */
    /**
     * Get the projected path based on the geometry. May also be called on
     * mapData options (not point instances), hence static.
     * @private
     */
    MapPoint.getProjectedPath = function (point, projection) {
        if (!point.projectedPath) {
            if (projection && point.geometry) {
                // Always true when given GeoJSON coordinates
                projection.hasCoordinates = true;
                point.projectedPath = projection.path(point.geometry);
                // SVG path given directly in point options
            }
            else {
                point.projectedPath = point.path;
            }
        }
        return point.projectedPath || [];
    };
    /* *
     *
     *  Functions
     *
     * */
    /**
     * Extend the Point object to split paths.
     * @private
     */
    MapPoint.prototype.applyOptions = function (options, x) {
        var _a;
        var series = this.series, point = _super.prototype.applyOptions.call(this, options, x), joinBy = series.joinBy;
        if (series.mapData && series.mapMap) {
            var joinKey = joinBy[1], mapKey = _super.prototype.getNestedProperty.call(this, joinKey), mapPoint = typeof mapKey !== 'undefined' &&
                series.mapMap[mapKey];
            if (mapPoint) {
                // Copy over properties; #20231 prioritize point.name
                extend(point, __assign(__assign({}, mapPoint), { name: (_a = point.name) !== null && _a !== void 0 ? _a : mapPoint.name }));
            }
            else if (series.pointArrayMap.indexOf('value') !== -1) {
                point.value = point.value || null;
            }
        }
        return point;
    };
    /**
     * Get the bounds in terms of projected units
     * @private
     */
    MapPoint.prototype.getProjectedBounds = function (projection) {
        var path = MapPoint.getProjectedPath(this, projection), bounds = boundsFromPath(path), properties = this.properties, mapView = this.series.chart.mapView;
        if (bounds) {
            // Cache point bounding box for use to position data labels, bubbles
            // etc
            var propMiddleLon = properties === null || properties === void 0 ? void 0 : properties['hc-middle-lon'], propMiddleLat = properties === null || properties === void 0 ? void 0 : properties['hc-middle-lat'];
            if (mapView && isNumber(propMiddleLon) && isNumber(propMiddleLat)) {
                var projectedPoint = projection.forward([propMiddleLon, propMiddleLat]);
                bounds.midX = projectedPoint[0];
                bounds.midY = projectedPoint[1];
            }
            else {
                var propMiddleX = properties === null || properties === void 0 ? void 0 : properties['hc-middle-x'], propMiddleY = properties === null || properties === void 0 ? void 0 : properties['hc-middle-y'];
                bounds.midX = (bounds.x1 + (bounds.x2 - bounds.x1) * pick(this.middleX, isNumber(propMiddleX) ? propMiddleX : 0.5));
                var middleYFraction = pick(this.middleY, isNumber(propMiddleY) ? propMiddleY : 0.5);
                // No geographic geometry, only path given => flip
                if (!this.geometry) {
                    middleYFraction = 1 - middleYFraction;
                }
                bounds.midY =
                    bounds.y2 - (bounds.y2 - bounds.y1) * middleYFraction;
            }
            return bounds;
        }
    };
    /**
     * Stop the fade-out
     * @private
     */
    MapPoint.prototype.onMouseOver = function (e) {
        U.clearTimeout(this.colorInterval);
        if (
        // Valid...
        (!this.isNull && this.visible) ||
            // ... or interact anyway
            this.series.options.nullInteraction) {
            _super.prototype.onMouseOver.call(this, e);
        }
        else {
            // #3401 Tooltip doesn't hide when hovering over null points
            this.series.onMouseOut();
        }
    };
    MapPoint.prototype.setVisible = function (vis) {
        var method = vis ? 'show' : 'hide';
        this.visible = this.options.visible = !!vis;
        // Show and hide associated elements
        if (this.dataLabel) {
            this.dataLabel[method]();
        }
        // For invisible map points, render them as null points rather than
        // fully removing them. Makes more sense for color axes with data
        // classes.
        if (this.graphic) {
            this.graphic.attr(this.series.pointAttribs(this));
        }
    };
    /**
     * Highmaps only. Zoom in on the point using the global animation.
     *
     * @sample maps/members/point-zoomto/
     *         Zoom to points from buttons
     *
     * @requires modules/map
     *
     * @function Highcharts.Point#zoomTo
     */
    MapPoint.prototype.zoomTo = function (animOptions) {
        var point = this, chart = point.series.chart, mapView = chart.mapView;
        var bounds = point.bounds;
        if (mapView && bounds) {
            var inset = isNumber(point.insetIndex) &&
                mapView.insets[point.insetIndex];
            if (inset) {
                // If in an inset, translate the bounds to pixels ...
                var px1 = inset.projectedUnitsToPixels({
                    x: bounds.x1,
                    y: bounds.y1
                }), px2 = inset.projectedUnitsToPixels({
                    x: bounds.x2,
                    y: bounds.y2
                }), 
                // ... then back to projected units in the main mapView
                proj1 = mapView.pixelsToProjectedUnits({
                    x: px1.x,
                    y: px1.y
                }), proj2 = mapView.pixelsToProjectedUnits({
                    x: px2.x,
                    y: px2.y
                });
                bounds = {
                    x1: proj1.x,
                    y1: proj1.y,
                    x2: proj2.x,
                    y2: proj2.y
                };
            }
            mapView.fitToBounds(bounds, void 0, false);
            point.series.isDirty = true;
            chart.redraw(animOptions);
        }
    };
    return MapPoint;
}(ScatterPoint));
extend(MapPoint.prototype, {
    dataLabelOnNull: ColorMapComposition.pointMembers.dataLabelOnNull,
    moveToTopOnHover: ColorMapComposition.pointMembers.moveToTopOnHover,
    isValid: ColorMapComposition.pointMembers.isValid
});
/* *
 *
 *  Default Export
 *
 * */
export default MapPoint;
