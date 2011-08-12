
    /*this.route = [

        {action: 'test'},

        {include: 'static.js'},

        {match: '/', route: [

            {template: 'main'}

        ]}
    ];*/

    //console.log(this.include);


    this.action('test');

    //this.include('static.js');

    this.match('/', function() {
        /*request: {
            'pid': 'int',
            ''
        }
        match();
            secure();
            method();
            check();
        action();
        tpl();
        end();*/

        this.template('main');

        this.end();

    });
