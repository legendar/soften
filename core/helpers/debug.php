<?

function dmpArr() {
    $arrs = func_get_args();
    $die_after_print = true;
    $plain_text = false;
    $result = '';
    if(count($arrs)>1 && is_bool($arrs[count($arrs)-2]) && is_bool($arrs[count($arrs)-1])) {
        $die_after_print = $arrs[count($arrs)-2];
        $plain_text = $arrs[count($arrs)-1];
        unset($arrs[count($arrs)-1],$arrs[count($arrs)-1]);
    } else if(count($arrs)>0 && is_bool($arrs[count($arrs)-1])) {
        $die_after_print = $arrs[count($arrs)-1];
        unset($arrs[count($arrs)-1]);
    }
    foreach($arrs as $arr){
        //if(!empty($result)) $result .= '\n';
        $result .= print_r($arr,true);
    }
    $result = htmlspecialchars($result);
    if($plain_text) print $result;
    else print "<pre>$result</pre>";
    if($die_after_print)die();
}

function dmp() {
    $args = func_get_args();
    //return dmpArr($args);
    return call_user_func_array('dmpArr', $args);
}


function debug($message) {
    if(DEBUG_MODE) cl('debug')->set($message);
}

function clDebug($className = null, $action = null, $params = null) {
    if(!DEBUG_MODE) return false;
    if($class = null) return false;
    $debugLine = "[class: {$className}]";
    if($action != null) $debugLine .= " {$action}";
    if($params != null) {
        $paramLine = $params;
        if(is_array($params)) {
            $paramLine = '';
            foreach($params as $key => $value) {
                $key = addslashes($key);
                $value = addslashes($value);
                if(!empty($paramLine)) $paramLine .= '; ';
                $paramLine .= "{$key}: {$value}";
            }
        } else {
            $paramLine = addslashes($params);
        }
        $debugLine .= " ({$paramLine})";
    }
    debug($debugLine);
}

?>