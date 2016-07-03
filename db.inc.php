<?php

$basepath = dirname(dirname($_SERVER['SCRIPT_FILENAME'])).DIRECTORY_SEPARATOR;

require_once('functions.inc.php');
require_once($basepath . 'adm_program/system/common.php');
// old admidio
//require_once($basepath . 'adm_program/system/classes/list_configuration.php');
//require_once($basepath . 'adm_program/system/classes/table_roles.php');
// new admidio
require_once($basepath . 'adm_program/system/classes/listconfiguration.php');
require_once($basepath . 'adm_program/system/classes/tableroles.php');

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
if ($gDb->num_rows($result_role) > 0) {
    $agnewsletter = true;
}
if (!isset($mailqueue) || !$mailqueue) {
    // prueft, ob der User die notwendigen Rechte hat, neue User anzulegen
    if ($gCurrentUser && $gCurrentUser->editUsers() == false) {
        $roles = [2, 37, 38, 39, 40, 41, 42, 43, 44, 45];
        $access = [];
        foreach ($roles as $getRoleId) {
            // Rollenobjekt erzeugen
            $role = new TableRoles($gDb, $getRoleId);

            // Testen ob Recht zur Listeneinsicht besteht
            if ($role->viewRole() == false && !$agnewsletter) {
            } else {
                $access[] = $getRoleId;
                if ($getRoleId == 2) {
                    $access = [2];
                    break;
                }
            }
        }
        if (count($access) == 0) {
            if (!$gCurrentUser || !$gValidLogin) {
                $gMessage->show($gL10n->get('SYS_NO_RIGHTS'));
            }
        }
    } else {
        $access = [2];
    }
    //Verwaltung der Session
    if (isset($_SESSION['navigation'])) {
        $_SESSION['navigation']->clear();
        $_SESSION['navigation']->addUrl(CURRENT_URL);
    }
}

class db
{
    private $dbConn_ = null;
    public $dbType_ = null;

    public function __construct($dbType, $name, $host, $user, $password)
    {
        $this->dbType_ = $dbType;
        switch ($this->dbType_) {
            case 'mysql':
                global $dbUser;
                global $dbPass;
                $this->dbConn_ = mysql_connect($host, $dbUser, $dbPass);
                if (!$this->dbConn_) {
                    exit('Could not connect: ' . mysql_error());
                }
                mysql_select_db($name, $this->dbConn_);
                break;
            case 'pgsql':
                $this->dbConn_ = pg_connect("host=$host dbname=$name user=$user password=$password")
                or exit('Could not connect: ' . pg_last_error());
                break;
            default:
                exit('invalid dbtype!');
        }
    }

    public function query($query)
    {
        $result_array = [];
        switch ($this->dbType_) {
            case 'mysql':
                $result = mysql_query($query);
                if (!$result) {
                    return $false;
                }
                if ($result === true) {
                    return true;
                }
                while ($line = mysql_fetch_assoc($result)) {
                    $result_array[] = $line;
                }
                break;
            case 'pgsql':
                $result = pg_query($query) or exit('Abfrage fehlgeschlagen: ' . pg_last_error() . '<br /><br />' . $query);
                if (!$result) {
                    return $false;
                }
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
        switch ($this->dbType_) {
            case 'mysql':
                mysql_close($this->dbConn_);
                break;
            case 'pgsql':
                pg_close($this->dbConn_);
                break;
            default:
                exit('invalid dbtype!');
        }
    }

    public function escape($text)
    {
        switch ($this->dbType_) {
            case 'mysql':
                return "'" . mysql_escape_string($text) . "'";
            case 'pgsql':
                return pg_escape_literal($text);
            default:
                exit('invalid dbtype!');
        }
    }
}
