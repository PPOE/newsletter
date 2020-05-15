<?php 
if (php_sapi_name() != 'cli') { exit('error'); }
global $mailqueue;
$mailqueue = true;
require_once('config.php');
require_once('db.php');
require_once('mail.php');
$work = 0;
$db = new db($dbLang, $dbName);
for ($i = 0; $i < 1; $i++)
{
    $results = $db->query("SELECT * FROM mail_queue ORDER BY mid ASC LIMIT 10;");
    foreach ($results as $result)
    {
        $work = 1;
        $id = intval($result['mid']);
        $to = base64_decode($result['mto']);
        $subject = base64_decode($result['msubject']);
        $headers = base64_decode($result['mheaders']);
        $body = base64_decode($result['mbody']);
        $db->query("DELETE FROM mail_queue WHERE mid = $id");
        mail($to,$subject,$body,$headers);
    }
}
$db->close();
$dbUser = $db2User;
$dbPass = $db2Pass;
$db = new db($db2Lang, $db2Name);
for ($i = 0; $i < 1; $i++)
{
        $results = $db->query("SELECT * FROM adm_mail_queue ORDER BY mid ASC LIMIT 10;");
        foreach ($results as $result)
        {
                $work = 1;
                $id = intval($result['mid']);
                $to = base64_decode($result['mto']);
                $subject = base64_decode($result['msubject']);
                $headers = null;
                if ($result['mheaders'] != '')
                    $headers = base64_decode($result['mheaders']);
                $body = base64_decode($result['mbody']);
        $params = null;
        if ($result['mparams'] != '')
                    $params = base64_decode($result['mparams']);
                $db->query("DELETE FROM adm_mail_queue WHERE mid = $id");
        if ($headers == null)
                    mail($to,$subject,$body);
        else if ($params == null)
            mail($to,$subject,$body,$headers);
        else
            mail($to,$subject,$body,$headers,$params);
        }
}

$db->close();
if ($work == 1)
    echo "OK\n";
    

