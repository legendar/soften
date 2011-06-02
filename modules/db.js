
    var defaults = {
        // TODO types
        //'type': 'pgsql',
        'host': 'localhost',
        'port': 5432,
        'name': 'test',
        'user': 'test',
        'pass': 'test',
    };

    soften.options({
        'db': defaults
    });

    var connections = {};

    var pg = require(soften.option('core-path') + '/lib/node-postgres');

    // global
    db = soften.db = function(name, type) {
        var id = 'db';
        if(name) {
            id += '-' + name;
            type && (id += '-' + type);
        }
        if(!connections[id]) {
            // create new connection
            console.log(soften.option(id));
            connections[id] = new DB(soften.option(id));
        }
        return connections[id];
    }

    function prepareConnectionString(options) {
        // TODO defaults, etc
        return 'tcp://' + options.user + ':' + options.pass + '@' + options.host + ':' + options.port + '/' + options.name;
    }

    var DB = function(options) {

        console.log(prepareConnectionString(options));
        this.client = new pg.Client(prepareConnectionString(options));
        this.client.connect();

        this.execute = function(type, callback) {
            // execute query
            this.client.query(this.query, (function(err, result){

                var r;

                switch(type) {
                    case 'one':
                        for(i in result.rows[0]) {
                            r = result.rows[0][i];
                            break;
                        }
                        break;
                    case 'row':
                        r = result.rows[0];
                        break;
                    case 'col':
                        r = [];
                        result.rows.forEach(function(row) {
                            for(i in row) {
                                r.push(row[i]);
                                break;
                            }
                        });
                        break;
                    case 'all':
                    default:
                        r = result.rows;
                }

                callback(r, result.rowCount);

            }).bind(this));
        };
    };

    DB.prototype.query = function(query, values) {
        // prepare query
        if(values) {
            if(!(values instanceof Array)) {
                values = [values];
            }

            /*
                %d
                %i
                %s
                %and
                %or
            */
            var placeholders = {

                f: parseFloat,
                i: parseInt,
                s: function(value) {
                    return '\'' + value.replace('\'', '\\\'') + '\'';
                },
                // TODO
                // how to usage??
                // SELECT * FROM tablename WHERE value in (%and)
                /*and: function(value) {
                    //
                    var ret = 
                    value.forEach(function(v){
                        if(v instanceof Array) {
                            
                        }
                    });
                },
                or: function(value) {
                    //
                }*/

            };

            var regexp = new RegExp('(^.*?)%([a-z]+|%)', 'gi'), match, q = '';
            while(values.length > 0 && (match = regexp.exec(query))) {
                query = query.replace(match[0], '');
                q += match[1];

                if(match[2] == '%') {
                    q += '%';
                    continue;
                } else if(placeholders[match[2]] == undefined) {
                    q += '%' + match[2];
                } else {
                    q += placeholders[match[2]](values.shift());
                }
            }
            this.query = q;
        } else {
            this.query = query;
        }
        return this;
    };

    // TODO maybe proxy ??
    DB.prototype.one = function(callback) {
        this.execute('one', callback);
    };

    DB.prototype.col = function(callback) {
        this.execute('col', callback);
    };

    DB.prototype.row = function(callback) {
        this.execute('row', callback);
    };

    DB.prototype.all = function(callback) {
        this.execute('all', callback);
    };

    // test
    /*db().query('SELECT id FROM list').col(function(result, length){

        console.log(length);
        console.log(result);

    });*/

    //console.log(db().query('SELECT id FROM list WHERE value = %s AND id = %i AND', ['te', '223s', '']).query);
