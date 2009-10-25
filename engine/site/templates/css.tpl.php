<?

clearBuffer();

header("Content-type: text/css; charset: " . SITE_ENCODING);
header("Vary: Accept-Encoding"); // Handle proxies

$expiresOffset = 30 * 60;        // 30 mins
header("Expires: " . gmdate("D, d M Y H:i:s", time() + $expiresOffset) . " GMT");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");

foreach($css as $filename => $csscode){
    $csscode = preg_replace("/\{SITEURI\}/is",SITEURI,$csscode);
    $csscode = preg_replace("/\{SITEURL\}/is",SITEURL,$csscode);    
    echo $csscode."\n";
}

die();

?>