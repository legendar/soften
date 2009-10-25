<?

class softenConf {
    
    private $conf;
    
    public function __construct() {

    }
    
    public function set() {
        $args = func_get_args();
        if(count($args) < 1) return;
        $cur = & $this->conf;
        for($i=0,$c=count($args);$i<$c;$i++) {
            $arg=$args[$i];
            $isVal=($i+1==$c);
            if($isVal) $cur = $arg;
            else {
                if(!isset($cur[$arg])) $cur[$arg] = '';
                $cur = & $cur[$arg];
            }
        }
    }

    public function get() {
        $args = func_get_args();
        $cur = $this->conf;
        for($i=0,$c=count($args);$i<$c;$i++) {
            $arg=$args[$i];
            /*$isVal=($i+1==$c);*/
            if(!isset($cur[$arg])) return false;
            else {
                /*if($isVal) $cur = $cur[$arg];*/
                $cur = $cur[$arg];
            }
        }
        return $cur;
    }

}

?>