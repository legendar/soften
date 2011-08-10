
    soften.options({

        'http-port': '8080'

    }, true);

    var http = require('http');

    http.createServer(function(request, response) {

/*        var context = {
            request: request,
            response: response,
            location: require('url').parse('http://' + request.headers.host + request.url)
        };*/

        soften.events.emit('http-request', new Context(request, response));

    }).listen(soften.option('http-port'));

    var Context = this.Context = function(request, response) {
        this.request = request;
        this.response = response;
        this.location = require('url').parse('http://' + request.headers.host + request.url);
        //this.data = {};
    };

    Context.prototype.match = function() {

    };

    Context.prototype.data = function() {

    }

    console.log('soften server running at port ' + soften.option('http-port'));
