"use strict";
var x=0;


RegExp.escape = function (s) {
  return s.replace(/[-/\\^$*+?.()|[\]{}]/g, '\\$&');
};

(function () {
  'use strict';

  var $;

  // to keep backwards compatibility
  if (typeof jQuery !== 'undefined' && jQuery) {
    $ = jQuery;
  } else {
    $ = {};
  }

  /**
   * jQuery.csv.defaults
   * Encapsulates the method paramater defaults for the CSV plugin module.
   */

  $.csv = {
    defaults: {
      separator: ',',
      delimiter: '"',
      headers: true
    },

    hooks: {
      castToScalar: function (value, state) {
        var hasDot = /\./;
        if (isNaN(value)) {
          return value;
        } else {
          if (hasDot.test(value)) {
            return parseFloat(value);
          } else {
            var integer = parseInt(value);
            if (isNaN(integer)) {
              return null;
            } else {
              return integer;
            }
          }
        }
      }
    },

    parsers: {
      parse: function (csv, options) {
        // cache settings
        var separator = options.separator;
        var delimiter = options.delimiter;

        // set initial state if it's missing
        if (!options.state.rowNum) {
          options.state.rowNum = 1;
        }
        if (!options.state.colNum) {
          options.state.colNum = 1;
        }

        // clear initial state
        var data = [];
        var entry = [];
        var state = 0;
        var value = '';
        var exit = false;

        function endOfEntry () {
          // reset the state
          state = 0;
          value = '';

          // if 'start' hasn't been met, don't output
          if (options.start && options.state.rowNum < options.start) {
            // update global state
            entry = [];
            options.state.rowNum++;
            options.state.colNum = 1;
            return;
          }

          if (options.onParseEntry === undefined) {
            // onParseEntry hook not set
            data.push(entry);
          } else {
            var hookVal = options.onParseEntry(entry, options.state); // onParseEntry Hook
            // false skips the row, configurable through a hook
            if (hookVal !== false) {
              data.push(hookVal);
            }
          }
          // console.log('entry:' + entry);

          // cleanup
          entry = [];

          // if 'end' is met, stop parsing
          if (options.end && options.state.rowNum >= options.end) {
            exit = true;
          }

          // update global state
          options.state.rowNum++;
          options.state.colNum = 1;
        }

        function endOfValue () {
          if (options.onParseValue === undefined) {
            // onParseValue hook not set
            entry.push(value);
          } else {
            var hook = options.onParseValue(value, options.state); // onParseValue Hook
            // false skips the row, configurable through a hook
            if (hook !== false) {
              entry.push(hook);
            }
          }
          // console.log('value:' + value);
          // reset the state
          value = '';
          state = 0;
          // update global state
          options.state.colNum++;
        }

        // escape regex-specific control chars
        var escSeparator = RegExp.escape(separator);
        var escDelimiter = RegExp.escape(delimiter);

        // compile the regEx str using the custom delimiter/separator
        var match = /(D|S|\r\n|\n|\r|[^DS\r\n]+)/;
        var matchSrc = match.source;
        matchSrc = matchSrc.replace(/S/g, escSeparator);
        matchSrc = matchSrc.replace(/D/g, escDelimiter);
        match = new RegExp(matchSrc, 'gm');

        // put on your fancy pants...
        // process control chars individually, use look-ahead on non-control chars
        csv.replace(match, function (m0) {
          if (exit) {
            return;
          }
          switch (state) {
            // the start of a value
            case 0:
              // null last value
              if (m0 === separator) {
                value += '';
                endOfValue();
                break;
              }
              // opening delimiter
              if (m0 === delimiter) {
                state = 1;
                break;
              }
              // null last value
              if (/^(\r\n|\n|\r)$/.test(m0)) {
                endOfValue();
                endOfEntry();
                break;
              }
              // un-delimited value
              value += m0;
              state = 3;
              break;

            // delimited input
            case 1:
              // second delimiter? check further
              if (m0 === delimiter) {
                state = 2;
                break;
              }
              // delimited data
              value += m0;
              state = 1;
              break;

            // delimiter found in delimited input
            case 2:
              // escaped delimiter?
              if (m0 === delimiter) {
                value += m0;
                state = 1;
                break;
              }
              // null value
              if (m0 === separator) {
                endOfValue();
                break;
              }
              // end of entry
              if (/^(\r\n|\n|\r)$/.test(m0)) {
                endOfValue();
                endOfEntry();
                break;
              }
              // broken paser?
              throw new Error('CSVDataError: Illegal State [Row:' + options.state.rowNum + '][Col:' + options.state.colNum + ']');

            // un-delimited input
            case 3:
              // null last value
              if (m0 === separator) {
                endOfValue();
                break;
              }
              // end of entry
              if (/^(\r\n|\n|\r)$/.test(m0)) {
                endOfValue();
                endOfEntry();
                break;
              }
              if (m0 === delimiter) {
              // non-compliant data
                throw new Error('CSVDataError: Illegal Quote [Row:' + options.state.rowNum + '][Col:' + options.state.colNum + ']');
              }
              // broken parser?
              throw new Error('CSVDataError: Illegal Data [Row:' + options.state.rowNum + '][Col:' + options.state.colNum + ']');
            default:
              // shenanigans
              throw new Error('CSVDataError: Unknown State [Row:' + options.state.rowNum + '][Col:' + options.state.colNum + ']');
          }
          // console.log('val:' + m0 + ' state:' + state);
        });

        // submit the last entry
        // ignore null last line
        if (entry.length !== 0) {
          endOfValue();
          endOfEntry();
        }

        return data;
      },

      // a csv-specific line splitter
      splitLines: function (csv, options) {
        if (!csv) {
          return undefined;
        }

        options = options || {};

        // cache settings
        var separator = options.separator || $.csv.defaults.separator;
        var delimiter = options.delimiter || $.csv.defaults.delimiter;

        // set initial state if it's missing
        options.state = options.state || {};
        if (!options.state.rowNum) {
          options.state.rowNum = 1;
        }

        // clear initial state
        var entries = [];
        var state = 0;
        var entry = '';
        var exit = false;

        function endOfLine () {
          // reset the state
          state = 0;

          // if 'start' hasn't been met, don't output
          if (options.start && options.state.rowNum < options.start) {
            // update global state
            entry = '';
            options.state.rowNum++;
            return;
          }

          if (options.onParseEntry === undefined) {
            // onParseEntry hook not set
            entries.push(entry);
          } else {
            var hookVal = options.onParseEntry(entry, options.state); // onParseEntry Hook
            // false skips the row, configurable through a hook
            if (hookVal !== false) {
              entries.push(hookVal);
            }
          }

          // cleanup
          entry = '';

          // if 'end' is met, stop parsing
          if (options.end && options.state.rowNum >= options.end) {
            exit = true;
          }

          // update global state
          options.state.rowNum++;
        }

        // escape regex-specific control chars
        var escSeparator = RegExp.escape(separator);
        var escDelimiter = RegExp.escape(delimiter);

        // compile the regEx str using the custom delimiter/separator
        var match = /(D|S|\n|\r|[^DS\r\n]+)/;
        var matchSrc = match.source;
        matchSrc = matchSrc.replace(/S/g, escSeparator);
        matchSrc = matchSrc.replace(/D/g, escDelimiter);
        match = new RegExp(matchSrc, 'gm');

        // put on your fancy pants...
        // process control chars individually, use look-ahead on non-control chars
        csv.replace(match, function (m0) {
          if (exit) {
            return;
          }
          switch (state) {
            // the start of a value/entry
            case 0:
              // null value
              if (m0 === separator) {
                entry += m0;
                state = 0;
                break;
              }
              // opening delimiter
              if (m0 === delimiter) {
                entry += m0;
                state = 1;
                break;
              }
              // end of line
              if (m0 === '\n') {
                endOfLine();
                break;
              }
              // phantom carriage return
              if (/^\r$/.test(m0)) {
                break;
              }
              // un-delimit value
              entry += m0;
              state = 3;
              break;

            // delimited input
            case 1:
              // second delimiter? check further
              if (m0 === delimiter) {
                entry += m0;
                state = 2;
                break;
              }
              // delimited data
              entry += m0;
              state = 1;
              break;

            // delimiter found in delimited input
            case 2:
              // escaped delimiter?
              var prevChar = entry.substr(entry.length - 1);
              if (m0 === delimiter && prevChar === delimiter) {
                entry += m0;
                state = 1;
                break;
              }
              // end of value
              if (m0 === separator) {
                entry += m0;
                state = 0;
                break;
              }
              // end of line
              if (m0 === '\n') {
                endOfLine();
                break;
              }
              // phantom carriage return
              if (m0 === '\r') {
                break;
              }
              // broken paser?
              throw new Error('CSVDataError: Illegal state [Row:' + options.state.rowNum + ']');

            // un-delimited input
            case 3:
              // null value
              if (m0 === separator) {
                entry += m0;
                state = 0;
                break;
              }
              // end of line
              if (m0 === '\n') {
                endOfLine();
                break;
              }
              // phantom carriage return
              if (m0 === '\r') {
                break;
              }
              // non-compliant data
              if (m0 === delimiter) {
                throw new Error('CSVDataError: Illegal quote [Row:' + options.state.rowNum + ']');
              }
              // broken parser?
              throw new Error('CSVDataError: Illegal state [Row:' + options.state.rowNum + ']');
            default:
              // shenanigans
              throw new Error('CSVDataError: Unknown state [Row:' + options.state.rowNum + ']');
          }
          // console.log('val:' + m0 + ' state:' + state);
        });

        // submit the last entry
        // ignore null last line
        if (entry !== '') {
          endOfLine();
        }

        return entries;
      },

      // a csv entry parser
      parseEntry: function (csv, options) {
        // cache settings
        var separator = options.separator;
        var delimiter = options.delimiter;

        // set initial state if it's missing
        if (!options.state.rowNum) {
          options.state.rowNum = 1;
        }
        if (!options.state.colNum) {
          options.state.colNum = 1;
        }

        // clear initial state
        var entry = [];
        var state = 0;
        var value = '';

        function endOfValue () {
          if (options.onParseValue === undefined) {
            // onParseValue hook not set
            entry.push(value);
          } else {
            var hook = options.onParseValue(value, options.state); // onParseValue Hook
            // false skips the value, configurable through a hook
            if (hook !== false) {
              entry.push(hook);
            }
          }
          // reset the state
          value = '';
          state = 0;
          // update global state
          options.state.colNum++;
        }

        // checked for a cached regEx first
        if (!options.match) {
          // escape regex-specific control chars
          var escSeparator = RegExp.escape(separator);
          var escDelimiter = RegExp.escape(delimiter);

          // compile the regEx str using the custom delimiter/separator
          var match = /(D|S|\n|\r|[^DS\r\n]+)/;
          var matchSrc = match.source;
          matchSrc = matchSrc.replace(/S/g, escSeparator);
          matchSrc = matchSrc.replace(/D/g, escDelimiter);
          options.match = new RegExp(matchSrc, 'gm');
        }

        // put on your fancy pants...
        // process control chars individually, use look-ahead on non-control chars
        csv.replace(options.match, function (m0) {
          switch (state) {
            // the start of a value
            case 0:
              // null last value
              if (m0 === separator) {
                value += '';
                endOfValue();
                break;
              }
              // opening delimiter
              if (m0 === delimiter) {
                state = 1;
                break;
              }
              // skip un-delimited new-lines
              if (m0 === '\n' || m0 === '\r') {
                break;
              }
              // un-delimited value
              value += m0;
              state = 3;
              break;

            // delimited input
            case 1:
              // second delimiter? check further
              if (m0 === delimiter) {
                state = 2;
                break;
              }
              // delimited data
              value += m0;
              state = 1;
              break;

            // delimiter found in delimited input
            case 2:
              // escaped delimiter?
              if (m0 === delimiter) {
                value += m0;
                state = 1;
                break;
              }
              // null value
              if (m0 === separator) {
                endOfValue();
                break;
              }
              // skip un-delimited new-lines
              if (m0 === '\n' || m0 === '\r') {
                break;
              }
              // broken paser?
              throw new Error('CSVDataError: Illegal State [Row:' + options.state.rowNum + '][Col:' + options.state.colNum + ']');

            // un-delimited input
            case 3:
              // null last value
              if (m0 === separator) {
                endOfValue();
                break;
              }
              // skip un-delimited new-lines
              if (m0 === '\n' || m0 === '\r') {
                break;
              }
              // non-compliant data
              if (m0 === delimiter) {
                throw new Error('CSVDataError: Illegal Quote [Row:' + options.state.rowNum + '][Col:' + options.state.colNum + ']');
              }
              // broken parser?
              throw new Error('CSVDataError: Illegal Data [Row:' + options.state.rowNum + '][Col:' + options.state.colNum + ']');
            default:
              // shenanigans
              throw new Error('CSVDataError: Unknown State [Row:' + options.state.rowNum + '][Col:' + options.state.colNum + ']');
          }
          // console.log('val:' + m0 + ' state:' + state);
        });

        // submit the last value
        endOfValue();

        return entry;
      }
    },

    helpers: {

      /**
       * $.csv.helpers.collectPropertyNames(objectsArray)
       * Collects all unique property names from all passed objects.
       *
       * @param {Array} objects Objects to collect properties from.
       *
       * Returns an array of property names (array will be empty,
       * if objects have no own properties).
       */
      collectPropertyNames: function (objects) {
        var o = [];
        var propName = [];
        var props = [];
        for (o in objects) {
          for (propName in objects[o]) {
            if ((objects[o].hasOwnProperty(propName)) &&
                (props.indexOf(propName) < 0) &&
                (typeof objects[o][propName] !== 'function')) {
              props.push(propName);
            }
          }
        }
        return props;
      }
    },

    /**
     * $.csv.toArray(csv)
     * Converts a CSV entry string to a javascript array.
     *
     * @param {Array} csv The string containing the CSV data.
     * @param {Object} [options] An object containing user-defined options.
     * @param {Character} [separator] An override for the separator character. Defaults to a comma(,).
     * @param {Character} [delimiter] An override for the delimiter character. Defaults to a double-quote(").
     *
     * This method deals with simple CSV strings only. It's useful if you only
     * need to parse a single entry. If you need to parse more than one line,
     * use $.csv2Array instead.
     */
    toArray: function (csv, options, callback) {
      options = (options !== undefined ? options : {});
      var config = {};
      config.callback = ((callback !== undefined && typeof (callback) === 'function') ? callback : false);
      config.separator = 'separator' in options ? options.separator : $.csv.defaults.separator;
      config.delimiter = 'delimiter' in options ? options.delimiter : $.csv.defaults.delimiter;
      var state = (options.state !== undefined ? options.state : {});

      // setup
      options = {
        delimiter: config.delimiter,
        separator: config.separator,
        onParseEntry: options.onParseEntry,
        onParseValue: options.onParseValue,
        state: state
      };

      var entry = $.csv.parsers.parseEntry(csv, options);

      // push the value to a callback if one is defined
      if (!config.callback) {
        return entry;
      } else {
        config.callback('', entry);
      }
    },

    /**
     * $.csv.toArrays(csv)
     * Converts a CSV string to a javascript array.
     *
     * @param {String} csv The string containing the raw CSV data.
     * @param {Object} [options] An object containing user-defined options.
     * @param {Character} [separator] An override for the separator character. Defaults to a comma(,).
     * @param {Character} [delimiter] An override for the delimiter character. Defaults to a double-quote(").
     *
     * This method deals with multi-line CSV. The breakdown is simple. The first
     * dimension of the array represents the line (or entry/row) while the second
     * dimension contains the values (or values/columns).
     */
    toArrays: function (csv, options, callback) {
      options = (options !== undefined ? options : {});
      var config = {};
      config.callback = ((callback !== undefined && typeof (callback) === 'function') ? callback : false);
      config.separator = 'separator' in options ? options.separator : $.csv.defaults.separator;
      config.delimiter = 'delimiter' in options ? options.delimiter : $.csv.defaults.delimiter;

      // setup
      var data = [];
      options = {
        delimiter: config.delimiter,
        separator: config.separator,
        onPreParse: options.onPreParse,
        onParseEntry: options.onParseEntry,
        onParseValue: options.onParseValue,
        onPostParse: options.onPostParse,
        start: options.start,
        end: options.end,
        state: {
          rowNum: 1,
          colNum: 1
        }
      };

      // onPreParse hook
      if (options.onPreParse !== undefined) {
        options.onPreParse(csv, options.state);
      }

      // parse the data
      data = $.csv.parsers.parse(csv, options);

      // onPostParse hook
      if (options.onPostParse !== undefined) {
        options.onPostParse(data, options.state);
      }

      // push the value to a callback if one is defined
      if (!config.callback) {
        return data;
      } else {
        config.callback('', data);
      }
    },

    /**
     * $.csv.toObjects(csv)
     * Converts a CSV string to a javascript object.
     * @param {String} csv The string containing the raw CSV data.
     * @param {Object} [options] An object containing user-defined options.
     * @param {Character} [separator] An override for the separator character. Defaults to a comma(,).
     * @param {Character} [delimiter] An override for the delimiter character. Defaults to a double-quote(").
     * @param {Boolean} [headers] Indicates whether the data contains a header line. Defaults to true.
     *
     * This method deals with multi-line CSV strings. Where the headers line is
     * used as the key for each value per entry.
     */
    toObjects: function (csv, options, callback) {
      options = (options !== undefined ? options : {});
      var config = {};
      config.callback = ((callback !== undefined && typeof (callback) === 'function') ? callback : false);
      config.separator = 'separator' in options ? options.separator : $.csv.defaults.separator;
      config.delimiter = 'delimiter' in options ? options.delimiter : $.csv.defaults.delimiter;
      config.headers = 'headers' in options ? options.headers : $.csv.defaults.headers;
      options.start = 'start' in options ? options.start : 1;

      // account for headers
      if (config.headers) {
        options.start++;
      }
      if (options.end && config.headers) {
        options.end++;
      }

      // setup
      var lines = [];
      var data = [];

      options = {
        delimiter: config.delimiter,
        separator: config.separator,
        onPreParse: options.onPreParse,
        onParseEntry: options.onParseEntry,
        onParseValue: options.onParseValue,
        onPostParse: options.onPostParse,
        start: options.start,
        end: options.end,
        state: {
          rowNum: 1,
          colNum: 1
        },
        match: false,
        transform: options.transform
      };

      // fetch the headers
      var headerOptions = {
        delimiter: config.delimiter,
        separator: config.separator,
        start: 1,
        end: 1,
        state: {
          rowNum: 1,
          colNum: 1
        }
      };

      // onPreParse hook
      if (options.onPreParse !== undefined) {
        options.onPreParse(csv, options.state);
      }

      // parse the csv
      var headerLine = $.csv.parsers.splitLines(csv, headerOptions);
      var headers = $.csv.toArray(headerLine[0], options);

      // fetch the data
      lines = $.csv.parsers.splitLines(csv, options);

      // reset the state for re-use
      options.state.colNum = 1;
      if (headers) {
        options.state.rowNum = 2;
      } else {
        options.state.rowNum = 1;
      }

      // convert data to objects
      for (var i = 0, len = lines.length; i < len; i++) {
        var entry = $.csv.toArray(lines[i], options);
        var object = {};
        for (var j = 0; j < headers.length; j++) {
          object[headers[j]] = entry[j];
        }
        if (options.transform !== undefined) {
          data.push(options.transform.call(undefined, object));
        } else {
          data.push(object);
        }

        // update row state
        options.state.rowNum++;
      }

      // onPostParse hook
      if (options.onPostParse !== undefined) {
        options.onPostParse(data, options.state);
      }

      // push the value to a callback if one is defined
      if (!config.callback) {
        return data;
      } else {
        config.callback('', data);
      }
    },

    /**
    * $.csv.fromArrays(arrays)
    * Converts a javascript array to a CSV String.
    *
    * @param {Array} arrays An array containing an array of CSV entries.
    * @param {Object} [options] An object containing user-defined options.
    * @param {Character} [separator] An override for the separator character. Defaults to a comma(,).
    * @param {Character} [delimiter] An override for the delimiter character. Defaults to a double-quote(").
    *
    * This method generates a CSV file from an array of arrays (representing entries).
    */
    fromArrays: function (arrays, options, callback) {
      options = (options !== undefined ? options : {});
      var config = {};
      config.callback = ((callback !== undefined && typeof (callback) === 'function') ? callback : false);
      config.separator = 'separator' in options ? options.separator : $.csv.defaults.separator;
      config.delimiter = 'delimiter' in options ? options.delimiter : $.csv.defaults.delimiter;

      var output = '';
      var line;
      var lineValues;
      var i;
      var j;

      for (i = 0; i < arrays.length; i++) {
        line = arrays[i];
        lineValues = [];
        for (j = 0; j < line.length; j++) {
          var strValue = (line[j] === undefined || line[j] === null) ? '' : line[j].toString();
          if (strValue.indexOf(config.delimiter) > -1) {
            strValue = strValue.replace(new RegExp(config.delimiter, 'g'), config.delimiter + config.delimiter);
          }

          var escMatcher = '\n|\r|S|D';
          escMatcher = escMatcher.replace('S', config.separator);
          escMatcher = escMatcher.replace('D', config.delimiter);

          if (strValue.search(escMatcher) > -1) {
            strValue = config.delimiter + strValue + config.delimiter;
          }
          lineValues.push(strValue);
        }
        output += lineValues.join(config.separator) + '\n';
      }

      // push the value to a callback if one is defined
      if (!config.callback) {
        return output;
      } else {
        config.callback('', output);
      }
    },

    /**
     * $.csv.fromObjects(objects)
     * Converts a javascript dictionary to a CSV string.
     *
     * @param {Object} objects An array of objects containing the data.
     * @param {Object} [options] An object containing user-defined options.
     * @param {Character} [separator] An override for the separator character. Defaults to a comma(,).
     * @param {Character} [delimiter] An override for the delimiter character. Defaults to a double-quote(").
     * @param {Character} [sortOrder] Sort order of columns (named after
     *   object properties). Use 'alpha' for alphabetic. Default is 'declare',
     *   which means, that properties will _probably_ appear in order they were
     *   declared for the object. But without any guarantee.
     * @param {Character or Array} [manualOrder] Manually order columns. May be
     * a strin in a same csv format as an output or an array of header names
     * (array items won't be parsed). All the properties, not present in
     * `manualOrder` will be appended to the end in accordance with `sortOrder`
     * option. So the `manualOrder` always takes preference, if present.
     *
     * This method generates a CSV file from an array of objects (name:value pairs).
     * It starts by detecting the headers and adding them as the first line of
     * the CSV file, followed by a structured dump of the data.
     */
    fromObjects: function (objects, options, callback) {
      options = (options !== undefined ? options : {});
      var config = {};
      config.callback = ((callback !== undefined && typeof (callback) === 'function') ? callback : false);
      config.separator = 'separator' in options ? options.separator : $.csv.defaults.separator;
      config.delimiter = 'delimiter' in options ? options.delimiter : $.csv.defaults.delimiter;
      config.headers = 'headers' in options ? options.headers : $.csv.defaults.headers;
      config.sortOrder = 'sortOrder' in options ? options.sortOrder : 'declare';
      config.manualOrder = 'manualOrder' in options ? options.manualOrder : [];
      config.transform = options.transform;

      if (typeof config.manualOrder === 'string') {
        config.manualOrder = $.csv.toArray(config.manualOrder, config);
      }

      if (config.transform !== undefined) {
        var origObjects = objects;
        objects = [];

        var i;
        for (i = 0; i < origObjects.length; i++) {
          objects.push(config.transform.call(undefined, origObjects[i]));
        }
      }

      var props = $.csv.helpers.collectPropertyNames(objects);

      if (config.sortOrder === 'alpha') {
        props.sort();
      } // else {} - nothing to do for 'declare' order

      if (config.manualOrder.length > 0) {
        var propsManual = [].concat(config.manualOrder);
        var p;
        for (p = 0; p < props.length; p++) {
          if (propsManual.indexOf(props[p]) < 0) {
            propsManual.push(props[p]);
          }
        }
        props = propsManual;
      }

      var o;
      var line;
      var output = [];
      var propName;
      if (config.headers) {
        output.push(props);
      }

      for (o = 0; o < objects.length; o++) {
        line = [];
        for (p = 0; p < props.length; p++) {
          propName = props[p];
          if (propName in objects[o] && typeof objects[o][propName] !== 'function') {
            line.push(objects[o][propName]);
          } else {
            line.push('');
          }
        }
        output.push(line);
      }

      // push the value to a callback if one is defined
      return $.csv.fromArrays(output, options, config.callback);
    }
  };

  // Maintenance code to maintain backward-compatibility
  // Will be removed in release 1.0
  $.csvEntry2Array = $.csv.toArray;
  $.csv2Array = $.csv.toArrays;
  $.csv2Dictionary = $.csv.toObjects;

  // CommonJS module is defined
  if (typeof module !== 'undefined' && module.exports) {
    module.exports = $.csv;
  }
}).call(this);


