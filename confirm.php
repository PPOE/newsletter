<?php
$display = "#confirm_view {display:none;}";
require_once 'config.php';
require_once 'functions.inc.php';
require_once $databaseFile;
require_once 'mail.inc.php';
if(isset($_GET['s']) && preg_match('/^-?\d+$/', $_GET['s']) == 1 && isset($_GET['q'])) {
  $s = intval($_GET['s']);
  $q = $_GET['q'];
} else {
  $error = "Ungültige Abfrage!";
  goto end;
}
$q_array = explode("|", mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $key . $s, base64_url_decode($q), MCRYPT_MODE_CBC));
if (count($q_array) <= 1 || preg_match('/^-?\d+$/', $q_array[0]) != 1) { 
  $error = "Ungültige Abfrage!";
  goto end;
}

$sid = $q_array[0];

$db = new db($dbLang, $dbName);

$confirmed = $db->query("SELECT confirmed FROM users WHERE sid = '$sid' LIMIT 1");
if ($confirmed[0]['confirmed'] != 0 && $confirmed[0]['confirmed'] != 'f')
{
  $error = "Diese E-Mail-Adresse wurde bereits bestätigt!";
  goto end;
}

$db->query("UPDATE users SET confirmed=TRUE WHERE sid = '$sid';");

$email = $db->query("SELECT email FROM users WHERE sid = '$sid' LIMIT 1");
$email = $email[0]['email'];

$checkmail_text = "Deine E-Mail-Adresse wurde erfolgreich bestätigt. Ab sofort erhältst du regelmäßig deinen personalisierten Newsletter!\n\n";
$checkmail_text .= "Mit einem Klick auf den folgenden Link kannst du die Inhaltseinstellungen deines Newsletters ansehen und verändern oder den Newsletter abbestellen:\n".change_link($sid);
mail_utf8($db,$confirm, $email, "[Piraten-Newsletter] E-Mail-Adresse bestätigt", $checkmail_text, from_header(1));

$db->close();
$display = "#error_view {display:none;}";
end:


$templateVariables = [
    'error' => $error,
    'id' => $sid,
];

displayTemplate('confirm', $templateVariables);
