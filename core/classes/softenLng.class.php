<?php

class softenLng {

    private $data;
    private $lng;
    
    public function __construct() {
        $this->data = Array();
        $this->lng = SITE_LNG;
	}
    
    public function add($label, $value) {
        if(!isset($this->data[$label])) $this->data[$label] = $value;
    }
    
    public function set($label, $value) {
        $this->data[$label] = $value;
    }
    
    public function get($label) {
        $result = isset($this->data[$label]) ? $this->data[$label] : $label;
        if(func_num_args() > 1) {
            $args = func_get_args();
            unset($args[0]);
            foreach($args as $k => $arg) {
                $result = preg_replace("/\\\\{$k}([^0-9])/is", $arg . '\1', $result);
                $result = preg_replace("/\\\\{$k}$/is", $arg . '\1', $result);
            }
        }
        return $result;
    }

}
?>