var MetodoEnum = {
  Login:1,
  Logout:2,
  ActualizaDash:3,
  ActualizaDashreport:4,
  Insertageneral:5,
  ActualizaLegales:6,
  ActualizaFuncionalidad:7,
  ExisteCupon:8,
  CargarCupones:9,
  ActualizaPlantillashtml:10,
  ActualizaPlantilla:11,
  CreaEditaVersionPlantilla:14,
  CreaDirectorio:16,
  GetPromoPlantilla:17
 };
 var codigo='';
 var idnvaprom=0;
 var exist=[];
 var nuevos=[];
 var infopromocrear=[];
 var infopromoedit=[];
 var bancarga=0;
 var plantseledit='';

function _(el){return document.querySelector(el); }
function __(el){return document.querySelectorAll(el); }
function showLoading(e){
  var wr = _("#loadingConf");
  if(e === 1){
    wr.setAttribute("style", " ");
  } else {
    wr.style.display = "none";
  }
}
function promoTabs(p,t){
  var w = _("#promoTabs"),
      ch = __(".tabButtons");
  w.style.left = p;
  for (var i = 0; i < ch.length; i++) {
    ch[i].setAttribute("class", "trans5 tabButtons");
  }
  t.setAttribute("class", "trans5 tabButtons selectTab");
}
function promoTabsrep(p,t){
  var w = _("#headerreport");
  promoTabs(p,t);
  if(p === "-100%"){
    w.setAttribute("style", " ");
  } else{
    w.setAttribute("style", "display:none");
  }
}

