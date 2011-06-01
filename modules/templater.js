
    soften.options({
        'templates-path': '/templates'
    }, true);

    // this need for soy
    navigator = {userAgent: 'WebKit'};
    // NOTE don't forgot to add 'module.exports = soy;' at the end of soyutils.js file
    soy = require(soften.option('core-path') + '/lib/closure-templates/soyutils.js');

    this.info = function(path) {
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
        info.compiled = soften.utils.exists(info.jsfile);
        return info;
    }

    this.compile = function(path, callback) {

        var file = soften.path('templates', true) + '/' + path;
        var chunks = path.split('/');
        chunks.unshift('tpl');
        var name = chunks.pop();
        var namespace = chunks.join('.');

        var info = this.info(path);

        // prepare soy file contents
        // load tpl contents
        var soy = soften.utils.readFile(info.file);
        // add name
        soy = soy.replace('{template}', '{template .' + info.name + '}');
        // ensure that we have template head
        if(!soy.match(new RegExp('^\\s*\\/\\*\\*', 'gi'))) {
            soy = '/**\n * ' + info.name + ' template\n */\n' + soy;
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
            callback();
        });

    };

    this.build = function(path, data, callback) {
        // TODO add cache module and use it here

        var info = this.info(path);

        var callback_ = function() {
            require(info.jsfile);
            callback(eval(info.fullname)(data));
        }

        if(!info.compiled) {
            this.compile(path, callback_);
        } else {
            callback_();
        }
    }
