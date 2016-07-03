<?php
require_once('config.inc.php');
require_once('functions.inc.php');
require_once($databaseFile);
require_once('mail.inc.php');

$db = new db($dbLang, $dbName, $dbHost, $dbUser, $dbPass);

$rights = checklogin($access);
$usr_id = -1;
if ($gCurrentUser) {
    $usr_id = $gCurrentUser->getValue('usr_id');
}

$pref_id = 0;
if (isset($_POST['pref_id'])) {
    $pref_id = (int) $_POST['pref_id'];
}

$header_location = "Location: " . $baseUrl . "login.php";
if (!$rights || ($rights != 1 && ($rights == 0 || ($pref_id != 0 && !($pref_id & $rights || -($pref_id) & $rights))))) {
    header("$header_location");
}

$save = false;
if (isset($_POST['save'])) {
    $save = true;
    $db->query("UPDATE content SET first_eyes_usr_id = NULL, second_eyes_usr_id = NULL WHERE pref_id = $pref_id");
}

$publish = false;
if (isset($_POST['publish'])) {
    $publish = true;
}

if ($save) {
    $content = $db->escape($_POST['content']);
        if ($pref_id == 1 && preg_match('/%%LO CONTENT%%/s',$content) != 1) {
          $publish = false;
        }
        $db->query("UPDATE content SET first_eyes_usr_id = NULL, second_eyes_usr_id = NULL WHERE pref_id = $pref_id");
    $db->query("UPDATE content SET content = $content WHERE pref_id = $pref_id");
}
if ($publish) {
    $eyes_usr_id = $db->query("SELECT first_eyes_usr_id, second_eyes_usr_id FROM content WHERE pref_id = $pref_id");
    if ((preg_match('/^\d+$/', $eyes_usr_id[0]['first_eyes_usr_id']) == 1 && $eyes_usr_id[0]['first_eyes_usr_id'] == $usr_id) ||
        (preg_match('/^\d+$/', $eyes_usr_id[0]['second_eyes_usr_id']) == 1 && $eyes_usr_id[0]['second_eyes_usr_id'] == $usr_id)) {
        
    } elseif (preg_match('/^\d+$/', $eyes_usr_id[0]['first_eyes_usr_id']) != 1) {
        $db->query("UPDATE content SET first_eyes_usr_id = $usr_id WHERE pref_id = $pref_id");
    } elseif (preg_match('/^\d+$/', $eyes_usr_id[0]['second_eyes_usr_id']) != 1) {
        $db->query("UPDATE content SET second_eyes_usr_id = $usr_id WHERE pref_id = $pref_id");
    } else {
        
    }

    $sendbo = $db->query("SELECT * FROM content WHERE first_eyes_usr_id IS NOT NULL AND second_eyes_usr_id IS NOT NULL AND pref_id = 1;");
        $sendsubject = $db->query("SELECT * FROM content WHERE first_eyes_usr_id IS NOT NULL AND second_eyes_usr_id IS NOT NULL AND pref_id = -1;");
    if (count($sendbo) == 1 && count($sendsubject) == 1) {
        $sendmails = true;
    }
}

$articles = $db->query("SELECT * FROM content WHERE NOT sent ORDER BY pref_id");

$db->close();

end:

$templateVariables = [
    'articles' => $articles,
];

displayTemplate('create', $templateVariables);
