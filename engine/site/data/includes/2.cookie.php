<?

    function getRandCookieFile($length = 8) {
        return SITEPATH . DIRECTORY_SEPARATOR . COOKIE_DIR . DIRECTORY_SEPARATOR . 'cookie' . getRand($length) . 'txt';
    }
    
    function getCookieFile($id) {
        return SITEPATH . DIRECTORY_SEPARATOR . COOKIE_DIR . DIRECTORY_SEPARATOR . 'cookie' . $id . 'txt';
    }

?>