<?php
$display = "#confirm_view {display:none;}";
require_once('config.php');
require_once('db.php');
require_once('mail.php');
if(isset($_GET['s']) && preg_match('/^-?\d+$/', $_GET['s']) == 1 && isset($_GET['q'])) {
  $s = intval($_GET['s']);
  $q = $_GET['q'];
} else {
  $error = "Ungültige Abfrage!";
  goto end;
}
$q_array = explode("|", mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $key . $s, base64_url_decode($q), MCRYPT_MODE_CBC));
if (count($q_array) <= 1 || preg_match('/^-?\d+$/', $q_array[0]) != 1) { 
  $error = "Ungültige Abfrage!";
  goto end;
}

$sid = $q_array[0];

$db = new db($dbLang, $dbName);

$confirmed = $db->query("SELECT confirmed FROM users WHERE sid = '$sid' LIMIT 1");
if ($confirmed[0]['confirmed'] != 0 && $confirmed[0]['confirmed'] != 'f')
{
  $error = "Diese E-Mail-Adresse wurde bereits bestätigt!";
  goto end;
}

$db->query("UPDATE users SET confirmed=TRUE WHERE sid = '$sid';");

$email = $db->query("SELECT email FROM users WHERE sid = '$sid' LIMIT 1");
$email = $email[0]['email'];

$checkmail_text = "Deine E-Mail-Adresse wurde erfolgreich bestätigt. Ab sofort erhältst du regelmäßig deinen personalisierten Newsletter!\n\n";
$checkmail_text .= "Mit einem Klick auf den folgenden Link kannst du die Inhaltseinstellungen deines Newsletters ansehen und verändern oder den Newsletter abbestellen:\n".change_link($sid);
mail_utf8($db,$confirm, $email, "[Piraten-Newsletter] E-Mail-Adresse bestätigt", $checkmail_text, from_header(1));

$db->close();
$display = "#error_view {display:none;}";
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
<?php echo $display;?>
    </style>

    <link href="css/bootstrap-responsive.css" rel="stylesheet">

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
        <div class="span8">
	  <div id="confirm_view" class="well">
	    <h1>Du bist erfolgreich angemeldet!</h1>
	    <p>Ab sofort erhältst du regelmäßig unseren personalisierten Newsletter. Viel Spa&szlig;!</p>
	    <p>Mit einem Klick auf den folgenden Link kannst du die Inhaltseinstellungen deines Newsletters verändern oder den Newsletter abbestellen:<br><a href="<?php echo change_link($sid);?>"><?php echo change_link($sid);?></a>
	    <p><a href="https://www.piratenpartei.at">Zurück zu piratenpartei.at</a></p>
	  </div>
	  <div id="error_view" class="well">
	    <h1>Ein Fehler ist aufgetreten!</h1>
<?php
if($error != "") {
  echo "<div class='alert alert-error'>".$error."</div>";
}
?>
        <p>Falls dieser Fehler wiederholt auftritt, wende dich an <a href="mailto:bgf@piratenpartei.at">bgf@piratenpartei.at</a>.</p>
      </div>
        </div><!--/span-->
      </div><!--/row-->

      <footer>
        <p>Piratenpartei Österreichs, Radetzkystraße 3/1, 8011 Graz</p>
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

