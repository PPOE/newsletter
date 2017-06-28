<?php
require_once('../adm_program/system/common.php');
require_once('../adm_program/system/classes/list_configuration.php');
require_once('../adm_program/system/classes/table_roles.php');

// prueft, ob der User die notwendigen Rechte hat, neue User anzulegen
global $mailqueue;
if (!isset($mailqueue) || !$mailqueue)
{
if($gCurrentUser && $gCurrentUser->editUsers() == false)
{
$roles = array(2,37,38,39,40,41,42,43,44,45,129);
$access = array();
foreach ($roles as $getRoleId)
{
// Rollenobjekt erzeugen
$role = new TableRoles($gDb, $getRoleId);

//Testen ob Recht zur Listeneinsicht besteht
if($role->viewRole() == false)
{
}
else
{
  $access[] = $getRoleId;
  if ($getRoleId == 2)
  {
    $access = array(2);
    break;
  }
}
}
if (count($access) == 0)
{
if(!$gCurrentUser || !$gValidLogin)
{
    $gMessage->show($gL10n->get('SYS_NO_RIGHTS'));
}
}
}
else
{
  $access = array(2);
}
//Verwaltung der Session
$_SESSION['navigation']->clear();
$_SESSION['navigation']->addUrl(CURRENT_URL);
}

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
if (in_array(129,$access))
  return 512;
return 0;
}

class db {
private $dbConn_ = null;
public $dbType_ = null;
public function __construct($dbType, $name, $host = null)
{
  $this->dbType_ = $dbType;
  switch ($this->dbType_)
  {
    case 'mysql':
      global $dbUser;
      global $dbPass;
      $this->dbConn_ = mysql_connect($host, $dbUser, $dbPass);
      if (!$this->dbConn_)
      {
        die('Could not connect: ' . mysql_error());
      }
      mysql_select_db($name, $this->dbConn_);
      break;
    case 'pgsql':
      $this->dbConn_ = pg_connect("dbname=$name")
        or die('Could not connect: ' . pg_last_error());
      break;
    default:
      die("invalid dbtype!");
  }
}
public function query($query)
{
  $result_array = array();
  switch ($this->dbType_)
  {
    case 'mysql':
      $result = mysql_query($query);
      if (!$result)
        return $false;
      if ($result === true)
 	return true;
      while ($line = mysql_fetch_assoc($result)) {
        $result_array[] = $line;
      }
      break;
    case 'pgsql':
      $result = pg_query($query) or die('Abfrage fehlgeschlagen: ' . pg_last_error() . '<br><br>' . $query);
      if (!$result)
        return $false;
      while ($line = pg_fetch_array($result, null, PGSQL_ASSOC)) {
        $result_array[] = $line;
      }
      pg_free_result($result);
      break;
  }
  return $result_array;
}
public function close()
{
  switch ($this->dbType_)
  {
    case 'mysql':
      mysql_close($this->dbConn_);
      break;
    case 'pgsql':
      pg_close($this->dbConn_);
      break;
    default:
      die("invalid dbtype!");
  }
}
public function escape($text)
{
  switch ($this->dbType_)
  {
    case 'mysql':
      return "'".mysql_real_escape_string($text)."'";
    case 'pgsql':
      return pg_escape_literal($text);
    default:
      die("invalid dbtype!");
  }
}
}

function decodePrefs($prefs)
{
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
  if ($prefs & 512)
    $pa[] = "Graz";
  return $pa;
}

function getAdminNames($admins)
{
  global $dbLang, $dbName;
  $db = new db($dbLang, $dbName);
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


?>
