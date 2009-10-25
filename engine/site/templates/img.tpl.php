<?

if(empty($img)) error404();

clearBuffer();

switch($ext) {
    case "png":
    case "gif":
    case "ico":
        $type = "image/{$ext}";
        break;
    case "jpeg":
    case "jpg":
    default:
        $type = "image/jpeg";
        break;
}

header("Content-type: {$type}; charset: " . SITE_ENCODING);
header("Vary: Accept-Encoding"); // Handle proxies

$expiresOffset = 30 * 60;        // 30 mins
header("Expires: " . gmdate("D, d M Y H:i:s", time() + $expiresOffset) . " GMT");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");

echo $img;

die();

?>