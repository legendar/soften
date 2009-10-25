<?
    if(date("YmdHis") < ($_SESSION["login_data"]["invalid_logins_time"]+INVALID_LOGINS_TIMEOUT)) {
        $_SESSION["login_data"]["invalid_logins_time"] = date("YmdHis");
        formErrorMessage(lng('Please wait \1 secs!', ' <b id="loginTimeoutObject">' . INVALID_LOGINS_TIMEOUT . '</b> '));
    } else {
        if(empty($_REQUEST["login"])) formErrorEmpty(lng('login'));
        if(empty($_REQUEST["passwd"])) formErrorEmpty(lng('pass'));

        if(intval($GLOBALS["_ERRORS"]["COUNT"]["empty"]) < 1) {
            $w = Array();
            $w[] = "login = " . db()->quote(trim($_REQUEST["login"]));
            $w[] = "passwd = " . db()->quote(md5(trim($_REQUEST["passwd"])));
            $c = db()->getOne("SELECT count(*) FROM users WHERE " . implode(" AND ", $w));
            if(intval($c) < 1) {
                formErrorMessage(lng("Invalid credentials!"));
                $_SESSION["login_data"]["invalid_logins_count"]++;
                if($_SESSION["login_data"]["invalid_logins_count"] >= INVALID_LOGINS_COUNT) {
                    $_SESSION["login_data"]["invalid_logins_count"] = 0;
                    $_SESSION["login_data"]["invalid_logins_time"] =  date("YmdHis");
                    formErrorMessage(lng('Please wait \1 secs!', ' <b id="loginTimeoutObject">' . INVALID_LOGINS_TIMEOUT . '</b> '));
                }
            }
        }
    }

?>