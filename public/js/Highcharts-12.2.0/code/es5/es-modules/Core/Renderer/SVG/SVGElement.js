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
import A from '../../Animation/AnimationUtilities.js';
var animate = A.animate, animObject = A.animObject, stop = A.stop;
import Color from '../../Color/Color.js';
import H from '../../Globals.js';
var deg2rad = H.deg2rad, doc = H.doc, svg = H.svg, SVG_NS = H.SVG_NS, win = H.win, isFirefox = H.isFirefox;
import U from '../../Utilities.js';
var addEvent = U.addEvent, attr = U.attr, createElement = U.createElement, crisp = U.crisp, css = U.css, defined = U.defined, erase = U.erase, extend = U.extend, fireEvent = U.fireEvent, getAlignFactor = U.getAlignFactor, isArray = U.isArray, isFunction = U.isFunction, isNumber = U.isNumber, isObject = U.isObject, isString = U.isString, merge = U.merge, objectEach = U.objectEach, pick = U.pick, pInt = U.pInt, pushUnique = U.pushUnique, replaceNested = U.replaceNested, syncTimeout = U.syncTimeout, uniqueKey = U.uniqueKey;
/* *
 *
 *  Class
 *
 * */
/* eslint-disable no-invalid-this, valid-jsdoc */
/**
 * The SVGElement prototype is a JavaScript wrapper for SVG elements used in the
 * rendering layer of Highcharts. Combined with the
 * {@link Highcharts.SVGRenderer}
 * object, these prototypes allow freeform annotation in the charts or even in
 * HTML pages without instanciating a chart. The SVGElement can also wrap HTML
 * labels, when `text` or `label` elements are created with the `useHTML`
 * parameter.
 *
 * The SVGElement instances are created through factory functions on the
 * {@link Highcharts.SVGRenderer}
 * object, like
 * {@link Highcharts.SVGRenderer#rect|rect},
 * {@link Highcharts.SVGRenderer#path|path},
 * {@link Highcharts.SVGRenderer#text|text},
 * {@link Highcharts.SVGRenderer#label|label},
 * {@link Highcharts.SVGRenderer#g|g}
 * and more.
 *
 * See [How to use the SVG Renderer](
 * https://www.highcharts.com/docs/advanced-chart-features/renderer) for a
 * comprehensive tutorial on how to draw SVG elements on a chart.
 *
 * @class
 * @name Highcharts.SVGElement
 */
