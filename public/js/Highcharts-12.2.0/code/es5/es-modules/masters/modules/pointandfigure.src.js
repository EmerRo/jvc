/**
 * @license Highstock JS v@product.version@ (@product.date@)
 * @module highcharts/modules/pointandfigure
 * @requires highcharts
 * @requires highcharts/modules/stock
 *
 * Point and figure series type for Highcharts Stock
 *
 * (c) 2010-2025 Kamil Musialowski
 *
 * License: www.highcharts.com/license
 */
'use strict';
import Highcharts from '../../Core/Globals.js';
import PointAndFigureSeries from '../../Series/PointAndFigure/PointAndFigureSeries.js';
var G = Highcharts;
PointAndFigureSeries.compose(G.Renderer);
export default Highcharts;