function menuMobile(e, t){
  var w = _("#menuMobile"),
      b = _('.login, .body'),
      c = t.children[0];
  if(e === "open"){
    b.style.position = "fixed";
    w.setAttribute("class", "displayFlex trans5");
    t.setAttribute("onclick", "menuMobile('close', this)");
    c.setAttribute("class", "hamburgerMenu menuCross displayFlex");
    setTimeout(function(){
      w.style.opacity = "1";
      c.setAttribute("class", "hamburgerMenu hoverCross menuCross displayFlex");
    },700);
  } else {
    b.style.position = "absolute";
    w.style.opacity = "0";
    t.setAttribute("onclick", "menuMobile('open', this)");
    c.setAttribute("class", "hamburgerMenu hamburgerHover displayFlex");
    setTimeout(function(){
      w.setAttribute("class", "displayNone trans5");
    },700);
  }
}
function errorOnLog(e){
  var w = _("#errorLog");
  if(e === "open"){
    w.style.height = "70px";
  } else {
    w.style.height = "0";
  }
}

function login(){
  var usr=_("#userNameLog").value;
  var psw=_("#userPassLog").value;
  var method=MetodoEnum.Login;
  errorOnLog('close');
  //$('#errorLog').css("height", "0px");
  var dataString ='&usr=' + usr + '&pwd=' + psw+'&m='+method;
      $.ajax({
         type : 'POST',
         url  : 'respuestaconfig.php',
         data:  dataString,

         success:function(data) {
           if(data=='Ambos valores son requerido'|| data=='Error con usuario')
           {
               //$('#errorLog').css("height", "65px");
               errorOnLog('open');
           }
           else {
              window.location.href='home.php';
           }

         }
      });
}
function logout()
{
  var method=MetodoEnum.Logout;
  var dataString ='&m='+method;
  $.ajax({
     type : 'POST',
     url  : 'respuestaconfig.php',
     data:  dataString,

     success:function(data) {
       if(data=='success')
       {
         window.location.href='login.php';
       }

     }
  });
}

