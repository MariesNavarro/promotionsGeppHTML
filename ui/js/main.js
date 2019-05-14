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

(function (name, context, definition) {
  'use strict'
  if (typeof window.define === 'function' && window.define.amd) { window.define(definition) } else if (typeof module !== 'undefined' && module.exports) { module.exports = definition() } else if (context.exports) { context.exports = definition() } else { context[name] = definition() }
})('Fingerprint2', this, function () {
  'use strict'
  /**
   * @constructor
   * @param {Object=} options
   **/
  var Fingerprint2 = function (options) {
    if (!(this instanceof Fingerprint2)) {
      return new Fingerprint2(options)
    }

    var defaultOptions = {
      swfContainerId: 'fingerprintjs2',
      swfPath: 'flash/compiled/FontList.swf',
      detectScreenOrientation: true,
      sortPluginsFor: [/palemoon/i],
      userDefinedFonts: [],
      excludeDoNotTrack: true,
      excludePixelRatio: true
    }
    this.options = this.extend(options, defaultOptions)
    this.nativeForEach = Array.prototype.forEach
    this.nativeMap = Array.prototype.map
  }
  Fingerprint2.prototype = {
    extend: function (source, target) {
      if (source == null) { return target }
      for (var k in source) {
        if (source[k] != null && target[k] !== source[k]) {
          target[k] = source[k]
        }
      }
      return target
    },
    get: function (done) {
      var that = this
      var keys = {
        data: [],
        addPreprocessedComponent: function (pair) {
          var componentValue = pair.value;
          if (typeof that.options.preprocessor === 'function') {
            componentValue = that.options.preprocessor(pair.key, componentValue)
          }
          keys.data.push({key: pair.key, value: componentValue})
        }
      }
      keys = this.userAgentKey(keys)
      keys = this.languageKey(keys)
      keys = this.colorDepthKey(keys)
      keys = this.deviceMemoryKey(keys)
      keys = this.pixelRatioKey(keys)
      keys = this.hardwareConcurrencyKey(keys)
      keys = this.screenResolutionKey(keys)
      keys = this.availableScreenResolutionKey(keys)
      keys = this.timezoneOffsetKey(keys)
      keys = this.sessionStorageKey(keys)
      keys = this.localStorageKey(keys)
      keys = this.indexedDbKey(keys)
      keys = this.addBehaviorKey(keys)
      keys = this.openDatabaseKey(keys)
      keys = this.cpuClassKey(keys)
      keys = this.platformKey(keys)
      keys = this.doNotTrackKey(keys)
      keys = this.pluginsKey(keys)
      keys = this.canvasKey(keys)
      keys = this.adBlockKey(keys)
      keys = this.hasLiedLanguagesKey(keys)
      keys = this.hasLiedResolutionKey(keys)
      keys = this.hasLiedOsKey(keys)
      keys = this.hasLiedBrowserKey(keys)
      keys = this.touchSupportKey(keys)
      keys = this.customEntropyFunction(keys)

            var values = []
            that.each(keys.data, function (pair) {
              var value = pair.value
              if (value && typeof value.join === 'function') {
                values.push(value.join(';'))
              } else {
               values.push(value)
              }
            })
            var murmur =that.x64hash128(values.join('~~~'), 31)
            return done(murmur, keys.data)

    },
    enumerateDevicesKey: function (keys, done) {
      if (this.options.excludeEnumerateDevices || !this.isEnumerateDevicesSupported()) {
        return done(keys)
      }

      navigator.mediaDevices.enumerateDevices()
      .then(function (devices) {
        var enumerateDevicesFp = []
        devices.forEach(function (device) {
        })
        keys.addPreprocessedComponent({key: 'enumerate_devices', value: enumerateDevicesFp})
        return done(keys)
      })
      .catch(function (e) {
        return done(keys)
      })
    },
    isEnumerateDevicesSupported: function () {
      return (navigator.mediaDevices && navigator.mediaDevices.enumerateDevices)
    },
    audioKey: function (keys, done) {
      if (this.options.excludeAudioFP) {
        return done(keys)
      }

      var AudioContext = window.OfflineAudioContext || window.webkitOfflineAudioContext

      if (AudioContext == null) {
        keys.addPreprocessedComponent({key: 'audio_fp', value: null})
        return done(keys)
      }

      var context = new AudioContext(1, 44100, 44100)

      var oscillator = context.createOscillator()
      oscillator.type = 'triangle'
      oscillator.frequency.setValueAtTime(10000, context.currentTime)

      var compressor = context.createDynamicsCompressor()
      this.each([
        ['threshold', -50],
        ['knee', 40],
        ['ratio', 12],
        ['reduction', -20],
        ['attack', 0],
        ['release', 0.25]
      ], function (item) {
        if (compressor[item[0]] !== undefined && typeof compressor[item[0]].setValueAtTime === 'function') {
          compressor[item[0]].setValueAtTime(item[1], context.currentTime)
        }
      })

      context.oncomplete = function (event) {
        var fingerprint = event.renderedBuffer.getChannelData(0)
                     .slice(4500, 5000)
                     .reduce(function (acc, val) { return acc + Math.abs(val) }, 0)
                     .toString()
        oscillator.disconnect()
        compressor.disconnect()

        keys.addPreprocessedComponent({key: 'audio_fp', value: fingerprint})
        return done(keys)
      }

      oscillator.connect(compressor)
      compressor.connect(context.destination)
      oscillator.start(0)
      context.startRendering()
    },
    customEntropyFunction: function (keys) {
      if (typeof this.options.customFunction === 'function') {
        keys.addPreprocessedComponent({key: 'custom', value: this.options.customFunction()})
      }
      return keys
    },
    userAgentKey: function (keys) {
      if (!this.options.excludeUserAgent) {
        keys.addPreprocessedComponent({key: 'user_agent', value: this.getUserAgent()})
      }
      return keys
    },
    getUserAgent: function () {
      return navigator.userAgent
    },
    languageKey: function (keys) {
      if (!this.options.excludeLanguage) {
        keys.addPreprocessedComponent({key: 'language', value: navigator.language || navigator.userLanguage || navigator.browserLanguage || navigator.systemLanguage || ''})
      }
      return keys
    },
    colorDepthKey: function (keys) {
      if (!this.options.excludeColorDepth) {
        keys.addPreprocessedComponent({key: 'color_depth', value: window.screen.colorDepth || -1})
      }
      return keys
    },
    deviceMemoryKey: function (keys) {
      if (!this.options.excludeDeviceMemory) {
        keys.addPreprocessedComponent({key: 'device_memory', value: this.getDeviceMemory()})
      }
      return keys
    },
    getDeviceMemory: function () {
      return navigator.deviceMemory || -1
    },
    pixelRatioKey: function (keys) {
      if (!this.options.excludePixelRatio) {
        keys.addPreprocessedComponent({key: 'pixel_ratio', value: this.getPixelRatio()})
      }
      return keys
    },
    getPixelRatio: function () {
      return window.devicePixelRatio || ''
    },
    screenResolutionKey: function (keys) {
      if (!this.options.excludeScreenResolution) {
        return this.getScreenResolution(keys)
      }
      return keys
    },
    getScreenResolution: function (keys) {
      var resolution
      if (this.options.detectScreenOrientation) {
        resolution = (window.screen.height > window.screen.width) ? [window.screen.height, window.screen.width] : [window.screen.width, window.screen.height]
      } else {
        resolution = [window.screen.width, window.screen.height]
      }
      keys.addPreprocessedComponent({key: 'resolution', value: resolution})
      return keys
    },
    availableScreenResolutionKey: function (keys) {
      if (!this.options.excludeAvailableScreenResolution) {
        return this.getAvailableScreenResolution(keys)
      }
      return keys
    },
    getAvailableScreenResolution: function (keys) {
      var available
      if (window.screen.availWidth && window.screen.availHeight) {
        if (this.options.detectScreenOrientation) {
          available = (window.screen.availHeight > window.screen.availWidth) ? [window.screen.availHeight, window.screen.availWidth] : [window.screen.availWidth, window.screen.availHeight]
        } else {
          available = [window.screen.availHeight, window.screen.availWidth]
        }
      }
      if (typeof available !== 'undefined') {
        keys.addPreprocessedComponent({key: 'available_resolution', value: available})
      }
      return keys
    },
    timezoneOffsetKey: function (keys) {
      if (!this.options.excludeTimezoneOffset) {
        keys.addPreprocessedComponent({key: 'timezone_offset', value: new Date().getTimezoneOffset()})
      }
      return keys
    },
    sessionStorageKey: function (keys) {
      if (!this.options.excludeSessionStorage && this.hasSessionStorage()) {
        keys.addPreprocessedComponent({key: 'session_storage', value: 1})
      }
      return keys
    },
    localStorageKey: function (keys) {
      if (!this.options.excludeSessionStorage && this.hasLocalStorage()) {
        keys.addPreprocessedComponent({key: 'local_storage', value: 1})
      }
      return keys
    },
    indexedDbKey: function (keys) {
      if (!this.options.excludeIndexedDB && this.hasIndexedDB()) {
        keys.addPreprocessedComponent({key: 'indexed_db', value: 1})
      }
      return keys
    },
    addBehaviorKey: function (keys) {
      if (!this.options.excludeAddBehavior && document.body && document.body.addBehavior) {
        keys.addPreprocessedComponent({key: 'add_behavior', value: 1})
      }
      return keys
    },
    openDatabaseKey: function (keys) {
      if (!this.options.excludeOpenDatabase && window.openDatabase) {
        keys.addPreprocessedComponent({key: 'open_database', value: 1})
      }
      return keys
    },
    cpuClassKey: function (keys) {
      if (!this.options.excludeCpuClass) {
        keys.addPreprocessedComponent({key: 'cpu_class', value: this.getNavigatorCpuClass()})
      }
      return keys
    },
    platformKey: function (keys) {
      if (!this.options.excludePlatform) {
        keys.addPreprocessedComponent({key: 'navigator_platform', value: this.getNavigatorPlatform()})
      }
      return keys
    },
    doNotTrackKey: function (keys) {
      if (!this.options.excludeDoNotTrack) {
        keys.addPreprocessedComponent({key: 'do_not_track', value: this.getDoNotTrack()})
      }
      return keys
    },
    canvasKey: function (keys) {
      if (!this.options.excludeCanvas && this.isCanvasSupported()) {
        keys.addPreprocessedComponent({key: 'canvas', value: this.getCanvasFp()})
      }
      return keys
    },
    webglKey: function (keys) {
      if (!this.options.excludeWebGL && this.isWebGlSupported()) {
        keys.addPreprocessedComponent({key: 'webgl', value: this.getWebglFp()})
      }
      return keys
    },
    webglVendorAndRendererKey: function (keys) {
      if (!this.options.excludeWebGLVendorAndRenderer && this.isWebGlSupported()) {
        keys.addPreprocessedComponent({key: 'webgl_vendor', value: this.getWebglVendorAndRenderer()})
      }
      return keys
    },
    adBlockKey: function (keys) {
      if (!this.options.excludeAdBlock) {
        keys.addPreprocessedComponent({key: 'adblock', value: this.getAdBlock()})
      }
      return keys
    },
    hasLiedLanguagesKey: function (keys) {
      if (!this.options.excludeHasLiedLanguages) {
        keys.addPreprocessedComponent({key: 'has_lied_languages', value: this.getHasLiedLanguages()})
      }
      return keys
    },
    hasLiedResolutionKey: function (keys) {
      if (!this.options.excludeHasLiedResolution) {
        keys.addPreprocessedComponent({key: 'has_lied_resolution', value: this.getHasLiedResolution()})
      }
      return keys
    },
    hasLiedOsKey: function (keys) {
      if (!this.options.excludeHasLiedOs) {
        keys.addPreprocessedComponent({key: 'has_lied_os', value: this.getHasLiedOs()})
      }
      return keys
    },
    hasLiedBrowserKey: function (keys) {
      if (!this.options.excludeHasLiedBrowser) {
        keys.addPreprocessedComponent({key: 'has_lied_browser', value: this.getHasLiedBrowser()})
      }
      return keys
    },
    fontsKey: function (keys, done) {
      if (this.options.excludeJsFonts) {
        return this.flashFontsKey(keys, done)
      }
      return this.jsFontsKey(keys, done)
    },
    flashFontsKey: function (keys, done) {
      if (this.options.excludeFlashFonts) {
        return done(keys)
      }
      if (!this.hasSwfObjectLoaded()) {
        return done(keys)
      }
      if (!this.hasMinFlashInstalled()) {
        return done(keys)
      }
      if (typeof this.options.swfPath === 'undefined') {
        return done(keys)
      }
      this.loadSwfAndDetectFonts(function (fonts) {
        keys.addPreprocessedComponent({key: 'swf_fonts', value: fonts.join(';')})
        done(keys)
      })
    },
    jsFontsKey: function (keys, done) {
      var that = this
      return setTimeout(function () {
        var baseFonts = ['monospace', 'sans-serif', 'serif']

        var fontList = [
          'Andale Mono', 'Arial', 'Arial Black', 'Arial Hebrew', 'Arial MT', 'Arial Narrow', 'Arial Rounded MT Bold', 'Arial Unicode MS',
          'Bitstream Vera Sans Mono', 'Book Antiqua', 'Bookman Old Style',
          'Calibri', 'Cambria', 'Cambria Math', 'Century', 'Century Gothic', 'Century Schoolbook', 'Comic Sans', 'Comic Sans MS', 'Consolas', 'Courier', 'Courier New',
          'Geneva', 'Georgia',
          'Helvetica', 'Helvetica Neue',
          'Impact',
          'Lucida Bright', 'Lucida Calligraphy', 'Lucida Console', 'Lucida Fax', 'LUCIDA GRANDE', 'Lucida Handwriting', 'Lucida Sans', 'Lucida Sans Typewriter', 'Lucida Sans Unicode',
          'Microsoft Sans Serif', 'Monaco', 'Monotype Corsiva', 'MS Gothic', 'MS Outlook', 'MS PGothic', 'MS Reference Sans Serif', 'MS Sans Serif', 'MS Serif', 'MYRIAD', 'MYRIAD PRO',
          'Palatino', 'Palatino Linotype',
          'Segoe Print', 'Segoe Script', 'Segoe UI', 'Segoe UI Light', 'Segoe UI Semibold', 'Segoe UI Symbol',
          'Tahoma', 'Times', 'Times New Roman', 'Times New Roman PS', 'Trebuchet MS',
          'Verdana', 'Wingdings', 'Wingdings 2', 'Wingdings 3'
        ]
        var extendedFontList = [
          'Abadi MT Condensed Light', 'Academy Engraved LET', 'ADOBE CASLON PRO', 'Adobe Garamond', 'ADOBE GARAMOND PRO', 'Agency FB', 'Aharoni', 'Albertus Extra Bold', 'Albertus Medium', 'Algerian', 'Amazone BT', 'American Typewriter',
          'American Typewriter Condensed', 'AmerType Md BT', 'Andalus', 'Angsana New', 'AngsanaUPC', 'Antique Olive', 'Aparajita', 'Apple Chancery', 'Apple Color Emoji', 'Apple SD Gothic Neo', 'Arabic Typesetting', 'ARCHER',
          'ARNO PRO', 'Arrus BT', 'Aurora Cn BT', 'AvantGarde Bk BT', 'AvantGarde Md BT', 'AVENIR', 'Ayuthaya', 'Bandy', 'Bangla Sangam MN', 'Bank Gothic', 'BankGothic Md BT', 'Baskerville',
          'Baskerville Old Face', 'Batang', 'BatangChe', 'Bauer Bodoni', 'Bauhaus 93', 'Bazooka', 'Bell MT', 'Bembo', 'Benguiat Bk BT', 'Berlin Sans FB', 'Berlin Sans FB Demi', 'Bernard MT Condensed', 'BernhardFashion BT', 'BernhardMod BT', 'Big Caslon', 'BinnerD',
          'Blackadder ITC', 'BlairMdITC TT', 'Bodoni 72', 'Bodoni 72 Oldstyle', 'Bodoni 72 Smallcaps', 'Bodoni MT', 'Bodoni MT Black', 'Bodoni MT Condensed', 'Bodoni MT Poster Compressed',
          'Bookshelf Symbol 7', 'Boulder', 'Bradley Hand', 'Bradley Hand ITC', 'Bremen Bd BT', 'Britannic Bold', 'Broadway', 'Browallia New', 'BrowalliaUPC', 'Brush Script MT', 'Californian FB', 'Calisto MT', 'Calligrapher', 'Candara',
          'CaslonOpnface BT', 'Castellar', 'Centaur', 'Cezanne', 'CG Omega', 'CG Times', 'Chalkboard', 'Chalkboard SE', 'Chalkduster', 'Charlesworth', 'Charter Bd BT', 'Charter BT', 'Chaucer',
          'ChelthmITC Bk BT', 'Chiller', 'Clarendon', 'Clarendon Condensed', 'CloisterBlack BT', 'Cochin', 'Colonna MT', 'Constantia', 'Cooper Black', 'Copperplate', 'Copperplate Gothic', 'Copperplate Gothic Bold',
          'Copperplate Gothic Light', 'CopperplGoth Bd BT', 'Corbel', 'Cordia New', 'CordiaUPC', 'Cornerstone', 'Coronet', 'Cuckoo', 'Curlz MT', 'DaunPenh', 'Dauphin', 'David', 'DB LCD Temp', 'DELICIOUS', 'Denmark',
          'DFKai-SB', 'Didot', 'DilleniaUPC', 'DIN', 'DokChampa', 'Dotum', 'DotumChe', 'Ebrima', 'Edwardian Script ITC', 'Elephant', 'English 111 Vivace BT', 'Engravers MT', 'EngraversGothic BT', 'Eras Bold ITC', 'Eras Demi ITC', 'Eras Light ITC', 'Eras Medium ITC',
          'EucrosiaUPC', 'Euphemia', 'Euphemia UCAS', 'EUROSTILE', 'Exotc350 Bd BT', 'FangSong', 'Felix Titling', 'Fixedsys', 'FONTIN', 'Footlight MT Light', 'Forte',
          'FrankRuehl', 'Fransiscan', 'Freefrm721 Blk BT', 'FreesiaUPC', 'Freestyle Script', 'French Script MT', 'FrnkGothITC Bk BT', 'Fruitger', 'FRUTIGER',
          'Futura', 'Futura Bk BT', 'Futura Lt BT', 'Futura Md BT', 'Futura ZBlk BT', 'FuturaBlack BT', 'Gabriola', 'Galliard BT', 'Gautami', 'Geeza Pro', 'Geometr231 BT', 'Geometr231 Hv BT', 'Geometr231 Lt BT', 'GeoSlab 703 Lt BT',
          'GeoSlab 703 XBd BT', 'Gigi', 'Gill Sans', 'Gill Sans MT', 'Gill Sans MT Condensed', 'Gill Sans MT Ext Condensed Bold', 'Gill Sans Ultra Bold', 'Gill Sans Ultra Bold Condensed', 'Gisha', 'Gloucester MT Extra Condensed', 'GOTHAM', 'GOTHAM BOLD',
          'Goudy Old Style', 'Goudy Stout', 'GoudyHandtooled BT', 'GoudyOLSt BT', 'Gujarati Sangam MN', 'Gulim', 'GulimChe', 'Gungsuh', 'GungsuhChe', 'Gurmukhi MN', 'Haettenschweiler', 'Harlow Solid Italic', 'Harrington', 'Heather', 'Heiti SC', 'Heiti TC', 'HELV',
          'Herald', 'High Tower Text', 'Hiragino Kaku Gothic ProN', 'Hiragino Mincho ProN', 'Hoefler Text', 'Humanst 521 Cn BT', 'Humanst521 BT', 'Humanst521 Lt BT', 'Imprint MT Shadow', 'Incised901 Bd BT', 'Incised901 BT',
          'Incised901 Lt BT', 'INCONSOLATA', 'Informal Roman', 'Informal011 BT', 'INTERSTATE', 'IrisUPC', 'Iskoola Pota', 'JasmineUPC', 'Jazz LET', 'Jenson', 'Jester', 'Jokerman', 'Juice ITC', 'Kabel Bk BT', 'Kabel Ult BT', 'Kailasa', 'KaiTi', 'Kalinga', 'Kannada Sangam MN',
          'Kartika', 'Kaufmann Bd BT', 'Kaufmann BT', 'Khmer UI', 'KodchiangUPC', 'Kokila', 'Korinna BT', 'Kristen ITC', 'Krungthep', 'Kunstler Script', 'Lao UI', 'Latha', 'Leelawadee', 'Letter Gothic', 'Levenim MT', 'LilyUPC', 'Lithograph', 'Lithograph Light', 'Long Island',
          'Lydian BT', 'Magneto', 'Maiandra GD', 'Malayalam Sangam MN', 'Malgun Gothic',
          'Mangal', 'Marigold', 'Marion', 'Marker Felt', 'Market', 'Marlett', 'Matisse ITC', 'Matura MT Script Capitals', 'Meiryo', 'Meiryo UI', 'Microsoft Himalaya', 'Microsoft JhengHei', 'Microsoft New Tai Lue', 'Microsoft PhagsPa', 'Microsoft Tai Le',
          'Microsoft Uighur', 'Microsoft YaHei', 'Microsoft Yi Baiti', 'MingLiU', 'MingLiU_HKSCS', 'MingLiU_HKSCS-ExtB', 'MingLiU-ExtB', 'Minion', 'Minion Pro', 'Miriam', 'Miriam Fixed', 'Mistral', 'Modern', 'Modern No. 20', 'Mona Lisa Solid ITC TT', 'Mongolian Baiti',
          'MONO', 'MoolBoran', 'Mrs Eaves', 'MS LineDraw', 'MS Mincho', 'MS PMincho', 'MS Reference Specialty', 'MS UI Gothic', 'MT Extra', 'MUSEO', 'MV Boli',
          'Nadeem', 'Narkisim', 'NEVIS', 'News Gothic', 'News GothicMT', 'NewsGoth BT', 'Niagara Engraved', 'Niagara Solid', 'Noteworthy', 'NSimSun', 'Nyala', 'OCR A Extended', 'Old Century', 'Old English Text MT', 'Onyx', 'Onyx BT', 'OPTIMA', 'Oriya Sangam MN',
          'OSAKA', 'OzHandicraft BT', 'Palace Script MT', 'Papyrus', 'Parchment', 'Party LET', 'Pegasus', 'Perpetua', 'Perpetua Titling MT', 'PetitaBold', 'Pickwick', 'Plantagenet Cherokee', 'Playbill', 'PMingLiU', 'PMingLiU-ExtB',
          'Poor Richard', 'Poster', 'PosterBodoni BT', 'PRINCETOWN LET', 'Pristina', 'PTBarnum BT', 'Pythagoras', 'Raavi', 'Rage Italic', 'Ravie', 'Ribbon131 Bd BT', 'Rockwell', 'Rockwell Condensed', 'Rockwell Extra Bold', 'Rod', 'Roman', 'Sakkal Majalla',
          'Santa Fe LET', 'Savoye LET', 'Sceptre', 'Script', 'Script MT Bold', 'SCRIPTINA', 'Serifa', 'Serifa BT', 'Serifa Th BT', 'ShelleyVolante BT', 'Sherwood',
          'Shonar Bangla', 'Showcard Gothic', 'Shruti', 'Signboard', 'SILKSCREEN', 'SimHei', 'Simplified Arabic', 'Simplified Arabic Fixed', 'SimSun', 'SimSun-ExtB', 'Sinhala Sangam MN', 'Sketch Rockwell', 'Skia', 'Small Fonts', 'Snap ITC', 'Snell Roundhand', 'Socket',
          'Souvenir Lt BT', 'Staccato222 BT', 'Steamer', 'Stencil', 'Storybook', 'Styllo', 'Subway', 'Swis721 BlkEx BT', 'Swiss911 XCm BT', 'Sylfaen', 'Synchro LET', 'System', 'Tamil Sangam MN', 'Technical', 'Teletype', 'Telugu Sangam MN', 'Tempus Sans ITC',
          'Terminal', 'Thonburi', 'Traditional Arabic', 'Trajan', 'TRAJAN PRO', 'Tristan', 'Tubular', 'Tunga', 'Tw Cen MT', 'Tw Cen MT Condensed', 'Tw Cen MT Condensed Extra Bold',
          'TypoUpright BT', 'Unicorn', 'Univers', 'Univers CE 55 Medium', 'Univers Condensed', 'Utsaah', 'Vagabond', 'Vani', 'Vijaya', 'Viner Hand ITC', 'VisualUI', 'Vivaldi', 'Vladimir Script', 'Vrinda', 'Westminster', 'WHITNEY', 'Wide Latin',
          'ZapfEllipt BT', 'ZapfHumnst BT', 'ZapfHumnst Dm BT', 'Zapfino', 'Zurich BlkEx BT', 'Zurich Ex BT', 'ZWAdobeF']

        if (that.options.extendedJsFonts) {
          fontList = fontList.concat(extendedFontList)
        }

        fontList = fontList.concat(that.options.userDefinedFonts)

        fontList = fontList.filter(function (font, position) {
          return fontList.indexOf(font) === position
        })

        var testString = 'mmmmmmmmmmlli'

        var testSize = '72px'

        var h = document.getElementsByTagName('body')[0]

        var baseFontsDiv = document.createElement('div')

        var fontsDiv = document.createElement('div')

        var defaultWidth = {}
        var defaultHeight = {}

        var createSpan = function () {
          var s = document.createElement('span')

          s.style.position = 'absolute'
          s.style.left = '-9999px'
          s.style.fontSize = testSize

          s.style.fontStyle = 'normal'
          s.style.fontWeight = 'normal'
          s.style.letterSpacing = 'normal'
          s.style.lineBreak = 'auto'
          s.style.lineHeight = 'normal'
          s.style.textTransform = 'none'
          s.style.textAlign = 'left'
          s.style.textDecoration = 'none'
          s.style.textShadow = 'none'
          s.style.whiteSpace = 'normal'
          s.style.wordBreak = 'normal'
          s.style.wordSpacing = 'normal'

          s.innerHTML = testString
          return s
        }

        var createSpanWithFonts = function (fontToDetect, baseFont) {
          var s = createSpan()
          s.style.fontFamily = "'" + fontToDetect + "'," + baseFont
          return s
        }

        var initializeBaseFontsSpans = function () {
          var spans = []
          for (var index = 0, length = baseFonts.length; index < length; index++) {
            var s = createSpan()
            s.style.fontFamily = baseFonts[index]
            baseFontsDiv.appendChild(s)
            spans.push(s)
          }
          return spans
        }

        var initializeFontsSpans = function () {
          var spans = {}
          for (var i = 0, l = fontList.length; i < l; i++) {
            var fontSpans = []
            for (var j = 0, numDefaultFonts = baseFonts.length; j < numDefaultFonts; j++) {
              var s = createSpanWithFonts(fontList[i], baseFonts[j])
              fontsDiv.appendChild(s)
              fontSpans.push(s)
            }
            spans[fontList[i]] = fontSpans
          }
          return spans
        }

        var isFontAvailable = function (fontSpans) {
          var detected = false
          for (var i = 0; i < baseFonts.length; i++) {
            detected = (fontSpans[i].offsetWidth !== defaultWidth[baseFonts[i]] || fontSpans[i].offsetHeight !== defaultHeight[baseFonts[i]])
            if (detected) {
              return detected
            }
          }
          return detected
        }

        var baseFontsSpans = initializeBaseFontsSpans()

        h.appendChild(baseFontsDiv)

        for (var index = 0, length = baseFonts.length; index < length; index++) {
          defaultWidth[baseFonts[index]] = baseFontsSpans[index].offsetWidth
          defaultHeight[baseFonts[index]] = baseFontsSpans[index].offsetHeight
        }

        var fontsSpans = initializeFontsSpans()

        h.appendChild(fontsDiv)

        var available = []
        for (var i = 0, l = fontList.length; i < l; i++) {
          if (isFontAvailable(fontsSpans[fontList[i]])) {
            available.push(fontList[i])
          }
        }

        h.removeChild(fontsDiv)
        h.removeChild(baseFontsDiv)

        keys.addPreprocessedComponent({key: 'js_fonts', value: available})
        done(keys)
      }, 1)
    },
    pluginsKey: function (keys) {
      if (!this.options.excludePlugins) {
        if (this.isIE()) {
          if (!this.options.excludeIEPlugins) {
            keys.addPreprocessedComponent({key: 'ie_plugins', value: this.getIEPlugins()})
          }
        } else {
          keys.addPreprocessedComponent({key: 'regular_plugins', value: this.getRegularPlugins()})
        }
      }
      return keys
    },
    getRegularPlugins: function () {
      var plugins = []
      if (navigator.plugins) {
        for (var i = 0, l = navigator.plugins.length; i < l; i++) {
          if (navigator.plugins[i]) { plugins.push(navigator.plugins[i]) }
        }
      }

      if (this.pluginsShouldBeSorted()) {
        plugins = plugins.sort(function (a, b) {
          if (a.name > b.name) { return 1 }
          if (a.name < b.name) { return -1 }
          return 0
        })
      }
      return this.map(plugins, function (p) {
        var mimeTypes = this.map(p, function (mt) {
          return [mt.type, mt.suffixes].join('~')
        }).join(',')
        return [p.name, p.description, mimeTypes].join('::')
      }, this)
    },
    getIEPlugins: function () {
      var result = []
      if ((Object.getOwnPropertyDescriptor && Object.getOwnPropertyDescriptor(window, 'ActiveXObject')) || ('ActiveXObject' in window)) {
        var names = [
          'AcroPDF.PDF',
          'Adodb.Stream',
          'AgControl.AgControl',
          'DevalVRXCtrl.DevalVRXCtrl.1',
          'MacromediaFlashPaper.MacromediaFlashPaper',
          'Msxml2.DOMDocument',
          'Msxml2.XMLHTTP',
          'PDF.PdfCtrl',
          'QuickTime.QuickTime',
          'QuickTimeCheckObject.QuickTimeCheck.1',
          'RealPlayer',
          'RealPlayer.RealPlayer(tm) ActiveX Control (32-bit)',
          'RealVideo.RealVideo(tm) ActiveX Control (32-bit)',
          'Scripting.Dictionary',
          'SWCtl.SWCtl',
          'Shell.UIHelper',
          'ShockwaveFlash.ShockwaveFlash',
          'Skype.Detection',
          'TDCCtl.TDCCtl',
          'WMPlayer.OCX',
          'rmocx.RealPlayer G2 Control',
          'rmocx.RealPlayer G2 Control.1'
        ]

        result = this.map(names, function (name) {
          try {

            new window.ActiveXObject(name)
            return name
          } catch (e) {
            return null
          }
        })
      }
      if (navigator.plugins) {
        result = result.concat(this.getRegularPlugins())
      }
      return result
    },
    pluginsShouldBeSorted: function () {
      var should = false
      for (var i = 0, l = this.options.sortPluginsFor.length; i < l; i++) {
        var re = this.options.sortPluginsFor[i]
        if (navigator.userAgent.match(re)) {
          should = true
          break
        }
      }
      return should
    },
    touchSupportKey: function (keys) {
      if (!this.options.excludeTouchSupport) {
        keys.addPreprocessedComponent({key: 'touch_support', value: this.getTouchSupport()})
      }
      return keys
    },
    hardwareConcurrencyKey: function (keys) {
      if (!this.options.excludeHardwareConcurrency) {
        keys.addPreprocessedComponent({key: 'hardware_concurrency', value: this.getHardwareConcurrency()})
      }
      return keys
    },
    hasSessionStorage: function () {
      try {
        return !!window.sessionStorage
      } catch (e) {
        return true
      }
    },

    hasLocalStorage: function () {
      try {
        return !!window.localStorage
      } catch (e) {
        return true
      }
    },
    hasIndexedDB: function () {
      try {
        return !!window.indexedDB
      } catch (e) {
        return true
      }
    },
    getHardwareConcurrency: function () {
      if (navigator.hardwareConcurrency) {
        return navigator.hardwareConcurrency
      }
      return 'unknown'
    },
    getNavigatorCpuClass: function () {
      if (navigator.cpuClass) {
        return navigator.cpuClass
      } else {
        return 'unknown'
      }
    },
    getNavigatorPlatform: function () {
      if (navigator.platform) {
        return navigator.platform
      } else {
        return 'unknown'
      }
    },
    getDoNotTrack: function () {
      if (navigator.doNotTrack) {
        return navigator.doNotTrack
      } else if (navigator.msDoNotTrack) {
        return navigator.msDoNotTrack
      } else if (window.doNotTrack) {
        return window.doNotTrack
      } else {
        return 'unknown'
      }
    },
    getTouchSupport: function () {
      var maxTouchPoints = 0
      var touchEvent = false
      if (typeof navigator.maxTouchPoints !== 'undefined') {
        maxTouchPoints = navigator.maxTouchPoints
      } else if (typeof navigator.msMaxTouchPoints !== 'undefined') {
        maxTouchPoints = navigator.msMaxTouchPoints
      }
      try {
        document.createEvent('TouchEvent')
        touchEvent = true
      } catch (_) {  }
      var touchStart = 'ontouchstart' in window
      return [maxTouchPoints, touchEvent, touchStart]
    },

    getCanvasFp: function () {
      var result = []

      var canvas = document.createElement('canvas')
      canvas.width = 2000
      canvas.height = 200
      canvas.style.display = 'inline'
      var ctx = canvas.getContext('2d')
      ctx.rect(0, 0, 10, 10)
      ctx.rect(2, 2, 6, 6)
      result.push('canvas winding:' + ((ctx.isPointInPath(5, 5, 'evenodd') === false) ? 'yes' : 'no'))

      ctx.textBaseline = 'alphabetic'
      ctx.fillStyle = '#f60'
      ctx.fillRect(125, 1, 62, 20)
      ctx.fillStyle = '#069'

      if (this.options.dontUseFakeFontInCanvas) {
        ctx.font = '11pt Arial'
      } else {
        ctx.font = '11pt no-real-font-123'
      }
      ctx.fillText('Cwm fjordbank glyphs vext quiz, \ud83d\ude03', 2, 15)
      ctx.fillStyle = 'rgba(102, 204, 0, 0.2)'
      ctx.font = '18pt Arial'
      ctx.fillText('Cwm fjordbank glyphs vext quiz, \ud83d\ude03', 4, 45)
      ctx.globalCompositeOperation = 'multiply'
      ctx.fillStyle = 'rgb(255,0,255)'
      ctx.beginPath()
      ctx.arc(50, 50, 50, 0, Math.PI * 2, true)
      ctx.closePath()
      ctx.fill()
      ctx.fillStyle = 'rgb(0,255,255)'
      ctx.beginPath()
      ctx.arc(100, 50, 50, 0, Math.PI * 2, true)
      ctx.closePath()
      ctx.fill()
      ctx.fillStyle = 'rgb(255,255,0)'
      ctx.beginPath()
      ctx.arc(75, 100, 50, 0, Math.PI * 2, true)
      ctx.closePath()
      ctx.fill()
      ctx.fillStyle = 'rgb(255,0,255)'
      ctx.arc(75, 75, 75, 0, Math.PI * 2, true)
      ctx.arc(75, 75, 25, 0, Math.PI * 2, true)
      ctx.fill('evenodd')

      if (canvas.toDataURL) { result.push('canvas fp:' + canvas.toDataURL()) }
      return result.join('~')
    },

    getWebglFp: function () {
      var gl
      var fa2s = function (fa) {
        gl.clearColor(0.0, 0.0, 0.0, 1.0)
        gl.enable(gl.DEPTH_TEST)
        gl.depthFunc(gl.LEQUAL)
        gl.clear(gl.COLOR_BUFFER_BIT | gl.DEPTH_BUFFER_BIT)
        return '[' + fa[0] + ', ' + fa[1] + ']'
      }
      var maxAnisotropy = function (gl) {
        var ext = gl.getExtension('EXT_texture_filter_anisotropic') || gl.getExtension('WEBKIT_EXT_texture_filter_anisotropic') || gl.getExtension('MOZ_EXT_texture_filter_anisotropic')
        if (ext) {
          var anisotropy = gl.getParameter(ext.MAX_TEXTURE_MAX_ANISOTROPY_EXT)
          if (anisotropy === 0) {
            anisotropy = 2
          }
          return anisotropy
        } else {
          return null
        }
      }
      gl = this.getWebglCanvas()
      if (!gl) { return null }
      var result = []
      var vShaderTemplate = 'attribute vec2 attrVertex;varying vec2 varyinTexCoordinate;uniform vec2 uniformOffset;void main(){varyinTexCoordinate=attrVertex+uniformOffset;gl_Position=vec4(attrVertex,0,1);}'
      var fShaderTemplate = 'precision mediump float;varying vec2 varyinTexCoordinate;void main() {gl_FragColor=vec4(varyinTexCoordinate,0,1);}'
      var vertexPosBuffer = gl.createBuffer()
      gl.bindBuffer(gl.ARRAY_BUFFER, vertexPosBuffer)
      var vertices = new Float32Array([-0.2, -0.9, 0, 0.4, -0.26, 0, 0, 0.732134444, 0])
      gl.bufferData(gl.ARRAY_BUFFER, vertices, gl.STATIC_DRAW)
      vertexPosBuffer.itemSize = 3
      vertexPosBuffer.numItems = 3
      var program = gl.createProgram()
      var vshader = gl.createShader(gl.VERTEX_SHADER)
      gl.shaderSource(vshader, vShaderTemplate)
      gl.compileShader(vshader)
      var fshader = gl.createShader(gl.FRAGMENT_SHADER)
      gl.shaderSource(fshader, fShaderTemplate)
      gl.compileShader(fshader)
      gl.attachShader(program, vshader)
      gl.attachShader(program, fshader)
      gl.linkProgram(program)
      gl.useProgram(program)
      program.vertexPosAttrib = gl.getAttribLocation(program, 'attrVertex')
      program.offsetUniform = gl.getUniformLocation(program, 'uniformOffset')
      gl.enableVertexAttribArray(program.vertexPosArray)
      gl.vertexAttribPointer(program.vertexPosAttrib, vertexPosBuffer.itemSize, gl.FLOAT, !1, 0, 0)
      gl.uniform2f(program.offsetUniform, 1, 1)
      gl.drawArrays(gl.TRIANGLE_STRIP, 0, vertexPosBuffer.numItems)
      try {
        result.push(gl.canvas.toDataURL())
      } catch (e) {

      }
      result.push('extensions:' + (gl.getSupportedExtensions() || []).join(';'))
      result.push('webgl aliased line width range:' + fa2s(gl.getParameter(gl.ALIASED_LINE_WIDTH_RANGE)))
      result.push('webgl aliased point size range:' + fa2s(gl.getParameter(gl.ALIASED_POINT_SIZE_RANGE)))
      result.push('webgl alpha bits:' + gl.getParameter(gl.ALPHA_BITS))
      result.push('webgl antialiasing:' + (gl.getContextAttributes().antialias ? 'yes' : 'no'))
      result.push('webgl blue bits:' + gl.getParameter(gl.BLUE_BITS))
      result.push('webgl depth bits:' + gl.getParameter(gl.DEPTH_BITS))
      result.push('webgl green bits:' + gl.getParameter(gl.GREEN_BITS))
      result.push('webgl max anisotropy:' + maxAnisotropy(gl))
      result.push('webgl max combined texture image units:' + gl.getParameter(gl.MAX_COMBINED_TEXTURE_IMAGE_UNITS))
      result.push('webgl max cube map texture size:' + gl.getParameter(gl.MAX_CUBE_MAP_TEXTURE_SIZE))
      result.push('webgl max fragment uniform vectors:' + gl.getParameter(gl.MAX_FRAGMENT_UNIFORM_VECTORS))
      result.push('webgl max render buffer size:' + gl.getParameter(gl.MAX_RENDERBUFFER_SIZE))
      result.push('webgl max texture image units:' + gl.getParameter(gl.MAX_TEXTURE_IMAGE_UNITS))
      result.push('webgl max texture size:' + gl.getParameter(gl.MAX_TEXTURE_SIZE))
      result.push('webgl max varying vectors:' + gl.getParameter(gl.MAX_VARYING_VECTORS))
      result.push('webgl max vertex attribs:' + gl.getParameter(gl.MAX_VERTEX_ATTRIBS))
      result.push('webgl max vertex texture image units:' + gl.getParameter(gl.MAX_VERTEX_TEXTURE_IMAGE_UNITS))
      result.push('webgl max vertex uniform vectors:' + gl.getParameter(gl.MAX_VERTEX_UNIFORM_VECTORS))
      result.push('webgl max viewport dims:' + fa2s(gl.getParameter(gl.MAX_VIEWPORT_DIMS)))
      result.push('webgl red bits:' + gl.getParameter(gl.RED_BITS))
      result.push('webgl renderer:' + gl.getParameter(gl.RENDERER))
      result.push('webgl shading language version:' + gl.getParameter(gl.SHADING_LANGUAGE_VERSION))
      result.push('webgl stencil bits:' + gl.getParameter(gl.STENCIL_BITS))
      result.push('webgl vendor:' + gl.getParameter(gl.VENDOR))
      result.push('webgl version:' + gl.getParameter(gl.VERSION))

      try {

        var extensionDebugRendererInfo = gl.getExtension('WEBGL_debug_renderer_info')
        if (extensionDebugRendererInfo) {
          result.push('webgl unmasked vendor:' + gl.getParameter(extensionDebugRendererInfo.UNMASKED_VENDOR_WEBGL))
          result.push('webgl unmasked renderer:' + gl.getParameter(extensionDebugRendererInfo.UNMASKED_RENDERER_WEBGL))
        }
      } catch (e) {  }

      if (!gl.getShaderPrecisionFormat) {
        return result.join('~')
      }

      var that = this

      that.each(['FLOAT', 'INT'], function (numType) {
        that.each(['VERTEX', 'FRAGMENT'], function (shader) {
          that.each(['HIGH', 'MEDIUM', 'LOW'], function (numSize) {
            that.each(['precision', 'rangeMin', 'rangeMax'], function (key) {
              var format = gl.getShaderPrecisionFormat(gl[shader + '_SHADER'], gl[numSize + '_' + numType])[key]
              if (key !== 'precision') {
                key = 'precision ' + key
              }
              var line = ['webgl ', shader.toLowerCase(), ' shader ', numSize.toLowerCase(), ' ', numType.toLowerCase(), ' ', key, ':', format]
              result.push(line.join(''))
            })
          })
        })
      })
      return result.join('~')
    },
    getWebglVendorAndRenderer: function () {

      try {
        var glContext = this.getWebglCanvas()
        var extensionDebugRendererInfo = glContext.getExtension('WEBGL_debug_renderer_info')
        return glContext.getParameter(extensionDebugRendererInfo.UNMASKED_VENDOR_WEBGL) + '~' + glContext.getParameter(extensionDebugRendererInfo.UNMASKED_RENDERER_WEBGL)
      } catch (e) {
        return null
      }
    },
    getAdBlock: function () {
      var ads = document.createElement('div')
      ads.innerHTML = '&nbsp;'
      ads.className = 'adsbox'
      var result = false
      try {

        document.body.appendChild(ads)
        result = document.getElementsByClassName('adsbox')[0].offsetHeight === 0
        document.body.removeChild(ads)
      } catch (e) {
        result = false
      }
      return result
    },
    getHasLiedLanguages: function () {

      if (typeof navigator.languages !== 'undefined') {
        try {
          var firstLanguages = navigator.languages[0].substr(0, 2)
          if (firstLanguages !== navigator.language.substr(0, 2)) {
            return true
          }
        } catch (err) {
          return true
        }
      }
      return false
    },
    getHasLiedResolution: function () {
      if (window.screen.width < window.screen.availWidth) {
        return true
      }
      if (window.screen.height < window.screen.availHeight) {
        return true
      }
      return false
    },
    getHasLiedOs: function () {
      var userAgent = navigator.userAgent.toLowerCase()
      var oscpu = navigator.oscpu
      var platform = navigator.platform.toLowerCase()
      var os

      if (userAgent.indexOf('windows phone') >= 0) {
        os = 'Windows Phone'
      } else if (userAgent.indexOf('win') >= 0) {
        os = 'Windows'
      } else if (userAgent.indexOf('android') >= 0) {
        os = 'Android'
      } else if (userAgent.indexOf('linux') >= 0) {
        os = 'Linux'
      } else if (userAgent.indexOf('iphone') >= 0 || userAgent.indexOf('ipad') >= 0) {
        os = 'iOS'
      } else if (userAgent.indexOf('mac') >= 0) {
        os = 'Mac'
      } else {
        os = 'Other'
      }

      var mobileDevice
      if (('ontouchstart' in window) ||
           (navigator.maxTouchPoints > 0) ||
           (navigator.msMaxTouchPoints > 0)) {
        mobileDevice = true
      } else {
        mobileDevice = false
      }

      if (mobileDevice && os !== 'Windows Phone' && os !== 'Android' && os !== 'iOS' && os !== 'Other') {
        return true
      }


      if (typeof oscpu !== 'undefined') {
        oscpu = oscpu.toLowerCase()
        if (oscpu.indexOf('win') >= 0 && os !== 'Windows' && os !== 'Windows Phone') {
          return true
        } else if (oscpu.indexOf('linux') >= 0 && os !== 'Linux' && os !== 'Android') {
          return true
        } else if (oscpu.indexOf('mac') >= 0 && os !== 'Mac' && os !== 'iOS') {
          return true
        } else if ((oscpu.indexOf('win') === -1 && oscpu.indexOf('linux') === -1 && oscpu.indexOf('mac') === -1) !== (os === 'Other')) {
          return true
        }
      }

      if (platform.indexOf('win') >= 0 && os !== 'Windows' && os !== 'Windows Phone') {
        return true
      } else if ((platform.indexOf('linux') >= 0 || platform.indexOf('android') >= 0 || platform.indexOf('pike') >= 0) && os !== 'Linux' && os !== 'Android') {
        return true
      } else if ((platform.indexOf('mac') >= 0 || platform.indexOf('ipad') >= 0 || platform.indexOf('ipod') >= 0 || platform.indexOf('iphone') >= 0) && os !== 'Mac' && os !== 'iOS') {
        return true
      } else if ((platform.indexOf('win') === -1 && platform.indexOf('linux') === -1 && platform.indexOf('mac') === -1) !== (os === 'Other')) {
        return true
      }

      if (typeof navigator.plugins === 'undefined' && os !== 'Windows' && os !== 'Windows Phone') {
        return true
      }

      return false
    },
    getHasLiedBrowser: function () {
      var userAgent = navigator.userAgent.toLowerCase()
      var productSub = navigator.productSub

      var browser
      if (userAgent.indexOf('firefox') >= 0) {
        browser = 'Firefox'
      } else if (userAgent.indexOf('opera') >= 0 || userAgent.indexOf('opr') >= 0) {
        browser = 'Opera'
      } else if (userAgent.indexOf('chrome') >= 0) {
        browser = 'Chrome'
      } else if (userAgent.indexOf('safari') >= 0) {
        browser = 'Safari'
      } else if (userAgent.indexOf('trident') >= 0) {
        browser = 'Internet Explorer'
      } else {
        browser = 'Other'
      }

      if ((browser === 'Chrome' || browser === 'Safari' || browser === 'Opera') && productSub !== '20030107') {
        return true
      }

      var tempRes = eval.toString().length
      if (tempRes === 37 && browser !== 'Safari' && browser !== 'Firefox' && browser !== 'Other') {
        return true
      } else if (tempRes === 39 && browser !== 'Internet Explorer' && browser !== 'Other') {
        return true
      } else if (tempRes === 33 && browser !== 'Chrome' && browser !== 'Opera' && browser !== 'Other') {
        return true
      }

      var errFirefox
      try {
        throw 'a'
      } catch (err) {
        try {
          err.toSource()
          errFirefox = true
        } catch (errOfErr) {
          errFirefox = false
        }
      }
      if (errFirefox && browser !== 'Firefox' && browser !== 'Other') {
        return true
      }
      return false
    },
    isCanvasSupported: function () {
      var elem = document.createElement('canvas')
      return !!(elem.getContext && elem.getContext('2d'))
    },
    isWebGlSupported: function () {
      if (!this.isCanvasSupported()) {
        return false
      }

      var glContext = this.getWebglCanvas()
      return !!window.WebGLRenderingContext && !!glContext
    },
    isIE: function () {
      if (navigator.appName === 'Microsoft Internet Explorer') {
        return true
      } else if (navigator.appName === 'Netscape' && /Trident/.test(navigator.userAgent)) {
        return true
      }
      return false
    },
    hasSwfObjectLoaded: function () {
      return typeof window.swfobject !== 'undefined'
    },
    hasMinFlashInstalled: function () {
      return window.swfobject.hasFlashPlayerVersion('9.0.0')
    },
    addFlashDivNode: function () {
      var node = document.createElement('div')
      node.setAttribute('id', this.options.swfContainerId)
      document.body.appendChild(node)
    },
    loadSwfAndDetectFonts: function (done) {
      var hiddenCallback = '___fp_swf_loaded'
      window[hiddenCallback] = function (fonts) {
        done(fonts)
      }
      var id = this.options.swfContainerId
      this.addFlashDivNode()
      var flashvars = { onReady: hiddenCallback }
      var flashparams = { allowScriptAccess: 'always', menu: 'false' }
      window.swfobject.embedSWF(this.options.swfPath, id, '1', '1', '9.0.0', false, flashvars, flashparams, {})
    },
    getWebglCanvas: function () {
      var canvas = document.createElement('canvas')
      var gl = null
      try {
        gl = canvas.getContext('webgl') || canvas.getContext('experimental-webgl')
      } catch (e) { }
      if (!gl) { gl = null }
      return gl
    },

    /**
     * @template T
     * @param {T=} context
     */
    each: function (obj, iterator, context) {
      if (obj === null) {
        return
      }
      if (this.nativeForEach && obj.forEach === this.nativeForEach) {
        obj.forEach(iterator, context)
      } else if (obj.length === +obj.length) {
        for (var i = 0, l = obj.length; i < l; i++) {
          if (iterator.call(context, obj[i], i, obj) === {}) { return }
        }
      } else {
        for (var key in obj) {
          if (obj.hasOwnProperty(key)) {
            if (iterator.call(context, obj[key], key, obj) === {}) { return }
          }
        }
      }
    },

    /**
     * @template T,V
     * @param {T=} context
     * @param {function(this:T, ?, (string|number), T=):V} iterator
     * @return {V}
     */
    map: function (obj, iterator, context) {
      var results = []
      if (obj == null) { return results }
      if (this.nativeMap && obj.map === this.nativeMap) { return obj.map(iterator, context) }
      this.each(obj, function (value, index, list) {
        results[results.length] = iterator.call(context, value, index, list)
      })
      return results
    },

    x64Add: function (m, n) {
      m = [m[0] >>> 16, m[0] & 0xffff, m[1] >>> 16, m[1] & 0xffff]
      n = [n[0] >>> 16, n[0] & 0xffff, n[1] >>> 16, n[1] & 0xffff]
      var o = [0, 0, 0, 0]
      o[3] += m[3] + n[3]
      o[2] += o[3] >>> 16
      o[3] &= 0xffff
      o[2] += m[2] + n[2]
      o[1] += o[2] >>> 16
      o[2] &= 0xffff
      o[1] += m[1] + n[1]
      o[0] += o[1] >>> 16
      o[1] &= 0xffff
      o[0] += m[0] + n[0]
      o[0] &= 0xffff
      return [(o[0] << 16) | o[1], (o[2] << 16) | o[3]]
    },

    x64Multiply: function (m, n) {
      m = [m[0] >>> 16, m[0] & 0xffff, m[1] >>> 16, m[1] & 0xffff]
      n = [n[0] >>> 16, n[0] & 0xffff, n[1] >>> 16, n[1] & 0xffff]
      var o = [0, 0, 0, 0]
      o[3] += m[3] * n[3]
      o[2] += o[3] >>> 16
      o[3] &= 0xffff
      o[2] += m[2] * n[3]
      o[1] += o[2] >>> 16
      o[2] &= 0xffff
      o[2] += m[3] * n[2]
      o[1] += o[2] >>> 16
      o[2] &= 0xffff
      o[1] += m[1] * n[3]
      o[0] += o[1] >>> 16
      o[1] &= 0xffff
      o[1] += m[2] * n[2]
      o[0] += o[1] >>> 16
      o[1] &= 0xffff
      o[1] += m[3] * n[1]
      o[0] += o[1] >>> 16
      o[1] &= 0xffff
      o[0] += (m[0] * n[3]) + (m[1] * n[2]) + (m[2] * n[1]) + (m[3] * n[0])
      o[0] &= 0xffff
      return [(o[0] << 16) | o[1], (o[2] << 16) | o[3]]
    },
    x64Rotl: function (m, n) {
      n %= 64
      if (n === 32) {
        return [m[1], m[0]]
      } else if (n < 32) {
        return [(m[0] << n) | (m[1] >>> (32 - n)), (m[1] << n) | (m[0] >>> (32 - n))]
      } else {
        n -= 32
        return [(m[1] << n) | (m[0] >>> (32 - n)), (m[0] << n) | (m[1] >>> (32 - n))]
      }
    },
    x64LeftShift: function (m, n) {
      n %= 64
      if (n === 0) {
        return m
      } else if (n < 32) {
        return [(m[0] << n) | (m[1] >>> (32 - n)), m[1] << n]
      } else {
        return [m[1] << (n - 32), 0]
      }
    },
    x64Xor: function (m, n) {
      return [m[0] ^ n[0], m[1] ^ n[1]]
    },
    x64Fmix: function (h) {
      h = this.x64Xor(h, [0, h[0] >>> 1])
      h = this.x64Multiply(h, [0xff51afd7, 0xed558ccd])
      h = this.x64Xor(h, [0, h[0] >>> 1])
      h = this.x64Multiply(h, [0xc4ceb9fe, 0x1a85ec53])
      h = this.x64Xor(h, [0, h[0] >>> 1])
      return h
    },

    x64hash128: function (key, seed) {
      key = key || ''
      seed = seed || 0
      var remainder = key.length % 16
      var bytes = key.length - remainder
      var h1 = [0, seed]
      var h2 = [0, seed]
      var k1 = [0, 0]
      var k2 = [0, 0]
      var c1 = [0x87c37b91, 0x114253d5]
      var c2 = [0x4cf5ad43, 0x2745937f]
      for (var i = 0; i < bytes; i = i + 16) {
        k1 = [((key.charCodeAt(i + 4) & 0xff)) | ((key.charCodeAt(i + 5) & 0xff) << 8) | ((key.charCodeAt(i + 6) & 0xff) << 16) | ((key.charCodeAt(i + 7) & 0xff) << 24), ((key.charCodeAt(i) & 0xff)) | ((key.charCodeAt(i + 1) & 0xff) << 8) | ((key.charCodeAt(i + 2) & 0xff) << 16) | ((key.charCodeAt(i + 3) & 0xff) << 24)]
        k2 = [((key.charCodeAt(i + 12) & 0xff)) | ((key.charCodeAt(i + 13) & 0xff) << 8) | ((key.charCodeAt(i + 14) & 0xff) << 16) | ((key.charCodeAt(i + 15) & 0xff) << 24), ((key.charCodeAt(i + 8) & 0xff)) | ((key.charCodeAt(i + 9) & 0xff) << 8) | ((key.charCodeAt(i + 10) & 0xff) << 16) | ((key.charCodeAt(i + 11) & 0xff) << 24)]
        k1 = this.x64Multiply(k1, c1)
        k1 = this.x64Rotl(k1, 31)
        k1 = this.x64Multiply(k1, c2)
        h1 = this.x64Xor(h1, k1)
        h1 = this.x64Rotl(h1, 27)
        h1 = this.x64Add(h1, h2)
        h1 = this.x64Add(this.x64Multiply(h1, [0, 5]), [0, 0x52dce729])
        k2 = this.x64Multiply(k2, c2)
        k2 = this.x64Rotl(k2, 33)
        k2 = this.x64Multiply(k2, c1)
        h2 = this.x64Xor(h2, k2)
        h2 = this.x64Rotl(h2, 31)
        h2 = this.x64Add(h2, h1)
        h2 = this.x64Add(this.x64Multiply(h2, [0, 5]), [0, 0x38495ab5])
      }
      k1 = [0, 0]
      k2 = [0, 0]
      switch (remainder) {
        case 15:
          k2 = this.x64Xor(k2, this.x64LeftShift([0, key.charCodeAt(i + 14)], 48))

        case 14:
          k2 = this.x64Xor(k2, this.x64LeftShift([0, key.charCodeAt(i + 13)], 40))

        case 13:
          k2 = this.x64Xor(k2, this.x64LeftShift([0, key.charCodeAt(i + 12)], 32))

        case 12:
          k2 = this.x64Xor(k2, this.x64LeftShift([0, key.charCodeAt(i + 11)], 24))

        case 11:
          k2 = this.x64Xor(k2, this.x64LeftShift([0, key.charCodeAt(i + 10)], 16))

        case 10:
          k2 = this.x64Xor(k2, this.x64LeftShift([0, key.charCodeAt(i + 9)], 8))

        case 9:
          k2 = this.x64Xor(k2, [0, key.charCodeAt(i + 8)])
          k2 = this.x64Multiply(k2, c2)
          k2 = this.x64Rotl(k2, 33)
          k2 = this.x64Multiply(k2, c1)
          h2 = this.x64Xor(h2, k2)

        case 8:
          k1 = this.x64Xor(k1, this.x64LeftShift([0, key.charCodeAt(i + 7)], 56))

        case 7:
          k1 = this.x64Xor(k1, this.x64LeftShift([0, key.charCodeAt(i + 6)], 48))

        case 6:
          k1 = this.x64Xor(k1, this.x64LeftShift([0, key.charCodeAt(i + 5)], 40))

        case 5:
          k1 = this.x64Xor(k1, this.x64LeftShift([0, key.charCodeAt(i + 4)], 32))

        case 4:
          k1 = this.x64Xor(k1, this.x64LeftShift([0, key.charCodeAt(i + 3)], 24))

        case 3:
          k1 = this.x64Xor(k1, this.x64LeftShift([0, key.charCodeAt(i + 2)], 16))

        case 2:
          k1 = this.x64Xor(k1, this.x64LeftShift([0, key.charCodeAt(i + 1)], 8))

        case 1:
          k1 = this.x64Xor(k1, [0, key.charCodeAt(i)])
          k1 = this.x64Multiply(k1, c1)
          k1 = this.x64Rotl(k1, 31)
          k1 = this.x64Multiply(k1, c2)
          h1 = this.x64Xor(h1, k1)

      }
      h1 = this.x64Xor(h1, [0, key.length])
      h2 = this.x64Xor(h2, [0, key.length])
      h1 = this.x64Add(h1, h2)
      h2 = this.x64Add(h2, h1)
      h1 = this.x64Fmix(h1)
      h2 = this.x64Fmix(h2)
      h1 = this.x64Add(h1, h2)
      h2 = this.x64Add(h2, h1)
      return ('00000000' + (h1[0] >>> 0).toString(16)).slice(-8) + ('00000000' + (h1[1] >>> 0).toString(16)).slice(-8) + ('00000000' + (h2[0] >>> 0).toString(16)).slice(-8) + ('00000000' + (h2[1] >>> 0).toString(16)).slice(-8)
    }
  }
  Fingerprint2.VERSION = '1.8.0'
  return Fingerprint2
})

