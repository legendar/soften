<?
    function getRand($n = 10) {
        $r = rand(1,pow(10,$n)-1);
        $r = str_repeat(0,$n-strlen($r)).$r;
        return $r;
    }
?>