if(_('#userPassLog')!=null)
{
$("#userPassLog").keypress(function (e)  {
  if (e.keyCode == 13) {
    $("#submitLogin").click();
  }
});
}
function actualizaDatos(p){

  var param3=MetodoEnum.ActualizaDash;
  var param4=p;  // promo
  var dataString = '&m=' + param3+'&prom=' + param4;
  //console.log(dataString);
   $.ajax({
    type : 'POST',
    url  : 'respuestaconfig.php',
    data:  dataString,
    success:function(data) {
      //console.log(data);
      //$("#cupEntregadosHoy").text(data.split(";")[0]); // Cupones entregados hoy
      //$("#cupEntregados").text(data.split(";")[1]); // Cupones entregados
      //$("#cupDisponibles").text(data.split(";")[2]); // Cupones Disponibles
      //$("#cupEntregadosPorc").text(data.split(";")[3]); // Cupones entregados %
    //  $("#cupDisponiblesPorc").text(data.split(";")[4]); // Cupones disponibles %
      //$("#cupUltimo").text(data.split(";")[6]); // Cupones último
      //x=60;
     $('#activePromoWrap').html(data).fadeIn();
    }
  });
  param3=MetodoEnum.ActualizaDashreport;
  dataString = '&m=' + param3+'&prom=' + param4;
  //console.log(dataString);
  $.ajax({
    type : 'POST',
    url  : 'respuestaconfig.php',
    data:  dataString,
    success:function(data) {
      //console.log(data);
      //$("#cupEntregadosHoy").text(data.split(";")[0]); // Cupones entregados hoy
      //$("#cupEntregados").text(data.split(";")[1]); // Cupones entregados
      //$("#cupDisponibles").text(data.split(";")[2]); // Cupones Disponibles
      //$("#cupEntregadosPorc").text(data.split(";")[3]); // Cupones entregados %
    //  $("#cupDisponiblesPorc").text(data.split(";")[4]); // Cupones disponibles %
      //$("#cupUltimo").text(data.split(";")[6]); // Cupones último
      //x=60;
     $('#forActivationWrap').html(data).fadeIn();
    }
  });
}

function putUserName(){
  var p = __(".userName");
  for (var i = 0; i < p.length; i++) {
    p[i].innerHTML = "Blablah"; //Nombre de usuario
  }
}

function closeLog(){
  console.log("Cerrar log");
}

function getPassForgot(){
  console.log("Recuperar correo");
}

function menuConfig(e){
  var w = _("#extMenu");
  if(e === "open"){
    w.style.left = "0";
  } else if (e === "close") {
    w.style.left = "-100vw";
  }
}

function hideMenuConfig(e, t){
  var m, mSub, w, foot, logo, dragon, index, ul;
  var elems = [m = _("#menuConfigNav"),
              mSub = _("#menuConfig"),
              index = _("#indexConfig"),
              ul = _("#menuConfig>ul"),
              w = _("#configWrap"),
              foot = _("#configFoot"),
              logo = _("#menuConfigNav > div> .logo"),
              dragon = _("#dragonConfig")];
  if(e === "hide"){
      t.setAttribute("onclick", "hideMenuConfig('show', this)");
      m.style.width  = "50px";
      w.style.width = "calc(100% - 50px)";
      mSub.style.margin = "5px";
      index.style.top = "120px";
      ul.style.height = "100px";
      dragon.style.display = "block";
      logo.style.display = "none";
      foot.style.display = "none";
  } else if(e === "show") {
    t.setAttribute("onclick", "hideMenuConfig('hide', this)");
    for (var i = 0; i < elems.length; i++) {
      elems[i].setAttribute("style", " ");
    }
  }
}

function loadFileName(t){
  var file = t.nextElementSibling,
      fileNameW = t.children[1];
  file.addEventListener("change", function(){
    fileNameW.innerHTML = file.files[0].name;
  });
}
function savedatageneral(ns,t)
{



  var fi=$('#fechaInicio')[0].value;
  var ff=$('#fechaFin')[0].value;
  var nom=$('#nombrePromo')[0].value;
  var desc=$('#descripcionPromo')[0].value;
  var mar=$('#selectBrand')[0].value;
  var pro=$('#selectProvider')[0].value;
  var  m=MetodoEnum.Insertageneral;
  var dataString = 'm=' + m+'&fi=' + fi+'&ff=' + ff+'&nom=' + nom+'&desc=' + desc+'&mar=' + mar+'&pro=' + pro+'&idnvaprom=' + idnvaprom;
  $.ajax({
    type : 'POST',
    url  : 'respuestaconfig.php',
    data:  dataString,
    success:function(data) {
      //console.log(data);
      if(data=='fallo sql insert')
      {
        responseStep(ns, t, 0);
      }
      else {
        sendfile(data);
        idnvaprom=data;
        responseStep(ns, t, 1);
      }

    }
  });


}
function sendfile(id){
  if($('#legalesUpload')[0].files.length>0)
  {

    var rutalegales='legales/';
    let files = new FormData(), // you can consider this as 'data bag'
        url = 'upload.php';

    files.append('fileName',$('#legalesUpload')[0].files[0]); // append selected file to the bag named 'file'
    files.append('id',id);
    files.append('dir',rutalegales);
    $.ajax({
        type: 'post',
        url: url,
        processData: false,
        contentType: false,
        data: files,
        success: function (response) {
            //console.log(response);
            actualizalegales(id,response)
        },
        error: function (err) {
            console.log(err);
        }
    });
  }
}

function actualizalegales(id,url)
{
  var m=MetodoEnum.ActualizaLegales;
  var dataString = 'm=' + m+'&id=' + id+'&url=' + url;
  $.ajax({
    type : 'POST',
    url  : 'respuestaconfig.php',
    data:  dataString,
    success:function(data) {
      //console.log(data);
    }
  });

}
function sliderConfigFun(e){
  var w = _("#sliderConfig"),
      bullet = __(".bulletStep>circle");
      clearBullets();
      completedSteps(e);
      if(e < 5){
        bullet[e].setAttribute("class", "stepInProgress trans5");
      }
  switch (e) {
    case 0:
     w.style.marginLeft = "0";
    break;
    case 1:
     w.style.marginLeft = "-100%";
    break;
    case 2:
     w.style.marginLeft = "-200%";
    break;
    case 3:
     w.style.marginLeft = "-300%";
    break;
    case 4:
     w.style.marginLeft = "-400%";
     changeScreen(0);
    break;
  }
  function clearBullets(){
    for (var i = 0; i < bullet.length; i++) {
      bullet[i].setAttribute("class", "stepUncomplete trans5");
    }
  }
  function completedSteps(e){
    for (var i = e; i > 0; i--) {
      bullet[i-1].setAttribute("class", "stepComplete trans5");
    }
  }
}

