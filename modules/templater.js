
    // TODO clear tpl cache folder on first start

    soften.options({
        'templates-path': '/templates',
        'templates-autocompile': true
    }, true);

    // this need for soy
    navigator = {userAgent: 'WebKit'};
    // NOTE don't forgot to add 'module.exports = soy;' at the end of soyutils.js file
    soy = require(soften.option('core-path') + '/lib/closure-templates/soyutils.js');

    var cache = {};

    this.info = function(path) {

        if(!cache[path]) {
            var info = {
                file: soften.path('templates') + '/' + path + '.tpl',
                jsfile: soften.path('templates', true) + '/' + path + '.js',
                soyfile: soften.path('templates', true) + '/' + path + '.soy'
            };
            var chunks = path.split('/');
            chunks.unshift('tpl');
            info.name = chunks.pop();
            info.namespace = chunks.join('.');
            info.fullname = info.namespace + '.' + info.name;
            //info.compiled = soften.utils.exists(info.jsfile);
            info.compiled = false;
            info.compiling = false;
            info.watches = [];

            cache[path] = info;

            soften.option('templates-autocompile') && this.compile(path);
        }
        return cache[path];
    }

    this.compile = function(path) {

        var info = this.info(path);

        info.compiling = true;

        // prepare soy file contents
        // load tpl contents
        var soy = soften.utils.readFile(info.file);
        // add name
        soy = soy.replace('{template}', '{template .' + info.name + '}');
        // ensure that we have template head
        var matches = (new RegExp('^\\s*\\/\\*\\*.*?{', 'gi')).exec(soy.split("\n").join('$^BR^$'));
        //if(!soy.match(new RegExp('^\\s*\\/\\*\\*', 'gi'))) {
        if(!matches) {
            soy = '/**\n * ' + info.name + ' template\n */\n' + soy;
        } else {
            // load template required data
            var head = matches[0].split('$^BR^$').join('\n'), r = new RegExp('@param\\\?? ([a-zA-Z0-9]+)( |\n)', 'gi');
            while(matches = r.exec(head)) {
                info.watches.push(matches[1]);
            }
        }
        // add namespace
        soy = '{namespace ' + info.namespace + '}\n\n' + soy;

        // create soy file
        soften.utils.writeFile(info.soyfile, soy);

        // compile soy file
        soften.utils.exec('java -jar ' + soften.option('core-path') + '/lib/closure-templates/SoyToJsSrcCompiler.jar --outputPathFormat ' + info.jsfile + ' ' + info.soyfile, function(){
            // TODO how to resolve this problem more properly ??
            // replace 'var tpl' with 'tpl', we need global variable
            soften.utils.writeFile(info.jsfile, soften.utils.readFile(info.jsfile).replace('var tpl', 'tpl'));
            //callback && callback();
            //soften.events.once('template-compiled-' + path, callback_);
            soften.events.emit('template-compiled-' + path);
            info.compiled = true;
        });

    };

    this.build = function(path, data, callback) {
        // TODO add cache module and use it here

        var info = this.info(path);

        var params = {};
        info.watches.forEach(function(id) {
            // send only watches params
            params[id] = data[id];
        });

        var callback_ = function() {
            require(info.jsfile);
            callback(eval(info.fullname)(params));
        }

        if(!info.compiled) {
            //this.compile(path, callback_);
            soften.events.once('template-compiled-' + path, callback_);
        } else {
            callback_();
        }
    }
