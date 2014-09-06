$(function(){

    $.Console = Class.extend(function(){

        var _console = this;
        _console.debugMode = 'all';

        _console.constructor = function(debugMode){
            _console.debugMode = ((typeof debugMode === 'undefined') ? 'all' : debugMode);
        };

        // print to console if available and we're in debug mode
        _console.console = function(type, data) {
            if(!_console.debugMode || !window.console) { return; }
            console[console[type] ? type : 'log'](data.length > 1 ? Array.prototype.slice.call(data) : data[0]);
        };

        // pass args onto console method for output
        _console.log = function() { _console.console('log', arguments); };
        _console.debug = function() { _console.console('debug', arguments); };
        _console.warn = function() { _console.console('warn', arguments); };
        _console.error = function() { _console.console('error', arguments); };


    });


});