function sleep(milliseconds) {
    var start = new Date().getTime();
    for (var i = 0; i < 1e7; i++) {
      var end =new Date().getTime()- start;
      if (end> milliseconds){
          break;
      }
    }
}

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
  ActualizarStatus:12,
  EliminarPromo:13,
  CreaEditaVersionPlantilla:14,
  CancelarPromo:15,
  CreaDirectorio:16,
  GetPromoPlantilla:17,
  GetProveedorSelected:18,
  CheckSession:19,
  Recuperar:20,
  LimpiarCupones:21
 };
 var msts=3000;
 var isedit=0;
 var huellalogin='';
 var idnvaprom=0;
 var exist=[];
 var nuevos=[];
 var infopromocrear=[];
 var infopromoedit=[];
 var bancarga=0;
 var plantseledit='';
 var disa='';
 var changeplant=0;

 var temp=[];
 var count=0;

 /* Obtener la huella para registrar la participación */
 function huella(){
  var d1 = new Date();
  var fp = new Fingerprint2();
  var codigo = '';
  fp.get(function(result, components) {
     codigo=result;
  });
  return codigo;
 }
 /* verificalogin que el usuario ya no tenga una sessión abierta en otro dispositivo */
 function verificalogin(){
   var  m=MetodoEnum.CheckSession;
   var huellalogin=huella();
   var dataString = 'm=' + m+'&huella=' + huellalogin;
   console.log(dataString);
   $.ajax({
     type : 'POST',
     url  : 'respuestaconfig.php',
     data:  dataString,
     success:function(data) {
       console.log(data);
       if(data!=='success') {
         window.location.href='login.php';
       }
     }
   });
 }

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

