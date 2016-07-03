<?php
require_once('config.inc.php');
require_once('functions.inc.php');
require_once($databaseFile);
require_once('mail.inc.php');

$db = new db($dbLang, $dbName);

$rights = checklogin($access);
$usr_id = -1;
if ($gCurrentUser) {
    $usr_id = $gCurrentUser->getValue('usr_id');
}

$header_location = 'Location: ' . $baseUrl . 'login.php';
if ($rights == 0) {
    header($header_location);
}

$selector = $rights == 1 ? 'pref_id > 0' : "pref_id = $rights OR pref_id = -$rights";
$articles = $db->query("SELECT * FROM content WHERE $selector ORDER BY pref_id");

foreach ($articles as $article) {
    if ($rights == 1 && $article['pref_id'] == 1) {
        $main_text = explode("%%LO CONTENT%%", stripslashes($article['content']));
    }
    elseif ($rights != 1 && $article['pref_id'] == $rights) {
        $main_text = [stripslashes($article['content']), ''];
    }
    else {
        if (isset($article['second_eyes_usr_id']) && $article['pref_id'] > 0) {
            $lo = decodePrefs($article['pref_id']);
            $pre = "--------------- Information der LO " . $lo[0] . " ";
            $pre .= str_repeat("-", 72 - strlen(mb_convert_encoding($pre,'ISO-8859-15','UTF-8'))) . "\n";
            $post = "\n" . str_repeat("-", strlen(mb_convert_encoding($pre,'ISO-8859-15','UTF-8')) - 1) . "\n";
            if (strlen(stripslashes($article['content'])) > 10) {
                $lo_text[] = $pre . stripslashes($article['content']) . $post;
            }
        }
    }
}

$preview_text_lo = implode('<br /><br />', $lo_text);
$preview_text = $main_text[0].'<br /><br />'.$preview_text_lo.'<br /><br />'.$main_text[1];

$testmail = false;
$eyes = " first_eyes_usr_id IS NOT NULL AND second_eyes_usr_id IS NOT NULL ";
if (isset($_POST['test']) && isset($_POST['testmail'])) {
    $testmail = true;
    $eyes = " 1 ";
}

$sendbo = $db->query("SELECT * FROM content WHERE " . $eyes . " AND pref_id = $rights;");
$sendsubject = $db->query("SELECT * FROM content WHERE " . $eyes . " AND pref_id = -$rights;");
$sendlos = $db->query("SELECT * FROM content WHERE " . $eyes . " AND pref_id != 1 AND pref_id > 0;");
if (count($sendbo) == 1) {
    $mailtext = stripslashes($sendbo[0]['content']);
}
$subject_r = $db->query("SELECT * FROM content WHERE pref_id = -$rights;");
if (count($subject_r) == 1) {
    $subject = stripslashes($subject_r[0]['content']);
} else {
    $subject = '';
}
$users = $db->query("SELECT * FROM users WHERE confirmed AND prefs & $rights");
if (count($sendbo) == 1 && count($sendsubject) == 1) {
    $may_send_mails = true;
}

if ($testmail) {
    $mailaddr = $db->escape($_POST['testmail']);
    $users = $db->query("SELECT * FROM users WHERE confirmed AND prefs & $rights AND email = $mailaddr LIMIT 1");
}

if ($testmail || (isset($_POST['sendmails']) && $may_send_mails)) {
    if (!($rights > 0)) {
        header($header_location);
    }
    $sendmails = true;
    $may_send_mails = false;
}

$db->close();

end:

$templateVariables = [
    'sendmails' => $sendmails,
    'testmail' => $testmail,
    'article' => $article,
    'articles' => $articles,
];

displayTemplate('register', $templateVariables);
