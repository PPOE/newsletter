<?php

function checklogin($access)
{
    if (in_array(2,$access))
        return 1;
    if (in_array(38,$access))
        return 2;
    if (in_array(40,$access))
        return 4;
    if (in_array(39,$access))
        return 8;
    if (in_array(41,$access))
        return 16;
    if (in_array(42,$access))
        return 32;
    if (in_array(43,$access))
        return 64;
    if (in_array(45,$access))
        return 128;
    if (in_array(37,$access))
        return 256;
    return 0;
}

function decodePrefs($prefs) {
    $prefs = intval($prefs);
    if ($prefs & 1)
        $pa[] = "Bundesweite Informationen";
    if ($prefs & 2)
        $pa[] = "Burgenland";
    if ($prefs & 4)
        $pa[] = "Kärnten";
    if ($prefs & 8)
        $pa[] = "Niederösterreich";
    if ($prefs & 16)
        $pa[] = "Oberösterreich";
    if ($prefs & 32)
        $pa[] = "Salzburg";
    if ($prefs & 64)
        $pa[] = "Steiermark";
    if ($prefs & 128)
        $pa[] = "Vorarlberg";
    if ($prefs & 256)
        $pa[] = "Wien";
    return $pa;
}

function getAdminNames($admins) {
    global $dbLang, $dbName;
    $db = new db($dbLang, $dbName);
    $admin_names = [];
    foreach ($admins as $admin)
    {
        if (preg_match('/^\d+$/', $admin) != 1)
            continue;
        $result = $db->query("SELECT usd_value FROM ppoe_mitglieder.adm_user_data WHERE usd_usf_id = 37 AND usd_usr_id = $admin");
        if (count($result) != 1)
            $admin_names[] = $admin;
        else
            $admin_names[] = $result[0]['usd_value'];
    }
    $db->close();
    return $admin_names;
}

function displayTemplate($name, $variables = []) {
    global $db;
    global $rights;
    global $impressum;

    foreach ($variables as $variableName => $variable) {
        ${$variableName} = $variable;
    }

    ob_start();
    require 'templates/' . $name . '.php';
    $pageContent = ob_get_clean();

    require 'templates/layout/main.php';
}
