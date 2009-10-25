<?

    function __autoload($className) {
        $path = correctPath(BASEPATH . '/classes/' . $className . '.class.php');
        //if(file_exists($path)) require_once($path);
        @include_once($path);
    }