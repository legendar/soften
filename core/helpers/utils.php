<?php

function clearBuffer() {
    cl('debug')->disable();
    @ob_end_clean();
    @ob_end_clean();
    @ob_end_clean();
}

if(!function_exists('file_put_contents')) {
	function file_put_contents($filename, $data, $add = false) {
		$fp = fopen($filename, $add?'a+':'w+');
		if ($fp) {
			fwrite($fp, ($add?'\n\r':'').$data);
			fclose($fp);
		}
	}
}

function clearMagicQuotes($arr) {
	if(is_array($arr)) {
		foreach($arr as $id => $val){
			$arr[$id] = clearMagicQuotes($val);
		}
	} else {
		$arr = stripslashes($arr);
	}
	return $arr;
}

function decodeRequest($arr) {
	foreach($arr as $key=>$val) {
        if(is_array($val)) {
			$arr[$key] = decodeRequest($val);
		} else {
            $arr[$key] = rawurldecode($val);
        }
    }
	return $arr;
}

function array_val( $key, $array ) {
	if ( !is_array($array) ) {
		return null;
	} elseif ( !array_key_exists($key, $array) ) {
		return null;
	} else {
		return $array[$key];
	}
}

function microtime_float() {
    list($usec, $sec) = explode(" ", microtime());
    return ((float)$usec + (float)$sec);
}

function getAllContains($strs,$str){
    if(is_string($strs)) $strs = Array($strs);
    if(!is_array($strs)) return false;
    $contains = Array();
    foreach($strs as $s) {
        if(substr($str,0,strlen($s)) == $s) $contains[] = $s;
    }
    return $contains;
}

function getMaxLongStr($strs) {
    if(is_string($strs)) return $strs;
    if(!is_array($strs)) return '';
    $maxLong = false;
    foreach($strs as $str) {
        if(strlen($maxLong) < strlen($str)) {
            $maxLong = $str;
        }
    }
    return $maxLong;
}

function getFArrValue($keys,$arr) {
    foreach($keys as $key) {
        if(array_key_exists($key,$arr)) return $arr[$key];
    }
    return false;
}

function error404() {
    header("HTTP/1.0 404 Not Found");
    die('HTTP/1.0 404 Not Found');
}

if(!function_exists('redirect')) {
    function redirect($nextpage = "") {
        $_SESSION["_ERRORS"] = $GLOBALS["_ERRORS"];
        cl('session')->close();
        if(empty($nextpage)){
            header("Location: ".$_SERVER["HTTP_REFERER"]);
        } else if(preg_match("/^[a-z0-9A-Z]+\:\/\//iUs",$nextpage)){
            header("Location: ".$nextpage);
        } else {
            if(defined('AJAX_KEY') && defined('AJAX_REQ')) {
                $nextpage = (AJAX_REQ ? ('/' . AJAX_KEY . preg_replace("/^\/" . AJAX_KEY . "/is", "", $nextpage)) : $nextpage);
            }
            header("Location: ".SITEURI.$nextpage);
        }
        ob_end_flush(); die();
    }
}

?>
