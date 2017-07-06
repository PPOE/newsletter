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
$pref_id = intval($_POST['pref_id']);
$header_location = "Location: " . $baseUrl . "login.php";
if (!$rights || ($rights != 1 && ($rights == 0 || ($pref_id != 0 && !($pref_id & $rights || -($pref_id) & $rights)))))
{
  header("$header_location");
}
if (isset($_POST['save']))
{
	$save = true;
	$db->query("UPDATE presse_content SET first_eyes_usr_id = NULL, second_eyes_usr_id = NULL WHERE pref_id = $pref_id");
}
if (isset($_POST['publish']))
{
	$publish = true;
}
if ($save)
{
	$content = $db->escape($_POST['content']);
        $db->query("UPDATE presse_content SET first_eyes_usr_id = NULL, second_eyes_usr_id = NULL WHERE pref_id = $pref_id");
	$db->query("UPDATE presse_content SET content = $content WHERE pref_id = $pref_id");
}
if ($publish)
{
	$eyes_usr_id = $db->query("SELECT first_eyes_usr_id, second_eyes_usr_id FROM presse_content WHERE pref_id = $pref_id");
	if ((preg_match('/^\d+$/', $eyes_usr_id[0]['first_eyes_usr_id']) == 1 && $eyes_usr_id[0]['first_eyes_usr_id'] == $usr_id) ||
            (preg_match('/^\d+$/', $eyes_usr_id[0]['second_eyes_usr_id']) == 1 && $eyes_usr_id[0]['second_eyes_usr_id'] == $usr_id)) {}
	elseif (preg_match('/^\d+$/', $eyes_usr_id[0]['first_eyes_usr_id']) != 1) {$db->query("UPDATE presse_content SET first_eyes_usr_id = $usr_id WHERE pref_id = $pref_id");}
	elseif (preg_match('/^\d+$/', $eyes_usr_id[0]['second_eyes_usr_id']) != 1) {$db->query("UPDATE presse_content SET second_eyes_usr_id = $usr_id WHERE pref_id = $pref_id");}
        else { }

	$sendbo = $db->query("SELECT * FROM presse_content WHERE first_eyes_usr_id IS NOT NULL $secondeyes AND pref_id = 1;");
        $sendsubject = $db->query("SELECT * FROM presse_content WHERE first_eyes_usr_id IS NOT NULL $secondeyes AND pref_id = -1;");
	if (count($sendbo) == 1 && count($sendsubject) == 1)
	{
		$sendmails = true;
	}
}

$articles = $db->query("SELECT * FROM presse_content WHERE (pref_id = $rights OR pref_id = -$rights) AND NOT sent ORDER BY pref_id");

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
foreach ($articles as $article)
{
$pid = intval($article['pref_id']);
if ($pid > 0)
  continue;
if ($pid < 0 && !(-$pid & $rights))
  continue;
if (isset($article['first_eyes_usr_id'])) {$admins[] = $article['first_eyes_usr_id'];}
if (isset($article['second_eyes_usr_id'])) {$admins[] = $article['second_eyes_usr_id'];}
$admins = getAdminNames($admins);
echo '
        <div class="span12">
          <div class="well">
            <h3>Betreff bearbeiten</h3>
            <div><form action="create.php" method="POST">
              <input type="hidden" name="pref_id" value="'.$article['pref_id'].'" />
              <textarea style="width:60%;" rows="1" name="content" onclick="document.getElementById(\'publish'.$article['pref_id'].'\').style.display=\'none\';">'.stripslashes($article['content']).'</textarea><br />
              <input type="submit" class="btn" name="save" value="Speichern (ohne Versandfreigabe)" />
              <input type="submit" class="btn" id="publish'.$article['pref_id'].'" name="publish" value="Versandfreigabe (ohne Speichern)" />
              <p>Versandfreigabe erfolgt durch ('.$persons.'): '.implode(", ", $admins).'</p>
            </form></div>
          </div>
        </div><!--/span-->
';
}
foreach ($articles as $article)
{
$pid = intval($article['pref_id']);
if ($pid > 0 && !($pid & $rights) && $rights != 1)
  continue;
if ($pid < 0)
  continue;
$admins = "";
$prefs = decodePrefs($article['pref_id']);
if (isset($article['first_eyes_usr_id'])) {$admins[] = $article['first_eyes_usr_id'];}
if (isset($article['second_eyes_usr_id'])) {$admins[] = $article['second_eyes_usr_id'];}
$admins = getAdminNames($admins);
$area_note = "";
$send_btn = "<p><a href='preview.php' class='btn btn-success'>Vorschau</a></p>";
echo '
        <div class="span12">
          <div class="well">
	    '.$send_btn.'
            <h3>Text bearbeiten</h3>
            <div><form action="create.php" method="POST">
	      <input type="hidden" name="pref_id" value="'.$article['pref_id'].'" />
              <input type="hidden" name="id" value="'.$article['id'].'" />
              <p>Bereich: '.$prefs[0].$area_note.'</p>
              <textarea style="width:60%;" rows="5" name="content" onclick="document.getElementById(\'publish'.$article['pref_id'].'\').style.display=\'none\';">'.stripslashes($article['content']).'</textarea><br />
              <input type="submit" class="btn" name="save" value="Speichern (ohne Versandfreigabe)" />
              <input type="submit" class="btn" id="publish'.$article['pref_id'].'" name="publish" value="Versandfreigabe (ohne Speichern)" />
              <p>Versandfreigabe erfolgt durch ('.$persons.'): '.implode(", ", $admins).'</p>
            </form></div>
	  </div>
        </div><!--/span-->
';
}
?>
      </div><!--/row-->

      <footer>
        <p><a href="https://wiki.piratenpartei.at/wiki/Piratenwiki:Impressum">Impressum</a></p>
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

