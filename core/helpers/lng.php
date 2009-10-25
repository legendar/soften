<?

    function lng() {
        $args = func_get_args();
        return call_user_func_array(array(cl('lng'), 'get'), $args);
    }