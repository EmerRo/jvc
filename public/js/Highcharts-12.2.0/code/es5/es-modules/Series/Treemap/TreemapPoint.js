/* *
 *
 *  (c) 2014-2025 Highsoft AS
 *
 *  Authors: Jon Arild Nygard / Oystein Moseng
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
import DPU from '../DrawPointUtilities.js';
import SeriesRegistry from '../../Core/Series/SeriesRegistry.js';
var _a = SeriesRegistry.seriesTypes, PiePoint = _a.pie.prototype.pointClass, ScatterPoint = _a.scatter.prototype.pointClass;
import U from '../../Core/Utilities.js';
var extend = U.extend, isNumber = U.isNumber, pick = U.pick;
/* *
 *
 *  Class
 *
 * */
var TreemapPoint = /** @class */ (function (_super) {
    __extends(TreemapPoint, _super);
    function TreemapPoint() {
        /* *
         *
         *  Properties
         *
         * */
        var _this = _super !== null && _super.apply(this, arguments) || this;
        _this.groupedPointsAmount = 0;
        _this.shapeType = 'rect';
        return _this;
    }
    /* *
     *
     *  Functions
     *
     * */
    TreemapPoint.prototype.draw = function (params) {
        DPU.draw(this, params);
    };
    TreemapPoint.prototype.getClassName = function () {
        var series = this.series, options = series.options;
        var className = _super.prototype.getClassName.call(this);
        // Above the current level
        if (this.node.level <= series.nodeMap[series.rootNode].level &&
            this.node.children.length) {
            className += ' highcharts-above-level';
        }
        else if (!this.node.isGroup &&
            !this.node.isLeaf &&
            !series.nodeMap[series.rootNode].isGroup &&
            !pick(options.interactByLeaf, !options.allowTraversingTree)) {
            className += ' highcharts-internal-node-interactive';
        }
        else if (!this.node.isGroup &&
            !this.node.isLeaf &&
            !series.nodeMap[series.rootNode].isGroup) {
            className += ' highcharts-internal-node';
        }
        return className;
    };
    /**
     * A tree point is valid if it has han id too, assume it may be a parent
     * item.
     *
     * @private
     * @function Highcharts.Point#isValid
     */
    TreemapPoint.prototype.isValid = function () {
        return Boolean(this.id || isNumber(this.value));
    };
    TreemapPoint.prototype.setState = function (state) {
        _super.prototype.setState.apply(this, arguments);
        // Graphic does not exist when point is not visible.
        if (this.graphic) {
            this.graphic.attr({
                zIndex: state === 'hover' ? 1 : 0
            });
        }
    };
    TreemapPoint.prototype.shouldDraw = function () {
        return isNumber(this.plotY) && this.y !== null;
    };
    return TreemapPoint;
}(ScatterPoint));
extend(TreemapPoint.prototype, {
    setVisible: PiePoint.prototype.setVisible
});
/* *
 *
 *  Default Export
 *
 * */
export default TreemapPoint;
