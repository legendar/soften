<?

    function makePostData($postdata){
        foreach($postdata as $key=>$val){
            $str[] = urlencode($key)."=".urlencode($val);
        }
        return join("&", $str);
    }

    function sendForm($url,$postdata,$cookies,$init=false,$headers=false) {
        $ch = curl_init();       
        curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_URL, $url);
        if ($init) curl_setopt($ch, CURLOPT_COOKIESESSION, TRUE);
        curl_setopt($ch, CURLOPT_COOKIEFILE, $cookies);
        curl_setopt($ch, CURLOPT_COOKIEJAR, $cookies);
        curl_setopt($ch, CURLOPT_HEADER, $headers);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        if(!$init) curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_REFERER, isset($GLOBALS["CURLREF"])?$GLOBALS["CURLREF"]:'');
        $GLOBALS["CURLREF"] = $url;
        if (count($postdata)) {
           curl_setopt ($ch, CURLOPT_POSTFIELDS, makePostData($postdata));
        }
        $result=curl_exec($ch);
        $GLOBALS["CURLHEADERS"] = curl_getinfo($ch);
        
        if($headers) {
            $r = explode("\n\r\n", $result);
            if($r == $result) $r = explode("\n\n", $result);
            $result['headers'] = $r[0];
            $result['content'] = $r[1];
        }
        
        return $result;
    }
    
?>