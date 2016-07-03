<?php
require_once('config.inc.php');
require_once('functions.inc.php');
require_once($databaseFile);


$db = new db($dbLang, $dbName);

$header_location = 'Location: ' . $baseUrl . 'login.php';
$rights = checklogin($access);
$usr_id = -1;
if ($gCurrentUser) {
    $usr_id = $gCurrentUser->getValue('usr_id');
}
if (!($rights > 0)) {
    header($header_location);
}

if (isset($_POST['delete'])) {
    if (!isset($_POST['email']) || !filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
        $error = 'Ein Fehler beim Löschen ist aufgetreten!';
        goto end;
    }
    $db->query("DELETE FROM users WHERE email = '{$_POST['email']}' AND prefs & $rights");
}
if ($rights & 1) {
    $rights = 1;
}
$users = $db->query("SELECT * FROM users WHERE prefs & $rights;");

end:
$db->close();

$templateVariables = [
    'users' => $users,
];

displayTemplate('list', $templateVariables);

