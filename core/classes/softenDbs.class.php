<?php

class softenDbs {
    private $db;
    private $conf;
    private $dsn;
    private $order;
    private $data;
    
    function __construct() {
        require_once('DB.php');
        $this->emptyData();
    }
    
    function setConf($conf) {

        if(!isset($conf["type"])) $conf["type"] = "mysql";
        if(!isset($conf["host"])) $conf["host"] = "localhost";
        if(!isset($conf["user"])) $conf["user"] = "ODBC";
        if(!isset($conf["pass"])) $conf["pass"] = "";
        if(!isset($conf["base"])) $conf["base"] = "";
        if(!isset($conf["port"])) $conf["port"] = "3306";
        if(!isset($conf["pref"])) $conf["pref"] = "";
        if(!isset($conf["autocommit"])) $conf["autocommit"] = false;
        if(!isset($conf["client_flags"])) $conf["client_flags"] = false;

        if($conf["type"] == "mysql" && extension_loaded("mysqli")) {
            $conf["type"] = "mysqli";
        }
        
        $this->conf = $conf;

	}
    
    function connect() {
        $conf = $this->conf;
        
        //if(!empty($conf["port"])) $conf["port"] = ':'.$conf["port"];
        //if(!empty($conf["pass"])) $conf["pass"] = ':'.$conf["pass"];
        
        //$this->dsn = $conf["type"].'://'.$conf["user"].$conf["pass"].'@'.$conf["host"].$conf["port"].'/'.$conf["base"];
        $this->dsn = array(
            'phptype'  => $conf['type'],
            //'dbsyntax' => false,
            'username' => $conf['user'],
            'password' => $conf['pass'],
            //'protocol' => false,
            /* we get stupid error with connection to mysqli. connection to 'localhost' works? but connection to 'localhost:3396' doesn't %) */
            //'hostspec' => $conf['host'] . (($conf['type'] == 'mysqli' && $conf['port'] == ':3306') ? '' : $conf['port']),
            'hostspec' => $conf['host'],
            'port'     => $conf['port'],
            //'socket'   => false,
            'database' => $conf['base'],
            'client_flags' => $conf['client_flags'],
        );
        
        $this->db = &DB::connect($this->dsn, false);
        if(DB::isError($this->db)) $this->error($this->db,false);
        
        $this->db->setErrorHandling(PEAR_ERROR_CALLBACK, Array($this,"error"));
        
        //Column data indexed by column names
        $this->db->setFetchMode(DB_FETCHMODE_ASSOC);    
    }
    
    function error($obj,$rollback = true) {
        if(is_object($this->db) && $rollback) $this->db->rollback();
        //error_log($obj->getMessage().": ".$obj->getDebugInfo(), 0);
        //die("<pre>".$obj->getMessage()."\n".$obj->getDebugInfo()."</pre>");
        throw new Exception($obj->getMessage()."\n".$obj->getDebugInfo());
    }

    function emptyData() {
        $this->order = array();
        $this->data = array();
    }

    function data($name, $str) {
        $this->order[] = $name;
        $this->data[$name] = $str;
    }

    function buildQuery() {
        $query = '';
        foreach($this->order as $v) {
            $query = $query . ' ' . $v . ' ' . $this->data[$v];
        }
        $this->emptyData();
        return trim($query);
    }

    function select($what) {
        $this->data('select', is_array($what) ? implode(', ', $what) : $what);
        return $this;
    }

    function from($w) {
        $this->data('from', is_array($w) ? implode(', ', $w) : $w);
        return $this;
    }

    function where($what, $d = 'AND') {
        if(!is_array($what)) {
            $this->data('where', $what);
        } else {
            $where = array();
            foreach($what as $k => $v) {
                $where[] = (is_int($k) ? '' : ($k . ' = ')) . $v;
            }
            $this->data('where', implode(' ' . $d . ' ', $where));
        }
        return $this;
    }

    function limit($n) {
        $this->data('limit', $n);
        return $this;
    }

    function one() {
        return $this->exec("getOne", array($this->buildQuery()));
    }

    function all() {
        return $this->exec("getAll", array($this->buildQuery()));
    }

    function row() {
        return $this->exec("getRow", array($this->buildQuery()));
    }

    function col() {
        return $this->exec("getCol", array($this->buildQuery()));
    }

