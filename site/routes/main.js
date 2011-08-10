
    /*this.route = [

        {action: 'test'},

        {include: 'static.js'},

        {match: '/', route: [

            {template: 'main'}

        ]}
    ];*/

    //console.log(this.include);


    this.action('test');
    this.check('test');

    //this.include('static.js');

    this.match('/', function() {

        this.template('main');

        this.end();

    });

