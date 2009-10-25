/* add 'isset' method to Hash object from prorotype library */
/* this should be removed if this methods will be present in new prorotype version */
Hash.addMethods({
    isset : function(key) {
        return this.keys().include(key);
    }    
});