

    this.process = function() {

        var info = soften.modules.templater.info(this.vars.name);

        var callback = (function() {
            this.template = soften.utils.readFile(info.jsfile);
            this.next();
        }).bind(this);

        if(!info.compiled) {
            soften.modules.templater.compile(this.vars.name, callback);
        } else {
            callback();
        }

    }
