
var softenDefaultFormCallback = function(form,name) {
    form.submit();
}

var softenDefaultFormErrorCallback = function(form,name,xmlhttp) {
    xmlhttp.responseText.evalScripts();
    if($('loginTimeoutObject')) {
        if(loginTimeout) clearTimeout(loginTimeout);
        loginTimeout = setTimeout(loginTimeoutFunction , 1000);
    }
}

function checkForm(form, name, callback, err_callback) {
    callback = callback || softenDefaultFormCallback;
    err_callback = err_callback || softenDefaultFormErrorCallback;
    new Ajax.Request(
		'{SITEURI}/forms/'+name+'/check/', 
		{
            parameters : Form.serialize(form),
            method: "post",
            onComplete: function(xmlhttp) {
                var r = xmlhttp.responseText.stripScripts().strip();
                if ( r == "" ) {
                    callback(form,name);
                    //form.submit();
                } else {
                    err_callback(form,name,xmlhttp);
                    //xmlhttp.responseText.evalScripts();
                }
                //xmlhttp.responseText.evalScripts();
            }
		} 
	);
    
    return false;
}