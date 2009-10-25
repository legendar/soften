var sftMsgTmp = [];
var sftMsg = Class.create({
    initialize: function(msg,type,num,showHideButton){
        if(!document.body) return false;
        this.type = type || '';
        this.num = num || 1;
        if(Object.isUndefined(showHideButton)) {
            this.showHideButton = ((this.type == 'error') ? true : false);
        } else this.showHideButton = showHideButton;
        this.el = getEl('sftMsg' + this.type + this.num);
        if(!this.el.hasClassName('sftMsg')) {
            this.el.addClassName('sftMsg');
        }
        this.elDiv = getEl('sftMsgDiv'+this.type+this.num,'DIV',this.el);
        if(!this.elDiv.hasClassName('sftMsgDiv '+this.type)) {
            this.elDiv.addClassName('sftMsgDiv '+this.type);
        }
        if(Object.isString(msg)) {
            if(this.showHideButton) {
                this.hideButton = '<a id="sftMsgHideButton' + this.type + this.num + '" class="sftMsgHideButton" href="#" onClick="new sftMsg(null,\'' + this.type + '\',\'' + this.num + '\').hide();return false;">Hide</a>';
            } else this.hideButton = '';
            this.elDiv.update(msg + this.hideButton);
        }
    },
    show: function(effect,duration) {
        effect = effect || 'Slide';
        duration = duration || 0.5;
        if(effect == 'Slide') {
            if(sftMsgTmp[this.el.id]) sftMsgTmp[this.el.id].cancel();
            this.el.show();
            this.el.style.top = (0 - this.el.getHeight()) + 'px';    
            sftMsgTmp[this.el.id] = new Effect.Move(this.el, { x: 0, y: this.el.getHeight(), duration: duration});
        } else {
            this.el.hide();
            this.el.style.top = '0px';
            eval('new Effect.'+(effect||'Appear')+'(this.el,{duration:'+duration+'});');
        }
    },
    hide: function(effect,duration) {
        effect = effect || 'Slide';
        duration = duration || 0.3;
        this.el.show();
        this.el.style.top = '0px';
        if(effect == 'Slide') {
            new Effect.Move(this.el, { x: 0, y: (0 - this.el.getHeight()), duration:duration});    
        } else {
            eval('new Effect.'+(effect||'Fade')+'(this.el,{duration:'+duration+'});');
        }
        
    },
    el:null,
    elDiv:null,
    hideButton:null,
    showHideButton:null,
    type:null
});
preloadImages(['/img/core/ok.gif','/img/core/i.png','/img/core/att.gif']);
function sftError(txt) { new sftMsg(txt,'error').show(); }
function sftMessage(txt,time) {
    setTimeout(function(){new sftMsg(txt,'message').show()}, 250);
    setTimeout(function(){new sftMsg(null,'message').hide();},(time||3.0)*1000+250);
}
function sftInfo(txt,time) {
    new sftMsg(txt).show();
    setTimeout(function(){new sftMsg(null).hide();},(time||3.0)*1000);
}
