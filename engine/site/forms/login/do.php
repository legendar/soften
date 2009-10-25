<?

    $w = Array();
    $w[] = "login = " . db()->quote(trim(strtolower($_REQUEST["login"])));
    $w[] = "passwd = " . db()->quote(md5(trim($_REQUEST["passwd"])));
    $user = db()->getRow("SELECT * FROM users WHERE " . implode(" AND ", $w));

    setUserData($user);
    setUserLevel($user["level"]);

    $name = empty($user['firstName']) ? $user["login"] : ((empty($user['lastName']) ? '' : ($user['lastName'] . ' ')) . $user['firstName']);
    formMessage(lng('Welcome, \1!', $name));
    
    redirect('/');
?>