var numCupones, csvLoaded = false;

function loadFileCSV(t){
  var file = t.nextElementSibling,
      fileText = _("#msgCsvUpload"),
      imgCheck = _("#imgCheck"),
      close = fileText.nextElementSibling,
      infoLoadCSV = __(".infoLoadCSV");
  file.addEventListener("change", function(){
    for (var i = 0; i < infoLoadCSV.length; i++) {
      infoLoadCSV[i].style.opacity = "0";
      infoLoadCSV[i].style.display = "none";
    }
    csvLoaded = true;
    imgCheck.setAttribute("src", "ui/img/ic/check.svg");
    fileText.innerHTML = file.files[0].name;
    close.setAttribute("class", "displayBlock trans5");
  });
}

function cleanCsv(t){
  var form = _("#formCSV"),
      input = _("#couponsUpload"),
      text = _("#msgCsvUpload"),
      imgCheck = _("#imgCheck"),
      infoLoadCSV = __(".infoLoadCSV");
      $('#couponsUpload')[0].value= null;
      exist=[];
      nuevos=[];
      csvLoaded = false;
      for (var i = 0; i < infoLoadCSV.length; i++) {
        infoLoadCSV[i].style.opacity = "0";
        infoLoadCSV[i].style.display = "none";
      }
      imgCheck.setAttribute("src", "ui/img/ic/upload.svg");
      t.setAttribute("class", "displayNone trans5");
      text.innerHTML = "Escoge un archivo .csv";
}


function getNumCSV(){

  var textCSVLoaded = _(".numCSV"),
      textCSVNoLoaded = _(".noneNumCSV"),
      numCSVW = _("#numCSV");

  if(csvLoaded){
    showLoading(1);
    readtextcsv(function(val){
       var p=val.split('\r\n');
       var c=p.length-1;
       p.splice(c,1);
       p.splice(0,1);
       existecupon(p)

    });
  } else {
    textCSVNoLoaded.style.display = "block";
    setTimeout(function(){
      textCSVNoLoaded.style.opacity = "1";
    },500);
  }


}

var compactMenu = false;
function checkSteps(n, t){
  switch (n) {
    case 1:
      checkConfig_1(n,t);
    break;
    case 2:
      ischeckedsome(n,t);
    break;
    case 3:
      checkcuponstoload(n,t);

    break;
    case 4:
      ischeckedsometheme(n,t);
    break;
    case 5:
      //  responseStep(n , t, 1);
      if(nuevos.length>0)
      {
        loadcupons(n,t);
      }
      else {
        checksaveversion();
      }

    break;
  }
}

function compactConfigMenu(n){
  var bArrow = _("#toggleMenuArrow"),
      bArrowImg = bArrow.children[0],
      menuConfig = _("#menuConfig");
  if(n === 0){
    bArrow.style.display = "none";
    hideMenuConfig('hide', bArrow);
    menuConfig.setAttribute("style", " ");
  } else if (n === 1) {
      bArrow.style.display = "block";
      hideMenuConfig('show', bArrow);
  }
}

function checkConfig_1(n, t){
  var nom,desc,mar,pro,fi,ff,url;
  var d=new Date();
  var datesinhoras=new Date(d.getFullYear()+'/'+(d.getMonth()+1)+'/'+d.getDate());
  if($('#fechaInicio')[0].value!=''&&datesinhoras.toISOString().slice(0,10)<=$('#fechaInicio')[0].value){
    fi=1;
  }
  else {
    fi=0;
  }
  if($('#fechaFin')[0].value!=''&&$('#fechaInicio')[0].value!=''&&$('#fechaInicio')[0].value>=$('#fechaInicio')[0].value){
    ff=1;
  }
  else {
    ff=0;
  }
  if($('#nombrePromo')[0].value!=''&&$('#nombrePromo')[0].value!=undefined){
    nom=1;
  }
  else {
    nom=0;
  }
  if($('#nombreURL')[0].value!=''&&$('#nombreURL')[0].value!=undefined){
    url=1;
  }
  else {
    url=0;
  }
  //nombreURL
  if($('#descripcionPromo')[0].value!=''&&$('#descripcionPromo')[0].value!=undefined){
    desc=1;
  }
  else {
    desc=0;
  }
  if($('#selectBrand')[0].value!=''&&$('#selectBrand')[0].value!=undefined){
    mar=1;
  }
  else {
    mar=0;
  }
  if($('#selectProvider')[0].value!=''&&$('#selectProvider')[0].value!=undefined){
    pro=1;
  }
  else {
    pro=0;
  }
  /* Comprobar Configuración 1 */
  //NUM SLIDER, THIS, nombre, descripción, marca, fecha Inicio, fecha Final
  responseConfig_1(n, t,nom,desc,mar,pro,fi,ff,url);
}

function responseConfig_1(ns,t,n, d, m, p, fInit, fLast,url){
  var g = n + d + m + p + fInit + fLast+url;
  var labels = __(".labelData1"),
      inputs = __(".inputData1");
      for (var i = 0; i < labels.length; i++) {
        labels[i].setAttribute("style", " ");
        inputs[i].setAttribute("style", " ");
      }
  if(g === 7){

    savedatageneral(ns,t);

  } else if (g < 7) {
    responseStep(ns, t, 0);
  }
  if(n === 0){ redField(0); } if(d === 0){ redField(1); } if(m === 0){ redField(2); } if(p === 0){ redField(3); } if(fInit === 0){ redField(4); } if(fLast === 0){ redField(5); } if(url === 0){ redField(6); }
  function redField(n){
    labels[n].style.color = "#D8353D";
    inputs[n].style.color = "#D8353D";
  }
}

function responseStep(n, t, c){
  var msgW = t.parentElement.parentElement.children[0],
      msgTx = msgW.children[0];
  if(c === 0){
    msgW.style.height = "40px";
    msgW.style.opacity = "1";
  } else if (c === 1) {
    msgW.setAttribute("style", " ");
    sliderConfigFun(n);
  }
}

function openLinks(c,t){
  var p = t.parentElement.parentElement,
      ul = p.lastElementChild;
  var w = window.innerWidth;
  if(c === "open"){
    t.setAttribute("onclick", "openLinks('close', this)");
    if (w>880) { ul.style.display = "block";}
    p.style.height = "125px";
    ul.style.height = "75px";
  } else if (c === "close") {
    t.setAttribute("onclick", "openLinks('open', this)");
    p.style.height = "auto";
    ul.style.height = "0";
    ul.style.display = "none";
  }
}
function rContEditor(wr){
  var wr = _(wr),
      w = window.innerWidth - 350,
      r = 1080/1920,
      h = w * r;
      wr.style.height = h+"px";
}

var countSliderMobScreens = 0;
function sliderMobScreens(n){
  var pageTx = ["Carga", "Inicio", "Cupón", "Mensaje Éxito", "Mensaje Error"],
      p = _("#pagScreen"),
      i = _("#indexPagScreen");
  if(n === "next"){
      countSliderMobScreens++;
      if(countSliderMobScreens > 4) countSliderMobScreens = 0;
      p.innerHTML = pageTx[countSliderMobScreens];
      i.innerHTML = countSliderMobScreens + 1;
      changeScreen(countSliderMobScreens);
  } else {
      countSliderMobScreens--;
      if(countSliderMobScreens < 0) countSliderMobScreens = 4;
      p.innerHTML = pageTx[countSliderMobScreens];
      i.innerHTML = countSliderMobScreens + 1;
      changeScreen(countSliderMobScreens);
  }
}

function selectClassScreens(n){
  var idxs = __(".indexScreenDesk");
  for (var i = 0; i < idxs.length; i++) {
    idxs[i].setAttribute("class", "indexScreenDesk screenDeskUnselect");
  }
  idxs[n].setAttribute("class", "indexScreenDesk screenDeskSelect");
}

