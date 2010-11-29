<?

class soften {
    
    protected $classes; 
    
    public function __construct() {
        $this->classes = Array();
        $GLOBALS["soften"] = & $this;
    }
    
    public function getExecutionTime() {
        setConf("endTime", microtime(true));
        $time = (float)getConf('endTime') - (float)getConf('startTime');
        if(DEBUG_MODE) {
            debug("end time: " . getConf('endTime'));
            debug("execution time: " . $time);
        }
        return $time;
    }
    
    public function init() {
    
        $this->setURLs(dirname($_SERVER['SCRIPT_FILENAME']));
        
        $webreq = true;
        if(isset($GLOBALS["argv"]) && count($GLOBALS["argv"]) > 1 && in_array(trim($GLOBALS["argv"][1]), array('dump', 'restore'))) $webreq = false;

        if(substr($_REQUEST['uri'], -1, 1) != '/' && $webreq) {
            $url = str_replace($_REQUEST['uri'], $_REQUEST['uri'] . '/', $_SERVER['REQUEST_URI']);
            $url = preg_replace("/^" . preg_quote(SITEURI, "/") . "/is", "", $url);
            redirect($url);
        }
        
        /* request modifications */
        if(!isset($_REQUEST["uri"])) $_REQUEST["uri"] = "/";
        if(get_magic_quotes_gpc()) $_REQUEST = clearMagicQuotes($_REQUEST);
        $_REQUEST = decodeRequest($_REQUEST);

        $this->loadConfigs();

        if(substr($_REQUEST["uri"], 0, strlen(AJAX_KEY) + 1) == '/' . AJAX_KEY) {
            $_REQUEST["uri"] = substr($_REQUEST["uri"], strlen(AJAX_KEY) + 1);
            def(AJAX_REQ, true);
        } else {
            def(AJAX_REQ, false);
        }
        
        register_shutdown_function(array(&$this, 'getExecutionTime'));
        if(DEBUG_MODE) debug("start time: " . getConf('startTime'));

        $this->initSession();
        
        if(count($GLOBALS["argv"]) > 1) {
            switch(trim($GLOBALS["argv"][1])) {
                case "dump": 
                    clearBuffer();
                    $this->dump(); 
                    die("Success!"); 
                    break;
                case "restore": 
                    clearBuffer();
                    $date = (count($GLOBALS["argv"]) > 2)?trim($GLOBALS["argv"][2]):null;
                    $this->restore($date);
                    die("Success!"); 
                    break;
            }            
        }
        
        $content = $this->parse();
        $this->sendContent($content);
    }
    
    private function setURLs($path) {
        
        $schema = "http://";
        if(array_key_exists("SSL_PROTOCOL",$_SERVER) && !empty($_SERVER["SSL_PROTOCOL"])) {
            $schema = "https://";
        }
        $host = $_SERVER["HTTP_HOST"];
        $uri = dirname($_SERVER["PHP_SELF"]);
        if($uri == "/" || $uri == "\\") $uri = "";
        $url = $schema . $host . $uri;

        define('SITEPATH', $path);
        define('SITEURI', $uri);
        define('SITEURL', $url);

    }
    
    private function loadConfigs() {
        
        ob_start();
        
        /* search for own engine configs */
        incl(SITEPATH . "/config/engine.conf.php");
        incl(SITEPATH . "/engine.conf.php");
        
        /* load default engine conf */
        incl(BASEPATH . "/config/engine.conf.php");
        
        /* load all other own configs */
        incl(SITEPATH   . '/' . CONFIG_DIR . '/*.conf.php');
        
        /* load all other default configs */
        incl(BASEPATH . '/config/*.conf.php');
        incl(ENGINEPATH . '/' . CONFIG_DIR . '/*.conf.php');
        
        ob_end_clean();
        
        iconv_set_encoding('input_encoding', SITE_ENCODING);
        iconv_set_encoding('internal_encoding', SITE_ENCODING);
        iconv_set_encoding('output_encoding', SITE_ENCODING);
    }
    
    private function parse() {
        /* load sitemap parser */
        cl("sitemap")->set(SITEMAP_FILE);
        cl("sitemap")->parse();
        cl("sitemap")->sendHeaders();
        return cl("sitemap")->getContent();
    }
    
    private function sendContent($content) {
        ob_end_clean();

        // Set default Cache-Control params. 
        header("Cache-Control: private, max-age=0, pre-check=0, post-check=0");
        header("Content-Length: ".strlen($content));
        
        //header("Content-type: text/html; charset: " . SITE_ENCODING);

        echo $content;
        die();
    }
    