    function pref($args) {
        $retArr = true;
        if(!is_array($args)) {
            $args = Array($args);
            $retArr = false;
        }
        
        foreach($args as &$arg) {
            $arg = preg_replace("/\{([^\}]*)\}/is","{$this->conf["pref"]}$1",$arg);
        }
        
        if($retArr) return $args;
        else return $args[0];
    }
    
    function exec($func,$args) {
        $args = $this->pref($args);
        return call_user_func_array(array($this->db,$func),$args);
    }
    
    function affectedRows() {
        $args = func_get_args();
		return $this->exec("affectedRows",$args);
	}
    
    function autoCommit() {
        $args = func_get_args();
		return $this->exec("autoCommit",$args);
	}
    
    function autoExecute() {
        $args = func_get_args();
		return $this->exec("autoExecute",$args);
	}
    
    function autoPrepare() {
        $args = func_get_args();
		return $this->exec("autoPrepare",$args);
	}
    
    function commit() {
        $args = func_get_args();
		return $this->exec("commit",$args);
	}
    
    function createSequence() {
        $args = func_get_args();
		return $this->exec("createSequence",$args);
	}
    
    function disconnect() {
        $args = func_get_args();
		return $this->db->disconnect();
	}
    
    function dropSequence() {
        $args = func_get_args();
		return $this->exec("dropSequence",$args);
	}
    
    function escapeSimple() {
        $args = func_get_args();
		return $this->exec("escapeSimple",$args);
	}
    
    function execute() {
        $args = func_get_args();
		return $this->exec("execute",$args);
	}
    
    function executeMultiple() {
        $args = func_get_args();
		return $this->exec("executeMultiple",$args);
	}
    
    function freePrepared() {
        $args = func_get_args();
		return $this->exec("freePrepared",$args);
	}
    
    function getAll() {
        $args = func_get_args();
		return $this->exec("getAll",$args);
	}
    
    function getAssoc() {
        $args = func_get_args();
		return $this->exec("getAssoc",$args);
	}
    
    function getCol() {
        $args = func_get_args();
		return $this->exec("getCol",$args);
	}
    
    function getListOf() {
        $args = func_get_args();
		return $this->exec("getListOf",$args);
	}
    
    function getOne() {
        $args = func_get_args();
		return $this->exec("getOne",$args);
	}
    
    function getOption() {
        $args = func_get_args();
		return $this->exec("getOption",$args);
	}
    
    function getRow() {
        $args = func_get_args();
		return $this->exec("getRow",$args);
	}
    
    function limitQuery() {
        $args = func_get_args();
		return $this->exec("limitQuery",$args);
	}
    
    function nextId() {
        $args = func_get_args();
		return $this->exec("nextId",$args);
	}
    
    function nextQueryIsManip() {
        $args = func_get_args();
        $ver = intval(str_replace(".","",$this->db->apiVersion()));
        if($ver < 178) return $this->db->raiseError(DB_ERROR_UNSUPPORTED);
		return $this->exec("nextQueryIsManip",$args);
	}
    
    function prepare() {
        $args = func_get_args();
		return $this->exec("prepare",$args);
	}
    
    function provides() {
        $args = func_get_args();
		return $this->exec("provides",$args);
	}
    
    function query() {
        $args = func_get_args();
		return $this->exec("query",$args);
	}
    
    function quote() {
        $args = func_get_args();
        
        // bug in PEAR::DB mysqli driver
        if ($this->conf["type"] == "mysqli") {
			return $this->exec("quoteSmart",$args);
		}
		
		return $this->exec("quote",$args);
    }
    
    function quoteIdentifier() {
        $args = func_get_args();
		return $this->exec("quoteIdentifier",$args);
	}
    
    function quoteSmart() {
        $args = func_get_args();
		return $this->exec("quoteSmart",$args);
	}
    
    function rollback() {
        $args = func_get_args();
		return $this->exec("rollback",$args);
	}
    
    function setFetchMode() {
        $args = func_get_args();
		return $this->exec("setFetchMode",$args);
	}
    
    function setOption() {
        $args = func_get_args();
		return $this->exec("setOption",$args);
	}
    
    function tableInfo() {
        $args = func_get_args();
		return $this->exec("tableInfo",$args);
	}
    
}
?>
