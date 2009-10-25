<?

function setConf() {
    $args = func_get_args();
    return call_user_func_array(Array(cl('conf'),"set"),$args);
}

function getConf() {
    $args = func_get_args();
    return call_user_func_array(Array(cl('conf'),"get"),$args);
}

?>