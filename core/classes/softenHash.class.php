<?

    class softenHash {
    
        function __construct($data = array()) {
            $this->update($data);
        }
        
        // clone is php core method
        /*function clone() {
            return $this;
        }*/
        
        function each($fname) {
            foreach($this->keys() as $key){
                $hash = new softenHash();
                $hash->update(array('key'=>$key, 'value'=>$this->$key));
                call_user_func($fname, $hash);
            }
        }
        
        function get($key) {
            return $this->$key || null;
        }
        
        //please use dmp() instead
        /*function inspect() {
            
        }*/
        
        /* can't use isset in php, use exists instead */
        function exists($key) {
            return isset($this->$key);
        }
        
        function keys() {
            $keys = array();
            foreach($this as $key => $value) {
                $keys[] = $key;
            }
            return $keys;
        }
        
        function merge($data) {
            $hash = new softenHash();
            foreach($this->keys() as $key) {
                if(!isset($data[$key])) {
                    $hash->set($key, $this->$key);
                } else {
                    $hash->set($key, $data[$key]);
                }
            }
            return $hash;
        }
        
        function set($key, $value) {
            $this->$key = $value;
            return $value;
        }
        
        function toJSON() {
            
            dmpArr($this, false);
            echo json_encode($this);
            dmpArr(json_decode(json_encode($this)), false);
            die();
            return json_encode($this);
        }
        
        // don't need because hash is object
        /*function toObject() {
            
        }*/
        
        // do not need on server
        /*function toQueryString() {
        
        }*/
        
        /* can't use unset in php, use remove instead */
        function remove($key) {
            unset($this->$key);
        }
        
        function update($data) {
            foreach($data as $key => $value) {
                $this->$key = $value;
            }
        }

        function values() {
            return get_object_vars($this);
        }        
        
    }

    class hash extends softenHash {}