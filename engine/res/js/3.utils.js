function preloadImages(images,fullpath) {
    if(fullpath==undefined) fullpath = false;
    if(images==undefined) return false;
    if(typeof(images) == 'string') images = [images];
    if((images instanceof Array) == false) return false;
    var img;
    for(var i=0;i<images.length;i++) {
        img = new Image();
        if(fullpath == false) img.src = '{SITEURI}'+images[i];
    }
    return true;
}

function newEl(type,id,owner) {
    if(!Object.isString(type) || type.length < 1) return false;
    var el = document.createElement(type);
    if(Object.isString(id)) el.id = id;
    if(Object.isString(owner)) owner = $(owner);
    if(!Object.isElement(owner)) owner = document.body;
    owner.appendChild(el);
    return el;
}

function getEl(id,type,owner) {
    return $(id) || newEl(type||'DIV',id,owner);
}

var loginTimeout = null;
function loginTimeoutFunction() {
    obj = $('loginTimeoutObject');
    sec = obj.innerHTML - 1;
    if(sec < 0) {
        new sftMsg(null,'error').hide();
    } else {
        obj.update(sec);
        loginTimeout = setTimeout(loginTimeoutFunction , 1000);        
    }
}