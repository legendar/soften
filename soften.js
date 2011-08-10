
    // global soften object
    soften = module.exports;

    // options object
    var options = {};

    // set options
    soften.options = function(options_, defaults) {
        for(i in options_) {
            (!defaults || options[i] == undefined) && (options[i] = options_[i]);
        }
    };

    // get option
    soften.option = function(name) {
        return options[name];
    };

    // get path
    soften.path = function(name, cached) {
        return soften.option('site-path') + (cached ? soften.option('cache-path'): '') + (name ? soften.option(name + '-path'): '');
    };

    // core events emitter
    soften.events = new (require('events').EventEmitter)();

    // initialize soften
    soften.init = function(options) {

        // set site options
        soften.options(options);

        // need for set paths
        var path = require('path');
        // load defaults
        soften.options({
            modules: ['db', 'router', 'templater'/*, 'cache'*/, 'utils', 'templater'],
            'core-path': path.normalize(__dirname),
            'site-path': path.normalize(path.dirname(module.parent.filename)),
            'cache-path': '/cache'
        }, true);

        require.paths.unshift(soften.path() + '/modules');

        // load modules
        soften.modules = {};
        soften.option('modules').forEach(function(name){
            soften.modules[name] = require(soften.option('core-path') + '/modules/' + name + '.js');
        });
    };

    soften.require = function(name) {
        soften.modules[name] ||
            (soften.modules[name] =
                require(soften.option('core-path') + '/modules/' + name + '.js'));
    };
/*
    soften.exception = {
        invoke: function(e, chunk) {
            if(typeof e != 'object') {
                e = {
                    type: soften.constants.SOFTEN,
                    chunk: chunk || soften.constants.SOFTEN,
                    code: e,
                    message: soften.constants.messages[e]
                };
            }
            throw e;
        },

        check: function(e, chunk, code) {

            //if(!type || e.type && e.type == type) {
            if(e.type && e.type == soften.constants.SOFTEN) {
                if(!chunk || e.chunk && e.chunk == chunk) {
                    if(!code || e.code && e.code == code) {
                        return true;
                    }
                }
            }

            return false;
        },
    };
*/
