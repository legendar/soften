<?

//define('_ERROR_TYPE_EMPTY','');

if(!isset($GLOBALS["_ERRORS"])) {
    $GLOBALS["_ERRORS"] = Array();
    $GLOBALS["_ERRORS"]["FORMS"] = Array();
    $GLOBALS["_ERRORS"]["MESSAGES"] = Array();
    $GLOBALS["_ERRORS"]["COUNT"] = Array();
    $GLOBALS["_ERRORS"]["COUNT"]['_ALL'] = 0;
    $GLOBALS["_ERRORS"]["COUNT"]['_ERRORS'] = 0;
    $GLOBALS["_ERRORS"]["COUNT"]['_MESSAGES'] = 0;
}


$GLOBALS["_ERRORS"]["CONFIG"] = Array(
    "MESSAGE_TYPES" => Array('message','information'),
    "TYPES" => Array(
        "empty" => Array(
            "mode" => "pref",
            /*"pref_one_previous" => "The",
            "pref_one_following" => "field is empty - please complete this field",
            "pref_many_previous" => "The",
            "pref_many_following" => "fields are empty - please complete these fields",*/
            "message" => lng('The \1 field is empty - please complete this field.'),
            "message_many" => lng('The \1 fields are empty - please complete these fields.'),
        ),
        "error" => Array(
            "mode" => "pref",
            /*"pref_one_previous" => "The",
            "pref_one_following" => "field is incorrect - please complete this field",
            "pref_many_previous" => "The",
            "pref_many_following" => "fields are incorrect - please complete these fields",*/
	    "message" => lng('The \1 field is incorrect - please complete this field.'),
	    "message_many" => lng('The \1 fields are incorrect - please complete this field.'),
        ),
        "errorMessage" => Array(
            "mode" => "msg",
        ),
        "message" => Array(
            "mode" => "msg",
        ),
        "information" => Array(
            "mode" => "msg",
        ),
    )
);

function formError($msg,$el="",$type="error"){
    if(!isset($GLOBALS["_ERRORS"]["MESSAGES"][$type])) $GLOBALS["_ERRORS"]["MESSAGES"][$type] = Array();
    $GLOBALS["_ERRORS"]["MESSAGES"][$type][] = $msg;

    if(!isset($GLOBALS["_ERRORS"]["COUNT"][$type])) $GLOBALS["_ERRORS"]["COUNT"][$type] = 0;
    $GLOBALS["_ERRORS"]["COUNT"][$type]++;
    $GLOBALS["_ERRORS"]["COUNT"]['_ALL']++;
    if(!in_array($type,$GLOBALS["_ERRORS"]["CONFIG"]["MESSAGE_TYPES"])) $GLOBALS["_ERRORS"]["COUNT"]["_ERRORS"]++;
    else $GLOBALS["_ERRORS"]["COUNT"]['_MESSAGES']++;
    
    if(!empty($el)) {
        if(!isset($GLOBALS["_ERRORS"]["FORMS"][$el])) $GLOBALS["_ERRORS"]["FORMS"][$el] = Array();
        if(!isset($GLOBALS["_ERRORS"]["FORMS"][$el][$type])) $GLOBALS["_ERRORS"]["FORMS"][$el][$type] = Array();
        $GLOBALS["_ERRORS"]["FORMS"][$el][$type] = $msg;
    }
}

function formMessage($msg,$el=""){
    formError($msg,$el,"message");
}

function formQuestion($msg,$type){
    formError($msg,'','question');
}

function formErrorEmpty($msg,$el=""){
    formError($msg,$el,"empty");
}

function formErrorMessage($msg,$el=""){
    formError($msg,$el,"errorMessage");
}

function checkScode($fieldName = 'code') {
    $code  = strtolower($_SESSION["c0dE"]);
    $rcode = strtolower($_REQUEST[$fieldName]);
    if(empty($rcode)&&!empty($code))formErrorEmpty("security code",$fieldName);
    else if ($rcode != $code )formError("Security code is empty or wrong",$fieldName);
}

?>