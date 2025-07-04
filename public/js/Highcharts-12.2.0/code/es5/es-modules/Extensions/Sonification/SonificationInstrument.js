/* *
 *
 *  (c) 2009-2025 Øystein Moseng
 *
 *  Class representing an Instrument with mappable parameters for sonification.
 *
 *  License: www.highcharts.com/license
 *
 *  !!!!!!! SOURCE GETS TRANSPILED BY TYPESCRIPT. EDIT TS FILE ONLY. !!!!!!!
 *
 * */
'use strict';
import SynthPatch from './SynthPatch.js';
import InstrumentPresets from './InstrumentPresets.js';
import U from '../../Core/Utilities.js';
var defined = U.defined, extend = U.extend;
/**
 * The SonificationInstrument class. This class represents an instrument with
 * mapping capabilities. The instrument wraps a SynthPatch object, and extends
 * it with functionality such as panning, tremolo, and global low/highpass
 * filters.
 *
 * @sample highcharts/sonification/instrument-raw
 *         Using SonificationInstrument directly, with no chart.
 *
 * @requires modules/sonification
 *
 * @class
 * @name Highcharts.SonificationInstrument
 *
 * @param {AudioContext} audioContext
 *        The AudioContext to use.
 * @param {AudioNode} outputNode
 *        The destination node to connect to.
 * @param {Highcharts.SonificationInstrumentOptionsObject} options
 *        Configuration for the instrument.
 */