    public function &getClass($name = "soften", $refresh = false) {
        $name = trim(strtolower($name));
        if($name == "soften") return $this;

        //if(!in_array($name, Array('debug', 'log'))) debug("get class (name:{$name})");
        
        $name = "soften".ucwords($name);
        
        if($refresh && isset($this->classes[$name])) {
            unset($this->classes[$name]);
        }
        
        if(!isset($this->classes[$name])) {
            require_once($name . ".class.php");
            if(!class_exists($name)) return null;
            $this->classes[$name] = & new $name;
        }
        return $this->classes[$name];
    }
    
    public function dump() {
        $file = correctPath(SITEPATH . "/" . SQL_DIR . "/" . "db_dump_{NAME}_{TYPE}_" . date("Ymd_His") . ".sql");
        foreach(array_keys(getConf("db")) as $name) {
            foreach(array_keys(getConf("db", $name)) as $type) {
                $dbtype = getConf("db", $name, $type, "type");
                if($dbtype === false || $dbtype == "mysql") {
                    $host = getConf("db", $name, $type, "host");
                    $port = getConf("db", $name, $type, "port");
                    $base = getConf("db", $name, $type, "base");
                    $user = getConf("db", $name, $type, "user");
                    $pass = getConf("db", $name, $type, "pass");
                    
                    $params = Array();                    
                    if($host) $params[] = "-h {$host}";
                    if($port) $params[] = "-p {$port}";
                    if($user) $params[] = "-u {$user}";
                    if($pass) $params[] = "--password={$pass}";
                    if($base) $params[] = $base;
                    $params = implode(" ", $params);

                    $f = str_replace("{NAME}", $name, $file);
                    $f = str_replace("{TYPE}", $type, $f);
                    $cmd = "mysqldump {$params} > {$f}";
                    exec($cmd);
                }
            }
        }
    }
    
    public function restore($date = null) {
        if($date === null) $date = date("Ymd");    
        $files = correctPath(SITEPATH . "/" . SQL_DIR . "/db_dump_*_*_{$date}_*.sql");
        $dumps = Array();
        foreach(glb($files) as $file) {
            list(,, $name, $type,, $time) = explode("_", $file);
            list($time,) = explode(".", $time);
            
            if(getConf("db", $name, $type) == false || (getConf("db", $name, $type, "type") != false && getConf("db", $name, $type, "type") != "mysql")) continue;
            
            if(isset($dumps[$name]) && isset($dumps[$name][$type])) {
                if($dumps[$name][$type] < $time) $dumps[$name][$type] = $time;
            } else $dumps[$name][$type] = $time;
        }
        
        foreach(array_keys($dumps) as $name) {
            foreach(array_keys($dumps[$name]) as $type) {
                $host = getConf("db", $name, $type, "host");
                $port = getConf("db", $name, $type, "port");
                $user = getConf("db", $name, $type, "user");
                $pass = getConf("db", $name, $type, "pass");
                $base = getConf("db", $name, $type, "base");
                
                $params = Array();                    
                if($host) $params[] = "-h {$host}";
                if($port) $params[] = "-p {$port}";
                if($user) $params[] = "-u {$user}";
                if($pass) $params[] = "--password={$pass}";
                if($base) $params[] = $base;
                $params = implode(" ", $params);

                $file = correctPath(SITEPATH . "/" . SQL_DIR . "/db_dump_{$name}_{$type}_{$date}_{$dumps[$name][$type]}.sql");
                $cmd = "mysql {$params} < {$file}";
                exec($cmd);
            }
        }
    }
    
    function initSession() {
        cl("session")->setStorage("file");
        cl("session")->setGcSmartProbability(1);
        cl("session")->setGcMaxLifeTime(300);
        cl("session")->setCacheLimiter('private');
        cl("session")->setCacheExpire(30);
        if ( defined('SESSION_NAME') ) {
            cl("session")->setName(SESSION_NAME);
        }
        cl("session")->start();
        debug('session init');

        if(isset($_SESSION["_ERRORS"])) {
            $GLOBALS["_ERRORS"] = $_SESSION["_ERRORS"];
            unset($_SESSION["_ERRORS"]);
            debug('get errors from session');
        }

        if(!isset($_SESSION["login_data"]["invalid_logins_count"])) 
            $_SESSION["login_data"]["invalid_logins_count"] = 0;
        if(!isset($_SESSION["login_data"]["invalid_logins_time"]))
            $_SESSION["login_data"]["invalid_logins_time"] = 0;
        
        defaultUserData();
        
        /* previous request data */
        setConf("_PREV_REQUEST", (isset($_SESSION["_PREV_REQUEST"])?$_SESSION["_PREV_REQUEST"]:Array()));
        $_SESSION["_PREV_REQUEST"] = $_REQUEST;
    }
    
}

?>
