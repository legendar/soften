
    soften.options({

        'http-port': '8080'

    }, true);

    var http = require('http');

    http.createServer(function(request, response) {

        var context = {
            request: request,
            response: response,
            location: require('url').parse('http://' + request.headers.host + request.url)
        };

        soften.events.emit('http-request', context);

    }).listen(soften.option('http-port'));

    console.log('soften server running at port ' + soften.option('http-port'));
