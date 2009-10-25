<?

function getUserLevel() {
    return $_SESSION[USER_SESSION_KEY][USER_LEVEL_KEY];
}

function getUserData($key = null) {
    $data = $_SESSION[USER_SESSION_KEY];
    if($key != null) return $data[$key];
    else return $data;
}

function setUserLevel($level) {
    return $_SESSION[USER_SESSION_KEY][USER_LEVEL_KEY] = constant('SL_'.strtoupper($level));
}

function setUserData($data) {
    return $_SESSION[USER_SESSION_KEY] = $data;
}

function checkUserLevel($level,$order="up",$only=false) {
    $level = constant('SL_'.strtoupper($level));
    if( ($only && getUserLevel() == $level) || 
        (!$only && $order=="up" && getUserLevel() >= $level) || 
        (!$only && $order=="down" && getUserLevel() <= $level) )
            return true;
    else return false;
}

function clearUserData() {
    $_SESSION[USER_SESSION_KEY] = Array();
}

function defaultUserData() {
    if(!isset($_SESSION[USER_SESSION_KEY])) $_SESSION[USER_SESSION_KEY] = Array();
    if(!isset($_SESSION[USER_SESSION_KEY][USER_LEVEL_KEY]) || empty($_SESSION[USER_SESSION_KEY][USER_LEVEL_KEY])) $_SESSION[USER_SESSION_KEY][USER_LEVEL_KEY] = SL_GUEST;
}
function matchUserName($name) {
    return preg_match('/^' . MATCH_USER_NAME . '$/is', $name);
}
function matchUserPassword($passwd) {
    return preg_match('/^' . MATCH_USER_PASSWD . '$/is', $passwd);
}

?>