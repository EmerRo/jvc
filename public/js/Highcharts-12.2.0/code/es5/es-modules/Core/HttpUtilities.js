/* *
 *
 *  (c) 2010-2025 Christer Vasseng, Torstein Honsi
 *
 *  License: www.highcharts.com/license
 *
 *  !!!!!!! SOURCE GETS TRANSPILED BY TYPESCRIPT. EDIT TS FILE ONLY. !!!!!!!
 *
 * */
'use strict';
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
import G from '../Core/Globals.js';
var win = G.win;
import U from '../Core/Utilities.js';
var discardElement = U.discardElement, objectEach = U.objectEach;
/* *
 *
 *  Functions
 *
 * */
/**
 * Perform an Ajax call.
 *
 * @function Highcharts.ajax
 *
 * @param {Highcharts.AjaxSettingsObject} settings
 *        The Ajax settings to use.
 *
 * @return {false|undefined}
 *         Returns false, if error occurred.
 */
function ajax(settings) {
    var _a;
    var headers = {
        json: 'application/json',
        xml: 'application/xml',
        text: 'text/plain',
        octet: 'application/octet-stream'
    }, r = new XMLHttpRequest();
    /**
     * Private error handler.
     * @private
     * @param {XMLHttpRequest} xhr
     * Internal request object.
     * @param {string|Error} err
     * Occurred error.
     */
    function handleError(xhr, err) {
        if (settings.error) {
            settings.error(xhr, err);
        }
        else {
            // @todo Maybe emit a highcharts error event here
        }
    }
    if (!settings.url) {
        return false;
    }
    r.open((settings.type || 'get').toUpperCase(), settings.url, true);
    if (!((_a = settings.headers) === null || _a === void 0 ? void 0 : _a['Content-Type'])) {
        r.setRequestHeader('Content-Type', headers[settings.dataType || 'json'] || headers.text);
    }
    objectEach(settings.headers, function (val, key) {
        r.setRequestHeader(key, val);
    });
    if (settings.responseType) {
        r.responseType = settings.responseType;
    }
    // @todo lacking timeout handling
    r.onreadystatechange = function () {
        var _a;
        var res;
        if (r.readyState === 4) {
            if (r.status === 200) {
                if (settings.responseType !== 'blob') {
                    res = r.responseText;
                    if (settings.dataType === 'json') {
                        try {
                            res = JSON.parse(res);
                        }
                        catch (e) {
                            if (e instanceof Error) {
                                return handleError(r, e);
                            }
                        }
                    }
                }
                return (_a = settings.success) === null || _a === void 0 ? void 0 : _a.call(settings, res, r);
            }
            handleError(r, r.responseText);
        }
    };
    if (settings.data && typeof settings.data !== 'string') {
        settings.data = JSON.stringify(settings.data);
    }
    r.send(settings.data);
}
/**
 * Get a JSON resource over XHR, also supporting CORS without preflight.
 *
 * @function Highcharts.getJSON
 * @param {string} url
 *        The URL to load.
 * @param {Function} success
 *        The success callback. For error handling, use the `Highcharts.ajax`
 *        function instead.
 */
function getJSON(url, success) {
    HttpUtilities.ajax({
        url: url,
        success: success,
        dataType: 'json',
        headers: {
            // Override the Content-Type to avoid preflight problems with CORS
            // in the Highcharts demos
            'Content-Type': 'text/plain'
        }
    });
}
/**
 * The post utility
 *
 * @private
 * @function Highcharts.post
 *
 * @param {string} url
 * Post URL
 *
 * @param {Object} data
 * Post data
 *
 * @param {RequestInit} [fetchOptions]
 * Additional attributes for the post request
 */
/**
 *
 */
function post(url, data, fetchOptions) {
    var formData = new win.FormData();
    // Add the data
    objectEach(data, function (val, name) {
        formData.append(name, val);
    });
    formData.append('b64', 'true');
    var filename = data.filename, type = data.type;
    return win.fetch(url, __assign({ method: 'POST', body: formData }, fetchOptions)).then(function (res) {
        if (res.ok) {
            res.text().then(function (text) {
                var link = document.createElement('a');
                link.href = "data:".concat(type, ";base64,").concat(text);
                link.download = filename;
                link.click();
                discardElement(link);
            });
        }
    });
}
/* *
 *
 *  Default Export
 *
 * */
var HttpUtilities = {
    ajax: ajax,
    getJSON: getJSON,
    post: post
};
export default HttpUtilities;
/* *
 *
 *  API Declarations
 *
 * */
/**
 * @interface Highcharts.AjaxSettingsObject
 */ /**
* The payload to send.
*
* @name Highcharts.AjaxSettingsObject#data
* @type {string|Highcharts.Dictionary<any>|undefined}
*/ /**
* The data type expected.
* @name Highcharts.AjaxSettingsObject#dataType
* @type {"json"|"xml"|"text"|"octet"|undefined}
*/ /**
* Function to call on error.
* @name Highcharts.AjaxSettingsObject#error
* @type {Function|undefined}
*/ /**
* The headers; keyed on header name.
* @name Highcharts.AjaxSettingsObject#headers
* @type {Highcharts.Dictionary<string>|undefined}
*/ /**
* Function to call on success.
* @name Highcharts.AjaxSettingsObject#success
* @type {Function|undefined}
*/ /**
* The HTTP method to use. For example GET or POST.
* @name Highcharts.AjaxSettingsObject#type
* @type {string|undefined}
*/ /**
* The URL to call.
* @name Highcharts.AjaxSettingsObject#url
* @type {string}
*/
(''); // Keeps doclets above in JS file