var SVGElement = /** @class */ (function () {
    /**
     * Initialize the SVG element. This function only exists to make the
     * initialization process overridable. It should not be called directly.
     *
     * @function Highcharts.SVGElement#init
     *
     * @param {Highcharts.SVGRenderer} renderer
     * The SVGRenderer instance to initialize to.
     *
     * @param {string} nodeName
     * The SVG node name.
     */
    function SVGElement(renderer, nodeName) {
        this.onEvents = {};
        this.opacity = 1; // Default base for animation
        this.SVG_NS = SVG_NS;
        /**
         * The primary DOM node. Each `SVGElement` instance wraps a main DOM
         * node, but may also represent more nodes.
         *
         * @name Highcharts.SVGElement#element
         * @type {Highcharts.SVGDOMElement|Highcharts.HTMLDOMElement}
         */
        this.element = nodeName === 'span' || nodeName === 'body' ?
            createElement(nodeName) :
            doc.createElementNS(this.SVG_NS, nodeName);
        /**
         * The renderer that the SVGElement belongs to.
         *
         * @name Highcharts.SVGElement#renderer
         * @type {Highcharts.SVGRenderer}
         */
        this.renderer = renderer;
        this.styles = {};
        fireEvent(this, 'afterInit');
    }
    // @todo public zIndex?: number;
    /* *
     *
     *  Functions
     *
     * */
    /**
     * Get the current value of an attribute or pseudo attribute,
     * used mainly for animation. Called internally from
     * the {@link Highcharts.SVGRenderer#attr} function.
     *
     * @private
     * @function Highcharts.SVGElement#_defaultGetter
     *
     * @param {string} key
     *        Property key.
     *
     * @return {number|string}
     *         Property value.
     */
    SVGElement.prototype._defaultGetter = function (key) {
        var ret = pick(this[key + 'Value'], // Align getter
        this[key], this.element ? this.element.getAttribute(key) : null, 0);
        if (/^-?[\d\.]+$/.test(ret)) { // Is numerical
            ret = parseFloat(ret);
        }
        return ret;
    };
    /**
     * @private
     * @function Highcharts.SVGElement#_defaultSetter
     *
     * @param {string} value
     *
     * @param {string} key
     *
     * @param {Highcharts.SVGDOMElement} element
     *
     */
    SVGElement.prototype._defaultSetter = function (value, key, element) {
        element.setAttribute(key, value);
    };
    /**
     * Add the element to the DOM. All elements must be added this way.
     *
     * @sample highcharts/members/renderer-g
     *         Elements added to a group
     *
     * @function Highcharts.SVGElement#add
     *
     * @param {Highcharts.SVGElement} [parent]
     *        The parent item to add it to. If undefined, the element is added
     *        to the {@link Highcharts.SVGRenderer.box}.
     *
     * @return {Highcharts.SVGElement}
     *         Returns the SVGElement for chaining.
     */
    SVGElement.prototype.add = function (parent) {
        var renderer = this.renderer, element = this.element;
        var inserted;
        if (parent) {
            this.parentGroup = parent;
        }
        // Build formatted text
        if (typeof this.textStr !== 'undefined' &&
            this.element.nodeName === 'text' // Not for SVGLabel instances
        ) {
            renderer.buildText(this);
        }
        // Mark as added
        this.added = true;
        // If we're adding to renderer root, or other elements in the group
        // have a z index, we need to handle it
        if (!parent || parent.handleZ || this.zIndex) {
            inserted = this.zIndexSetter();
        }
        // If zIndex is not handled, append at the end
        if (!inserted) {
            (parent ?
                parent.element :
                renderer.box).appendChild(element);
        }
        // Fire an event for internal hooks
        if (this.onAdd) {
            this.onAdd();
        }
        return this;
    };
    /**
     * Add a class name to an element.
     *
     * @function Highcharts.SVGElement#addClass
     *
     * @param {string} className
     * The new class name to add.
     *
     * @param {boolean} [replace=false]
     * When true, the existing class name(s) will be overwritten with the new
     * one. When false, the new one is added.
     *
     * @return {Highcharts.SVGElement}
     * Return the SVG element for chainability.
     */
    SVGElement.prototype.addClass = function (className, replace) {
        var currentClassName = replace ? '' : (this.attr('class') || '');
        // Trim the string and remove duplicates
        className = (className || '')
            .split(/ /g)
            .reduce(function (newClassName, name) {
            if (currentClassName.indexOf(name) === -1) {
                newClassName.push(name);
            }
            return newClassName;
        }, (currentClassName ?
            [currentClassName] :
            []))
            .join(' ');
        if (className !== currentClassName) {
            this.attr('class', className);
        }
        return this;
    };
    /**
     * This method is executed in the end of `attr()`, after setting all
     * attributes in the hash. In can be used to efficiently consolidate
     * multiple attributes in one SVG property -- e.g., translate, rotate and
     * scale are merged in one "transform" attribute in the SVG node.
     *
     * @private
     * @function Highcharts.SVGElement#afterSetters
     */
    SVGElement.prototype.afterSetters = function () {
        // Update transform. Do this outside the loop to prevent redundant
        // updating for batch setting of attributes.
        if (this.doTransform) {
            this.updateTransform();
            this.doTransform = false;
        }
    };
    /**
     * Align the element relative to the chart or another box.
     *
     * @function Highcharts.SVGElement#align
     *
     * @param {Highcharts.AlignObject} [alignOptions]
     *        The alignment options. The function can be called without this
     *        parameter in order to re-align an element after the box has been
     *        updated.
     *
     * @param {boolean} [alignByTranslate]
     *        Align element by translation.
     *
     * @param {string|Highcharts.BBoxObject} [alignTo]
     *        The box to align to, needs a width and height. When the box is a
     *        string, it refers to an object in the Renderer. For example, when
     *        box is `spacingBox`, it refers to `Renderer.spacingBox` which
     *        holds `width`, `height`, `x` and `y` properties.
     *
     * @param {boolean} [redraw]
     *        Decide if SVGElement should be redrawn with new alignment or
     *        just change its attributes.
     *
     * @return {Highcharts.SVGElement} Returns the SVGElement for chaining.
     */
    SVGElement.prototype.align = function (alignOptions, alignByTranslate, alignTo, redraw) {
        if (redraw === void 0) { redraw = true; }
        var renderer = this.renderer, alignedObjects = renderer.alignedObjects, initialAlignment = Boolean(alignOptions);
        // First call on instanciate
        if (alignOptions) {
            this.alignOptions = alignOptions;
            this.alignByTranslate = alignByTranslate;
            this.alignTo = alignTo;
            // When called on resize, no arguments are supplied
        }
        else {
            alignOptions = this.alignOptions || {};
            alignByTranslate = this.alignByTranslate;
            alignTo = this.alignTo;
        }
        var alignToKey = !alignTo || isString(alignTo) ?
            alignTo || 'renderer' :
            void 0;
        // When aligned to a key, automatically re-align on redraws
        if (alignToKey) {
            // Prevent duplicates, like legendGroup after resize
            if (initialAlignment) {
                pushUnique(alignedObjects, this);
            }
            alignTo = void 0; // Do not use the box
        }
        var alignToBox = pick(alignTo, renderer[alignToKey], renderer), 
        // Default: left align
        x = (alignToBox.x || 0) + (alignOptions.x || 0) +
            ((alignToBox.width || 0) - (alignOptions.width || 0)) *
                getAlignFactor(alignOptions.align), 
        // Default: top align
        y = (alignToBox.y || 0) + (alignOptions.y || 0) +
            ((alignToBox.height || 0) - (alignOptions.height || 0)) *
                getAlignFactor(alignOptions.verticalAlign), attribs = {
            'text-align': alignOptions === null || alignOptions === void 0 ? void 0 : alignOptions.align
        };
        attribs[alignByTranslate ? 'translateX' : 'x'] = Math.round(x);
        attribs[alignByTranslate ? 'translateY' : 'y'] = Math.round(y);
        // Animate only if already placed
        if (redraw) {
            this[this.placed ? 'animate' : 'attr'](attribs);
            this.placed = true;
        }
        this.alignAttr = attribs;
        return this;
    };
    /**
     * @private
     * @function Highcharts.SVGElement#alignSetter
     * @param {"left"|"center"|"right"} value
     */
    SVGElement.prototype.alignSetter = function (value) {
        var convert = {
            left: 'start',
            center: 'middle',
            right: 'end'
        };
        if (convert[value]) {
            this.alignValue = value;
            this.element.setAttribute('text-anchor', convert[value]);
        }
    };
    /**
     * Animate to given attributes or CSS properties.
     *
     * @sample highcharts/members/element-on/
     *         Setting some attributes by animation
     *
     * @function Highcharts.SVGElement#animate
     *
     * @param {Highcharts.SVGAttributes} params
     *        SVG attributes or CSS to animate.
     *
     * @param {boolean|Partial<Highcharts.AnimationOptionsObject>} [options]
     *        Animation options.
     *
     * @param {Function} [complete]
     *        Function to perform at the end of animation.
     *
     * @return {Highcharts.SVGElement}
     *         Returns the SVGElement for chaining.
     */
    SVGElement.prototype.animate = function (params, options, complete) {
        var _this = this;
        var animOptions = animObject(pick(options, this.renderer.globalAnimation, true)), deferTime = animOptions.defer;
        // When the page is hidden save resources in the background by not
        // running animation at all (#9749).
        if (doc.hidden) {
            animOptions.duration = 0;
        }
        if (animOptions.duration !== 0) {
            // Allows using a callback with the global animation without
            // overwriting it
            if (complete) {
                animOptions.complete = complete;
            }
            // If defer option is defined delay the animation #12901
            syncTimeout(function () {
                if (_this.element) {
                    animate(_this, params, animOptions);
                }
            }, deferTime);
        }
        else {
            this.attr(params, void 0, complete || animOptions.complete);
            // Call the end step synchronously
            objectEach(params, function (val, prop) {
                if (animOptions.step) {
                    animOptions.step.call(this, val, { prop: prop, pos: 1, elem: this });
                }
            }, this);
        }
        return this;
    };
    /**
     * Apply a text outline through a custom CSS property, by copying the text
     * element and apply stroke to the copy. Used internally. Contrast checks at
     * [example](https://jsfiddle.net/highcharts/43soe9m1/2/).
     *
     * @example
     * // Specific color
     * text.css({
     *    textOutline: '1px black'
     * });
     * // Automatic contrast
     * text.css({
     *    color: '#000000', // black text
     *    textOutline: '1px contrast' // => white outline
     * });
     *
     * @private
     * @function Highcharts.SVGElement#applyTextOutline
     *
     * @param {string} textOutline
     *        A custom CSS `text-outline` setting, defined by `width color`.
     */
    SVGElement.prototype.applyTextOutline = function (textOutline) {
        var elem = this.element, hasContrast = textOutline.indexOf('contrast') !== -1, styles = {};
        // When the text shadow is set to contrast, use dark stroke for light
        // text and vice versa.
        if (hasContrast) {
            styles.textOutline = textOutline = textOutline.replace(/contrast/g, this.renderer.getContrast(elem.style.fill));
        }
        // Extract the stroke width and color
        var spacePos = textOutline.indexOf(' '), color = textOutline.substring(spacePos + 1);
        var strokeWidth = textOutline.substring(0, spacePos);
        if (strokeWidth && strokeWidth !== 'none' && H.svg) {
            this.fakeTS = true; // Fake text shadow
            // Since the stroke is applied on center of the actual outline, we
            // need to double it to get the correct stroke-width outside the
            // glyphs.
            strokeWidth = strokeWidth.replace(/(^[\d\.]+)(.*?)$/g, function (match, digit, unit) {
                return (2 * Number(digit)) + unit;
            });
            // Remove shadows from previous runs.
            this.removeTextOutline();
            var outline_1 = doc.createElementNS(SVG_NS, 'tspan');
            attr(outline_1, {
                'class': 'highcharts-text-outline',
                fill: color,
                stroke: color,
                'stroke-width': strokeWidth,
                'stroke-linejoin': 'round'
            });
            // For each of the tspans and text nodes, create a copy in the
            // outline.
            var parentElem = elem.querySelector('textPath') || elem;
            [].forEach.call(parentElem.childNodes, function (childNode) {
                var clone = childNode.cloneNode(true);
                if (clone.removeAttribute) {
                    ['fill', 'stroke', 'stroke-width', 'stroke'].forEach(function (prop) { return clone
                        .removeAttribute(prop); });
                }
                outline_1.appendChild(clone);
            });
            // Collect the sum of dy from all children, included nested ones
            var totalHeight_1 = 0;
            [].forEach.call(parentElem.querySelectorAll('text tspan'), function (element) {
                totalHeight_1 += Number(element.getAttribute('dy'));
            });
            // Insert an absolutely positioned break before the original text
            // to keep it in place
            var br = doc.createElementNS(SVG_NS, 'tspan');
            br.textContent = '\u200B';
            // Reset the position for the following text
            attr(br, {
                x: Number(elem.getAttribute('x')),
                dy: -totalHeight_1
            });
            // Insert the outline
            outline_1.appendChild(br);
            parentElem.insertBefore(outline_1, parentElem.firstChild);
        }
    };
    /**
     * @function Highcharts.SVGElement#attr
     * @param {string} key
     * @return {number|string}
     */ /**
    * Apply native and custom attributes to the SVG elements.
    *
    * In order to set the rotation center for rotation, set x and y to 0 and
    * use `translateX` and `translateY` attributes to position the element
    * instead.
    *
    * Attributes frequently used in Highcharts are `fill`, `stroke`,
    * `stroke-width`.
    *
    * @sample highcharts/members/renderer-rect/
    *         Setting some attributes
    *
    * @example
    * // Set multiple attributes
    * element.attr({
    *     stroke: 'red',
    *     fill: 'blue',
    *     x: 10,
    *     y: 10
    * });
    *
    * // Set a single attribute
    * element.attr('stroke', 'red');
    *
    * // Get an attribute
    * element.attr('stroke'); // => 'red'
    *
    * @function Highcharts.SVGElement#attr
    *
    * @param {string|Highcharts.SVGAttributes} [hash]
    *        The native and custom SVG attributes.
    *
    * @param {number|string|Highcharts.SVGPathArray} [val]
    *        If the type of the first argument is `string`, the second can be a
    *        value, which will serve as a single attribute setter. If the first
    *        argument is a string and the second is undefined, the function
    *        serves as a getter and the current value of the property is
    *        returned.
    *
    * @param {Function} [complete]
    *        A callback function to execute after setting the attributes. This
    *        makes the function compliant and interchangeable with the
    *        {@link SVGElement#animate} function.
    *
    * @param {boolean} [continueAnimation=true]
    *        Used internally when `.attr` is called as part of an animation
    *        step. Otherwise, calling `.attr` for an attribute will stop
    *        animation for that attribute.
    *
    * @return {Highcharts.SVGElement}
    *         If used as a setter, it returns the current
    *         {@link Highcharts.SVGElement} so the calls can be chained. If
    *         used as a getter, the current value of the attribute is returned.
    */
    SVGElement.prototype.attr = function (hash, val, complete, continueAnimation) {
        var element = this.element, symbolCustomAttribs = SVGElement.symbolCustomAttribs;
        var key, hasSetSymbolSize, ret = this, skipAttr, setter;
        // Single key-value pair
        if (typeof hash === 'string' && typeof val !== 'undefined') {
            key = hash;
            hash = {};
            hash[key] = val;
        }
        // Used as a getter: first argument is a string, second is undefined
        if (typeof hash === 'string') {
            ret = (this[hash + 'Getter'] ||
                this._defaultGetter).call(this, hash, element);
            // Setter
        }
        else {
            objectEach(hash, function eachAttribute(val, key) {
                skipAttr = false;
                // Unless .attr is from the animator update, stop current
                // running animation of this property
                if (!continueAnimation) {
                    stop(this, key);
                }
                // Special handling of symbol attributes
                if (this.symbolName &&
                    symbolCustomAttribs.indexOf(key) !== -1) {
                    if (!hasSetSymbolSize) {
                        this.symbolAttr(hash);
                        hasSetSymbolSize = true;
                    }
                    skipAttr = true;
                }
                if (this.rotation && (key === 'x' || key === 'y')) {
                    this.doTransform = true;
                }
                if (!skipAttr) {
                    setter = (this[key + 'Setter'] ||
                        this._defaultSetter);
                    setter.call(this, val, key, element);
                }
            }, this);
            this.afterSetters();
        }
        // In accordance with animate, run a complete callback
        if (complete) {
            complete.call(this);
        }
        return ret;
    };
    /**
     * Apply a clipping shape to this element.
     *
     * @function Highcharts.SVGElement#clip
     *
     * @param {SVGElement} [clipElem]
     *        The clipping shape. If skipped, the current clip is removed.
     *
     * @return {Highcharts.SVGElement}
     *         Returns the SVG element to allow chaining.
     */
    SVGElement.prototype.clip = function (clipElem) {
        if (clipElem && !clipElem.clipPath) {
            // Add a hyphen at the end to avoid confusion in testing indexes
            // -1 and -10, -11 etc (#6550)
            var id = uniqueKey() + '-', clipPath = this.renderer.createElement('clipPath')
                .attr({ id: id })
                .add(this.renderer.defs);
            extend(clipElem, { clipPath: clipPath, id: id, count: 0 });
            clipElem.add(clipPath);
        }
        return this.attr('clip-path', clipElem ?
            "url(".concat(this.renderer.url, "#").concat(clipElem.id, ")") :
            'none');
    };
    /**
     * Calculate the coordinates needed for drawing a rectangle crisply and
     * return the calculated attributes.
     *
     * @function Highcharts.SVGElement#crisp
     *
     * @param {Highcharts.RectangleObject} rect
     * Rectangle to crisp.
     *
     * @param {number} [strokeWidth]
     * The stroke width to consider when computing crisp positioning. It can
     * also be set directly on the rect parameter.
     *
     * @return {Highcharts.RectangleObject}
     * The modified rectangle arguments.
     */
    SVGElement.prototype.crisp = function (rect, strokeWidth) {
        // Math.round because strokeWidth can sometimes have roundoff errors
        strokeWidth = Math.round(strokeWidth || rect.strokeWidth || 0);
        var x1 = rect.x || this.x || 0, y1 = rect.y || this.y || 0, x2 = (rect.width || this.width || 0) + x1, y2 = (rect.height || this.height || 0) + y1, 
        // Find all the rounded coordinates for corners
        x = crisp(x1, strokeWidth), y = crisp(y1, strokeWidth), x2Crisp = crisp(x2, strokeWidth), y2Crisp = crisp(y2, strokeWidth);
        extend(rect, {
            x: x,
            y: y,
            width: x2Crisp - x,
            height: y2Crisp - y
        });
        if (defined(rect.strokeWidth)) {
            rect.strokeWidth = strokeWidth;
        }
        return rect;
    };
    /**
     * Build and apply an SVG gradient out of a common JavaScript configuration
     * object. This function is called from the attribute setters. An event
     * hook is added for supporting other complex color types.
     *
     * @private
     * @function Highcharts.SVGElement#complexColor
     *
     * @param {Highcharts.GradientColorObject|Highcharts.PatternObject} colorOptions
     * The gradient or pattern options structure.
     *
     * @param {string} prop
     * The property to apply, can either be `fill` or `stroke`.
     *
     * @param {Highcharts.SVGDOMElement} elem
     * SVG element to apply the gradient on.
     */
    SVGElement.prototype.complexColor = function (colorOptions, prop, elem) {
        var renderer = this.renderer;
        var colorObject, gradName, gradAttr, radAttr, gradients, stops, stopColor, stopOpacity, radialReference, id, key = [], value;
        fireEvent(this.renderer, 'complexColor', {
            args: arguments
        }, function () {
            // Apply linear or radial gradients
            if (colorOptions.radialGradient) {
                gradName = 'radialGradient';
            }
            else if (colorOptions.linearGradient) {
                gradName = 'linearGradient';
            }
            if (gradName) {
                gradAttr = colorOptions[gradName];
                gradients = renderer.gradients;
                stops = colorOptions.stops;
                radialReference = elem.radialReference;
                // Keep < 2.2 compatibility
                if (isArray(gradAttr)) {
                    colorOptions[gradName] = gradAttr = {
                        x1: gradAttr[0],
                        y1: gradAttr[1],
                        x2: gradAttr[2],
                        y2: gradAttr[3],
                        gradientUnits: 'userSpaceOnUse'
                    };
                }
                // Correct the radial gradient for the radial reference system
                if (gradName === 'radialGradient' &&
                    radialReference &&
                    !defined(gradAttr.gradientUnits)) {
                    // Save the radial attributes for updating
                    radAttr = gradAttr;
                    gradAttr = merge(gradAttr, renderer.getRadialAttr(radialReference, radAttr), { gradientUnits: 'userSpaceOnUse' });
                }
                // Build the unique key to detect whether we need to create a
                // new element (#1282)
                objectEach(gradAttr, function (value, n) {
                    if (n !== 'id') {
                        key.push(n, value);
                    }
                });
                objectEach(stops, function (val) {
                    key.push(val);
                });
                key = key.join(',');
                // Check if a gradient object with the same config object is
                // created within this renderer
                if (gradients[key]) {
                    id = gradients[key].attr('id');
                }
                else {
                    // Set the id and create the element
                    gradAttr.id = id = uniqueKey();
                    var gradientObject_1 = gradients[key] =
                        renderer.createElement(gradName)
                            .attr(gradAttr)
                            .add(renderer.defs);
                    gradientObject_1.radAttr = radAttr;
                    // The gradient needs to keep a list of stops to be able to
                    // destroy them
                    gradientObject_1.stops = [];
                    stops.forEach(function (stop) {
                        if (stop[1].indexOf('rgba') === 0) {
                            colorObject = Color.parse(stop[1]);
                            stopColor = colorObject.get('rgb');
                            stopOpacity = colorObject.get('a');
                        }
                        else {
                            stopColor = stop[1];
                            stopOpacity = 1;
                        }
                        var stopObject = renderer.createElement('stop').attr({
                            offset: stop[0],
                            'stop-color': stopColor,
                            'stop-opacity': stopOpacity
                        }).add(gradientObject_1);
                        // Add the stop element to the gradient
                        gradientObject_1.stops.push(stopObject);
                    });
                }
                // Set the reference to the gradient object
                value = 'url(' + renderer.url + '#' + id + ')';
                elem.setAttribute(prop, value);
                elem.gradient = key;
                // Allow the color to be concatenated into tooltips formatters
                // etc. (#2995)
                colorOptions.toString = function () {
                    return value;
                };
            }
        });
    };
    /**
     * Set styles for the element. In addition to CSS styles supported by
     * native SVG and HTML elements, there are also some custom made for
     * Highcharts, like `width`, `ellipsis` and `textOverflow` for SVG text
     * elements.
     *
     * @sample highcharts/members/renderer-text-on-chart/
     *         Styled text
     *
     * @function Highcharts.SVGElement#css
     *
     * @param {Highcharts.CSSObject} styles
     *        The new CSS styles.
     *
     * @return {Highcharts.SVGElement}
     *         Return the SVG element for chaining.
     */
    SVGElement.prototype.css = function (styles) {
        var oldStyles = this.styles, newStyles = {}, elem = this.element;
        var textWidth, hasNew = !oldStyles;
        // Filter out existing styles to increase performance (#2640)
        if (oldStyles) {
            objectEach(styles, function (value, n) {
                if (oldStyles && oldStyles[n] !== value) {
                    newStyles[n] = value;
                    hasNew = true;
                }
            });
        }
        if (hasNew) {
            // Merge the new styles with the old ones
            if (oldStyles) {
                styles = extend(oldStyles, newStyles);
            }
            // Get the text width from style
            // Previously set, unset it (#8234)
            if (styles.width === null || styles.width === 'auto') {
                delete this.textWidth;
                // Apply new
            }
            else if (elem.nodeName.toLowerCase() === 'text' &&
                styles.width) {
                textWidth = this.textWidth = pInt(styles.width);
            }
            // Store object
            extend(this.styles, styles);
            if (textWidth && (!svg && this.renderer.forExport)) {
                delete styles.width;
            }
            var fontSize = isFirefox && styles.fontSize || null;
            // Necessary in firefox to be able to set font-size, #22124
            if (fontSize && (isNumber(fontSize) ||
                /^\d+$/.test(fontSize))) {
                styles.fontSize += 'px';
            }
            var stylesToApply_1 = merge(styles);
            if (elem.namespaceURI === this.SVG_NS) {
                // These CSS properties are interpreted internally by the SVG
                // renderer, but are not supported by SVG and should not be
                // added to the DOM. In styled mode, no CSS should find its way
                // to the DOM whatsoever (#6173, #6474).
                ['textOutline', 'textOverflow', 'whiteSpace', 'width'].forEach(function (key) { return (stylesToApply_1 &&
                    delete stylesToApply_1[key]); });
                // SVG requires fill for text
                if (stylesToApply_1.color) {
                    stylesToApply_1.fill = stylesToApply_1.color;
                    delete stylesToApply_1.color;
                }
            }
            css(elem, stylesToApply_1);
        }
        if (this.added) {
            // Rebuild text after added. Cache mechanisms in the buildText will
            // prevent building if there are no significant changes.
            if (this.element.nodeName === 'text') {
                this.renderer.buildText(this);
            }
            // Apply text outline after added
            if (styles.textOutline) {
                this.applyTextOutline(styles.textOutline);
            }
        }
        return this;
    };
    /**
     * @private
     * @function Highcharts.SVGElement#dashstyleSetter
     * @param {string} value
     */
    SVGElement.prototype.dashstyleSetter = function (value) {
        var i, strokeWidth = this['stroke-width'];
        // If "inherit", like maps in IE, assume 1 (#4981). With HC5 and the new
        // strokeWidth function, we should be able to use that instead.
        if (strokeWidth === 'inherit') {
            strokeWidth = 1;
        }
        if (value) {
            value = value.toLowerCase();
            var v = value
                .replace('shortdashdotdot', '3,1,1,1,1,1,')
                .replace('shortdashdot', '3,1,1,1')
                .replace('shortdot', '1,1,')
                .replace('shortdash', '3,1,')
                .replace('longdash', '8,3,')
                .replace(/dot/g, '1,3,')
                .replace('dash', '4,3,')
                .replace(/,$/, '')
                .split(','); // Ending comma
            i = v.length;
            while (i--) {
                v[i] = '' + (pInt(v[i]) * pick(strokeWidth, NaN));
            }
            value = v.join(',').replace(/NaN/g, 'none'); // #3226
            this.element.setAttribute('stroke-dasharray', value);
        }
    };
    /**
     * Destroy the element and element wrapper and clear up the DOM and event
     * hooks.
     *
     * @function Highcharts.SVGElement#destroy
     */
    SVGElement.prototype.destroy = function () {
        var wrapper = this, element = wrapper.element || {}, renderer = wrapper.renderer, ownerSVGElement = element.ownerSVGElement;
        var parentToClean = (element.nodeName === 'SPAN' &&
            wrapper.parentGroup ||
            void 0), grandParent, i;
        // Remove events
        element.onclick = element.onmouseout = element.onmouseover =
            element.onmousemove = element.point = null;
        stop(wrapper); // Stop running animations
        if (wrapper.clipPath && ownerSVGElement) {
            var clipPath_1 = wrapper.clipPath;
            // Look for existing references to this clipPath and remove them
            // before destroying the element (#6196).
            // The upper case version is for Edge
            [].forEach.call(ownerSVGElement.querySelectorAll('[clip-path],[CLIP-PATH]'), function (el) {
                if (el.getAttribute('clip-path').indexOf(clipPath_1.element.id) > -1) {
                    el.removeAttribute('clip-path');
                }
            });
            wrapper.clipPath = clipPath_1.destroy();
        }
        // Destroy stops in case this is a gradient object @todo old code?
        if (wrapper.stops) {
            for (i = 0; i < wrapper.stops.length; i++) {
                wrapper.stops[i].destroy();
            }
            wrapper.stops.length = 0;
            wrapper.stops = void 0;
        }
        // Remove element
        wrapper.safeRemoveChild(element);
        // In case of useHTML, clean up empty containers emulating SVG groups
        // (#1960, #2393, #2697).
        while ((parentToClean === null || parentToClean === void 0 ? void 0 : parentToClean.div) &&
            parentToClean.div.childNodes.length === 0) {
            grandParent = parentToClean.parentGroup;
            wrapper.safeRemoveChild(parentToClean.div);
            delete parentToClean.div;
            parentToClean = grandParent;
        }
        // Remove from alignObjects
        if (wrapper.alignOptions) {
            erase(renderer.alignedObjects, wrapper);
        }
        objectEach(wrapper, function (val, key) {
            var _a, _b, _c;
            if (
            // Destroy child elements of a group
            ((_a = wrapper[key]) === null || _a === void 0 ? void 0 : _a.parentGroup) === wrapper ||
                // Destroy own elements
                ['connector', 'foreignObject'].indexOf(key) !== -1) {
                (_c = (_b = wrapper[key]) === null || _b === void 0 ? void 0 : _b.destroy) === null || _c === void 0 ? void 0 : _c.call(_b);
            }
            // Delete all properties
            delete wrapper[key];
        });
        return;
    };
    /**
     * @private
     * @function Highcharts.SVGElement#dSettter
     * @param {number|string|Highcharts.SVGPathArray} value
     * @param {string} key
     * @param {Highcharts.SVGDOMElement} element
     */
    SVGElement.prototype.dSetter = function (value, key, element) {
        if (isArray(value)) {
            // Backwards compatibility, convert one-dimensional array into an
            // array of segments
            if (typeof value[0] === 'string') {
                value = this.renderer.pathToSegments(value);
            }
            this.pathArray = value;
            value = value.reduce(function (acc, seg, i) {
                if (!(seg === null || seg === void 0 ? void 0 : seg.join)) {
                    return (seg || '').toString();
                }
                return (i ? acc + ' ' : '') + seg.join(' ');
            }, '');
        }
        if (/(NaN| {2}|^$)/.test(value)) {
            value = 'M 0 0';
        }
        // Check for cache before resetting. Resetting causes disturbance in the
        // DOM, causing flickering in some cases in Edge/IE (#6747). Also
        // possible performance gain.
        if (this[key] !== value) {
            element.setAttribute(key, value);
            this[key] = value;
        }
    };
    /**
     * @private
     * @function Highcharts.SVGElement#fillSetter
     * @param {Highcharts.ColorType} value
     * @param {string} key
     * @param {Highcharts.SVGDOMElement} element
     */
    SVGElement.prototype.fillSetter = function (value, key, element) {
        if (typeof value === 'string') {
            element.setAttribute(key, value);
        }
        else if (value) {
            this.complexColor(value, key, element);
        }
    };
    /**
     * @private
     * @function Highcharts.SVGElement#hrefSetter
     * @param {Highcharts.ColorType} value
     * @param {string} key
     * @param {Highcharts.SVGDOMElement} element
     */
    SVGElement.prototype.hrefSetter = function (value, key, element) {
        // Namespace is needed for offline export, #19106
        element.setAttributeNS('http://www.w3.org/1999/xlink', key, value);
    };
    /**
     * Get the bounding box (width, height, x and y) for the element. Generally
     * used to get rendered text size. Since this is called a lot in charts,
     * the results are cached based on text properties, in order to save DOM
     * traffic. The returned bounding box includes the rotation, so for example
     * a single text line of rotation 90 will report a greater height, and a
     * width corresponding to the line-height.
     *
     * @sample highcharts/members/renderer-on-chart/
     *         Draw a rectangle based on a text's bounding box
     *
     * @function Highcharts.SVGElement#getBBox
     *
     * @param {boolean} [reload]
     *        Skip the cache and get the updated DOM bounding box.
     *
     * @param {number} [rot]
     *        Override the element's rotation. This is internally used on axis
     *        labels with a value of 0 to find out what the bounding box would
     *        be have been if it were not rotated.
     *
     * @return {Highcharts.BBoxObject}
     *         The bounding box with `x`, `y`, `width` and `height` properties.
     */
    SVGElement.prototype.getBBox = function (reload, rot) {
        var wrapper = this, alignValue = wrapper.alignValue, element = wrapper.element, renderer = wrapper.renderer, styles = wrapper.styles, textStr = wrapper.textStr, cache = renderer.cache, cacheKeys = renderer.cacheKeys, isSVG = element.namespaceURI === wrapper.SVG_NS, rotation = pick(rot, wrapper.rotation, 0), fontSize = renderer.styledMode ? (element &&
            SVGElement.prototype.getStyle.call(element, 'font-size')) : (styles.fontSize);
        var bBox, height, toggleTextShadowShim, cacheKey;
        // Avoid undefined and null (#7316)
        if (defined(textStr)) {
            cacheKey = textStr.toString();
            // Since numbers are monospaced, and numerical labels appear a lot
            // in a chart, we assume that a label of n characters has the same
            // bounding box as others of the same length. Unless there is inner
            // HTML in the label. In that case, leave the numbers as is (#5899).
            if (cacheKey.indexOf('<') === -1) {
                cacheKey = cacheKey.replace(/\d/g, '0');
            }
            // Properties that affect bounding box
            cacheKey += [
                '',
                renderer.rootFontSize,
                fontSize,
                rotation,
                wrapper.textWidth, // #7874, also useHTML
                alignValue,
                styles.lineClamp,
                styles.textOverflow, // #5968
                styles.fontWeight // #12163
            ].join(',');
        }
        if (cacheKey && !reload) {
            bBox = cache[cacheKey];
        }
        // No cache found
        if (!bBox || bBox.polygon) {
            // SVG elements
            if (isSVG || renderer.forExport) {
                try { // Fails in Firefox if the container has display: none.
                    // When the text shadow shim is used, we need to hide the
                    // fake shadows to get the correct bounding box (#3872)
                    toggleTextShadowShim = this.fakeTS && function (display) {
                        var outline = element.querySelector('.highcharts-text-outline');
                        if (outline) {
                            css(outline, { display: display });
                        }
                    };
                    // Workaround for #3842, Firefox reporting wrong bounding
                    // box for shadows
                    if (isFunction(toggleTextShadowShim)) {
                        toggleTextShadowShim('none');
                    }
                    bBox = element.getBBox ?
                        // SVG: use extend because IE9 is not allowed to change
                        // width and height in case of rotation (below)
                        extend({}, element.getBBox()) : {
                        // HTML elements with `exporting.allowHTML` and
                        // legacy IE in export mode
                        width: element.offsetWidth,
                        height: element.offsetHeight,
                        x: 0,
                        y: 0
                    };
                    // #3842
                    if (isFunction(toggleTextShadowShim)) {
                        toggleTextShadowShim('');
                    }
                }
                catch (e) {
                    '';
                }
                // If the bBox is not set, the try-catch block above failed. The
                // other condition is for Opera that returns a width of
                // -Infinity on hidden elements.
                if (!bBox || bBox.width < 0) {
                    bBox = { x: 0, y: 0, width: 0, height: 0 };
                }
                // Use HTML within SVG
            }
            else {
                bBox = wrapper.htmlGetBBox();
            }
            // True SVG elements as well as HTML elements in modern browsers
            // using the .useHTML option need to compensated for rotation
            height = bBox.height;
            // Workaround for wrong bounding box in IE, Edge and Chrome on
            // Windows. With Highcharts' default font, IE and Edge report
            // a box height of 16.899 and Chrome rounds it to 17. If this
            // stands uncorrected, it results in more padding added below
            // the text than above when adding a label border or background.
            // Also vertical positioning is affected.
            // https://jsfiddle.net/highcharts/em37nvuj/
            // (#1101, #1505, #1669, #2568, #6213).
            if (isSVG) {
                bBox.height = height = ({
                    '11px,17': 14,
                    '13px,20': 16
                }["".concat(fontSize || '', ",").concat(Math.round(height))] ||
                    height);
            }
            // Adjust for rotated text
            if (rotation) {
                bBox = this.getRotatedBox(bBox, rotation);
            }
            // Create a reference to catch changes to bBox
            var e = { bBox: bBox };
            fireEvent(this, 'afterGetBBox', e);
            // Pick up any changes after the fired event
            bBox = e.bBox;
        }
        // Cache it. When loading a chart in a hidden iframe in Firefox and
        // IE/Edge, the bounding box height is 0, so don't cache it (#5620).
        if (cacheKey && (textStr === '' || bBox.height > 0)) {
            // Rotate (#4681)
            while (cacheKeys.length > 250) {
                delete cache[cacheKeys.shift()];
            }
            if (!cache[cacheKey]) {
                cacheKeys.push(cacheKey);
            }
            cache[cacheKey] = bBox;
        }
        return bBox;
    };
    /**
     * Get the rotated box.
     * @private
     */
    SVGElement.prototype.getRotatedBox = function (box, rotation) {
        var boxX = box.x, boxY = box.y, width = box.width, height = box.height, _a = this, alignValue = _a.alignValue, translateY = _a.translateY, _b = _a.rotationOriginX, rotationOriginX = _b === void 0 ? 0 : _b, _c = _a.rotationOriginY, rotationOriginY = _c === void 0 ? 0 : _c, alignFactor = getAlignFactor(alignValue), baseline = Number(this.element.getAttribute('y') || 0) -
            (translateY ? 0 : boxY), rad = rotation * deg2rad, rad90 = (rotation - 90) * deg2rad, cosRad = Math.cos(rad), sinRad = Math.sin(rad), wCosRad = width * cosRad, wSinRad = width * sinRad, cosRad90 = Math.cos(rad90), sinRad90 = Math.sin(rad90), _d = [
            rotationOriginX,
            rotationOriginY
        ].map(function (rotOrigin) { return [
            rotOrigin - (rotOrigin * cosRad),
            rotOrigin * sinRad
        ]; }), _e = _d[0], xOriginCosRad = _e[0], xOriginSinRad = _e[1], _f = _d[1], yOriginCosRad = _f[0], yOriginSinRad = _f[1], 
        // Find the starting point on the left side baseline of
        // the text
        pX = ((boxX + alignFactor * (width - wCosRad)) +
            xOriginCosRad + yOriginSinRad), pY = ((boxY + baseline - alignFactor * wSinRad) -
            xOriginSinRad + yOriginCosRad), 
        // Find all corners
        aX = pX + baseline * cosRad90, bX = aX + wCosRad, cX = bX - height * cosRad90, dX = cX - wCosRad, aY = pY + baseline * sinRad90, bY = aY + wSinRad, cY = bY - height * sinRad90, dY = cY - wSinRad;
        // Deduct the bounding box from the corners
        var x = Math.min(aX, bX, cX, dX), y = Math.min(aY, bY, cY, dY), boxWidth = Math.max(aX, bX, cX, dX) - x, boxHeight = Math.max(aY, bY, cY, dY) - y;
        /* Uncomment to debug boxes
        this.renderer.path([
            ['M', aX, aY],
            ['L', bX, bY],
            ['L', cX, cY],
            ['L', dX, dY],
            ['Z']
        ])
            .attr({
                stroke: 'red',
                'stroke-width': 1
            })
            .add();
        // */
        return {
            x: x,
            y: y,
            width: boxWidth,
            height: boxHeight,
            polygon: [
                [aX, aY],
                [bX, bY],
                [cX, cY],
                [dX, dY]
            ]
        };
    };
    /**
     * Get the computed style. Only in styled mode.
     *
     * @example
     * chart.series[0].points[0].graphic.getStyle('stroke-width'); // => '1px'
     *
     * @function Highcharts.SVGElement#getStyle
     *
     * @param {string} prop
     *        The property name to check for.
     *
     * @return {string}
     *         The current computed value.
     */
    SVGElement.prototype.getStyle = function (prop) {
        return win
            .getComputedStyle(this.element || this, '')
            .getPropertyValue(prop);
    };
    /**
     * Check if an element has the given class name.
     *
     * @function Highcharts.SVGElement#hasClass
     *
     * @param {string} className
     * The class name to check for.
     *
     * @return {boolean}
     * Whether the class name is found.
     */
    SVGElement.prototype.hasClass = function (className) {
        return ('' + this.attr('class'))
            .split(' ')
            .indexOf(className) !== -1;
    };
    /**
     * Hide the element, similar to setting the `visibility` attribute to
     * `hidden`.
     *
     * @function Highcharts.SVGElement#hide
     *
     * @return {Highcharts.SVGElement}
     *         Returns the SVGElement for chaining.
     */
    SVGElement.prototype.hide = function () {
        return this.attr({ visibility: 'hidden' });
    };
    /**
     * @private
     */
    SVGElement.prototype.htmlGetBBox = function () {
        return { height: 0, width: 0, x: 0, y: 0 };
    };
    /**
     * Add an event listener. This is a simple setter that replaces the
     * previous event of the same type added by this function, as opposed to
     * the {@link Highcharts#addEvent} function.
     *
     * @sample highcharts/members/element-on/
     *         A clickable rectangle
     *
     * @function Highcharts.SVGElement#on
     *
     * @param {string} eventType
     * The event type.
     *
     * @param {Function} handler
     * The handler callback.
     *
     * @return {Highcharts.SVGElement}
     * The SVGElement for chaining.
     */
    SVGElement.prototype.on = function (eventType, handler) {
        var onEvents = this.onEvents;
        if (onEvents[eventType]) {
            // Unbind existing event
            onEvents[eventType]();
        }
        onEvents[eventType] = addEvent(this.element, eventType, handler);
        return this;
    };
    /**
     * @private
     * @function Highcharts.SVGElement#opacitySetter
     * @param {string} value
     * @param {string} key
     * @param {Highcharts.SVGDOMElement} element
     */
    SVGElement.prototype.opacitySetter = function (value, key, element) {
        // Round off to avoid float errors, like tests where opacity lands on
        // 9.86957e-06 instead of 0
        var opacity = Number(Number(value).toFixed(3));
        this.opacity = opacity;
        element.setAttribute(key, opacity);
    };
    /**
     * Re-align an aligned text or label after setting the text.
     *
     * @private
     * @function Highcharts.SVGElement#reAlign
     *
     */
    SVGElement.prototype.reAlign = function () {
        var _a;
        if (((_a = this.alignOptions) === null || _a === void 0 ? void 0 : _a.width) && this.alignOptions.align !== 'left') {
            this.alignOptions.width = this.getBBox().width;
            this.placed = false; // Block animation
            this.align();
        }
    };
    /**
     * Remove a class name from the element.
     *
     * @function Highcharts.SVGElement#removeClass
     *
     * @param {string|RegExp} className
     *        The class name to remove.
     *
     * @return {Highcharts.SVGElement} Returns the SVG element for chainability.
     */
    SVGElement.prototype.removeClass = function (className) {
        return this.attr('class', ('' + this.attr('class'))
            .replace(isString(className) ?
            new RegExp("(^| )".concat(className, "( |$)")) : // #12064, #13590
            className, ' ')
            .replace(/ +/g, ' ')
            .trim());
    };
    /**
     *
     * @private
     */
    SVGElement.prototype.removeTextOutline = function () {
        var outline = this.element
            .querySelector('tspan.highcharts-text-outline');
        if (outline) {
            this.safeRemoveChild(outline);
        }
    };
    /**
     * Removes an element from the DOM.
     *
     * @private
     * @function Highcharts.SVGElement#safeRemoveChild
     *
     * @param {Highcharts.SVGDOMElement|Highcharts.HTMLDOMElement} element
     * The DOM node to remove.
     */
    SVGElement.prototype.safeRemoveChild = function (element) {
        var parentNode = element.parentNode;
        if (parentNode) {
            parentNode.removeChild(element);
        }
    };
    /**
     * Set the coordinates needed to draw a consistent radial gradient across
     * a shape regardless of positioning inside the chart. Used on pie slices
     * to make all the slices have the same radial reference point.
     *
     * @function Highcharts.SVGElement#setRadialReference
     *
     * @param {Array<number>} coordinates
     * The center reference. The format is `[centerX, centerY, diameter]` in
     * pixels.
     *
     * @return {Highcharts.SVGElement}
     * Returns the SVGElement for chaining.
     */
    SVGElement.prototype.setRadialReference = function (coordinates) {
        var existingGradient = (this.element.gradient &&
            this.renderer.gradients[this.element.gradient]) || void 0;
        this.element.radialReference = coordinates;
        // On redrawing objects with an existing gradient, the gradient needs
        // to be repositioned (#3801)
        if (existingGradient === null || existingGradient === void 0 ? void 0 : existingGradient.radAttr) {
            existingGradient.animate(this.renderer.getRadialAttr(coordinates, existingGradient.radAttr));
        }
        return this;
    };
    /**
     * Add a shadow to the element. In styled mode, this method is not used,
     * instead use `defs` and filters.
     *
     * @example
     * renderer.rect(10, 100, 100, 100)
     *     .attr({ fill: 'red' })
     *     .shadow(true);
     *
     * @function Highcharts.SVGElement#shadow
     *
     * @param {boolean|Highcharts.ShadowOptionsObject} [shadowOptions] The
     *        shadow options. If `true`, the default options are applied. If
     *        `false`, the current shadow will be removed.
     *
     * @return {Highcharts.SVGElement} Returns the SVGElement for chaining.
     */
    SVGElement.prototype.shadow = function (shadowOptions) {
        var _a;
        var renderer = this.renderer, options = merge(((_a = this.parentGroup) === null || _a === void 0 ? void 0 : _a.rotation) === 90 ? {
            offsetX: -1,
            offsetY: -1
        } : {}, isObject(shadowOptions) ? shadowOptions : {}), id = renderer.shadowDefinition(options);
        return this.attr({
            filter: shadowOptions ?
                "url(".concat(renderer.url, "#").concat(id, ")") :
                'none'
        });
    };
    /**
     * Show the element after it has been hidden.
     *
     * @function Highcharts.SVGElement#show
     *
     * @param {boolean} [inherit=true]
     *        Set the visibility attribute to `inherit` rather than `visible`.
     *        The difference is that an element with `visibility="visible"`
     *        will be visible even if the parent is hidden.
     *
     * @return {Highcharts.SVGElement}
     *         Returns the SVGElement for chaining.
     */
    SVGElement.prototype.show = function (inherit) {
        if (inherit === void 0) { inherit = true; }
        return this.attr({ visibility: inherit ? 'inherit' : 'visible' });
    };
    /**
     * Set the stroke-width and record it on the SVGElement
     *
     * @private
     * @function Highcharts.SVGElement#strokeSetter
     * @param {number|string|ColorType} value
     * @param {string} key
     * @param {Highcharts.SVGDOMElement} element
     */
    SVGElement.prototype['stroke-widthSetter'] = function (value, key, element) {
        // Record it for quick access in getter
        this[key] = value;
        element.setAttribute(key, value);
    };
    /**
     * Get the computed stroke width in pixel values. This is used extensively
     * when drawing shapes to ensure the shapes are rendered crisp and
     * positioned correctly relative to each other. Using
     * `shape-rendering: crispEdges` leaves us less control over positioning,
     * for example when we want to stack columns next to each other, or position
     * things pixel-perfectly within the plot box.
     *
     * The common pattern when placing a shape is:
     * - Create the SVGElement and add it to the DOM. In styled mode, it will
     *   now receive a stroke width from the style sheet. In classic mode we
     *   will add the `stroke-width` attribute.
     * - Read the computed `elem.strokeWidth()`.
     * - Place it based on the stroke width.
     *
     * @function Highcharts.SVGElement#strokeWidth
     *
     * @return {number}
     * The stroke width in pixels. Even if the given stroke width (in CSS or by
     * attributes) is based on `em` or other units, the pixel size is returned.
     */
    SVGElement.prototype.strokeWidth = function () {
        // In non-styled mode, read the stroke width as set by .attr
        if (!this.renderer.styledMode) {
            return this['stroke-width'] || 0;
        }
        // In styled mode, read computed stroke width
        var val = this.getStyle('stroke-width');
        var ret = 0, tempElement;
        // Read pixel values directly
        if (/px$/.test(val)) {
            ret = pInt(val);
            // Other values like em, pt etc need to be measured
        }
        else if (val !== '') {
            tempElement = doc.createElementNS(SVG_NS, 'rect');
            attr(tempElement, {
                width: val,
                'stroke-width': 0
            });
            this.element.parentNode.appendChild(tempElement);
            ret = tempElement.getBBox().width;
            tempElement.parentNode.removeChild(tempElement);
        }
        return ret;
    };
    /**
     * If one of the symbol size affecting parameters are changed,
     * check all the others only once for each call to an element's
     * .attr() method
     *
     * @private
     * @function Highcharts.SVGElement#symbolAttr
     *
     * @param {Highcharts.SVGAttributes} hash
     * The attributes to set.
     */
    SVGElement.prototype.symbolAttr = function (hash) {
        var wrapper = this;
        SVGElement.symbolCustomAttribs.forEach(function (key) {
            wrapper[key] = pick(hash[key], wrapper[key]);
        });
        wrapper.attr({
            d: wrapper.renderer.symbols[wrapper.symbolName](wrapper.x, wrapper.y, wrapper.width, wrapper.height, wrapper)
        });
    };
    /**
     * @private
     * @function Highcharts.SVGElement#textSetter
     * @param {string} value
     */
    SVGElement.prototype.textSetter = function (value) {
        if (value !== this.textStr) {
            // Delete size caches when the text changes
            // delete this.bBox; // old code in series-label
            delete this.textPxLength;
            this.textStr = value;
            if (this.added) {
                this.renderer.buildText(this);
            }
            this.reAlign();
        }
    };
    /**
     * @private
     * @function Highcharts.SVGElement#titleSetter
     * @param {string} value
     */
    SVGElement.prototype.titleSetter = function (value) {
        var el = this.element;
        var titleNode = el.getElementsByTagName('title')[0] ||
            doc.createElementNS(this.SVG_NS, 'title');
        // Move to first child
        if (el.insertBefore) {
            el.insertBefore(titleNode, el.firstChild);
        }
        else {
            el.appendChild(titleNode);
        }
        // Replace text content and escape markup
        titleNode.textContent = replaceNested(// Scan #[73]
        pick(value, ''), // #3276, #3895
        [/<[^>]*>/g, '']).replace(/&lt;/g, '<').replace(/&gt;/g, '>');
    };
    /**
     * Bring the element to the front. Alternatively, a new zIndex can be set.
     *
     * @sample highcharts/members/element-tofront/
     *         Click an element to bring it to front
     *
     * @function Highcharts.SVGElement#toFront
     *
     * @return {Highcharts.SVGElement}
     * Returns the SVGElement for chaining.
     */
    SVGElement.prototype.toFront = function () {
        var element = this.element;
        element.parentNode.appendChild(element);
        return this;
    };
    /**
     * Move an object and its children by x and y values.
     *
     * @function Highcharts.SVGElement#translate
     *
     * @param {number} x
     * The x value.
     *
     * @param {number} y
     * The y value.
     *
     * @return {Highcharts.SVGElement}
     * Translated element.
     */
    SVGElement.prototype.translate = function (x, y) {
        return this.attr({
            translateX: x,
            translateY: y
        });
    };
    /**
     * Update the transform attribute based on internal properties. Deals with
     * the custom `translateX`, `translateY`, `rotation`, `scaleX` and `scaleY`
     * attributes and updates the SVG `transform` attribute.
     *
     * @private
     * @function Highcharts.SVGElement#updateTransform
     */
    SVGElement.prototype.updateTransform = function (attrib) {
        var _a, _b, _c, _d;
        if (attrib === void 0) { attrib = 'transform'; }
        var _e = this, element = _e.element, foreignObject = _e.foreignObject, matrix = _e.matrix, padding = _e.padding, _f = _e.rotation, rotation = _f === void 0 ? 0 : _f, rotationOriginX = _e.rotationOriginX, rotationOriginY = _e.rotationOriginY, scaleX = _e.scaleX, scaleY = _e.scaleY, text = _e.text, _g = _e.translateX, translateX = _g === void 0 ? 0 : _g, _h = _e.translateY, translateY = _h === void 0 ? 0 : _h;
        // Apply translate. Nearly all transformed elements have translation,
        // so instead of checking for translate = 0, do it always (#1767,
        // #1846).
        var transform = ['translate(' + translateX + ',' + translateY + ')'];
        // Apply matrix
        if (defined(matrix)) {
            transform.push('matrix(' + matrix.join(',') + ')');
        }
        // Apply rotation
        if (rotation) {
            transform.push('rotate(' + rotation + ' ' +
                ((_b = (_a = rotationOriginX !== null && rotationOriginX !== void 0 ? rotationOriginX : element.getAttribute('x')) !== null && _a !== void 0 ? _a : this.x) !== null && _b !== void 0 ? _b : 0) +
                ' ' +
                ((_d = (_c = rotationOriginY !== null && rotationOriginY !== void 0 ? rotationOriginY : element.getAttribute('y')) !== null && _c !== void 0 ? _c : this.y) !== null && _d !== void 0 ? _d : 0) +
                ')');
            // HTML labels rotation (#20685)
            if ((text === null || text === void 0 ? void 0 : text.element.tagName) === 'SPAN' &&
                !(text === null || text === void 0 ? void 0 : text.foreignObject)) {
                text.attr({
                    rotation: rotation,
                    rotationOriginX: (rotationOriginX || 0) - padding,
                    rotationOriginY: (rotationOriginY || 0) - padding
                });
            }
        }
        // Apply scale
        if (defined(scaleX) || defined(scaleY)) {
            transform.push('scale(' + pick(scaleX, 1) + ' ' + pick(scaleY, 1) + ')');
        }
        if (transform.length && !(text || this).textPath) {
            ((foreignObject === null || foreignObject === void 0 ? void 0 : foreignObject.element) || element)
                .setAttribute(attrib, transform.join(' '));
        }
    };
    /**
     * @private
     * @function Highcharts.SVGElement#visibilitySetter
     *
     * @param {string} value
     *
     * @param {string} key
     *
     * @param {Highcharts.SVGDOMElement} element
     *
     */
    SVGElement.prototype.visibilitySetter = function (value, key, element) {
        // IE9-11 doesn't handle visibility:inherit well, so we remove the
        // attribute instead (#2881, #3909)
        if (value === 'inherit') {
            element.removeAttribute(key);
        }
        else if (this[key] !== value) { // #6747
            element.setAttribute(key, value);
        }
        this[key] = value;
    };
    /**
     * @private
     * @function Highcharts.SVGElement#xGetter
     */
    SVGElement.prototype.xGetter = function (key) {
        if (this.element.nodeName === 'circle') {
            if (key === 'x') {
                key = 'cx';
            }
            else if (key === 'y') {
                key = 'cy';
            }
        }
        return this._defaultGetter(key);
    };
    /**
     * @private
     * @function Highcharts.SVGElement#zIndexSetter
     */
    SVGElement.prototype.zIndexSetter = function (value, key) {
        var renderer = this.renderer, parentGroup = this.parentGroup, parentWrapper = parentGroup || renderer, parentNode = parentWrapper.element || renderer.box, element = this.element, svgParent = parentNode === renderer.box;
        var childNodes, otherElement, otherZIndex, inserted = false, undefinedOtherZIndex, run = this.added, i;
        if (defined(value)) {
            // So we can read it for other elements in the group
            element.setAttribute('data-z-index', value);
            value = +value;
            if (this[key] === value) {
                // Only update when needed (#3865)
                run = false;
            }
        }
        else if (defined(this[key])) {
            element.removeAttribute('data-z-index');
        }
        this[key] = value;
        // Insert according to this and other elements' zIndex. Before .add() is
        // called, nothing is done. Then on add, or by later calls to
        // zIndexSetter, the node is placed on the right place in the DOM.
        if (run) {
            value = this.zIndex;
            if (value && parentGroup) {
                parentGroup.handleZ = true;
            }
            childNodes = parentNode.childNodes;
            for (i = childNodes.length - 1; i >= 0 && !inserted; i--) {
                otherElement = childNodes[i];
                otherZIndex = otherElement.getAttribute('data-z-index');
                undefinedOtherZIndex = !defined(otherZIndex);
                if (otherElement !== element) {
                    if (
                    // Negative zIndex versus no zIndex:
                    // On all levels except the highest. If the parent is
                    // <svg>, then we don't want to put items before <desc>
                    // or <defs>
                    value < 0 &&
                        undefinedOtherZIndex &&
                        !svgParent &&
                        !i) {
                        parentNode.insertBefore(element, childNodes[i]);
                        inserted = true;
                    }
                    else if (
                    // Insert after the first element with a lower zIndex
                    pInt(otherZIndex) <= value ||
                        // If negative zIndex, add this before first undefined
                        // zIndex element
                        (undefinedOtherZIndex &&
                            (!defined(value) || value >= 0))) {
                        parentNode.insertBefore(element, childNodes[i + 1]);
                        inserted = true;
                    }
                }
            }
            if (!inserted) {
                parentNode.insertBefore(element, childNodes[svgParent ? 3 : 0]);
                inserted = true;
            }
        }
        return inserted;
    };
    /* *
     *
     *  Properties
     *
     * */
    // Custom attributes used for symbols, these should be filtered out when
    // setting SVGElement attributes (#9375).
    SVGElement.symbolCustomAttribs = [
        'anchorX',
        'anchorY',
        'clockwise',
        'end',
        'height',
        'innerR',
        'r',
        'start',
        'width',
        'x',
        'y'
    ];
    return SVGElement;
}());
// Some shared setters and getters
SVGElement.prototype.strokeSetter = SVGElement.prototype.fillSetter;
SVGElement.prototype.yGetter = SVGElement.prototype.xGetter;
SVGElement.prototype.matrixSetter =
    SVGElement.prototype.rotationOriginXSetter =
        SVGElement.prototype.rotationOriginYSetter =
            SVGElement.prototype.rotationSetter =
                SVGElement.prototype.scaleXSetter =
                    SVGElement.prototype.scaleYSetter =
                        SVGElement.prototype.translateXSetter =
                            SVGElement.prototype.translateYSetter =
                                SVGElement.prototype.verticalAlignSetter = function (value, key) {
                                    this[key] = value;
                                    this.doTransform = true;
                                };
