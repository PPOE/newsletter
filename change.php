<?php
$display = "#confirm_view {display:none;}\n#delete_view {display:none;}";

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
$s_get = isset($_GET['s']) ? $_GET['s'] : '';
$q_get = isset($_GET['q']) ? $_GET['q'] : '';

$mailqueue = true;

require_once 'config.php';
require_once 'functions.inc.php';
require_once $databaseFile;
require_once 'mail.inc.php';

$db = new db($dbLang,$dbName);

if(isset($_GET['s']) && preg_match('/^-?\d+$/', $_GET['s']) == 1 && isset($_GET['q'])) {
  $s = intval($_GET['s']);
  $q = $_GET['q'];
} else {
  $error = "Ungültige Abfrage, Error-Code: A!";
  $display = "#confirm_view {display:none;}\n#change_view {display:none;}\n#delete_view {display:none;}";
  goto end;
}

$q_array = explode("|", mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $key . $s, base64_url_decode($q), MCRYPT_MODE_CBC));
if (count($q_array) <= 1 || preg_match('/^-?\d+$/', $q_array[0]) != 1) {
  $error = "Ungültige Abfrage, Error-Code: B!";
  goto end;
}

$sid = $q_array[0];

if($submit != "true"){
  goto end;
}

if($_POST['delete'] == "true") {
  $delete = "true";
}

$prefs = 1;
//if($bund == "bund") {$prefs += 1;}
if($bgld == "bgld") {$prefs += 2;}
if($ktn == "ktn") {$prefs += 4;}
if($noe == "noe") {$prefs += 8;}
if($ooe == "ooe") {$prefs += 16;}
if($sbg == "sbg") {$prefs += 32;}
if($stmk == "stmk") {$prefs += 64;}
if($vlbg == "vlbg") {$prefs += 128;}
if($w == "w") {$prefs += 256;}

$id = $db->query("SELECT id FROM users WHERE sid = '$sid' LIMIT 1;");
$email = $db->query("SELECT email FROM users WHERE sid = '$sid' LIMIT 1;");
$email = $email[0]['email'];

if (count($id[0]['id']) == '')
{
  $error = "Ungültige Abfrage, Error-Code: C!";
  goto end;
}

if ($delete == "true") {
  $db->query("DELETE FROM users WHERE sid = $sid;");
  $display = "#confirm_view {display:none;}\n#change_view {display:none;}";
  mail_utf8($db,$email, "[Piraten-Newsletter] Newsletter abbestellt", "Ab sofort erhältst du keinen Newsletter mehr. Außerdem wurden deine Daten aus unserer Datenbank unwiderbringlich gelöscht.", from_header(1));
  goto end;
}

$db->query("UPDATE users SET prefs = $prefs WHERE sid = $sid;");
mail_utf8($db,$email, "[Piraten-Newsletter] Einstellung geändert", "Deine Newslettereinstellungen wurden geändert. Mit einem Klick auf den folgenden Klick können die Einstellungen noch einmal geändert werden:\n".change_link($sid),from_header(1));

$prefs = $db->query("SELECT prefs FROM users WHERE sid = $sid;");
$prefs = $prefs[0]['prefs'];

$display = "#change_view {display:none;}\n#delete_view {display:none;}";
end:
if ($sid != null)
{
	$prefs = $db->query("SELECT prefs FROM users WHERE sid = $sid;");
	$prefs = $prefs[0]['prefs'];
}
$db->close();

$templateVariables = [
    'error' => $error,
    'id' => $sid,
    'prefs' => $prefs
];

displayTemplate('change', $templateVariables);

