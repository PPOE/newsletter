<?php

$mailqueue = true;

require_once('config.inc.php');
require_once('functions.inc.php');
require_once($databaseFile);
require_once('mail.inc.php');

$display = "#welcome_view {display:none;}\n#dse_view {display:none;}";

$email = isset($_POST['email']) ? $_POST['email'] : '';
$bund = isset($_POST['bund']) ? $_POST['bund'] : '';
$bgld = isset($_POST['bgld']) ? $_POST['bgld'] : '';
$ktn = isset($_POST['ktn']) ? $_POST['ktn'] : '';
$noe = isset($_POST['noe']) ? $_POST['noe'] : '';
$ooe = isset($_POST['ooe']) ? $_POST['ooe'] : '';
$sbg = isset($_POST['sbg']) ? $_POST['sbg'] : '';
$stmk = isset($_POST['stmk']) ? $_POST['stmk'] : '';
$vlbg = isset($_POST['vlbg']) ? $_POST['vlbg'] : '';
$w = isset($_POST['w']) ? $_POST['w'] : '';
$submit = isset($_POST['submit']) ? $_POST['submit'] : '';
$error = null;

if (isset($_GET['dse'])) {
    $display = "#form_view {display:none;}\n#welcome_view {display:none;}";
    goto end;
}

if ($submit != 'true') {
    goto end;
}

if ($email === '') {
    $error = 'Keine E-Mail-Adresse angegeben!';
    goto end;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $error = 'Diese E-Mail-Adresse ist ungültig!';
    goto end;
}

$db = new db($dbLang, $dbName);

$prefs = 1;
//if ($bund === 'bund') {$prefs += 1;}
if ($bgld === 'bgld') {$prefs += 2;}
if ($ktn === 'ktn') {$prefs += 4;}
if ($noe === 'noe') {$prefs += 8;}
if ($ooe === 'ooe') {$prefs += 16;}
if ($sbg === 'sbg') {$prefs += 32;}
if ($stmk === 'stmk') {$prefs += 64;}
if ($vlbg === 'vlbg') {$prefs += 128;}
if ($w === 'w') {$prefs += 256;}

$id = $db->query("SELECT id FROM users WHERE email = '$email' LIMIT 1");
if (count($id) > 0) {
    $error = 'Diese E-Mail-Adresse ist bereits für den Newsletter-Empfang eingetragen!';
    goto end;
}

do {
    $sid = mt_rand();
} while (count($db->query("SELECT * FROM users WHERE sid = $sid")) > 0);


$db->query("INSERT INTO users (email, prefs, sid) VALUES ('$email', $prefs, $sid);");

$checkmail_text = "Jemand (hoffentlich du selbst) möchte deine Mailadresse \"".$email."\" für den Piraten-Newsletter anmelden. \nWenn du damit einverstanden bist, klicke bitte auf den folgenden Link:\n".change_link($sid,"confirm"). "\n\nWenn du diesen Newsletter nicht empfangen willst, brauchst du nichts zu unternehmen. Nur wenn du den obigen Bestätigungslink anklickst, wirst du den Newsletter bekommen.";
mail_utf8($db,$email, '[Piraten-Newsletter] Bestätigung deiner E-Mail-Adresse', $checkmail_text, from_header(1));

$db->close();

$display = "#form_view {display:none;}\n#dse_view {display:none;}";
end:

$templateVariables = [
    'display' => $display,
    'error' => $error,
    'email' => $email
];

displayTemplate('register', $templateVariables);