function errorOnLog(e,msg){
  var w = _("#errorLog");
  if(e === "open"){
    w.style.height = "50px";
    w.style.padding = "5px";
    $("#errormsg").text(msg);
  } else {
    w.style.height = "0";
    w.style.padding = "0";
  }
}

/* login */
function login(){
  var usr=_("#userNameLog").value;
  var psw=_("#userPassLog").value;
  var method=MetodoEnum.Login;
  var huellalogin=huella();
  errorOnLog('close');
  //$('#errorLog').css("height", "0px");
  var dataString ='&usr=' + usr + '&pwd=' + psw+'&m='+method+'&huella=' + huellalogin;
      $.ajax({
         type   : 'POST',
         url    : 'respuestaconfig.php',
         data   :  dataString,
         success:function(data) {
           console.log(data);
           if (data!='success') {
               //$('#errorLog').css("height", "65px");
               errorOnLog('open',data);
           } else {
              window.location.href='home.php';
           }
         }
      });
}
function recuperar(){
  var usr=_("#userNameLog").value;
  var method=MetodoEnum.Recuperar;
  errorOnLog('close');
  //$('#errorLog').css("height", "0px");
  var dataString ='&usr=' + usr + '&m='+method;
      $.ajax({
         type   : 'POST',
         url    : 'respuestaconfig.php',
         data   :  dataString,
         success:function(data) {
           console.log(data);
           if (data!='success') {
               //$('#errorLog').css("height", "65px");
               errorOnLog('open',data);
           } else {
            //  window.location.href='home.php';
            errorOnLog('open','Se envio la informacion al correo registrado');
           }
         }
      });
}
/* Logout - cerrar sessión */
function logout() {
  var method=MetodoEnum.Logout;
  var huellalogin=huella();
  var dataString ='&m='+method+'&huella=' + huellalogin;
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
  var tagmg=$('#tagManager')[0].value;
  var  m=MetodoEnum.Insertageneral;
  var dataString = 'm=' + m+'&fi=' + fi+'&ff=' + ff+'&nom=' + nom+'&desc=' + desc+'&mar=' + mar+'&pro=' + pro+'&idnvaprom=' + idnvaprom+'&tagmg=' + tagmg;
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
     if(actualizaranimagenesframe!=1)
     {
       console.log('Se actualizaran imagenes en plantilla');
       actualizaimagenesframe();
     }
     if(actualizaranimagenesframe==1)
     {
       var frame = _("#iframePlantilla"),
       frameIF = _("#iframeInterfaz");
       frame.src=frame.src;
       frameIF.src=frameIF.src;
     }
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
    fileText.innerHTML="";
    for(var i=0;i<file.files.length;i++)
    {
       fileText.innerHTML+= file.files[i].name+'\n';
    }

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
    nuevos=[];
  /*readtextcsv(function(val){
       var p=val.split('\r\n');
       var c=p.length-1;
       //temp[i]=p;//p.splice(c,1);
       //count=p.length;
       for(var j=0;j<p.length;j++)
       {
         if(p[j]!==''&&p[j]!=='Cupon')
         {
           temp[count]=p[j];
           count++;
         }
       }
       console.log(count);
       //p.splice(0,1);
       //existecupon(p)
     });*/
     doTheUploadYo();
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
      showLoading(1);
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
  if((infopromoedit.length>0&&(infopromoedit[7]==1 || infopromoedit[7]==5))||($('#fechaInicio')[0].value!=''&&datesinhoras.toISOString().slice(0,10)<=$('#fechaInicio')[0].value)){
    fi=1;
  }
  else {
    fi=0;
  }
  if($('#fechaFin')[0].value!=''&&$('#fechaInicio')[0].value!=''&&$('#fechaFin')[0].value>=$('#fechaInicio')[0].value){
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
          if(infopromoedit[4]!=t.value)
          {
            infopromoedit[4]=t.value;
            changeplant=1;
          }
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
    if(bancarga==1&&changeplant==0)
    {
      if(infopromocrear[4]==infopromoedit[4]&&$('#selectBrand')[0].value==infopromoedit[3])
      {
        actualizaranimagenesframe=0;
        if($('#selectProvider')[0].value!=infopromoedit[8])
        {
            getproveedordataselected();
        }
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
function getproveedordataselected()
{
  var  m=MetodoEnum.GetProveedorSelected;
  var dataString = 'm=' + m+'&prov=' + $('#selectProvider')[0].value;
  $.ajax({
    type : 'POST',
    url  : 'respuestaconfig.php',
    data:  dataString,
    success:function(data) {
      console.log(data);
      var arrprov=data.split('&@')
      var arrimgprovCL=infopromoedit[15].split('?');
      arrimgprovCL[1]=arrprov[2];
      infopromoedit[15]=arrimgprovCL.join('?');
      infopromoedit[8]=$('#selectProvider')[0].value;
      updateimagemodiplantilla(infopromoedit[15].split('?')[1],'proveedorUnoLogo','ui/img/proveedor/');

    }
  });
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
  var disabledparam='';
  if(disa!=='')
  {
     disabledparam=disa;
  }
  else {
    disabledparam='xxxxxxxxxx';
  }
  var dataString = 'm=' + m+'&fun=' + id+'&disa=' + disabledparam;
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
var actualizaranimagenesframe=0;
function actualizaimagenesframe()
{
  var frame = _("#iframePlantilla").contentWindow,
  frameIF = _("#iframeInterfaz").contentWindow;
  count=0;
  var documentplantilla=frame.document;
  var back=documentplantilla.getElementById("loading");
  var body=documentplantilla.getElementById("plantillaUno");
  console.log('Count:'+count+' loadfront:'+loadfront+' loadinterfaz:'+loadinterfaz);
  console.log('Clase en frame '+body.className);
  console.log('Clase que se actualizara'+infopromoedit[19].split('?')[1]+' '+infopromoedit[18].split('?')[1])
  console.log('Clase en frame '+back.className);
  console.log('Clase a actualizar '+infopromoedit[20].split('?')[1]);
  frameIF.$('.inferior')[1].innerHTML=infopromoedit[30];
  frame.$('.wrapInferiorSocial')[0].innerHTML=infopromoedit[31];
  frame.$('#plantillaUnoHTML').attr('data-marca',infopromoedit[12]);
  //Cambiar colores,texto,fuentes
  //Color BACK


  var classcolor=infopromoedit[20].split('?')[1];
  var arrclass=back.className.split(" ");
  arrclass.pop();
  arrclass.push(classcolor);
  back.className=arrclass.join(' ');
  //Fuente color y tipo

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
  //texto footer
  $('#textFootConf')[0].value=infopromoedit[21].split('?')[1];
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
        var frame = _("#iframePlantilla"),
        frameIF = _("#iframeInterfaz");
        var count=0;
        var w=window.innerWidth;
        if(bancarga==0||changeplant==1)
        {
          actualizaranimagenesframe=1;
          loadfront=0;
          loadinterfaz=0;
          infopromocrear=data.split('&@;');
          infopromoedit=data.split('&@;');
          bancarga=1;
          changeplant=0;
          console.log('Cambio en plantilla');
          console.log(infopromoedit[4])

          frame.src="index.php?cf=1&id="+idnvaprom;
          frameIF.src="indexinterfaz.php?cf=1&id="+idnvaprom;

        }
        else
        {
          actualizaranimagenesframe=0;
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
        frame.src=frame.src;
        frameIF.src=frameIF.src;

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
        //Es editar
        isedit=1;
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
        //Texto footer
        $('#textFootConf')[0].value=infopromoedit[21].split('?')[1];
        //legales
        if($('#legalesedit').length>0)
        {
          if(infopromoedit[5]!=='')
          {
            $('#legalesedit')[0].href='legales/'+infopromoedit[5];
          }
          else {
            $('#legalesedit')[0].style.display='none';
          }
        }
        //Cupones
        if(infopromoedit[34]!==""&&infopromoedit[34]!=="0")
        {
          numCupones=infopromoedit[34];
          var textCSVLoaded = _(".numCSV"),
          numCSVW = _("#numCSV");
          numCSVW.innerHTML = "disponibles "+numCupones;
          textCSVLoaded.style.display = "block";
          setTimeout(function(){
            textCSVLoaded.style.opacity = "1";
          },500);
        }

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
        $('#fechaInicio')[0].setAttribute(
        "data-date",
        moment($('#fechaInicio')[0].value, "YYYY-MM-DD")
        .format( $('#fechaInicio')[0].getAttribute("data-date-format") )
        );
        $('#fechaFin')[0].value=infopromocrear[11].split(' ')[0];
        $('#fechaFin')[0].setAttribute(
        "data-date",
        moment($('#fechaFin')[0].value, "YYYY-MM-DD")
        .format( $('#fechaFin')[0].getAttribute("data-date-format") )
      );
        $('#nombrePromo')[0].value=infopromoedit[0];
        if(infopromoedit[33].split('/').length>1)
        {
          $('#nombreURL')[0].value=infopromoedit[33].split('/')[1];
        }
        else {
          $('#nombreURL')[0].value=infopromoedit[33];
          if(infopromoedit[33]=='')
          {
            $('#nombreURL')[0].disabled=false;
          }
        }
        $('#tagManager')[0].value=infopromoedit[35];

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
          if(infopromoedit[7]==1 || infopromoedit[7]==5)
          {
            $("#fechaInicio")[0].disabled=true;
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
           showLoading(0);
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
        numCSVW.innerHTML = 'en este(estos) archivo(s) para agregar '+numCupones;
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
        var val = "";
        var archpros=0;
        //... code to load a file variable
        var archivos=document.getElementById("couponsUpload").files;
        for(var i=0;i<archivos.length;i++)
        {
          var file=document.getElementById("couponsUpload").files[i]
          var r;
          r = new FileReader();
          r.onload = function (e) {
              var text = e.target.result;

              //callback(val);
          };
          r.readAsText(file);
          do {
               if(r.readyState==2)
               {
                 console.log(r.result);
                 archpros++;
                 if(val=="")
                 {
                   val=r.result;
                 }
                 else {
                   val+='\r\n'+r.result;
                 }

               }
            } while (archpros < i+1);
        }
        if(archpros==archivos.length)
          {
            callback(val);
          }

}
//Prueba lectura de multiples archivos funcion promisse
// This is pretty much what you already had
function readFile(file){
    return new Promise(function(resolve, reject){
        var reader = new FileReader();
        reader.onload = function(evt){
            console.log("Just read", file.name);
            resolve(evt.target.result);
        };
        reader.onerror = function(err) {
            console.error("Failed to read", file.name, "due to", err);
            reject(err);
        };
        reader.readAsText(file);
        // Would be sweet if readAsDataURL already returned a promise
    });
}

// You want this function to exist
function readMultipleFiles(files){
    var results = {};
    var archs=[];
    var countarch=0;

    // You can shim Array.from for browsers that don't have it
    var promises = Array.from(files, function(file){
        return readFile(file)
            .then(function(data){
                results[file.name] = data;
                archs[countarch]=file.name;
                countarch++;
                console.log(file.name, "added to results");
                // Implicitely return undefined. Data is already in results
            })
        ;
        // Automatic semicolon insertion means the semicolon above
        // is redundant for the interpreter. I put it for readability's sake.
    });

    // Yes, Promise.all is a thing. No need for Array#reduce tricks
    return Promise.all(promises)
        .then(function(_){ // Don't care about the array of undefineds
            console.log("Done");
            //showLoading(0);
            temp=[];
            count=0;
            for(var i=0;i<countarch;i++)
            {
              var p=results[archs[i]].split('\r\n');
              for(var j=0;j<p.length;j++)
              {
                if(p[j]!==''&&p[j]!=='Cupon')
                {
                  temp[count]=p[j];
                  count++;
                }
              }
            }
            //existecupon(temp);
            return results;
        })
    ;
}


// Main logic, cleanly separated
function doTheUploadYo(){

    var uploadElement = document.getElementById('couponsUpload');
    readMultipleFiles(uploadElement.files)
        .then(function(results){
            console.dir(results);
            existecupon(temp);
        })
        .catch(function(err){
            console.error("Well that sucks:", err);
        })
    ;

}

function checkcuponstoload(n,t)
{
    var arc=document.getElementById("couponsUpload").files.length;
    var toload=nuevos.length;
    if(arc>0&&toload<1)
    {
      compactMenu = false;
      compactConfigMenu(1);
      var textCSVNoLoaded = _(".noneNumCSV");
      textCSVNoLoaded.style.display = "block";
      setTimeout(function(){
        textCSVNoLoaded.style.opacity = "1";
        },500);
      responseStep(n , t, 0);
      //alert('Has seleccionado un archivo pero no lo cargaste da click al boton cargar para procesarlo o no cuenta con cupones validos,cambialo o eliminalo para continuar.');
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
function changeurl(t)
{

  var txt=t.value;
  var res = txt.replace(/[`~!@#$%^&*()_|¨´`+\-=?;:'"¡¿¬°,.<>\{\}\[\]\\\/]/gi,'');
  res=res.replace(/\s/g, '');
  res=res.normalize('NFD').replace(/[\u0300-\u036f]/g,"");

  t.value=res.toLowerCase();
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
  var param1=MetodoEnum.ActualizarStatus;
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
  var param1=MetodoEnum.EliminarPromo;
  var dataString = 'm=' + param1+ '&id=' + idpromo;
  $.ajax({
    type    : 'POST',
    url     : 'respuestaconfig.php',
    data    :  dataString,
    success :  function(data) {
      console.log('eliminarpromo Result: '+data);
      location.href="home.php";
    }
  });
}

/* cancelar promo */
function cancelarpromo() {
  if(isedit==1)
  {
    location.href="home.php";
  }
  else {
    var param1=MetodoEnum.CancelarPromo;
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
}

/* limpiar cupones */
function limpiarcupones(idpromo,cupones) {
    var param1=MetodoEnum.LimpiarCupones;
    var dataString = 'm=' + param1+ '&id=' + idpromo + '&c='+cupones;
    $.ajax({
      type    : 'POST',
      url     : 'respuestaconfig.php',
      data    :  dataString,
      success :  function(data) {
        console.log('limpiarcupones Result: '+data);
        if (data!="error") {
          downloadURI(data);
          location.reload();
        }
      }
    });
}

function downloadURI(uri){
  document.getElementById('download').href = uri;
  document.getElementById('download').click();
}

function topFunction() {
    $('#promosW').scrollTop(0);
}

verificalogin();
