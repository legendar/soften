
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
        this.query = query;
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
