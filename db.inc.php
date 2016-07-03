<?php

$basepath = dirname(dirname($_SERVER['SCRIPT_FILENAME'])).DIRECTORY_SEPARATOR;

require_once 'functions.inc.php';
require_once $basepath . 'adm_program/system/common.php';
require_once $basepath . 'adm_program/system/classes/list_configuration.php';
require_once $basepath . 'adm_program/system/classes/table_roles.php';

global $mailqueue;
global $gDb;
$sql = 'SELECT 1
                      FROM '. TBL_MEMBERS. ', '. TBL_ROLES. '
                     WHERE mem_rol_id = rol_id
                       AND mem_end   >= \''.DATE_NOW.'\'
                       AND mem_usr_id = '.$gCurrentUser->getValue('usr_id').'
                       AND rol_valid  = 1
                       AND rol_id = 127';
$result_role = $gDb->query($sql);
$agnewsletter = false;
if($gDb->num_rows($result_role) > 0)
{
  $agnewsletter = true;
}
if (!isset($mailqueue) || !$mailqueue)
{
// prueft, ob der User die notwendigen Rechte hat, neue User anzulegen
if($gCurrentUser && $gCurrentUser->editUsers() == false)
{
$roles = array(2,37,38,39,40,41,42,43,44,45);
$access = array();
foreach ($roles as $getRoleId)
{
// Rollenobjekt erzeugen
$role = new TableRoles($gDb, $getRoleId);

//Testen ob Recht zur Listeneinsicht besteht
if($role->viewRole() == false && !$agnewsletter)
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
      return "'".mysql_escape_string($text)."'";
    case 'pgsql':
      return pg_escape_literal($text);
    default:
      die("invalid dbtype!");
  }
}
}