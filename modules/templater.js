
    soften.options({
        'templates-path': '/templates'
    }, true);

    // this need for soy
    navigator = {userAgent: 'WebKit'};
    // NOTE don't forgot to add 'module.exports = soy;' at the end of soyutils.js file
    soy = require(soften.option('core-path') + '/lib/closure-templates/soyutils.js');

    this.build = function(path, data, callback) {
        // TODO add cache module and use it here

        var file = soften.path('templates', true) + '/' + path;
        var chunks = path.split('/');
        var name = chunks.pop();
        var namespace = 'tpl.' + chunks.join('.');

        var callback_ = function() {
            require(file + '.js');
            callback(eval(namespace + '.' + name)(data));
        }

        if(!soften.utils.exists(file + '.js')) {
            // we should compile template
            // create soy file
            soften.utils.writeFile(file + '.soy',
                '{namespace ' + namespace + '}\n\n' +
                soften.utils.readFile(soften.path('templates') + '/' + path + '.tpl')
                 .replace('{template}', '{template .' + name + '}'));
            // compile soy file
            soften.utils.exec('java -jar ' + soften.option('core-path') + '/lib/closure-templates/SoyToJsSrcCompiler.jar --outputPathFormat ' + file + '.js ' + file + '.soy', function(){
                // TODO how to resolve this problem more properly ??
                // replace 'var tpl' with 'tpl', we need global variable
                soften.utils.writeFile(file + '.js', soften.utils.readFile(file + '.js').replace('var tpl', 'tpl'));

                callback_();
            });
        } else {
            callback_();
        }
    }
