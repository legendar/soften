<?

function def($name, $value = "") {
    if(!is_array($name)) {
        $name = Array($name => $value);
    }
    foreach($name as $n => $v) {
        if(!defined($n)) define($n,$v);
    }    
}

?>