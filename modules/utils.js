
    soften.utils = this;

    var fs = require('fs');
    var path = require('path');
    var core = soften.path();
    var cp = require('child_process');

    this.mkdirs = function(dir) {

        dir = dir.replace(core, '');

        var chunks = dir.split('/');

        dir = core + chunks.shift();

        chunks.forEach(function(chunk){

            dir = dir + '/' + chunk;

            //console.log(dir);
            path.existsSync(dir) || fs.mkdirSync(dir, 0777);

        });

    };

    this.exists = function(file) {

        return path.existsSync(file);

    };

    this.readFile = function(file) {

        return fs.readFileSync(file, 'utf8');

    };

    this.writeFile = function(file, body) {

        this.mkdirs(path.dirname(file));

        fs.writeFileSync(file, body);

    };

    this.exec = function(cmd, callback) {

        cp.exec(cmd, function (error) {
            if (error !== null) {
                console.log(cmd + '\n' + error);
            }
            callback();
        });

    }
