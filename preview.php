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

$articles = $db->query("SELECT * FROM content WHERE pref_id != 512 ORDER BY pref_id");

foreach ($articles as $article) {
  if($article['pref_id'] == 1) {
    $main_text = explode("%%LO CONTENT%%", $article['content']);
  } else {
    if(isset($article['second_eyes_usr_id'])) {
      $lo = decodePrefs($article['pref_id']);
      $pre = "--------------- Information der LO " . $lo[0] . " ";
      $pre .= str_repeat("-", 72 - strlen(mb_convert_encoding($pre,'ISO-8859-15','UTF-8'))) . "\n";
      $post = "\n" . str_repeat("-", strlen(mb_convert_encoding($pre,'ISO-8859-15','UTF-8')) - 1) . "\n";
      if (strlen($article['content']) > 10)
        $lo_text[] = $pre . $article['content'] . $post;
    }
  }
}

$preview_text_lo = implode('<br><br>', $lo_text);
$preview_text = $main_text[0].'<br><br>'.$preview_text_lo.'<br><br>'.$main_text[1];

$sendbo = $db->query("SELECT * FROM content WHERE first_eyes_usr_id IS NOT NULL AND second_eyes_usr_id IS NOT NULL AND third_eyes_usr_id IS NOT NULL AND pref_id = 1;");
$sendsubject = $db->query("SELECT * FROM content WHERE first_eyes_usr_id IS NOT NULL AND second_eyes_usr_id IS NOT NULL AND third_eyes_usr_id IS NOT NULL AND pref_id = 512;");
$sendlos = $db->query("SELECT * FROM content WHERE first_eyes_usr_id IS NOT NULL AND second_eyes_usr_id IS NOT NULL AND pref_id != 1 AND pref_id != 512;");
if (count($sendbo) == 1)
  $mailtext = $sendbo[0]['content'];
$subject_r = $db->query("SELECT * FROM content WHERE pref_id = 512;");
if (count($subject_r) == 1)
  $subject = $subject_r[0]['content'];
else
  $subject = '';
$users = $db->query("SELECT * FROM users WHERE confirmed");
if (count($sendbo) == 1 && count($sendsubject) == 1)
{
    $may_send_mails = true;
}

if (isset($_POST['sendmails']) && $may_send_mails) {
  if ($rights != 1)
  {
    header("$header_location");
  }
  $sendmails = true;
}

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
if ($sendmails)
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
	      $lo = decodePrefs($sendlo['pref_id']);
	      $pre = "--------------- Information der LO " . $lo[0] . " "; 
	      $pre .= str_repeat("-", 72 - strlen(mb_convert_encoding($pre,'ISO-8859-15','UTF-8'))) . "\n";
	      $post = "\n" . str_repeat("-", strlen(mb_convert_encoding($pre,'ISO-8859-15','UTF-8')) - 1) . "\n";
	      if (strlen($article['content']) > 10 && intval($sendlo['pref_id']) & intval($user['prefs']))
                        $lo_mailtext .= $pre . $sendlo['content'] . $post;
        }

        $user_mailtext = str_replace('%%LO CONTENT%%',$lo_mailtext,$mailtext);

        mail_utf8($user['email'], "[Piraten-Newsletter] $subject", $user_mailtext, change_link($user['sid']));

        $i++;
        if ($i % ($user_count / $nth) == 0) {
          echo $i . " / " . $user_count . "<br />";
	}
}
echo '
              </p>
            <h3>Versand abgeschlossen.</h3>
            <script type="text/javascript">document.getElementById("please_wait").style.display="none";</script>
          </div>
        </div><!--/span-->
';
$db = new db($dbLang, $dbName);
$db->query("UPDATE content SET content = NULL, first_eyes_usr_id = NULL, second_eyes_usr_id = NULL, third_eyes_usr_id = NULL WHERE first_eyes_usr_id IS NOT NULL AND second_eyes_usr_id IS NOT NULL AND third_eyes_usr_id IS NOT NULL AND pref_id = 1;");
$db->query("UPDATE content SET content = NULL, first_eyes_usr_id = NULL, second_eyes_usr_id = NULL, third_eyes_usr_id = NULL WHERE first_eyes_usr_id IS NOT NULL AND second_eyes_usr_id IS NOT NULL AND third_eyes_usr_id IS NOT NULL AND pref_id = 512;");
$db->query("UPDATE content SET content = NULL, first_eyes_usr_id = NULL, second_eyes_usr_id = NULL, third_eyes_usr_id = NULL WHERE first_eyes_usr_id IS NOT NULL AND second_eyes_usr_id IS NOT NULL AND pref_id != 1;");
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
	      <input type="submit" class="btn btn-success" value="Newsletter aussenden" />
';
?>
	      <a class="btn" href="create.php">Newsletter bearbeiten</a></form></p>
	    <p>Betreff: [Piraten-Newsletter] <?echo $subject;?></p>
	    <p><?echo "<pre>".$preview_text."</pre>";?></p>
	  </div>
	</div>
        <div class="span4">
<?
if (512 & $rights)
{
$article = $subject_r[0];
$admins = "";
$prefs = decodePrefs($article['pref_id']);
$send = true;
if (isset($article['first_eyes_usr_id'])) {$admins[] = $article['first_eyes_usr_id'];}
if (isset($article['second_eyes_usr_id'])) {$admins[] = $article['second_eyes_usr_id'];} else {$send = false;}
if ($article['pref_id'] == 1)
  if (isset($article['third_eyes_usr_id'])) {$admins[] = $article['third_eyes_usr_id'];} else {$send = false;}
$admins = getAdminNames($admins);
if ($send) {$send_color = "alert-success";} else {$send_color = "alert-danger";}
echo '
            <div class="alert '.$send_color.'">
              <p>Betreff</br>
              Versandfreigabe erfolgt durch: '.implode(", ", $admins).'</p>
            </div>
';
}
foreach ($articles as $article)
{
if ($article['pref_id'] == 512) {continue;}
if (!(intval($article['pref_id']) & $rights))
  continue;
$admins = "";
$prefs = decodePrefs($article['pref_id']);
$send = true;
if (isset($article['first_eyes_usr_id'])) {$admins[] = $article['first_eyes_usr_id'];}
if (isset($article['second_eyes_usr_id'])) {$admins[] = $article['second_eyes_usr_id'];} else {$send = false;}
if ($article['pref_id'] == 1)
  if (isset($article['third_eyes_usr_id'])) {$admins[] = $article['third_eyes_usr_id'];} else {$send = false;}
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

