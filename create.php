<!--
newsletter=> \d content
                                             Table "public.content"
          Column           |            Type             |                      Modifiers                       
---------------------------+-----------------------------+------------------------------------------------------
 id                        | integer                     | not null default nextval('content_id_seq'::regclass)
 pref_id                   | integer                     | 
 content                   | text                        | 
 earliest_date_for_sending | timestamp without time zone | 
 latest_date_for_sending   | timestamp without time zone | 
 sent                      | boolean                     | 
 first_eyes_usr_id         | integer                     | 
 second_eyes_usr_id        | integer                     | 
 third_eyes_usr_id         | integer                     | 

newsletter=> \d users
                         Table "public.users"
 Column |  Type   |                     Modifiers                      
--------+---------+----------------------------------------------------
 id     | integer | not null default nextval('users_id_seq'::regclass)
 email  | text    | 
 prefs  | integer |
 confirmed | boolean | default false	 
 confirm_int | integer | default 0

newsletter=> \d admins
    Table "public.admins"
 Column |  Type   | Modifiers 
--------+---------+------------
 usr_id | integer | 
 rights | integer | 
-->

<?
require("config.php");
require("db.php");
require("mail.php");

$db = new db($dbLang, $dbName);

$rights = checklogin($db);
$usr_id = checklogin_id($db);

$header_location = "Location: " . $baseUrl . "login.php";
if ($rights == 0)
{
header("$header_location");
}
if (isset($_POST['save']))
{
	$save = true;
	$pref_id = intval($_POST['pref_id']);
        if (!($pref_id & $rights))
          header("$header_location");
	$db->query("UPDATE content SET first_eyes_usr_id = NULL, second_eyes_usr_id = NULL, third_eyes_usr_id = NULL WHERE pref_id = $pref_id");
}
if (isset($_POST['publish']))
{
	$save = true;
	$publish = true;
}
if ($save)
{
	$content = $db->escape($_POST['content']);
	$pref_id = intval($_POST['pref_id']);
        if ($pref_id == 1 && preg_match('/%%LO CONTENT%%/s',$content) != 1)
        {
          $publish = false;
          $db->query("UPDATE content SET first_eyes_usr_id = NULL, second_eyes_usr_id = NULL, third_eyes_usr_id = NULL WHERE pref_id = $pref_id");
        }
        if (!($pref_id & $rights))
          header("$header_location");
	$db->query("UPDATE content SET content = '$content' WHERE pref_id = $pref_id");
}
if ($publish)
{
	$eyes_usr_id = $db->query("SELECT first_eyes_usr_id, second_eyes_usr_id, third_eyes_usr_id FROM content WHERE pref_id = $pref_id");
	if ((preg_match('/^\d+$/', $eyes_usr_id[0]['first_eyes_usr_id']) == 1 && $eyes_usr_id[0]['first_eyes_usr_id'] == $usr_id) ||
            (preg_match('/^\d+$/', $eyes_usr_id[0]['second_eyes_usr_id']) == 1 && $eyes_usr_id[0]['second_eyes_usr_id'] == $usr_id) ||
            (preg_match('/^\d+$/', $eyes_usr_id[0]['third_eyes_usr_id']) == 1 && $eyes_usr_id[0]['third_eyes_usr_id'] == $usr_id)) {}
	elseif (preg_match('/^\d+$/', $eyes_usr_id[0]['first_eyes_usr_id']) != 1) {$db->query("UPDATE content SET first_eyes_usr_id = $usr_id WHERE pref_id = $pref_id");}
	elseif (preg_match('/^\d+$/', $eyes_usr_id[0]['second_eyes_usr_id']) != 1) {$db->query("UPDATE content SET second_eyes_usr_id = $usr_id WHERE pref_id = $pref_id");}
	elseif (preg_match('/^\d+$/', $eyes_usr_id[0]['third_eyes_usr_id']) != 1) {$db->query("UPDATE content SET third_eyes_usr_id = $usr_id WHERE pref_id = $pref_id");}
        else { }

	$sendbo = $db->query("SELECT * FROM content WHERE first_eyes_usr_id NOTNULL AND second_eyes_usr_id NOTNULL AND third_eyes_usr_id NOTNULL AND pref_id = 1;");
        $sendsubject = $db->query("SELECT * FROM content WHERE first_eyes_usr_id NOTNULL AND second_eyes_usr_id NOTNULL AND third_eyes_usr_id NOTNULL AND pref_id = 512;");
	if (count($sendbo) == 1 && count($sendsubject) == 1)
	{
/*		$sendlos = $db->query("SELECT * FROM content WHERE first_eyes_usr_id NOTNULL AND second_eyes_usr_id NOTNULL AND pref_id != 1;");
		$mailtext = $sendbo[0]['content'];
		$subject = $db->query("SELECT content FROM content WHERE pref_id = 512;");
		$subject = $subject[0]['content'];
		$users = $db->query("SELECT * FROM users WHERE confirmed");*/
		$sendmails = true;
	}
}