/* *
 *
 *  Default Export
 *
 * */
export default SVGElement;
/* *
 *
 *  API Declarations
 *
 * */
/**
 * Reference to the global SVGElement class as a workaround for a name conflict
 * in the Highcharts namespace.
 *
 * @global
 * @typedef {global.SVGElement} GlobalSVGElement
 *
 * @see https://developer.mozilla.org/en-US/docs/Web/API/SVGElement
 */
/**
 * The horizontal alignment of an element.
 *
 * @typedef {"center"|"left"|"right"} Highcharts.AlignValue
 */
/**
 * Options to align the element relative to the chart or another box.
 *
 * @interface Highcharts.AlignObject
 */ /**
* Horizontal alignment. Can be one of `left`, `center` and `right`.
*
* @name Highcharts.AlignObject#align
* @type {Highcharts.AlignValue|undefined}
*
* @default left
*/ /**
* Vertical alignment. Can be one of `top`, `middle` and `bottom`.
*
* @name Highcharts.AlignObject#verticalAlign
* @type {Highcharts.VerticalAlignValue|undefined}
*
* @default top
*/ /**
* Horizontal pixel offset from alignment.
*
* @name Highcharts.AlignObject#x
* @type {number|undefined}
*
* @default 0
*/ /**
* Vertical pixel offset from alignment.
*
* @name Highcharts.AlignObject#y
* @type {number|undefined}
*
* @default 0
*/ /**
* Use the `transform` attribute with translateX and translateY custom
* attributes to align this elements rather than `x` and `y` attributes.
*
* @name Highcharts.AlignObject#alignByTranslate
* @type {boolean|undefined}
*
* @default false
*/
/**
 * Bounding box of an element.
 *
 * @interface Highcharts.BBoxObject
 * @extends Highcharts.PositionObject
 */ /**
* Height of the bounding box.
*
* @name Highcharts.BBoxObject#height
* @type {number}
*/ /**
* Width of the bounding box.
*
* @name Highcharts.BBoxObject#width
* @type {number}
*/ /**
* Horizontal position of the bounding box.
*
* @name Highcharts.BBoxObject#x
* @type {number}
*/ /**
* Vertical position of the bounding box.
*
* @name Highcharts.BBoxObject#y
* @type {number}
*/
/**
 * An object of key-value pairs for SVG attributes. Attributes in Highcharts
 * elements for the most parts correspond to SVG, but some are specific to
 * Highcharts, like `zIndex`, `rotation`, `rotationOriginX`,
 * `rotationOriginY`, `translateX`, `translateY`, `scaleX` and `scaleY`. SVG
 * attributes containing a hyphen are _not_ camel-cased, they should be
 * quoted to preserve the hyphen.
 *
 * @example
 * {
 *     'stroke': '#ff0000', // basic
 *     'stroke-width': 2, // hyphenated
 *     'rotation': 45 // custom
 *     'd': ['M', 10, 10, 'L', 30, 30, 'z'] // path definition, note format
 * }
 *
 * @interface Highcharts.SVGAttributes
 */ /**
* @name Highcharts.SVGAttributes#[key:string]
* @type {*}
*/ /**
* @name Highcharts.SVGAttributes#d
* @type {string|Highcharts.SVGPathArray|undefined}
*/ /**
* @name Highcharts.SVGAttributes#dx
* @type {number|undefined}
*/ /**
* @name Highcharts.SVGAttributes#dy
* @type {number|undefined}
*/ /**
* @name Highcharts.SVGAttributes#fill
* @type {Highcharts.ColorString|Highcharts.GradientColorObject|Highcharts.PatternObject|undefined}
*/ /**
* @name Highcharts.SVGAttributes#inverted
* @type {boolean|undefined}
*/ /**
* @name Highcharts.SVGAttributes#matrix
* @type {Array<number>|undefined}
*/ /**
* @name Highcharts.SVGAttributes#rotation
* @type {number|undefined}
*/ /**
* @name Highcharts.SVGAttributes#rotationOriginX
* @type {number|undefined}
*/ /**
* @name Highcharts.SVGAttributes#rotationOriginY
* @type {number|undefined}
*/ /**
* @name Highcharts.SVGAttributes#scaleX
* @type {number|undefined}
*/ /**
* @name Highcharts.SVGAttributes#scaleY
* @type {number|undefined}
*/ /**
* @name Highcharts.SVGAttributes#stroke
* @type {Highcharts.ColorString|Highcharts.GradientColorObject|Highcharts.PatternObject|undefined}
*/ /**
* @name Highcharts.SVGAttributes#style
* @type {string|Highcharts.CSSObject|undefined}
*/ /**
* @name Highcharts.SVGAttributes#translateX
* @type {number|undefined}
*/ /**
* @name Highcharts.SVGAttributes#translateY
* @type {number|undefined}
*/ /**
* @name Highcharts.SVGAttributes#zIndex
* @type {number|undefined}
*/
/**
 * An SVG DOM element. The type is a reference to the regular SVGElement in the
 * global scope.
 *
 * @typedef {globals.GlobalSVGElement} Highcharts.SVGDOMElement
 *
 * @see https://developer.mozilla.org/en-US/docs/Web/API/SVGElement
 */
/**
 * The vertical alignment of an element.
 *
 * @typedef {"bottom"|"middle"|"top"} Highcharts.VerticalAlignValue
 */
''; // Keeps doclets above in JS file
