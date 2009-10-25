
var sftURL = Class.create({
    initialize : function(url, mode){
        this.mode = mode || false;
        this.url = url || document.location.href.toString();;
        this.parse();
        return this;
    },
    parse : function() {
        var url = this.url;
        if(url.indexOf('#') > -1) {
            this.anchor = url.substring(url.indexOf('#') + 1);
            url = url.substring(0, url.indexOf('#'));
        }
        if(url.indexOf('?') > -1) {
            this.request = url.substring(url.indexOf('?') + 1);
            url = url.substring(0, url.indexOf('?'));
        }
        if(url.indexOf('://') > -1) {
            this.protocol = url.substring(0, url.indexOf('://'));
            url = url.substring(url.indexOf('://') + 3);
            this.uri = url.substring(url.indexOf('/'));
            url = url.substring(0, url.indexOf('/'));
            if(url.indexOf(':') > -1) {
                this.port = url.substring(url.indexOf(':') + 1);
                url = url.substring(0, url.indexOf(':'));
            }
            this.site = url;
        } else {
            this.uri = url;
        }
        if(this.mode == false && this.uri != '' && this.uri.substring(this.uri.length - 1) == '/') {
            this.uri = this.uri.substring(0, this.uri.length - 1);
        }
        if(this.mode == true && (this.uri == '' || this.uri.substring(this.uri.length - 1) != '/')) {
            this.uri += '/';
        }
        return this;
    },
    get : function() {
        var url = '';
        if(this.protocol != '') url = url + this.protocol + '://';
        if(this.site != '') url = url + this.site;
        if(this.port != '') url = url + ':' + this.port + '/';
        else if(this.site != '') url = url + '/';
        if(this.uri != '') url = url + this.uri + (this.mode==true?'':'/');
        if(this.request != '') url = url + '?' + this.request;
        if(this.anchor != '') url = url + '#' + this.anchor;
        return url;
    },
    getMin : function() {
        var url = '';
        if(this.uri != '') url = url + this.uri + (this.mode==true?'':'/');
        if(this.request != '') url = url + '?' + this.request;
        if(this.anchor != '') url = url + '#' + this.anchor;
        return url;
    },
    getMSG : function() {
        var msg = '';
        msg += 'url: ' + this.url + '\n';
        msg += 'protocol: ' + this.protocol + '\n';
        msg += 'site: ' + this.site + '\n';
        msg += 'port: ' + this.port + '\n';
        msg += 'uri: ' + this.uri + '\n';
        msg += 'request: ' + this.request + '\n';
        msg += 'anchor: ' + this.anchor + '\n';
        msg += 'mode: ' + (this.mode?'true':'false') + '\n';

        return msg;
    },
    mode : false,
    url : '',
    protocol : '',
    site : '',
    port : '',
    uri : '',
    request : '',
    anchor : ''
});