function changeScreen(n){
  var frame = _("#iframePlantilla"),
      frameIF = _("#iframeInterfaz");
  frame.contentWindow.screensOnConf(n);
  frameIF.contentWindow.changeScreenInterfaz(n);
  selectClassScreens(n);
  optionsConfig(n);
}
function optionsConfig(n){
  var ban=0;
  do {
    if(_("#iframePlantilla").contentWindow.document.getElementById("plantillaUnoHTML")!==null)
    {
      ban=1;
    }
      // execute code
  } while (ban==0);


  var m = _("#iframePlantilla").contentWindow.document.getElementById("plantillaUnoHTML").getAttribute("data-marca"),
      opts = __(".optionStep"),
      opsLoad = _("#optionsCargando"),
      opsHome = _("#optionsInicio");
  switch (n) {
    case 0:
      clearAllOptions();
      changeColorBrand(m, "#optionsCargando");
      opsLoad.setAttribute("class", "optionStep displayFlex");
    break;
    case 1:
      clearAllOptions();
      changeColorBrand(m, "#optionsInicio");
      opsHome.setAttribute("class", "optionStep displayFlex");
    break;
    case 2:
      clearAllOptions();
    break;
    case 3:
      clearAllOptions();
    break;
    case 4:
      clearAllOptions();
    break;
  }
  function clearAllOptions(){
    for (var i = 0; i < opts.length; i++) {
      opts[i].setAttribute("class", "optionStep displayNone");
    }
  }
  function changeColorBrand(m, form){
    var form = _(form),
        formsColor = form.querySelectorAll(".optionsColor");
    switch (m) {
      case "pepsi":
        clearAllForms();
        formsColor[1].setAttribute("class", "optionsColor displayFlex");
      break;
      case "gatorade":
        clearAllForms();
        formsColor[2].setAttribute("class", "optionsColor displayFlex");
      break;
      case "sevenup":
        clearAllForms();
        formsColor[3].setAttribute("class", "optionsColor displayFlex");
      break;
      case "epura":
        clearAllForms();
        formsColor[4].setAttribute("class", "optionsColor displayFlex");
      break;
      case "frutzzo":
        clearAllForms();
        formsColor[5].setAttribute("class", "optionsColor displayFlex");
      break;
    }
    function clearAllForms(){
      for (var i = 1; i < formsColor.length; i++) {
        formsColor[i].setAttribute("class", "optionsColor displayNone");
      }
    }
  }
}

