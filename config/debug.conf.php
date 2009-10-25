<?

def('DEBUG_MODE', false);
def('DEBUG_FIREPHP', true); 

ini_set('display_errors', DEBUG_MODE ? true:false);
error_reporting(E_ALL & ~E_NOTICE);

?>