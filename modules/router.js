
    soften.options({

        'router-path': '/routes',
        'router-file': 'main.js',
        'actions-path': '/actions',
        'templates-path': '/templates'

    }, true);

    // array with all avaliables router items, order by priority
    var order = [
        // check url to mathwith pattern
        'match',
        // check privileges
        'secure',
        // allowed methods POST/GET/PUT/DELETE
        'method',
        // check have we cache for this action ??
        'cache',
        // clip url
        'clip',
        // change actions dir
        'dir',
        // load vars (matches from url)
        'vars',
        // run action
        'action',
        // templating data
        'template',
        // templating into section
        //'section', // we don't need function for it
        // send headers
        'headers',
        // stop route
        'end',
        // include another router file,
        'include',
        // sub-route
        'route',
    ];

    // for speed up
    var length = order.length;

    // some constants
    var NEXT  = 100;
    var SKIP  = 200;
    // following constants should be global, used in route maps
    BREAK = 300;
    END   = 400;


    function end(context, status) {
        // TODO move to http module
        if(!context.headers['content-type']) {
            context.headers['content-type'] = 'text/html; charset=utf-8'
        }

        context.response.writeHead(status || 200, context.headers);

        context.response.write(context.template || '');
        context.response.end();
        return;
    }

    function levelUp(context) {

        var route = context.router.routes.pop();
        context.router.matches.pop();

        if(!route) {
            end(context);
            return;
        }

        context.router.route = route;
        nextItem(context);
    }

    function nextItem(context) {

        //console.log('next item');

        var item = context.router.route.shift();

        if(!item || item === BREAK) {

            levelUp(context);
            return;

        }

        if(item === END) {
            end(context);
            return;
        }

        context.router.index = -1;
        context.router.item = item;

        next(context);

    }

    function next(context, state) {

        //console.log('next');

        context.router.index++;

        if(!state && context.router.index == length) {
            context.router.macth && context.router.matches.pop();
            state = NEXT;
        }

        if(state) {

            state <= SKIP && nextItem(context);

            state == BREAK && levelUp(context);

            // TODO error(404) ??
            state == END && end(context, status);

            return;
        }

        if(context.router.item[order[context.router.index]]) {

            //console.log('== ' + order[context.router.index]);

            items[order[context.router.index]](
                context.router.item[order[context.router.index]],
                context);

        } else {

            next(context);

        }

    }

    var items = {

        route: function(route, context) {
            // level down
            context.router.routes.push(context.router.route);
            context.router.route = route.slice();
            nextItem(context);
        },

        match: function(pattern, context) {
            var matches = (new RegExp('^' + pattern + '$', 'gi')).exec(context.router.path);
            matches && (context.router.matches.push(matches));
            next(context, matches ? null : SKIP); // for tests
        },

        secure: function(level, context) {
            // TODO
            next(context);
        },

        clip: function(part, context) {
            context.router.path = context.router.path.replace(part, '');
            next(context);
        },

        dir: function(path, context) {
            // TODO
            next(context);
        },

        action: function(name, context) {
            var action = require(soften.path('actions') + '/' + name + '.js');

            if(action.sync) {

                context.next = function(){};

                action.check && action.check.apply(context);
                action.process && action.process.apply(context);
                next(context);
            } else {

                action.check || (action.check = function(){context.next()});
                action.process || (action.process = function(){context.next()});

                context.next = function() {

                    context.next = function() {
                        next(context);
                    };

                    action.process.apply(context);

                };

                action.check.apply(context);
            }
        },

        cache: function(expire, context) {
            // TODO
            next(context);
        },

        vars: function(names, context) {
            var vars = {}, mx, my, matches;
            names.forEach(function(name, i) {
                name = name.split(':');
                mx = name.length - 1;
                my = name.pop();
                if(mx == 0) {
                    mx = 1;
                    name = my;
                    my = i+1;
                }
                matches = context.router.matches[context.router.matches.length - mx];
                vars[name] = matches[my];
            });
            context.vars = vars;
            next(context);
        },

        template: function(path, context) {
            var section = context.router.item.section || '';
            soften.modules.templater.build(path, context.data, function(tpl){
                if(section) {
                    context.data.sections[section] = tpl;
                } else {
                    context.template = tpl;
                }
                console.log(tpl);

                next(context);
            });
        },

        end: function(status, context) {
            end(context, END, status);
        },

        include: function(file, context) {
            file = soften.path('router') + '/' + file;
            items.route(require(file).route, context);
        },

        headers: function(headers, context) {
            for(i in headers) {
                context.headers[i] = headers[i];
            }
            next(context);
        }
    };

    soften.events.on('http-request', function(context){
        // prepare context
        context.router = {
            routes: [],
            route: [],
            item: null,
            index: -1,
            matches: [],
            path: context.location.pathname
        };

        context.headers = {};
        context.data = {sections: {}};
        context.template = '';

        console.log('=============================');
        console.log(context.location.href);

        context.next = function(context) {
            next(context);
        };

        items.include(soften.option('router-file'), context);
    });