$articles = $db->query("SELECT * FROM content WHERE NOT sent ORDER BY pref_id");

$db->close();

end:
?>

<!DOCTYPE html>
<html lang="de">
  <head>
    <meta charset="utf-8">
    <title>Piraten-Newsletter</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Hier können sich Interessenten und Mitglieder für den Newsletter der Piratenpartei Österreichs anmelden.">
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
/*if ($sendmails)
{
	echo '
        <div class="span12">
          <div class="well">
            <h3 id="please_wait">Bitte warten...</h3>
              <p>
';
$user_count = count($users);
$nth = 10;
foreach ($users as $user)
{
        $lo_mailtext = '';
        foreach ($sendlos as $sendlo)
        {
		if (intval($sendlo['pref_id']) & intval($user['prefs']))
	                $lo_mailtext .= $sendlo['content'] . "\n";
        }
        $user_mailtext = str_replace('%%LO CONTENT%%',$lo_mailtext,$mailtext);

	mail_utf8($user['email'], "[Piraten-Newsletter] $subject", $user_mailtext, change_link($user['sid']));

	$i++;
	if ($i % ($user_count / $nth) == 0)
	echo $i . " / " . $user_count . "<br />";
}
echo '
              </p>
            <h3>Versand abgeschlossen.</h3>
            <script type="text/javascript">document.getElementById("please_wait").style.display="none";</script>
          </div>
        </div><!--/span-->
';
}
else*/
{
foreach ($articles as $article)
{
if (!(intval($article['pref_id']) & $rights))
  continue;
if($article['pref_id'] != 512) {continue;}
if (isset($article['first_eyes_usr_id'])) {$admins[] = $article['first_eyes_usr_id'];}
if (isset($article['second_eyes_usr_id'])) {$admins[] = $article['second_eyes_usr_id'];}
if (isset($article['third_eyes_usr_id'])) {$admins[] = $article['third_eyes_usr_id'];}
echo '
        <div class="span12">
          <div class="well">
            <h3>Betreff bearbeiten</h3>
            <div><form action="create.php" method="POST">
              <input type="hidden" name="pref_id" value="512" />
              <textarea style="width:60%;" rows="1" name="content">'.$article['content'].'</textarea><br />
              <input type="submit" class="btn" name="save" value="Speichern (ohne Versandfreigabe)" />
              <input type="submit" class="btn" name="publish" value="Versandfreigabe" />
              <p>Versandfreigabe erfolgt durch: '.implode(", ", $admins).'</p>
            </form></div>
          </div>
        </div><!--/span-->
';
}
foreach ($articles as $article)
{
if (!(intval($article['pref_id']) & $rights))
  continue;
$admins = "";
$prefs = decodePrefs($article['pref_id']);
if (isset($article['first_eyes_usr_id'])) {$admins[] = $article['first_eyes_usr_id'];}
if (isset($article['second_eyes_usr_id'])) {$admins[] = $article['second_eyes_usr_id'];}
if ($article['pref_id'] == 1)
  if (isset($article['third_eyes_usr_id'])) {$admins[] = $article['third_eyes_usr_id'];}
  $send_btn = "";
$area_note = "";
if ($article['pref_id'] == 1)
{
  $send_btn = "<p><a href='preview.php' class='btn btn-success'>Newsletter absenden (Vorschau)</a></p>";
  $area_note = "<br />Beachte dass der Text die Zeichenfolge <code>%%LO CONTENT%%</code> enthalten muss. An dieser Stelle wird der LO-spezifische Inhalt eingefügt.";
}
if ($article['pref_id'] == 512)
  continue;
echo '
        <div class="span12">
          <div class="well">
	    '.$send_btn.'
            <h3>Text bearbeiten</h3>
            <div><form action="create.php" method="POST">
	      <input type="hidden" name="pref_id" value="'.$article['pref_id'].'" />
              <input type="hidden" name="id" value="'.$article['id'].'" />
              <p>Bereich: '.$prefs[0].$area_note.'</p>
              <textarea style="width:60%;" rows="5" name="content">'.$article['content'].'</textarea><br />
              <input type="submit" class="btn" name="save" value="Speichern (ohne Versandfreigabe)" />
              <input type="submit" class="btn" name="publish" value="Versandfreigabe" />
              <p>Versandfreigabe erfolgt durch: '.implode(", ", $admins).'</p>
            </form></div>
	  </div>
        </div><!--/span-->
';
}
}
?>
      </div><!--/row-->

      <footer>
        <p>Piratenpartei Österreichs, Lange Gasse 1/4, 1080 Wien</p>
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

