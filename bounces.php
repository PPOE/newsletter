<?php

//error_reporting(-1);
//ini_set('display_errors', 'On');

include_once 'config.php';
include_once $databaseFile;

$url = $redmineBaseUrl . 'issues.json';
$url .= '?project_id=' . $projectId;
$url .= '&status_id=open';
$url .= '&limit=100'; // doesn't work
$url .= '&key=' . $redminapiKey;

$contents = file_get_contents($url);
if ($contents === false) {
    exit;
}
$json = json_decode($contents);

$db = new db($dbLang, $dbName);
foreach($json->issues as $issue) {
    if (strpos($issue->subject, 'Undelivered Mail Returned to Sender') !== false) {
        preg_match("/[<<](\S+@\S+)[>>]/", $issue->description, $found);
        $email = $found[1];

        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            // todo: remove them form newsletter
            //print_r($db->query("SELECT * FROM users WHERE email = '{$email}'"));
            $db->query("DELETE FROM users WHERE email = '{$email}'");

            // todo: mark as done
            $url = $redmineBaseUrl . 'issues/' . $issue->id . '.json';
            $payload = "{\"issue\":{\"status_id\":5,\"notes\":\"automatisiert erledigt.\"}}";

            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "PUT",
                CURLOPT_POSTFIELDS => $payload,
                CURLOPT_HTTPHEADER => array(
                    "cache-control: no-cache",
                    "content-length: " . strlen($payload),
                    "content-type: application/json",
                    "x-redmine-api-key: " . $redminapiKey
                ),
            ));

            $response = curl_exec($curl);
            $err = curl_error($curl);

            curl_close($curl);

            /*if ($err) {
                echo "cURL Error #:" . $err;
            } else {
                echo $response;
            }*/
        }
    }
}
