
var sftArray = Class.create({
    data : [],
    initialize : function(url, mode){
        this.data = [];
        
        if(arguments.length > 0) {
            for(i=0; i<arguments.length; i++) {
                this.data[i] = arguments[i];
            }
        }
        
        return this;
    },
    add : function(key, value) {
        this.data[key] = value;
        return this;
    },
    set : function(key, value) {
        return this.add(key, value);
    },
    get : function(key) {
        return this.data[key];
    },
    getAll : function() {
        return this.data;
    },
    remove : function(key) {
        this.data[key] = null;
        return this;
    },
    exists : function(key) {
        return !!this.data[key];
    }
});