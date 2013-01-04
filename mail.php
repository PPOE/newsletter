<?php

require("config.php");

function base64_url_encode($input)
{
    return strtr(base64_encode($input), '+/=', '-_$');
}

function base64_url_decode($input)
{
    return base64_decode(strtr($input, '-_$', '+/='));
}

function mail_utf8($to, $subject, $message, $unsubscribe_link = null)
{
  $subject = "=?UTF-8?B?".base64_encode($subject)."?=";

  $headers = "From: Piratenpartei Newsletter <newsletter@piratenpartei.at>\r\n";
  $headers .= "MIME-Version: 1.0\r\nContent-type: text/plain; charset=UTF-8\r\n";

  $message .= '

--

Piratenpartei Österreichs, Lange Gasse 1/4, 1080 Wien

Impressum: https://www.piratenpartei.at/rechtliches/impressum/

Kontakt zur TF Newsletter (öffentlich!): tf-newsletter@forum.piratenpartei.at';
if ($unsubscribe_link != null)
{
  $message .= '

Einstellungen ändern bzw. Abmeldung vom Newsletter: ' . $unsubscribe_link;
}
  return mail($to, $subject, $message, $headers);
}

function change_link($sid,$page = 'change')
{
  global $baseUrl,$key;
  $date = new DateTime();
  $rand = mt_rand();
  return $baseUrl.$page.".php?s=" . $rand . "&q=" . base64_url_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $key . $rand, $sid . '|' . $date->getTimestamp(), MCRYPT_MODE_CBC));
}

?>
