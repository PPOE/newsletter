<?php 
/* update from before 06042019
 @ToDo Database Update in 'users' 
 ADD "date" - field (UNIX Timestamp) to save Request-Lifetime 
 ADD "key" registration Key (Random AlphaNumeric, ~30 Signs?)
  
 in confirm.php 23-> No Fetch after query

change.php on line 35
confirm.php on line 13 

mail.php  74, 94
register.php 141

*/

require_once('../config.php');
require_once('../db.php');

