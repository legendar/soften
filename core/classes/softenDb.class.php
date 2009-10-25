<?php

class softenDb {
    private $dbs;
    
    public function __construct() {
        $this->dbs = Array();
        $this->encoding = (strtolower(SITE_ENCODING) == 'windows-1251') ? 'cp1251' : str_replace('-', '', strtolower(SITE_ENCODING));
	}
    
    public function &getDBS($name = "default", $type = "default") {
        if(!isset($this->dbs[$name]) || !isset($this->dbs[$name][$type])) 
            $this->dbs[$name][$type] = & $this->loadDBS(getConf("db",$name,$type));
        return $this->dbs[$name][$type];
    }
    
    private function loadDBS($conf) {
        $dbs = & cl("dbs");
        $dbs->setConf($conf);
        $dbs->connect();
        $dbs->query("SET NAMES {$this->encoding}");
        $dbs->query("SET CHARACTER SET {$this->encoding}");
	$dbs->query("SET character_set_client = {$this->encoding}");
	$dbs->query("SET character_set_connection = {$this->encoding}");
	$dbs->query("SET character_set_results = {$this->encoding}");
        return $dbs;
    }
}
?>