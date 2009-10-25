<?

if(DEBUG_FIREPHP) {
    incl('FirePHPCore/fb.php' . (version_compare(phpversion(), '5.0.0', '<') ? '4' : ''));
}
require_once('softenLog.class.php');

class softenDebug extends softenLog {
    
    private $buffer;
    private $status;

    public function __construct() {
        //$this->conf = getConf('debug');
        
        $this->conf = Array();
        $this->setDefaultConf();
        $this->conf['buffering'] = false;
        
        $this->handler = 'firebug';
        $this->ident = 'soften';
        $this->type = 'debug';
        $this->level = PEAR_LOG_DEBUG;
        
        $this->buffer = Array();
        $this->status = true;
        
	if(!DEBUG_FIREPHP) {
    	    register_shutdown_function(array(&$this, 'flush'));
	}
    }
    
    public function setDisplayType($type) {
        $this->handler = $type;
    }
    
    public function set($message) {
        if($this->status === true && DEBUG_MODE && !DEBUG_FIREPHP) {
            ob_start();
            $this->_set($message, 'debug');
            $this->buffer[] = ob_get_contents();
            ob_end_clean();        
        } elseif(DEBUG_FIREPHP) { 
	    FB::log($message);
	}
    }
    
    public function flush() {
        if($this->status === true) {
            foreach($this->buffer as $line) {
                print "\n{$line}";
            }
        }
    }
    
    public function disable() {
        $this->status = false;
    }
    
    
    
}

?>
