<?php

/*************************************************\
|*        soften php engine index file           *|
|*************************************************|
|*  Copyright (C) 2010 legendar (legendar.info)  *|
\*************************************************/

/* get start time */
$start = microtime(true);

ob_start();

/* define globals */
define('BASEPATH', dirname(__FILE__));
define('UNIX',  (substr(PHP_OS, 0, 3) != 'WIN'));
define('WINDOWS',  (substr(PHP_OS, 0, 3) == 'WIN'));

/* includes and path modification functions */
require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . "core" . DIRECTORY_SEPARATOR . "helpers" . DIRECTORY_SEPARATOR . "path_incl.php");

/* set include path */
ini_set("include_path", correctPath(dirname(__FILE__) . "/core/classes") . PATH_SEPARATOR . ini_get("include_path"));
ini_set("include_path", correctPath(dirname(__FILE__) . "/core") . PATH_SEPARATOR . ini_get("include_path"));
ini_set("include_path", correctPath(dirname(__FILE__) . "/core/PEAR") . PATH_SEPARATOR . ini_get("include_path"));

/* include helpers functions */
incl("helpers/*.php");

if(!getConf('startTime')) setConf("startTime", $start);

?>
