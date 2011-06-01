

    this.route = [

        {match: '/static/tpl/(.*).js', route: [

            {headers: {'content-type': 'text/javascript'}},

            {action: 'static/tpl', vars: ['name']},

            END

        ]}

    ];