function hideInterfaz(e, t){
  var f = t.previousElementSibling,
      ic = t.children[1];
  if(e === "hide"){
    t.setAttribute("onclick", "hideInterfaz('show', this)");
    f.style.opacity = "0";
    ic.style.opacity = "1";
  } else {
    t.setAttribute("onclick", "hideInterfaz('hide', this)");
    f.style.opacity = "1";
    ic.setAttribute("style", " ");
  }
}
function popActionFun(e, tx, fun){
  var wr = _("#popAction"),
      yes = _("#popAction>div>div>.doAction"),
      p = _("#popAction>div>p"),
      main = _("main");
  if(e === "show"){
    wr.setAttribute("class", "displayFlex");
    yes.setAttribute("onclick", fun);
    main.classList.add("blur");
    if(tx != 0){
      p.innerHTML = tx;
    } else {
      p.innerHTML = "¿Estás seguro que quieres realizar esta acción?";
    }
  } else {
    wr.setAttribute("class", "displayNone");
    main.classList.remove("blur");
    p.innerHTML = "¿Estás seguro que quieres realizar esta acción?";
  }
}
function popInfoFun(e, tx){
  var wr = _("#popInfo"),
      p = _("#popInfo>div>p"),
      main = _("main");
  if(e === "show"){
    wr.setAttribute("class", "displayFlex");
    main.classList.add("blur");
    if(tx != 0){
      p.innerHTML = tx;
    } else {
      p.innerHTML = "";
    }
  } else {
    wr.setAttribute("class", "displayNone");
    main.classList.remove("blur");
    p.innerHTML = "";
  }
}
function uncheckedfunctionall(t){
      var c = __('.checkBoxFunction');
      if(t.checked)
      {
        for (var i = 0; i < c.length; i++) {
          c[i].checked=false;
        }
        t.checked=true;
      }
      actualizaplantilla(t.value)
}
function uncheckedthemeall(t){
      var c = __('.checkBoxTheme');
      if(t.checked)
      {
        for (var i = 0; i < c.length; i++) {
          c[i].checked=false;
        }
        t.checked=true;
        if(bancarga==1)
        {
          infopromoedit[4]=t.value;
        }
      }

}
function ischeckedsome(n,t){
  var c = __('.checkBoxFunction');
  var id='';
  for (var i = 0; i < c.length; i++) {
    if(c[i].checked){
       id=c[i].value;
       i=c.length+1;
    }
  }
  if(id!='')
  {
    actualizafuncionalidad(n,t,id);
  }
  else {
    responseStep(n,t,0);
  }

}
function ischeckedsometheme(n,t){
  var c = __('.checkBoxTheme');
  var id='';
  for (var i = 0; i < c.length; i++) {
    if(c[i].checked){
       id=c[i].value;
       i=c.length+1;
    }
  }
  if(id!='')
  {
    if(bancarga==1)
    {
      if(infopromocrear[4]==infopromoedit[4])
      {
        var w=window.innerWidth;
        //console.log('iguales');
        optionsConfig(0);
        compactMenu = true;
        if(w>=880)compactConfigMenu(0);
        responseStep(n,t,1);
      }
      else {
        actualizaplantillabd(n,t,id);
      }
    }
    else {
      actualizaplantillabd(n,t,id);
    }

  }
  else {
    var w=window.innerWidth;
    optionsConfig(0);
    compactMenu = true;
    if(w>=880)compactConfigMenu(0);
    responseStep(n,t,0);
  }

}
function actualizafuncionalidad(n,t,id){
  var  m=MetodoEnum.ActualizaFuncionalidad;
  var dataString = 'm=' + m+'&fun=' + id+'&prom=' + idnvaprom;
  $.ajax({
    type : 'POST',
    url  : 'respuestaconfig.php',
    data:  dataString,
    success:function(data) {
      //console.log(data);
      if(data=='error')
      {
        responseStep(n,t,0);
      }
      else {

        responseStep(n,t,1);
      }

    }
  });


}
function actualizaplantilla(id){
  var  m=MetodoEnum.ActualizaPlantillashtml;
  var dataString = 'm=' + m+'&fun=' + id;
  $.ajax({
    type : 'POST',
    url  : 'respuestaconfig.php',
    data:  dataString,
    success:function(data) {
      //console.log(data);
      $('#plantillas').html(data).fadeIn();
      if(plantseledit!=='')
      {
        var c = __('.checkBoxTheme');
          for (var i = 0; i < c.length; i++) {
            if(c[i].value==plantseledit)
            {
               c[i].checked=true;
            }
          }


      }
    }
  });


}
function actualizaplantillabd(n,t,id){
  var  m=MetodoEnum.ActualizaPlantilla;
  var dataString = 'm=' + m+'&plan=' + id+'&prom=' + idnvaprom;
  $.ajax({
    type : 'POST',
    url  : 'respuestaconfig.php',
    data:  dataString,
    success:function(data) {
      //console.log(data);
      if(data=='error')
      {
        var w=window.innerWidth;
        optionsConfig(0);
        compactMenu = true;
        if(w>=880)compactConfigMenu(0);
        responseStep(n,t,0);
      }
      else {
        var w=window.innerWidth;
        if(bancarga==0)
        {
          infopromocrear=data.split('&@;');
          infopromoedit=data.split('&@;');
          bancarga=1;
        }
        else
        {
          var infotemp=data.split('&@;');
          if(infopromoedit[3]!=infotemp[3])
          {
            infopromoedit=infotemp;
          }
          else {
            infopromoedit[0]=infotemp[0];
            infopromoedit[1]=infotemp[1];
            infopromoedit[2]=infotemp[2];
            infopromoedit[3]=infotemp[3];
            infopromoedit[4]=infotemp[4];
            infopromoedit[5]=infotemp[5];
            infopromoedit[6]=infotemp[6];
            infopromoedit[7]=infotemp[7];
            infopromoedit[8]=infotemp[8];
            infopromoedit[9]=infotemp[9];
            infopromoedit[10]=infotemp[10];
            infopromoedit[11]=infotemp[11];
            infopromoedit[12]=infotemp[12];
            infopromoedit[13]=infotemp[13];
            infopromoedit[15]=infotemp[15];

          }
        }
        var frame = _("#iframePlantilla").contentWindow,
        frameIF = _("#iframeInterfaz").contentWindow;
        frameIF.$('.inferior')[1].innerHTML=infopromoedit[30];
        frame.$('.wrapInferiorSocial')[0].innerHTML=infopromoedit[31];
        frame.$('#plantillaUnoHTML').attr('data-marca',infopromoedit[12]);
        //Cambiar colores,texto,fuentes
        //Color BACK
        var documentplantilla=frame.document;
        var back=documentplantilla.getElementById("loading");
        var classcolor=infopromoedit[20].split('?')[1];
        var arrclass=back.className.split(" ");
        arrclass.pop();
        arrclass.push(classcolor);
        back.className=arrclass.join(' ');
        //Fuente color y tipo
        var body=documentplantilla.getElementById("plantillaUno");
        var colorfuente=infopromoedit[19].split('?')[1];
        var fuente=infopromoedit[18].split('?')[1];
        var arrclassbody=body.className.split(" ");
        arrclassbody[0]=fuente;
        arrclassbody[1]=colorfuente;
        body.className=arrclassbody.join(' ');
        //Texto footer
        var footer=documentplantilla.getElementById("footerPromoCopy");
        var txt=infopromoedit[21].split('?')[1];
        footer.textContent=txt;
        //Cambiar imagenes
        updateimagemodiplantilla(infopromoedit[15].split('?')[1],'proveedorUnoLogo','ui/img/proveedor/');
        updateimagemodiplantilla(infopromoedit[14].split('?')[1],'prefetchLogo,navLogo,msgLogo,loadLogo','ui/img/logotipo/');
        updateimagemodiplantilla(infopromoedit[16].split('?')[1],'plantillaUno','ui/img/back/');
        updateimagemodiplantilla(infopromoedit[17].split('?')[1],'productoImg','ui/img/producto/');
        updateimagemodiplantilla(infopromoedit[22].split('?')[1],'textoInicioImg','ui/img/textoInicio/');
        updateimagemodiplantilla(infopromoedit[23].split('?')[1],'prizeImg','ui/img/precio/');
        updateimagemodiplantilla(infopromoedit[24].split('?')[1],'btCouponImg','ui/img/botonCupon/');
        updateimagemodiplantilla(infopromoedit[25].split('?')[1],'couponImg','ui/img/cupon/');
        updateimagemodiplantilla(infopromoedit[26].split('?')[1],'btCaptureScreen','ui/img/botonDescarga/');
        updateimagemodiplantilla(infopromoedit[27].split('?')[1],'msgExitoImg','ui/img/mensajeExito/');
        updateimagemodiplantilla(infopromoedit[28].split('?')[1],'msgHashtagImg','ui/img/hashtag/');
        updateimagemodiplantilla(infopromoedit[29].split('?')[1],'msgErrorImg','ui/img/mensajeError/');
        //Redes sociales icons
        var arrayRS=infopromoedit[32].split('|');
        for(var irs=0;irs<arrayRS.length;irs++)
        {

          var rsClaveValor=arrayRS[irs].split('?');
          if(rsClaveValor.length>2)
          {
            var idel='ic'+rsClaveValor[0];
            var valor=rsClaveValor[2];
            updateimagemodiplantilla(valor,idel,'ui/img/ic/');
          }


        }
        //var mar=$('#selectBrand')[0].value;
        //frame.src='index.php?id='+idnvaprom+'&cf=1';
        //frameIF.src='interfaz-uno.php?mar='+mar;
        optionsConfig(0);
        compactMenu = true;
        if(w>=880)compactConfigMenu(0);
        responseStep(n,t,1);
      }

    }
  });


}
function getpromoplantillabd(id){
  var  m=MetodoEnum.GetPromoPlantilla;
  var dataString = 'm=' + m+'&prom=' + id;
  $.ajax({
    type : 'POST',
    url  : 'respuestaconfig.php',
    data:  dataString,
    success:function(data) {
      //console.log(data);
      if(data=='error')
      {

      }
      else {
        var w=window.innerWidth;
        if(bancarga==0)
        {
          infopromocrear=data.split('&@;');
          infopromoedit=data.split('&@;');
          bancarga=1;
        }
        else
        {
          var infotemp=data.split('&@;');
          if(infopromoedit[3]!=infotemp[3])
          {
            infopromoedit=infotemp;
          }
          else {
            infopromoedit[0]=infotemp[0];
            infopromoedit[1]=infotemp[1];
            infopromoedit[2]=infotemp[2];
            infopromoedit[3]=infotemp[3];
            infopromoedit[4]=infotemp[4];
            infopromoedit[5]=infotemp[5];
            infopromoedit[6]=infotemp[6];
            infopromoedit[7]=infotemp[7];
            infopromoedit[8]=infotemp[8];
            infopromoedit[9]=infotemp[9];
            infopromoedit[10]=infotemp[10];
            infopromoedit[11]=infotemp[11];
            infopromoedit[12]=infotemp[12];
            infopromoedit[13]=infotemp[13];
            infopromoedit[15]=infotemp[15];

          }
        }
        var frame = _("#iframePlantilla").contentWindow,
        frameIF = _("#iframeInterfaz").contentWindow;
        frameIF.$('.inferior')[1].innerHTML=infopromoedit[30];
        frame.$('.wrapInferiorSocial')[0].innerHTML=infopromoedit[31];
        frame.$('#plantillaUnoHTML').attr('data-marca',infopromoedit[12]);
        //Cambiar colores,texto,fuentes
        //Color BACK
        var documentplantilla=frame.document;
        var back=documentplantilla.getElementById("loading");
        var classcolor=infopromoedit[20].split('?')[1];
        var arrclass=back.className.split(" ");
        arrclass.pop();
        arrclass.push(classcolor);
        back.className=arrclass.join(' ');
        //Fuente color y tipo
        var body=documentplantilla.getElementById("plantillaUno");
        var colorfuente=infopromoedit[19].split('?')[1];
        var fuente=infopromoedit[18].split('?')[1];
        var arrclassbody=body.className.split(" ");
        arrclassbody[0]=fuente;
        arrclassbody[1]=colorfuente;
        body.className=arrclassbody.join(' ');
        //Texto footer
        var footer=documentplantilla.getElementById("footerPromoCopy");
        var txt=infopromoedit[21].split('?')[1];
        footer.textContent=txt;
        //Cambiar imagenes
        updateimagemodiplantilla(infopromoedit[15].split('?')[1],'proveedorUnoLogo','ui/img/proveedor/');
        updateimagemodiplantilla(infopromoedit[14].split('?')[1],'prefetchLogo,navLogo,msgLogo,loadLogo','ui/img/logotipo/');
        updateimagemodiplantilla(infopromoedit[16].split('?')[1],'plantillaUno','ui/img/back/');
        updateimagemodiplantilla(infopromoedit[17].split('?')[1],'productoImg','ui/img/producto/');
        updateimagemodiplantilla(infopromoedit[22].split('?')[1],'textoInicioImg','ui/img/textoInicio/');
        updateimagemodiplantilla(infopromoedit[23].split('?')[1],'prizeImg','ui/img/precio/');
        updateimagemodiplantilla(infopromoedit[24].split('?')[1],'btCouponImg','ui/img/botonCupon/');
        updateimagemodiplantilla(infopromoedit[25].split('?')[1],'couponImg','ui/img/cupon/');
        updateimagemodiplantilla(infopromoedit[26].split('?')[1],'btCaptureScreen','ui/img/botonDescarga/');
        updateimagemodiplantilla(infopromoedit[27].split('?')[1],'msgExitoImg','ui/img/mensajeExito/');
        updateimagemodiplantilla(infopromoedit[28].split('?')[1],'msgHashtagImg','ui/img/hashtag/');
        updateimagemodiplantilla(infopromoedit[29].split('?')[1],'msgErrorImg','ui/img/mensajeError/');
        //Redes sociales icons
        var arrayRS=infopromoedit[32].split('|');
        for(var irs=0;irs<arrayRS.length;irs++)
        {

          var rsClaveValor=arrayRS[irs].split('?');
          if(rsClaveValor.length>2)
          {
            var idel='ic'+rsClaveValor[0];
            var valor=rsClaveValor[2];
            updateimagemodiplantilla(valor,idel,'ui/img/ic/');
          }


        }
        plantseledit=infopromoedit[4];
        $('#fechaInicio')[0].value=infopromocrear[10].split(' ')[0];
        $('#fechaFin')[0].value=infopromocrear[11].split(' ')[0];
        $('#nombrePromo')[0].value=infopromoedit[0];
        if(infopromoedit[33].split('/').length>1)
        {
          $('#nombreURL')[0].value=infopromoedit[33].split('/')[1];
        }
        else {
          $('#nombreURL')[0].value=infopromoedit[33];
        }

        $('#descripcionPromo')[0].value=infopromoedit[2];
        $('#selectBrand')[0].value=infopromoedit[3];
        $('#selectProvider')[0].value=infopromoedit[8];
        var inputfun = __('.checkBoxFunction');
        for (var i = 0; i < inputfun.length; i++) {
           if(inputfun[i].value==infopromocrear[9])
            {
              var t= inputfun[i];
              t.checked=true;
              actualizaplantilla(t.value)
            }
          }

        }
      }
  });


}
function checksaveversion()
{

  var updcre='';
  var plantillaedit=[];


  plantillaedit.push(infopromoedit[3]+'-'+idnvaprom);
  plantillaedit.push(infopromoedit[4]);
  plantillaedit.push(infopromoedit[6]);
  plantillaedit.push(infopromoedit[14].split('?').join('-'));
  plantillaedit.push(infopromoedit[16].split('?').join('-'));
  plantillaedit.push(infopromoedit[17].split('?').join('-'));
  plantillaedit.push(infopromoedit[18].split('?').join('-'));
  plantillaedit.push(infopromoedit[19].split('?').join('-'));
  plantillaedit.push(infopromoedit[20].split('?').join('-'));
  plantillaedit.push(infopromoedit[21].split('?').join('-'));
  plantillaedit.push(infopromoedit[22].split('?').join('-'));
  plantillaedit.push(infopromoedit[23].split('?').join('-'));
  plantillaedit.push(infopromoedit[24].split('?').join('-'));
  plantillaedit.push(infopromoedit[25].split('?').join('-'));
  plantillaedit.push(infopromoedit[26].split('?').join('-'));
  plantillaedit.push(infopromoedit[27].split('?').join('-'));
  plantillaedit.push(infopromoedit[28].split('?').join('-'));
  plantillaedit.push(infopromoedit[29].split('?').join('-'));
  var arrayplantillaedirs=infopromoedit[32].split('|');
  for(var i=0;i<arrayplantillaedirs.length;i++)
  {
    var rsClaveValor=arrayplantillaedirs[i].split('?');
    if(rsClaveValor.length>2)
    {
      plantillaedit.push(rsClaveValor[1]+'-'+rsClaveValor[2]);
    }
  }

  if(infopromocrear[3]==infopromoedit[3]&&infopromocrear[4]==infopromoedit[4]&&infopromocrear[6]==infopromoedit[6])
  {
       if(infopromocrear[6]==0)
       {
         if(revisarconfigplantilla()==1)
         {
           updcre='crear';
         }
       }
       else
       {
         if(revisarconfigplantilla()==1)
         {
           updcre='update';
         }
       }
  }
  if(updcre=='crear'||updcre=='update')
  {
    var  m=MetodoEnum.CreaEditaVersionPlantilla;
    var dataString = 'm=' + m+'&data=' + plantillaedit.join(',')+'&updcre=' + updcre;
    $.ajax({
      type : 'POST',
      url  : 'respuestaconfig.php',
      data:  dataString,
      success:function(data) {
        //console.log(data);
        creardir();
      }
    });
  }
  else {
    //console.log('misma plantilla');
    creardir();
  }

}
function creardir()
{
  if($('#nombreURL')[0].value!=''&&$('#nombreURL')[0].value!=undefined){
       var dirprom=$('#nombreURL')[0].value;
       var  m=MetodoEnum.CreaDirectorio;
       var dataString = 'm=' + m+'&idpromo=' +idnvaprom+'&dir=' + dirprom;
       $.ajax({
         type : 'POST',
         url  : 'respuestaconfig.php',
         data:  dataString,
         success:function(data) {
           //console.log(data);
           window.location.href='home.php';
         }
       });

  }
}
function revisarconfigplantilla()
{
  var ban=0;
   for(var i=14;i<infopromocrear.length;i++)
   {
     if(infopromocrear[i]!==infopromoedit[i])
     {
       ban=1;
       break;
     }
   }
   return ban;
}
function existecupon(p)
{

  var str='\''+p.join('\',\'')+'\'';
  var  m=MetodoEnum.ExisteCupon;
  var dataString = 'm=' + m+'&cup=' +str+'&prom=' + idnvaprom;
  var valor;
  $.ajax({
    type : 'POST',
    url  : 'respuestaconfig.php',
    data:  dataString,
    success:function(data) {
      exist=data.split(',');
      var le=exist.length-1;
      exist.splice(le,1)
      nuevos=[];
      for(var i=0;i<p.length;i++)
      {
        if(!exist.includes(p[i])&&!nuevos.includes(p[i]))
        {
          nuevos.push(p[i]);
        }
      }
      if(csvLoaded){
        numCupones=nuevos.length;
        var textCSVLoaded = _(".numCSV"),
            textCSVNoLoaded = _(".noneNumCSV"),
            numCSVW = _("#numCSV");
        numCSVW.innerHTML = numCupones;
        textCSVLoaded.style.display = "block";
        setTimeout(function(){
          showLoading(0);
          textCSVLoaded.style.opacity = "1";
        },500);
      } else {
        textCSVNoLoaded.style.display = "block";
        setTimeout(function(){
          showLoading(0);
          textCSVNoLoaded.style.opacity = "1";
        },500);
      }
    }
  });
}
function readtextcsv(callback) {
        var val = "x";
        //... code to load a file variable
        var file=document.getElementById("couponsUpload").files[0]
        var r;
        r = new FileReader();
        r.onload = function (e) {
            val = e.target.result;
            callback(val);
        };
        r.readAsText(file);
}
function checkcuponstoload(n,t)
{
    var arc=document.getElementById("couponsUpload").files.length;
    var toload=nuevos.length;
    if(arc>0&&toload<1)
    {
      compactMenu = false;
      compactConfigMenu(1);
      responseStep(n , t, 0);
      alert('Has seleccionado un archivo pero no lo cargaste da click al boton cargar para procesarlo o no cuenta con cupones validos,cambialo o eliminalo para continuar.');
    }
    else {
      compactMenu = false;
      compactConfigMenu(1);
      responseStep(n , t, 1);
    }
}
function loadcupons(n,t){
  var str='\''+nuevos.join('\',\'')+'\'';
  var  m=MetodoEnum.CargarCupones;
  var dataString = 'm=' + m+'&cup=' +str+'&prom=' + idnvaprom;
  var valor;
  $.ajax({
    type : 'POST',
    url  : 'respuestaconfig.php',
    data:  dataString,
    success:function(data) {
      //console.log(data);
      //window.location.href='home.php';
      checksaveversion();
    }
  });
}
function changecolorback(t)
{
    var ar=infopromoedit[20].split('?');
    var back= _("#iframePlantilla").contentWindow.document.getElementById("loading");
    var classcolor=t.value.split('.')[1];
    var arrclass=back.className.split(" ");
    arrclass.pop();
    arrclass.push(classcolor);
    ar[1]=classcolor;
    infopromoedit[20]=ar.join('?');
    back.className=arrclass.join(' ');
}
function changecolortext(t)
{
  var ar=infopromoedit[19].split('?');
  var body= _("#iframePlantilla").contentWindow.document.getElementById("plantillaUno");
  var classcolor=t.value.split('.')[1];
  var arrclass=body.className.split(" ");
  arrclass[1]=classcolor;
  ar[1]=classcolor;
  infopromoedit[19]=ar.join('?');
  body.className=arrclass.join(' ');

}
function changefont(t)
{
  var ar=infopromoedit[18].split('?');
  var body= _("#iframePlantilla").contentWindow.document.getElementById("plantillaUno");
  var classcolor=t.value.split('.')[1];
  var arrclass=body.className.split(" ");
  arrclass[0]=classcolor;
  ar[1]=classcolor;
  infopromoedit[18]=ar.join('?');
  body.className=arrclass.join(' ');
}
function changetxt(t)
{
  var ar=infopromoedit[21].split('?');
  var footer= _("#iframePlantilla").contentWindow.document.getElementById("footerPromoCopy");
  var txt=t.value;
  ar[1]=txt;
  infopromoedit[21]=ar.join('?');
  footer.textContent=txt;
}
var folderui='';
var imguifolder='';
function updateimagemodiplantilla(input,idelement,ruta){
  folderui=ruta;
  imguifolder=input;
  if(idelement.includes(',')){
      var arrelem=idelement.split(',');
      arrelem.forEach(changeimgemodiplantilla);
  }
  else {
    changeimgemodiplantilla(idelement);
  }
}
function changeimgemodiplantilla(idelement) {
  var ele=_("#iframePlantilla").contentWindow.document.getElementById(idelement);
  if(idelement==='plantillaUno')
  {
    ele.style.backgroundImage='url("'+folderui+imguifolder+'")';
  }
  else {
      ele.src=folderui+imguifolder;
  }
}
/* actualizar estatus de la promo */
function actualizarstatus(idpromo,estatus) {
  var param1=12;
  var dataString = 'm=' + param1+ '&id=' + idpromo +'&st=' + estatus;
  $.ajax({
    type    : 'POST',
    url     : 'respuestaconfig.php',
    data    :  dataString,
    success :  function(data) {
      //console.log('actualizarstatus Result: '+data);
      location.reload();
    }
  });
}

/* eliminar promo */
function eliminarpromo(idpromo) {
  var param1=13;
  var dataString = 'm=' + param1+ '&id=' + idpromo;
  $.ajax({
    type    : 'POST',
    url     : 'respuestaconfig.php',
    data    :  dataString,
    success :  function(data) {
      //console.log('eliminarpromo Result: '+data);
      location.href="home.php";
    }
  });
}

/* calvelar promo */
function cancelarpromo() {
  var param1=15;
  var dataString = 'm=' + param1+ '&id=' + idnvaprom;
  $.ajax({
    type    : 'POST',
    url     : 'respuestaconfig.php',
    data    :  dataString,
    success :  function(data) {
      //console.log('cancelarpromo Result: '+data);
      location.href="home.php";
    }
  });
}
