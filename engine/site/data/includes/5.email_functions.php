<?

function sendEmail($to, $from, $subject, $text, $html="", $attachment="", $cc="") {

    incl("PEAR/Mail.php");
    incl("PEAR/Mail/mime.php");

    switch(EMAIL_NOTIFICATIONS) {
        case 0: return ""; break;
        case 2: if(!defined('EMAIL_NOTIFICATION_EMAILS')){return "";} $to = EMAIL_NOTIFICATION_EMAILS; break;
        default: break;
    }

    if (is_array($to)) { $to = implode(",", $to); }

    $mime = new Mail_mime();

    if($html != ""){
        $mime->setHTMLBody($html);
    }
    if($text != ""){
        $mime->setTXTBody($text);
    }

    if($attachment != ""){
        $mime->addAttachment($attachment);
    }

    $headers["From"] = $from;
    $headers["To"] = $to;
    $headers["Cc"] = $cc;
    $headers["Subject"] = $subject;

    if ( defined('REPLY_TO') ) {
        $headers["Reply-To"] = REPLY_TO;
    }
    
    $params["text_encoding"] = "8bit";
    $params["head_encoding"] = "base64";
    $params["head_charset"] = "UTF-8";
    $params["text_charset"] = "UTF-8";
    $params["html_charset"] = "UTF-8";

    $body = $mime->get($params);
    $headers = $mime->headers($headers);
    $params["host"] = MAIL_HOST;
    $params["auth"] = MAIL_AUTH;
    $params["username"] = MAIL_AUTH_USER;
    $params["password"] = MAIL_AUTH_PASSWORD;
    $params["port"] = MAIL_PORT;

    $mail = &Mail::factory(MAIL_TYPE, $params);
    $result = $mail->send($to, $headers, $body);

    if(PEAR::isError($result)){
        $msg[] = $result->getMessage();
        $msg[] = $result->getDebugInfo();
        return $msg;
    }

    return true;
}

function matchEmail($email) {
    if(!preg_match('/^[a-z0-9_\.\-]+@([a-zA-Z0-9\-]+\.)+[a-zA-Z0-9]+$/im', trim($email))) return false;
    else return true;
}

function getSMTP($server) {
    incl("PEAR/Net/DNS.php");
    
    $conf = array(
        'nameservers' => array('205.234.170.215','205.234.170.217'),
        'port'        => '53',
        'domain'      => '',
        'debug'       => 0,
        'tcp_timeout' => 240
    );
    $nd = new Net_DNS($conf);
    $answer = $nd->resolver->search($server, "MX");
    $smtp = Array();
    foreach($answer->answer as $a) {
        $smtp[] = $a->exchange;
    }
    if(count($smtp) > 1)return $smtp;
    else if(count($smtp) == 1)return $smtp[0];
    else return false;
}

function emailValidation($email) {

    incl("PEAR/Net/SMTP.php");
    
    $email = trim(strtolower($email));
    
    //check email syntax
    if(!macthEmail($email)) return false;
    
    //check domain exist
    $emails = explode("@",$email);
    if(gethostbyname($emails[1])==$emails[1]) return false;
    
    //check email exist in domain
    $smtpServer = getSMTP($emails[1]);
    if(!$smtpServer)return false;
    else if(is_array($smtpServer))$smtpServer = $smtpServer[0]; //get first smtp server

    $smtp = new Net_SMTP($smtpServer, 25, 'localhost');
    //$smtp->setDebug(true);

    $smtp->connect(240); // 240 - valiadtion timeout
    $smtp->helo($emails[1]);
    $smtp->mailFrom($email);
    $result = $smtp->getResponse();
    if($result[0] != 250) {
        $smtp->disconnect();
        return false;
    }
    $smtp->rcptTo($email);
    $result = $smtp->getResponse();
    if($result[0] != 250) {
        $smtp->disconnect();
        return false;
    }
    $smtp->vrfy($email);
    if($result[0] != 250 && (in_array(intval($result[0]),array(550,551,553,450,251)))) {
        $smtp->disconnect();
        return false;
    }
   
    $smtp->disconnect();

    return true;
}

?>
