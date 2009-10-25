<?

function isFullPath($path) {
    if(UNIX && substr($path,0,1) == '/') return true;
    if(WINDOWS && strpos($path, ':\\') == 1) return true;
    return false;
}

function correctPath($path) {
    $path = str_replace('\\', "/", $path);
    if(substr($path, -1) == '/') $path = substr($path, 0, strlen($path)-1);
    $path = str_replace('/',DIRECTORY_SEPARATOR,$path);
    return $path;
}	

function glb($pattern) {
    $r = _glb($pattern);
    if(!empty($r)) {
	    sort($r);
    }
    return $r;
}
function _glb($pattern, $dir = null) {
    $all = ini_get('open_basedir');
    if(!$all || empty($all)) {
	    return glob($pattern);
    }
    if(!is_array($pattern)) {
	    //print $pattern;
	    $p = str_replace('\\', '/', $pattern);
	    $p = str_replace('.', '\.', $p);
	    $p = str_replace('*', '.*', $p);
	    $p = explode('/', $p);
    } else {
        $p = $pattern;
    }
    if($dir === null && WINDOWS && preg_match('/^[a-zA-Z]{1}\:$/is', $p[0])) {
	    $dir = array_shift($p);
	    return _glb($p, $dir);
    }
    if($dir === null && UNIX && empty($p[0])) {
	    $dir = array_shift($p);
	    return _glb($p, $dir);
    }
    if($dir === null) {
	    $dir = getcwd();
    }
    $dir .= '/';
    $r = array();
    $n = 'dir';
    if(count($p) == 1) $n = 'file';
    if(@is_dir($dir)) {
	    $allow_check = true;
    } else {
	    $all = explode(PATH_SEPARATOR, $all);
	    //print_r($all); die();
	    foreach($all as $al) {
	        //if(substr($al, -1) != '/') $al .= '/';
	        if(substr($al, 0, strlen($dir)) != $dir) continue;
	        else $al = substr($al, strlen($dir));
	        //die($p[0]);
	        //die("/^{$p[0]}/is");
	        if(preg_match("/^{$p[0]}/is", $al)) {
		        $dp = $p;
		        $d = array_shift($dp);
		        $d = preg_replace("/^({$p[0]}).*$/is", '$1', $al);
		        //die($d);
		        $r = array_merge($r, _glb($dp, $dir . $d));
	        }
	    }
	    $allow_check = false;
    }
    if($allow_check) {
	    $h = opendir($dir);
	    //echo "<pre>"; print $dir . "\n"; print_r($p); echo "</pre>";
	    while(($f = readdir($h)) !== false) {
	        if($f == '.' || $f == '..') continue;
	        //print $dir . $f . "\n";
	        $ft = @filetype($dir . $f);
	        if($ft != $n) continue;
	        if(preg_match("/^{$p[0]}$/is", $f)) {
		        if($n == 'dir') {
		            $dp = $p;
		            $d = array_shift($dp);
		            $d = $f;
		            $r = array_merge($r, _glb($dp, $dir .  $d));
		        } else {
		            $r[] = $dir . $f;
		        }
	        }
	    }
    }
    return $r;
}

//print "<pre>";
//$x = glb('/www/soften/core/helpers/*.php');
//print_r($x);
//die('x');

function incl($path, $require = true, $once = true) {
    $path = correctPath($path);
    $files = Array();
    if(isFullPath($path)) {
        $files = glb($path);
    } else {
        $dirs = explode(PATH_SEPARATOR,ini_get("include_path"));
        foreach($dirs as $d) {
            $d = correctPath($d);
            $files = array_merge($files,glb($d . DIRECTORY_SEPARATOR . $path));
        }
    }
    foreach($files as $file) {
        if($require) {
            if($once) require_once($file);
            else require($file);
        } else {
            if($once) include_once($file);
            else include($file);
        }
    }
}

function inc($path, $require = true) {
    incl($path, $require, false);
}

?>
