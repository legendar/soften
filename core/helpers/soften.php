<?

function &cl($name = "soften", $refresh = false) {
    if(!isset($GLOBALS["soften"])) {
        require_once("soften.class.php");
        if(!class_exists("soften")) return false;
        new soften;
    }
    return $GLOBALS["soften"]->getClass($name, $refresh);
}

?>