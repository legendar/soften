<?

clearBuffer();

header("Content-type: text/javascript; charset: " . SITE_ENCODING);
header("Vary: Accept-Encoding"); // Handle proxies

$expiresOffset = 30 * 60;        // 30 mins
header("Expires: " . gmdate("D, d M Y H:i:s", time() + $expiresOffset) . " GMT");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");

foreach($js as $fname => $jscode){
    $jscode = preg_replace("/\{SITEURI\}/is",SITEURI,$jscode);
    $jscode = preg_replace("/\{SITEURL\}/is",SITEURL,$jscode);
    echo $jscode."\n";
}

die();

?>