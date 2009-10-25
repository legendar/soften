<?php
// Date in the past
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
// always modified
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");

if($_SERVER["SERVER_PROTOCOL"] == "HTTP/1.1"){
   header("Cache-Control: no-store, no-cache, must-revalidate");
   header("Cache-Control: max-age=1, post-check=1, pre-check=1", false);
} else {
   header("Pragma: no-cache");
}
?>