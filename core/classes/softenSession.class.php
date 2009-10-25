<?php
class softenSession {   
    var $_SQL_CREATE_TABLE = "CREATE TABLE IF NOT EXISTS `php_sessions` (
                    `id` VARCHAR(40) NOT NULL DEFAULT '',
                    `data` LONGTEXT NOT NULL,
                    `expiry` INT UNSIGNED NOT NULL DEFAULT '0',
                    `user_id` INT UNSIGNED,
                    `is_admin` ENUM('yes','no') NOT NULL DEFAULT 'no',
                    PRIMARY KEY  (`id`)
                ) TYPE=InnoDB";
    var $_table_name = 'php_sessions';
    var $_db = null;
    var $_crc = false;
    var $_optimize_threshold = 100;
    var $_storage = "file";
    var $_gc_maxlifetime = 1440;
    var $_gc_probability = 100;
    var $_gc_divisor = 100;
    var $storage = "file";
    function __construct() {
        // Read the maxlifetime setting from PHP
        $this->_gc_maxlifetime = ini_get("session.gc_maxlifetime");
        $this->_gc_probability = ini_get("session.gc_probability");
        $this->_gc_divisor = ini_get("session.gc_divisor");
    }
    
    function setStorage($storage = "file") {
        $this->storage = $storage;
        switch ( $storage ) {
            case 'db': 
                session_set_save_handler(
                    array( &$this, "openDB" ),
                    array( &$this, "closeDB" ),
                    array( &$this, "readDB" ),
                    array( &$this, "writeDB"),
                    array( &$this, "destroyDB"),
                    array( &$this, "gcDB" )
                );
                break;
            case 'file':
            default: 
                ini_set('session.save_path',SITEPATH . '/' . SESSION_DIR);
        }
    }
    
    function openDB( $save_path, $session_name ) {
        // Create connection to the database 
        $this->_db = new LegDB($GLOBALS["CONFIGURATION"]["DBS"]);		
        
        // Don't need to do anything. Just return TRUE.
        return true;
    }

    function closeDB() {
        return true;
    }
    
    function readDB ( $s_id ) {
        //@TODO Save data state in class variable, so it's easy to check if the session is new or not in write()
        $s_data = '';
        
        $s_data = $this->_db->getOne( 'SELECT data FROM '.$this->_table_name.
                        ' WHERE id='.$this->_db->quote($s_id).
                        ' AND expiry >= '.time() 
                    );
        $this->_crc = strlen($s_data) . crc32($s_data);
        
        return $s_data; 
    }
    
    function writeDB( $s_id, $data ) {
        if ( (false !== $this->_crc) && ($this->_crc === strlen($data).crc32($data)) ) {
            // Session hasn't been updated, just update expiry field
            $query = 'UPDATE '.$this->_table_name.
                        ' SET expiry='.time()+$this->_gc_maxlifetime.
                        ' WHERE id='.$this->_db->quote($s_id).
                        ' AND expiry >='.time();
        } else {
            $exists = $this->_db->getOne( 'SELECT COUNT(*) FROM '.$this->_table_name. ' WHERE id='.$this->_db->quote($s_id) );
            if ( 0 == intval($exists) ) {
                // New session, insert record
                $query = sprintf( 'INSERT INTO %s (id,data,expiry) VALUES (%s, %s, %d)',
                                $this->_table_name, $this->_db->quote($s_id), 
                                $this->_db->quote($data), time()+$this->_gc_maxlifetime 
                            );   
            } else {
                // Session exists, update record
                $query = sprintf( 'UPDATE %s SET data=%s, expiry=%d, user_id=NULLIF(%d,0) WHERE id=%s AND expiry >= %d'
                                , $this->_table_name
                                , $this->_db->quote($data)
                                , time()+$this->_gc_maxlifetime
                                , intval($this->_getArrayVal("id",$_SESSION["user"]))
                                , $this->_db->quote($s_id)
                                , time()
                            );
            }
        }
        $this->_db->query($query);
        $this->_db->commit();
        
        return true;
    }
    
    function destroyDB( $s_id ) {
        $this->_db->query( 'DELETE FROM '.$this->_table_name.
                            ' WHERE id='.$this->_db->quote($s_id) 
                        );
        $this->_db->commit();
        return true;
    }

    function gcDB( $maxlifetime ) {
        $rows = $this->_db->delete( 'DELETE FROM '.$this->_table_name.
                            ' WHERE expiry < '.time() 
                        );
        $this->_db->commit();
        if ( $rows >= $this->_optimize_threshold ) {
            // Table optimization required
            if ( 'pgsql' ==  $this->_db->_driver ) {
                $this->_db->query( 'VACUUM '.$this->_table_name );
            } elseif ( 'mysql' ==  $this->_db->_driver || 'mysqli' ==  $this->_db->_driver ) {
                $this->_db->query( 'OPTIMIZE TABLE '.$this->_table_name );
            }
        }
        
        return true;
    }
    
    function setName($name = null) {
        if ( isset($name) ) {
            return session_name($name);
        }
        return session_name();
    }
    
    function getName() {
        return session_name();
    }
    
    function setCacheLimiter( $name = null) {
        if ( isset($name) ) {
            return session_cache_limiter($name);
        }
        return session_cache_limiter();
    }
    
    function getCacheLimiter() {
        return session_cache_limiter();
    }
    
    function setCacheExpire( $mins = null ) {
        if ( isset($mins) ) {
            return session_cache_expire($mins);
        }
        return session_cache_expire();
    }

    function getCacheExpire() {
        return session_cache_expire();
    }
    
    function setGcMaxLifeTime( $gc_maxlifetime = null ) {
        if ( isset($gc_maxlifetime) && is_int($gc_maxlifetime) && $gc_maxlifetime > 0 ) {
            $this->_gc_maxlifetime = $gc_maxlifetime;
            return ini_set( "session.gc_maxlifetime",$this->_gc_maxlifetime );
        }
        
        return false;
    }
    
    function getGcMaxLifeTime() {
        return $this->_gc_maxlifetime;
    }
    
    function setGcProbability ( $gc_probability = null ) {
        if ( isset($gc_probability) && is_int($gc_probability) && $gc_probability > 0 && $gc_probability <= 100 ) {
            $this->_gc_probability = $gc_probability;
            return ini_set( "session.gc_probability", $this->_gc_probability );
        }
        
        return false;
    }
    
    function getGcProbability() {
        return $this->_gc_probability;		
    }
    
    function setGcDivisor( $gc_divisor = null ) {
        if ( isset($gc_divisor) && is_int($gc_divisor) && $gc_divisor > 0 && $gc_divisor <= 100 ) {
            $this->_gc_divisor = $gc_divisor;
            return ini_set( "session.gc_divisor", $this->_gc_divisor );
        }
        
        return false;
    }
    
    function getGcDivisor() {
        return $this->_gc_divisor;
    }
    
    function setGcSmartProbability( $gc_probability = null ) {
        if ( isset($gc_probability) && is_numeric($gc_probability) && $gc_probability > 0 && $gc_probability <= 100 ) {
            $this->setGcProbability(1);
            $this->setGcDivisor( intval(floor(100/$gc_probability) ) );
        }
    }
    
    function start( $name = null ) {
        $this->setName($name);
        session_start();
    }
    
    function close( $name = null ) {
        $this->setName($name);
        session_write_close();
    }
    
    function _getArrayVal($key, $array) {
        if ( !is_array($array) ) {
            return null;
        } elseif ( !array_key_exists($key, $array) ) {
            return null;
        } else {
            return $array[$key];
        }
            
    }
}
?>
