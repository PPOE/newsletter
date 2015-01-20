<?
require("config.php");
require("db.php");
require("mail.php");

$db = new db($dbLang, $dbName);

$rights = checklogin($access);
$persons = "2 Personen";
$personsn = 2;
$secondeyes = " AND second_eyes_usr_id IS NOT NULL ";
if ($rights == 129)
{
  $persons = "1 Person";
  $personsn = 1;
  $secondeyes = "";
}
$usr_id = -1;
if ($gCurrentUser)
  $usr_id = $gCurrentUser->getValue('usr_id');

$header_location = "Location: " . $baseUrl . "login.php";
if ($rights == 0)
{
  header("$header_location");
}

$selector = "pref_id = $rights OR pref_id = -$rights";
$articles = $db->query("SELECT * FROM presse_content WHERE $selector ORDER BY pref_id");

foreach ($articles as $article) {
  if ($article['pref_id'] == $rights) {
    $main_text = stripslashes($article['content']);
  }
}

$sendsubjectpreview = $db->query("SELECT * FROM presse_content WHERE pref_id = -$rights;");
$subject = stripslashes($sendsubjectpreview[0]['content']);

$preview_text = $main_text;

$testmail = false;
$eyes = " first_eyes_usr_id IS NOT NULL $secondeyes ";
if (isset($_POST['test']) && isset($_POST['testmail']))
{
  $testmail = true;
  $eyes = " 1 ";
}

$sendbo = $db->query("SELECT * FROM presse_content WHERE " . $eyes . " AND pref_id = $rights;");
$sendsubject = $db->query("SELECT * FROM presse_content WHERE " . $eyes . " AND pref_id = -$rights;");
$users = $db->query("SELECT * FROM presse_users WHERE confirmed AND prefs & $rights");
if (count($sendbo) == 1 && count($sendsubject) == 1)
{
    $may_send_mails = true;
}

if ($testmail)
{
  $mailaddr = $db->escape($_POST['testmail']);
  $users = $db->query("SELECT * FROM presse_users WHERE confirmed AND prefs & $rights AND email = $mailaddr LIMIT 1");
}

if ($testmail || (isset($_POST['sendmails']) && $may_send_mails)) {
  if (!($rights > 0))
  {
    header("$header_location");
  }
  $sendmails = true;
  $may_send_mails = false;
}

$db->close();

end:
?>

<!DOCTYPE html>
<html lang="de">
  <head>
    <meta charset="utf-8">
    <title>Piratenpartei Presseverteiler</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Hier können sich Interessenten für Presseinformationen der Piratenpartei Österreichs anmelden.">
    <meta name="author" content="Piratenpartei Österreichs">

    <!-- Le styles -->
    <link href="css/bootstrap.css" rel="stylesheet">
    <style type="text/css">
	body {
	background-color: #4c2582;
        padding-top: 60px;
        padding-bottom: 40px;
        }
	footer {
	color: white;
	}
    </style>

    <link href="css/bootstrap-responsive.css" rel="stylesheet">

    <!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
    <!--[if lt IE 9]>
      <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->

    <!-- Fav and touch icons
    <link rel="shortcut icon" href="ico/favicon.ico">
    <link rel="apple-touch-icon-precomposed" sizes="144x144" href="ico/apple-touch-icon-144-precomposed.png">
    <link rel="apple-touch-icon-precomposed" sizes="114x114" href="ico/apple-touch-icon-114-precomposed.png">
    <link rel="apple-touch-icon-precomposed" sizes="72x72" href="ico/apple-touch-icon-72-precomposed.png">
    <link rel="apple-touch-icon-precomposed" href="ico/apple-touch-icon-57-precomposed.png">-->
  </head>

  <body>

    <div class="container">
      <div class="row">
