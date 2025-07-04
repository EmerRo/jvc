/**
 * @license Highcharts JS v@product.version@ (@product.date@)
 * @module highcharts/modules/draggable-points
 * @requires highcharts
 *
 * (c) 2009-2025 Torstein Honsi
 *
 * License: www.highcharts.com/license
 */
'use strict';
import Highcharts from '../../Core/Globals.js';
import DraggablePoints from '../../Extensions/DraggablePoints/DraggablePoints.js';
var G = Highcharts;
DraggablePoints.compose(G.Chart, G.Series);
export default Highcharts;
