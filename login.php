<?php
require_once('config.inc.php');
require_once('functions.inc.php');
require_once($databaseFile);
require_once('mail.inc.php');

$submit = isset($_POST['submit']) ? $_POST['submit'] : '';
$name = isset($_POST['name']) ? $_POST['name'] : '';
$pass = isset($_POST['pass']) ? $_POST['pass'] : '';

displayTemplate('login');
