<?php

require_once('config.php');

function from_header($n)
{
        switch ($n) {
                case 2:
                        return "From: Piratenpartei Newsletter <lv-burgenland@piratenpartei.at>\r\n";
                case 4:
                        return "From: Piratenpartei Newsletter <lv-kaernten@piratenpartei.at>\r\n";
                case 8:
                        return "From: Piratenpartei Newsletter <lv-noe@piratenpartei.at>\r\n";
                case 16:
                        return "From: Piratenpartei Newsletter <lv-ooe@piratenpartei.at>\r\n";
                case 32:
                        return "From: Piratenpartei Newsletter <lv-sbg@piratenpartei.at>\r\n";
                case 64:
                        return "From: Piratenpartei Newsletter <lv-steiermark@piratenpartei.at>\r\n";
                case 128:
                        return "From: Piratenpartei Newsletter <lv-vorarlberg@piratenpartei.at>\r\n";
                case 256:
                        return "From: Piratenpartei Newsletter <lv-wien@piratenpartei.at>\r\n";
                case 512:
                        return "From: Piratenpartei Newsletter <lv-tirol@piratenpartei.at>\r\n";
                default:
                        return "From: Piratenpartei Newsletter <newsletter@piratenpartei.at>\r\n";
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
  $subject = "=?UTF-8?B?".base64_encode($subject)."?=";

  $headers = $from;
  $headers .= "MIME-Version: 1.0\r\nContent-type: text/plain; charset=UTF-8\r\n";

  $message .= '

--

Piratenpartei Österreichs

Impressum: https://wiki.piratenpartei.at/wiki/Piratenwiki:Impressum

Kontakt zur TF Newsletter (öffentlich!): tf-newsletter@forum.piratenpartei.at';
if ($unsubscribe_link != null)
{
  $message .= '

Einstellungen ändern bzw. Abmeldung vom Newsletter: ' . $unsubscribe_link;
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
  return $baseUrl.$page.".php?s=" . $rand . "&q=" . base64_url_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $key , $sid . '|' . $date->getTimestamp(), MCRYPT_MODE_CBC));
}

?>
