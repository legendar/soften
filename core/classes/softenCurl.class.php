<?

class softenCurl {
    
    private $cookieFile;
    private $options;
    private $referer;
    private $curl;
    private $curlUrl;
    private $curlHeaders;
    private $curlResult;
    
    public function __construct() {
        $this->_new();
    }
    
    private function _new() {
        $this->cookieFile = '';
        $this->options = Array(
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_RETURNTRANSFER => true,
        );
        $this->referer = '';
        $this->explodeResult = false;
        $this->curlHeaders = Array();
        $this->curl = null;
        $this->curlUrl = '';
        $this->curlHeaders = null;
        $this->curlResult = null;
    }
    
    public function clean() {
        $this->_new();
    }
    
    public function setCookieFile($file='', $url='', $reset = false) {
        if(empty($this->cookieFile) || $reset) {
            if(empty($file) || $reset) $file = md5($url);
            $this->cookieFile = correctPath(SITEPATH . "/" . COOKIE_DIR . "/" . $file);
            return true;
        } else return false;
    }
    
    public function setOpt($option, $value) {
        $this->options[$option] = $value;
    }
    
    private function _explodeResult($contents) {
        $contents = explode("\n\r\n", $contents);
        $result = Array();
        $result['headers'] = Array();
        $result['headers']['_all'] = Array();
        $result['content'] = $contents[count($contents)-1];
        unset($contents[count($contents)-1]);
        foreach($contents as $num => $content) {
            $result['headers']['_all'][$num]['_original'] = $content;
            $headers = explode("\n", $content);
            $result['headers']['_all'][$num]["_header"] = $headers[0];
            unset($headers[0]);
            foreach($headers as $h) {
                preg_match("/^(.*?)\:\s(.*)$/is", $h, $match);
                $result['headers']['_all'][$num][$match[1]] = $match[2];
            }
        }
        if(isset($result['headers']['_all'][count($result['headers']['_all'])-2])&&isset($result['headers']['_all'][count($result['headers']['_all'])-2]['Location']))
            $result['headers']['_location'] = $result['headers']['_all'][count($result['headers']['_all'])-2]['Location'];
        else $result['headers']['_location'] = $this->curlUrl;
        $result['headers'] = array_merge($result['headers'], $result['headers']['_all'][count($result['headers']['_all'])-1]);
        return $result;
    }
    
    private function _makeResult() {
        if($this->options[CURLOPT_HEADER] == true) {
            return $this->_explodeResult($this->curlResult);
        } else {
            return $this->curlResult;
        }
    }
    
    private function _setOptions() {
        curl_setopt_array($this->curl, $this->options);
        curl_setopt($this->curl, CURLOPT_COOKIEFILE, $this->cookieFile);
        curl_setopt($this->curl, CURLOPT_COOKIEJAR, $this->cookieFile);
    }
    
    private function _makeData($data, $url = null) {
        if($url !== null) {
            $urlData = preg_replace('/^.*?\?(.*)$/', '$2', $url)
            $url = preg_replace('/^(.*?)\?(.*)$/', '$1', $url)
            $urlData = explode('&', $urlData);
            foreach($urlData as $key => $value) {
                $data[$key] = $value;
            }
        }
        $newData = Array();
        foreach($data as $key => $val){
            $newData[] = urlencode($key) . "=" . urlencode($val);
        }
        $data = join("&", $newData);
        if($url !== null) {
            $data = $url . '?' . $data;
        }
        return $data;
    }
    
    private function _init() {
        $this->curl = curl_init();
        $this->_setOptions();
        $this->curlResult = curl_exec($this->curl);
        $this->curlHeaders = curl_getinfo($this->curl);
        curl_close($this->curl);
        return $this->curlResult;
    }
    
    public function sendForm($url, $data = Array()) {
        $this->curlUrl = $url;
        if($this->setCookieFile('',$url)) {
            $this->setOpt(CURLOPT_COOKIESESSION, true);
            $this->referer = '';
        } else {
            $this->setOpt(CURLOPT_POST, true);
        }
        $this->setOpt(CURLOPT_URL, $url);
        $this->setOpt(CURLOPT_REFERER, $this->referer);
        $this->referer = $url;
        if (!empty($data)) {
            $this->setOpt(CURLOPT_POSTFIELDS, $this->_makeData($data));
        }
        
        $this->_init();
        
        return $this->_makeResult();        
    }

    public function get($url, $data = array()) {
        $url = $this->_makeData($data, $url);
        $this->curlUrl = $url;
        $this->referer = '';
        $this->setCookieFile('',$url,true);
        $this->setOpt(CURLOPT_COOKIESESSION, true);
        $this->setOpt(CURLOPT_URL, $url);
        $this->_init();
        return $this->_makeResult();        
    }

}

?>
