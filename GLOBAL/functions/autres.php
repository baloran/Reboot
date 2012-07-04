<?php

function exception_handler($myException)
{ 
    error_handler($myException->getCode(),$myException->getMessage(),$myException->getFile(),$myException->getLine(),'');
}

function error_handler($errno, $errstr, $errfile, $errline, $errcontext)
{
    $exclude = '';
    $exclude = array(
        '_FILES', '_SERVER', '_SESSION', '_COOKIE', 'GLOBALS'
    );
    
    foreach($exclude as $key){
        if(isset($errcontext[$key])) unset($errcontext[$key]);
    }
    if(isset($errcontext['_POST']) && count($errcontext['_POST']) == 0) unset($errcontext['_POST']);
    if(isset($errcontext['_GET']) && count($errcontext['_GET']) == 0) unset($errcontext['_GET']);
    
    $errfile = Manager::getFile($errfile);
    
    //$ERROR = _VAR::$ROOT->initThrow();
    $ERROR['str'] = $errstr;
    $ERROR['file'] = $errfile;
    $ERROR['line'] = $errline;
    if(count($errcontext) > 0) $ERROR['context'] = $errcontext;
    echo '<pre>';print_r($ERROR);
    
    //_VAR::$ROOT->sendThrow($ERROR, 'PHP');
}


function sendMail($to,$objet,$corps)
{
    $headers ='From: "Admin MySocial"<admin@mysocial.fr>'."\n"; 
    $headers .='Reply-To: admin@mysocial.fr'."\n"; 
    $headers .='Content-Type: text/html; charset="utf-8"'."\n"; 
    $headers .='Content-Transfer-Encoding: 8bit'; 

    $objet = stripslashes($objet);
    $corps = stripslashes($corps);

    mail($to, $objet, $corps, $headers,"-f admin@mysocial.fr -F MySocial.fr");
}

function sendMailSMTP($to, $subject, $message, $entete = '')
{
    global $HOME;
    $retour = false;
    require_once($HOME."Model/Php/class.phpmailer.php");
    require_once($HOME."Model/Php/class.smtp.php");

    $mail = new PHPMailer();
    $mail->IsSMTP();
    $mail->SMTPAuth = true;
    $mail->Host = "in.mailjet.com"; 
    $mail->Port = 25;
    $mail->Username = "270f8f9f7d2314410a68c5349bb184dc";
    $mail->Password = "ff4dca509a5b3f18ba14a4a80f66e4ac";
    $mail->From = "admin@mysocial.fr";
    $mail->AddCustomHeader('x-mailjet-campaign:'.$entete);
    $mail->FromName = 'Admin MySocial';
    $mail->Subject = $subject;
    $mail->Body = $message;
    $mail->isHTML(true);
    $mail->AddAddress($to, "");
    if(!$mail->Send())
        echo "Une erreur c'est produite lors de l'envoi vers  ".$to." avec l\'erreur : ".$mail->ErrorInfo;
    else
        $retour = true;
    $mail->ClearAddresses();

    return $retour;
}


function informationsVisiteur()
{
    if(!isset($_SERVER)) return array("ip" => '', "os" => '', "browser" => '', "url" => '', "referer" => '');
    
    $user_agent = $_SERVER['HTTP_USER_AGENT'];

    if (strpos($user_agent, "Android") !== FALSE)
            $os = "Android";
    elseif (strpos($user_agent, "iPhone") !== FALSE)
            $os = "iPhone";
    elseif (strpos($user_agent, "Windows XP") !== FALSE | strpos($user_agent, "Windows NT 5.1") !== FALSE)
            $os = "Windows XP";
    elseif (strpos($user_agent, "Windows NT 5.2") !== FALSE)
            $os = "Windows Server 2003";
    elseif (strpos($user_agent, "Windows NT 6.0") !== FALSE)
            $os = "Windows Vista";
    elseif (strpos($user_agent, "Windows NT 7.0") !== FALSE | strpos($user_agent, "Windows NT 6.1") !== FALSE)
            $os = "Windows Seven";
    elseif ((strpos($user_agent, "Mac") !== FALSE) || (strpos($user_agent, "PPC") !== FALSE))
            $os = "Mac";
    elseif (strpos($user_agent, "Linux") !== FALSE)
            $os = "Linux";
    elseif (strpos($user_agent, "FreeBSD") !== FALSE)
            $os = "FreeBSD";
    elseif (strpos($user_agent, "SunOS") !== FALSE)
            $os = "SunOS";
    elseif (strpos($user_agent, "IRIX") !== FALSE)
            $os = "IRIX";
    elseif (strpos($user_agent, "BeOS") !== FALSE)
            $os = "BeOS";
    elseif (strpos($user_agent, "OS/2") !== FALSE)
            $os = "OS/2";
    elseif (strpos($user_agent, "AIX") !== FALSE)
            $os = "AIX";
    else
            $os = "Autre";
    
    $tabUserAgent = explode(' ',$user_agent);

    if ((strpos($user_agent, "Nav") !== FALSE) || (strpos($user_agent, "Gold") !== FALSE) ||
    (strpos($user_agent, "X11") !== FALSE) || (strpos($user_agent, "Mozilla") !== FALSE) ||
    (strpos($user_agent, "Netscape") !== FALSE)
    AND (!strpos($user_agent, "MSIE") !== FALSE) 
    AND (!strpos($user_agent, "Konqueror") !== FALSE)
    AND (!strpos($user_agent, "Firefox") !== FALSE)
    AND (!strpos($user_agent, "Safari") !== FALSE))
                    $browser = "Netscape";
    elseif (strpos($user_agent, "Opera") !== FALSE)
    {
        $browser = "Opera";
    }
    elseif (strpos($user_agent, "MSIE") !== FALSE)
    {
        $tabVersion = explode('.',$tabUserAgent[3]);
        $browser = "InternetExplorer ".$tabVersion[0];
        
    }
    elseif (strpos($user_agent, "Lynx") !== FALSE)
                    $browser = "Lynx";
    elseif (strpos($user_agent, "WebTV") !== FALSE)
                    $browser = "WebTV";
    elseif (strpos($user_agent, "Konqueror") !== FALSE)
                    $browser = "Konqueror";
    elseif (strpos($user_agent, "Chrome") !== FALSE)
    {
        preg_match('/Chrome\/([0-9\.]+)/', $user_agent, $matches);
        $tabVersion = explode('.',$matches[1]);
        $browser = "Chrome ".$tabVersion[0];
    }
    elseif (strpos($user_agent, "Safari") !== FALSE)
                    $browser = "Safari";
    elseif (strpos($user_agent, "Firefox") !== FALSE)
    {       
        preg_match('/Firefox\/([\w\.]+)/', $user_agent, $matches);
        
        $tabVersion = explode('.',$matches[1]);
        $browser = "Firefox ".$matches[1];
    }
    elseif ((stripos($user_agent, "bot") !== FALSE) || (strpos($user_agent, "Google") !== FALSE) ||
    (strpos($user_agent, "Slurp") !== FALSE) || (strpos($user_agent, "Scooter") !== FALSE) ||
    (stripos($user_agent, "Spider") !== FALSE) || (stripos($user_agent, "Infoseek") !== FALSE))
        $browser = "Bot";
    else
        $browser = "Autre";

    $ip = $_SERVER['REMOTE_ADDR'];
    $url = "http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
    $origine = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';

    return array("ip" => $ip, "os" => $os, "browser" => $browser, "url" => $url, "referer" => $origine);
}
?>
