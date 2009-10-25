<?

function &db($name = 'default', $type = 'default') {
    return cl("db")->getDBS($name, $type);
}

function dbConf($name,$type,$conf) {
    setConf("db",$name,$type,$conf);
}

?>