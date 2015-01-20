<?php

require("config.php");

function from_header($n)
{
        switch ($n) {
                case 2:
                        return "From: Piratenpartei Burgenland <lv-burgenland@piratenpartei.at>\r\n";
                case 4:
                        return "From: =?UTF-8?B?".base64_encode("Piratenpartei Kärnten"). "?= <lv-kaernten@piratenpartei.at>\r\n";
                case 8:
                        return "From: =?UTF-8?B?".base64_encode("Piratenpartei Niederösterreich"). "?= <lv-noe@piratenpartei.at>\r\n";
                case 16:
                        return "From: =?UTF-8?B?".base64_encode("Piratenpartei Oberösterreich"). "?= <lv-ooe@piratenpartei.at>\r\n";
                case 32:
                        return "From: Piratenpartei Salzburg <lv-sbg@piratenpartei.at>\r\n";
                case 64:
                        return "From: Piratenpartei Steiermark <lv-steiermark@piratenpartei.at>\r\n";
                case 128:
                        return "From: Piratenpartei Vorarlberg <lv-vorarlberg@piratenpartei.at>\r\n";
                case 256:
                        return "From: Piratenpartei Wien <lv-wien@piratenpartei.at>\r\n";
                default:
                        return "From: =?UTF-8?B?".base64_encode("Piratenpartei Österreichs"). "?= <bv@piratenpartei.at>\r\n";
        }
}

function base64_url_encode($input)
{
    return strtr(base64_encode($input), '+/=', '-_$');
}

function base64_url_decode($input)
{
    return base64_decode(strtr($input, '-_$', '+/='));
}

function mail_utf8($db, $to, $subject, $message, $from, $unsubscribe_link = null)
{
$impressum = '

--

Piratenpartei Österreichs, Schadinagasse 3, 1170 Wien

Impressum: https://www.piratenpartei.at/rechtliches/impressum/';
$htmlimpressum = 'Impressum: <a href=3D"https://www.piratenpartei.at/rechtliches/impressum/">https://www.piratenpartei.at/rechtliches/impressum/</a>';
$unsubscribe = '';
$htmlunsubscribe = '';
if ($unsubscribe_link != null)
{
  $unsubscribe = '

Mit einem Klick auf den folgenden Link koennen Sie auswaehlen welche Presseinformationen Sie erhalten möchten oder unsere Presseinformationen abbestellen: ' . $unsubscribe_link;
  $htmlunsubscribe = quoted_printable_encode('

Mit einem Klick auf den folgenden Link koennen Sie auswaehlen welche Presseinformationen Sie erhalten möchten oder unsere Presseinformationen abbestellen: <a href="' . $unsubscribe_link . '">Abbestellen</a>');
}
  $subject = "=?UTF-8?B?".base64_encode($subject)."?=";

  $headers = $from;
  if (substr($message,0,strlen('Content-Type:')) == 'Content-Type:')
  {
    $headers .= "MIME-Version: 1.0\r\n" . substr($message,0,strpos($message,"\n"));
    $message = substr($message,strpos($message,"\n"),-1);
    $message = str_replace("<IMPRESSUM>",'

Impressum: https://www.piratenpartei.at/rechtliches/impressum/
',$message);
    $message = str_replace("<HTMLIMPRESSUM>",$htmlimpressum,$message);
    $message = str_replace("<UNSUBSCRIBE>",$unsubscribe,$message);
    $message = str_replace("<HTMLUNSUBSCRIBE>",$htmlunsubscribe,$message);
  }
  else
  {
    $headers .= "MIME-Version: 1.0\r\nContent-type: text/plain; charset=UTF-8\r\n";
    $message .= $impressum;
    $message .= $unsubscribe;
  }
  $to = base64_encode($to);
  $subject = base64_encode($subject);
  $message = base64_encode($message);
  $headers = base64_encode($headers);
  $db->query("INSERT INTO mail_queue (mto,msubject,mbody,mheaders) VALUES ('$to', '$subject', '$message', '$headers');");
  return true;
}

function change_link($sid,$page = 'change')
{
  global $baseUrl,$key;
  $date = new DateTime();
  $rand = mt_rand();
  return $baseUrl.$page.".php?s=" . $rand . "&q=" . base64_url_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $key . $rand, $sid . '|' . $date->getTimestamp(), MCRYPT_MODE_CBC));
}

?>