<?
if ($sendmails)
{
        echo '
        <div class="span12">
          <div class="well">
            <h3 id="please_wait">Bitte warten...</h3>
              <p>
';
$db = new db($dbLang, $dbName);
if (!$testmail)
{
$db->query("UPDATE presse_content SET first_eyes_usr_id = NULL, second_eyes_usr_id = NULL WHERE pref_id = $rights OR pref_id = -$rights;");
}
$user_count = count($users);
$nth = 10;
$lo_real_id = 1;
foreach ($users as $user)
{
    $user_mailtext = stripslashes($sendbo[0]['content']);
    $subject = stripslashes($sendsubject[0]['content']);
    $lo_real_id = $sendbo[0]['pref_id'];

        mail_utf8($db, $user['email'], "[Presseinformation Piratenpartei] $subject", $user_mailtext, from_header($lo_real_id), change_link($user['sid']));
	
	if ($testmail)
	{
		echo '<p>Versand an ' . $user['email'] . " erfolgt.</p>\n";
	}
	
        $i++;
        if ($i % ($user_count / $nth) == 0) {
          echo $i . " / " . $user_count . "<br />";
	}
}
echo '
              </p>
';
if ($i > 0)
  echo '
            <h3>Versand abgeschlossen.</h3>
';
echo '
            <script type="text/javascript">document.getElementById("please_wait").style.display="none";</script>
          </div>
        </div><!--/span-->
';
if (!$testmail)
{
if ($rights == 1)
  $db->query("UPDATE presse_content SET first_eyes_usr_id = NULL, second_eyes_usr_id = NULL;");
else
  $db->query("UPDATE presse_content SET first_eyes_usr_id = NULL, second_eyes_usr_id = NULL WHERE pref_id = $rights OR pref_id = -$rights;");
}
$db->close();
}?>
	<div class="span8">
	  <div class="well">
	    <h1>Vorschau</h1>
	    <p><form action="preview.php" method="POST">
<?
if ($may_send_mails)
echo '
	      <input type="hidden" name="sendmails" value="true" />
	      <input type="submit" class="btn btn-success" value="Pressemitteilung aussenden" />
';
?>
	      <a class="btn" href="create.php">Pressemitteilung bearbeiten</a>
              <textarea style="width:180px;" rows="1" name="testmail"></textarea>
              <input type="submit" class="btn btn-success" name="test" value="Test an diese Mailadresse aussenden" />
              </form></p>
	    <p>Betreff: <?echo $subject;?></p>
	    <p><?echo "<pre>".$preview_text."</pre>";?></p>
	  </div>
	</div>
        <div class="span4">
<?
$article = $sendsubjectpreview[0];
$admins = "";
$prefs = decodePrefs($article['pref_id']);
$send = true;
if (isset($article['first_eyes_usr_id'])) {$admins[] = $article['first_eyes_usr_id'];}
if ($personsn >= 2) { if (isset($article['second_eyes_usr_id'])) {$admins[] = $article['second_eyes_usr_id'];} else {$send = false;} }
$admins = getAdminNames($admins);
if ($send) {$send_color = "alert-success";} else {$send_color = "alert-danger";}
echo '
            <div class="alert '.$send_color.'">
              <p>Betreff '.$prefs[0].'</br>
              Versandfreigabe erfolgt durch: '.implode(", ", $admins).'</p>
            </div>
';
foreach ($articles as $article)
{
if ($article['pref_id'] < 0) {continue;}
if (!(intval($article['pref_id']) & $rights))
  continue;
$admins = "";
$prefs = decodePrefs($article['pref_id']);
$send = true;
if (isset($article['first_eyes_usr_id'])) {$admins[] = $article['first_eyes_usr_id'];}
if ($personsn >= 2) { if (isset($article['second_eyes_usr_id'])) {$admins[] = $article['second_eyes_usr_id'];} else {$send = false;} }
$admins = getAdminNames($admins);
if ($send) {$send_color = "alert-success";} else {$send_color = "alert-danger";}
echo '
            <div class="alert '.$send_color.'">
              <p>Bereich: '.$prefs[0].'</br>
              Versandfreigabe erfolgt durch: '.implode(", ", $admins).'</p>
            </div>
';
}
?>
	  </div>
        </div><!--/span-->
      </div><!--/row-->

      <footer>
        <p>Piratenpartei Österreichs, Schadinagasse 3, 1170 Wien</p>
      </footer>

    </div><!--/.fluid-container-->

    <!-- Le javascript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="js/jquery.js"></script>
    <script src="js/bootstrap-transition.js"></script>
    <script src="js/bootstrap-alert.js"></script>
    <script src="js/bootstrap-modal.js"></script>
    <script src="js/bootstrap-dropdown.js"></script>
    <script src="js/bootstrap-scrollspy.js"></script>
    <script src="js/bootstrap-tab.js"></script>
    <script src="js/bootstrap-tooltip.js"></script>
    <script src="js/bootstrap-popover.js"></script>
    <script src="js/bootstrap-button.js"></script>
    <script src="js/bootstrap-collapse.js"></script>
    <script src="js/bootstrap-carousel.js"></script>
    <script src="js/bootstrap-typeahead.js"></script>
  </body>
</html>

