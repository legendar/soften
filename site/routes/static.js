

    /*this.route = [

        {match: '/static/tpl/(.*).js', route: [

            {headers: {'content-type': 'text/javascript'}},

            {action: 'static/tpl', vars: ['name']},

            END

        ]}

    ];*/

    this.match('/static/tpl/(.*).js', function() {

        this.headers({
            'content-type': 'text/javascript'
        });

        this.action('static/tpl', {vars: ['name']});

        this.end();

    });
