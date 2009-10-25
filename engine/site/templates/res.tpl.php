<?

clearBuffer();

$type="text/html";
switch($ext) {
    case "png":
    case "gif":
    case "ico":
        $type = "image/{$ext}";
        break;
    case "jpeg":
    case "jpg":
        $type = "image/jpeg";
        break;
    case "css":
        $type = "text/css";
        break;
    case "js":
        $type = "text/javascript";
        break;
    default:
        $type = "text/html";
}

header("Content-type: text/javascript; charset: ".ENCODING);
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