var SonificationInstrument = /** @class */ (function () {
    function SonificationInstrument(audioContext, outputNode, options) {
        this.audioContext = audioContext;
        this.curParams = {};
        this.midiTrackName = options.midiTrackName;
        this.masterVolNode = new GainNode(audioContext);
        this.masterVolNode.connect(outputNode);
        this.volumeNode = new GainNode(audioContext);
        this.createNodesFromCapabilities(extend({
            pan: true
        }, options.capabilities || {}));
        this.connectCapabilityNodes(this.volumeNode, this.masterVolNode);
        this.synthPatch = new SynthPatch(audioContext, typeof options.synthPatch === 'string' ?
            InstrumentPresets[options.synthPatch] : options.synthPatch);
        this.midiInstrument = this.synthPatch.midiInstrument || 1;
        this.synthPatch.startSilently();
        this.synthPatch.connect(this.volumeNode);
    }
    /**
     * Set the overall volume.
     * @function Highcharts.SonificationInstrument#setMasterVolume
     * @param {number} volume The volume to set, from 0 to 1.
     */
    SonificationInstrument.prototype.setMasterVolume = function (volume) {
        this.masterVolNode.gain.setTargetAtTime(volume, 0, SonificationInstrument.rampTime);
    };
    /**
     * Schedule an instrument event at a given time offset, whether it is
     * playing a note or changing the parameters of the instrument.
     * @function Highcharts.SonificationInstrument#scheduleEventAtTime
     * @param {number} time Time is given in seconds, where 0 is now.
     * @param {Highcharts.SonificationInstrumentScheduledEventOptionsObject} params
     * Parameters for the instrument event.
     */
    SonificationInstrument.prototype.scheduleEventAtTime = function (time, params) {
        var mergedParams = extend(this.curParams, params), freq = defined(params.frequency) ?
            params.frequency : defined(params.note) ?
            SonificationInstrument.musicalNoteToFrequency(params.note) :
            220;
        if (defined(freq)) {
            this.synthPatch.playFreqAtTime(time, freq, mergedParams.noteDuration);
        }
        if (defined(mergedParams.tremoloDepth) ||
            defined(mergedParams.tremoloSpeed)) {
            this.setTremoloAtTime(time, mergedParams.tremoloDepth, mergedParams.tremoloSpeed);
        }
        if (defined(mergedParams.pan)) {
            this.setPanAtTime(time, mergedParams.pan);
        }
        if (defined(mergedParams.volume)) {
            this.setVolumeAtTime(time, mergedParams.volume);
        }
        if (defined(mergedParams.lowpassFreq) ||
            defined(mergedParams.lowpassResonance)) {
            this.setFilterAtTime('lowpass', time, mergedParams.lowpassFreq, mergedParams.lowpassResonance);
        }
        if (defined(mergedParams.highpassFreq) ||
            defined(mergedParams.highpassResonance)) {
            this.setFilterAtTime('highpass', time, mergedParams.highpassFreq, mergedParams.highpassResonance);
        }
    };
    /**
     * Schedule silencing the instrument at a given time offset.
     * @function Highcharts.SonificationInstrument#silenceAtTime
     * @param {number} time Time is given in seconds, where 0 is now.
     */
    SonificationInstrument.prototype.silenceAtTime = function (time) {
        this.synthPatch.silenceAtTime(time);
    };
    /**
     * Cancel currently playing sounds and any scheduled actions.
     * @function Highcharts.SonificationInstrument#cancel
     */
    SonificationInstrument.prototype.cancel = function () {
        this.synthPatch.mute();
        [
            this.tremoloDepth && this.tremoloDepth.gain,
            this.tremoloOsc && this.tremoloOsc.frequency,
            this.lowpassNode && this.lowpassNode.frequency,
            this.lowpassNode && this.lowpassNode.Q,
            this.highpassNode && this.highpassNode.frequency,
            this.highpassNode && this.highpassNode.Q,
            this.panNode && this.panNode.pan,
            this.volumeNode.gain
        ].forEach(function (p) { return (p && p.cancelScheduledValues(0)); });
    };
    /**
     * Stop instrument and destroy it, cleaning up used resources.
     * @function Highcharts.SonificationInstrument#destroy
     */
    SonificationInstrument.prototype.destroy = function () {
        this.cancel();
        this.synthPatch.stop();
        if (this.tremoloOsc) {
            this.tremoloOsc.stop();
        }
        [
            this.tremoloDepth, this.tremoloOsc, this.lowpassNode,
            this.highpassNode, this.panNode, this.volumeNode,
            this.masterVolNode
        ].forEach((function (n) { return n && n.disconnect(); }));
    };
    /**
     * Schedule a pan value at a given time offset.
     * @private
     */
    SonificationInstrument.prototype.setPanAtTime = function (time, pan) {
        if (this.panNode) {
            this.panNode.pan.setTargetAtTime(pan, time + this.audioContext.currentTime, SonificationInstrument.rampTime);
        }
    };
    /**
     * Schedule a filter configuration at a given time offset.
     * @private
     */
    SonificationInstrument.prototype.setFilterAtTime = function (filter, time, frequency, resonance) {
        var node = this[filter + 'Node'], audioTime = this.audioContext.currentTime + time;
        if (node) {
            if (defined(resonance)) {
                node.Q.setTargetAtTime(resonance, audioTime, SonificationInstrument.rampTime);
            }
            if (defined(frequency)) {
                node.frequency.setTargetAtTime(frequency, audioTime, SonificationInstrument.rampTime);
            }
        }
    };
    /**
     * Schedule a volume value at a given time offset.
     * @private
     */
    SonificationInstrument.prototype.setVolumeAtTime = function (time, volume) {
        if (this.volumeNode) {
            this.volumeNode.gain.setTargetAtTime(volume, time + this.audioContext.currentTime, SonificationInstrument.rampTime);
        }
    };
    /**
     * Schedule a tremolo configuration at a given time offset.
     * @private
     */
    SonificationInstrument.prototype.setTremoloAtTime = function (time, depth, speed) {
        var audioTime = this.audioContext.currentTime + time;
        if (this.tremoloDepth && defined(depth)) {
            this.tremoloDepth.gain.setTargetAtTime(depth, audioTime, SonificationInstrument.rampTime);
        }
        if (this.tremoloOsc && defined(speed)) {
            this.tremoloOsc.frequency.setTargetAtTime(15 * speed, audioTime, SonificationInstrument.rampTime);
        }
    };
    /**
     * Create audio nodes according to instrument capabilities
     * @private
     */
    SonificationInstrument.prototype.createNodesFromCapabilities = function (capabilities) {
        var ctx = this.audioContext;
        if (capabilities.pan) {
            this.panNode = new StereoPannerNode(ctx);
        }
        if (capabilities.tremolo) {
            this.tremoloOsc = new OscillatorNode(ctx, {
                type: 'sine',
                frequency: 3
            });
            this.tremoloDepth = new GainNode(ctx);
            this.tremoloOsc.connect(this.tremoloDepth);
            this.tremoloDepth.connect(this.masterVolNode.gain);
            this.tremoloOsc.start();
        }
        if (capabilities.filters) {
            this.lowpassNode = new BiquadFilterNode(ctx, {
                type: 'lowpass',
                frequency: 20000
            });
            this.highpassNode = new BiquadFilterNode(ctx, {
                type: 'highpass',
                frequency: 0
            });
        }
    };
    /**
     * Connect audio node chain from output down to input, depending on which
     * nodes exist.
     * @private
     */
    SonificationInstrument.prototype.connectCapabilityNodes = function (input, output) {
        [
            this.panNode,
            this.lowpassNode,
            this.highpassNode,
            input
        ].reduce(function (prev, cur) {
            return (cur ?
                (cur.connect(prev), cur) :
                prev);
        }, output);
    };
    /**
     * Get number of notes from C0 from a string like "F#4"
     * @static
     * @private
     */
    SonificationInstrument.noteStringToC0Distance = function (note) {
        var match = note.match(/^([a-g][#b]?)([0-8])$/i), semitone = match ? match[1] : 'a', wholetone = semitone[0].toLowerCase(), accidental = semitone[1], octave = match ? parseInt(match[2], 10) : 4, accidentalOffset = accidental === '#' ?
            1 : accidental === 'b' ? -1 : 0;
        return ({
            c: 0, d: 2, e: 4, f: 5, g: 7, a: 9, b: 11
        }[wholetone] || 0) + accidentalOffset + octave * 12;
    };
    /**
     * Convert a note value to a frequency.
     * @static
     * @function Highcharts.SonificationInstrument#musicalNoteToFrequency
     * @param {string|number} note
     * Note to convert. Can be a string 'c0' to 'b8' or a number of semitones
     * from c0.
     * @return {number} The converted frequency
     */
    SonificationInstrument.musicalNoteToFrequency = function (note) {
        var notesFromC0 = typeof note === 'string' ?
            this.noteStringToC0Distance(note) : note;
        return 16.3516 * Math.pow(2, Math.min(notesFromC0, 107) / 12);
    };
    SonificationInstrument.rampTime = SynthPatch.stopRampTime / 4;
    return SonificationInstrument;
}());
/* *
 *
 *  Default Export
 *
 * */
export default SonificationInstrument;
/* *
 *
 *  API definitions
 *
 * */
/**
 * Capabilities configuration for a SonificationInstrument.
 * @requires modules/sonification
 * @interface Highcharts.SonificationInstrumentCapabilitiesOptionsObject
 */ /**
* Whether or not instrument should be able to pan. Defaults to `true`.
* @name Highcharts.SonificationInstrumentCapabilitiesOptionsObject#pan
* @type {boolean|undefined}
*/ /**
* Whether or not instrument should be able to use tremolo effects. Defaults
* to `false`.
* @name Highcharts.SonificationInstrumentCapabilitiesOptionsObject#tremolo
* @type {boolean|undefined}
*/ /**
* Whether or not instrument should be able to use filter effects. Defaults
* to `false`.
* @name Highcharts.SonificationInstrumentCapabilitiesOptionsObject#filters
* @type {boolean|undefined}
*/
/**
 * Configuration for a SonificationInstrument.
 * @requires modules/sonification
 * @interface Highcharts.SonificationInstrumentOptionsObject
 */ /**
* The synth configuration for the instrument. Can be either a string,
* referencing the instrument presets, or an actual SynthPatch configuration.
* @name Highcharts.SonificationInstrumentOptionsObject#synthPatch
* @type {Highcharts.SonificationSynthPreset|Highcharts.SynthPatchOptionsObject}
* @sample highcharts/demo/all-instruments
*      All instrument presets
* @sample highcharts/sonification/custom-instrument
*      Custom instrument preset
*/ /**
* Define additional capabilities for the instrument, such as panning, filters,
* and tremolo effects.
* @name Highcharts.SonificationInstrumentOptionsObject#capabilities
* @type {Highcharts.SonificationInstrumentCapabilitiesOptionsObject|undefined}
*/ /**
* A track name to use for this instrument in MIDI export.
* @name Highcharts.SonificationInstrumentOptionsObject#midiTrackName
* @type {string|undefined}
*/
/**
 * Options for a scheduled event for a SonificationInstrument
 * @requires modules/sonification
 * @interface Highcharts.SonificationInstrumentScheduledEventOptionsObject
 */ /**
* Number of semitones from c0, or a note string - such as "c4" or "F#6".
* @name Highcharts.SonificationInstrumentScheduledEventOptionsObject#note
* @type {number|string|undefined}
*/ /**
* Note frequency in Hertz. Overrides note, if both are given.
* @name Highcharts.SonificationInstrumentScheduledEventOptionsObject#frequency
* @type {number|undefined}
*/ /**
* Duration to play the note in milliseconds. If not given, the note keeps
* playing indefinitely
* @name Highcharts.SonificationInstrumentScheduledEventOptionsObject#noteDuration
* @type {number|undefined}
*/ /**
* Depth/intensity of the tremolo effect - which is a periodic change in
* volume. From 0 to 1.
* @name Highcharts.SonificationInstrumentScheduledEventOptionsObject#tremoloDepth
* @type {number|undefined}
*/ /**
* Speed of the tremolo effect, from 0 to 1.
* @name Highcharts.SonificationInstrumentScheduledEventOptionsObject#tremoloSpeed
* @type {number|undefined}
*/ /**
* Stereo panning value, from -1 (left) to 1 (right).
* @name Highcharts.SonificationInstrumentScheduledEventOptionsObject#pan
* @type {number|undefined}
*/ /**
* Volume of the instrument, from 0 to 1. Can be set independent of the
* master/overall volume.
* @name Highcharts.SonificationInstrumentScheduledEventOptionsObject#volume
* @type {number|undefined}
*/ /**
* Frequency of the lowpass filter, in Hertz.
* @name Highcharts.SonificationInstrumentScheduledEventOptionsObject#lowpassFreq
* @type {number|undefined}
*/ /**
* Resonance of the lowpass filter, in dB. Can be negative for a dip, or
* positive for a bump.
* @name Highcharts.SonificationInstrumentScheduledEventOptionsObject#lowpassResonance
* @type {number|undefined}
*/ /**
* Frequency of the highpass filter, in Hertz.
* @name Highcharts.SonificationInstrumentScheduledEventOptionsObject#highpassFreq
* @type {number|undefined}
*/ /**
* Resonance of the highpass filter, in dB. Can be negative for a dip, or
* positive for a bump.
* @name Highcharts.SonificationInstrumentScheduledEventOptionsObject#highpassResonance
* @type {number|undefined}
*/
(''); // Keep above doclets in JS file
