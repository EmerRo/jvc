/* *
 *
 *  (c) 2009-2025 Highsoft AS
 *
 *  License: www.highcharts.com/license
 *
 *  !!!!!!! SOURCE GETS TRANSPILED BY TYPESCRIPT. EDIT TS FILE ONLY. !!!!!!!
 *
 *  Authors:
 *  - Sophie Bremer
 *
 * */
'use strict';
import FormulaProcessor from '../FormulaProcessor.js';
var getArgumentsValues = FormulaProcessor.getArgumentsValues;
/* *
 *
 *  Functions
 *
 * */
/**
 * Processor for the `MAX(...values)` implementation. Calculates the largest
 * of the given values that are numbers.
 *
 * @private
 * @function Formula.processorFunctions.MAX
 *
 * @param {Highcharts.FormulaArguments} args
 * Arguments to process.
 *
 * @param {Highcharts.DataTable} [table]
 * Table to use for references and ranges.
 *
 * @return {number}
 * Result value of the process.
 */
function MAX(args, table) {
    var values = getArgumentsValues(args, table);
    var result = Number.NEGATIVE_INFINITY;
    for (var i = 0, iEnd = values.length, value = void 0; i < iEnd; ++i) {
        value = values[i];
        switch (typeof value) {
            case 'number':
                if (value > result) {
                    result = value;
                }
                break;
            case 'object':
                value = MAX(value);
                if (value > result) {
                    result = value;
                }
                break;
        }
    }
    return isFinite(result) ? result : 0;
}
/* *
 *
 *  Registry
 *
 * */
FormulaProcessor.registerProcessorFunction('MAX', MAX);
/* *
 *
 *  Default Export
 *
 * */
export default MAX;
