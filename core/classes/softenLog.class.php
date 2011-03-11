<?

require_once('Log.php');

/*$handler, 
$name = '', 
$ident = '', 
$conf = array(),
$level = PEAR_LOG_DEBUG*/

class softenLog {
    
    protected $conf;  //
    
    protected $handler;   //file
    protected $ident;
    protected $type;    //log (db,message,debug,etc)
    protected $level;    //log

    function __construct() {
        $this->conf = getConf('log');
        $this->setDefaultConf();
    }
    
    protected function setDefaultConf() {
        $this->handler = 'file';
        $this->ident = '';
        $this->type = 'log';
        $this->level = PEAR_LOG_INFO;
        $this->conf = Array();
    
        if(!isset($this->conf['mode'])) $this->conf['mode'] = 0644;
        if(!isset($this->conf['lineFormat'])) $this->conf['lineFormat'] = '[%1$s] [%3$s] %4$s';    
    }
    
    protected function _set($message = null, $name = null, $level = null) {
        if($message == null || $name == null) return false;
        if($level == null) $level = $this->level;
        Log::singleton($this->handler, $name, $this->ident, $this->conf)->log($message, $level);
    }
    
    private function set($message = null, $name = null, $level = null) {
        $this->_set($message, $name, $level);
    }
    
    private function getType($type = null) {
        if($type == null) $type = date('Ymd');
        return correctPath(SITEPATH . '/' . LOG_DIR . '/' . $type . '.log');
    }

    public function warning($message = '', $type = null) {
        $this->set($message, $this->getType($type), PEAR_LOG_WARNING);
    }

    public function info($message = '', $type = null) {
        $this->set($message, $this->getType($type), PEAR_LOG_INFO);
    }
    
}



?>
