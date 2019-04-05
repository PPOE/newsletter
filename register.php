<?php

$mailqueue = true;

require_once('config.php');
require_once('db.php');
require_once('mail.php');

$display = "#welcome_view {display:none;}\n#dse_view {display:none;}";

$db = new db($dbLang, $dbName);

$email = isset($_POST['email']) ? $db->escape_string($_POST['email']) : '';
$bund = isset($_POST['bund']) ? $db->escape_string($_POST['bund']) : '';
$bgld = isset($_POST['bgld']) ? $db->escape_string($_POST['bgld']) : '';
$ktn = isset($_POST['ktn']) ? $db->escape_string($_POST['ktn']) : '';
$noe = isset($_POST['noe']) ? $db->escape_string($_POST['noe']) : '';
$ooe = isset($_POST['ooe']) ? $db->escape_string($_POST['ooe']) : '';
$sbg = isset($_POST['sbg']) ? $db->escape_string($_POST['sbg']) : '';
$stmk = isset($_POST['stmk']) ? $db->escape_string($_POST['stmk']) : '';
$vlbg = isset($_POST['vlbg']) ? $db->escape_string($_POST['vlbg']) : '';
$w = isset($_POST['w']) ? $db->escape_string($_POST['w']) : '';
$submit = isset($_POST['submit']) ? $_POST['submit'] : '';

if (isset($_GET['dse']))
{
  $display = "#form_view {display:none;}\n#welcome_view {display:none;}";
  goto end;
}

if($submit != "true"){
  goto end;
}

if($email == "") {
  $error = "Keine E-Mail-Adresse angegeben!";
  goto end;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
  $error = "Diese E-Mail-Adresse ist ungültig!";
  goto end;
}



$prefs = 1;
//if($bund == "bund") {$prefs += 1;}
if($bgld == "bgld") {$prefs += 2;}
if($ktn == "ktn") {$prefs += 4;}
if($noe == "noe") {$prefs += 8;}
if($ooe == "ooe") {$prefs += 16;}
if($sbg == "sbg") {$prefs += 32;}
if($stmk == "stmk") {$prefs += 64;}
if($vlbg == "vlbg") {$prefs += 128;}
if($w == "w") {$prefs += 256;}

$id = $db->query("SELECT id FROM users WHERE email = '$email' LIMIT 1");
if (count($id) > 0)
{
  $error = "Diese E-Mail-Adresse ist bereits für den Newsletter-Empfang eingetragen!";
  goto end;
}

do
{
$sid = mt_rand();
} while (count($db->query("SELECT * FROM users WHERE sid = $sid")) > 0);


$db->query("INSERT INTO users (email, prefs, sid) VALUES ('$email', $prefs, $sid);");

$checkmail_text = "Jemand (hoffentlich du selbst) möchte deine Mailadresse \"".$email."\" für den Piraten-Newsletter anmelden. \nWenn du damit einverstanden bist, klicke bitte auf den folgenden Link:\n".change_link($sid,"confirm"). "\n\nWenn du diesen Newsletter nicht empfangen willst, brauchst du nichts zu unternehmen. Nur wenn du den obigen Bestätigungslink anklickst, wirst du den Newsletter bekommen.";
mail_utf8($db,$email, "[Piraten-Newsletter] Bestätigung deiner E-Mail-Adresse", $checkmail_text, from_header(1));

$db->close();

$display = "#form_view {display:none;}\n#dse_view {display:none;}";
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
          <div id="dse_view" class="well">
            <h1>Datenschutzrichtlinien</h1>
            <p>
Nach Anmeldung wird die E-Mail-Adresse des Beziehers von der Piratenpartei &Ouml;sterreichs als Auftraggeber verarbeitet (gespeichert und f&uuml;r Zwecke der Versendung ben&uuml;tzt). Es werden keinerlei Daten zum &Uuml;bermittlungsvorgang (Zustell- oder Lesebest&auml;tigungen) ermittelt. Nach Abmeldung vom Bezug werden die Daten aus dieser Datenanwendung gel&ouml;scht. Eine &Uuml;bermittlung dieser Daten ist nicht vorgesehen. Die Datenanwendung f&uuml;r Zwecke dieses Newsletters (einschlie&szlig;lich der zur Verbreitung ben&uuml;tzten Mailserver) wird auf EDV-Anlagen der Piratenpartei &Ouml;sterreichs gehostet.
            </p>
            <p>
              <a href="register.php">Zur&uuml;ck</a>
            </p>
          </div>
      <div id="welcome_view" class="well">
        <h1>Danke für deine Anmeldung!</h1>
        <p>An die von dir eingebene E-Mail-Adresse wird in Kürze eine Bestätigungsmail versendet.</p>
      </div>
      <div id="form_view" class="well">
        <h1>Piraten-Newsletter</h1>
<?php
if($error != "") {
  echo "<div class='alert alert-error'>".$error."</div>";
}
?>
        <p>Hier kannst du dich zum Newsletter der Piratenpartei Österreichs schnell und einfach anmelden.<br>
        Unsere aktuellen Datenschutzrichtlinien findest du hier: <a href="register.php?dse=1">Datenschutzrichtlinien</a></p>
        <form action="register.php" method="post">
        <h4>Bitte trage hier deine E-Mail-Adresse ein:<?php echo $validemail;?></h4>
        <div class="input-prepend">
          <span class="add-on">@</span>
          <input id="inputEmail" type="text" name="email" placeholder="E-Mail-Adresse" value="<?php echo $email; ?>">
        </div>
        <div>
          <h4>Für welche Teile des Newsletters willst du dich registieren?</h4>
          <input type="hidden" name="bund" value="bund" />
          <label class="checkbox"><input type="checkbox" name="" value="" checked="checked" disabled>Bundesweite Informationen</label>
          <label class="checkbox"><input type="checkbox" name="bgld" value="bgld">Burgenland</label>
          <label class="checkbox"><input type="checkbox" name="ktn" value="ktn">Kärnten</label>
          <label class="checkbox"><input type="checkbox" name="noe" value="noe">Niederösterreich</label>
          <label class="checkbox"><input type="checkbox" name="ooe" value="ooe">Oberösterreich</label>
          <label class="checkbox"><input type="checkbox" name="sbg" value="sbg">Salzburg</label>
          <label class="checkbox"><input type="checkbox" name="stmk" value="stmk">Steiermark</label>
          <label class="checkbox"><input type="checkbox" name="vlbg" value="vlbg">Vorarlberg</label>
          <label class="checkbox"><input type="checkbox" name="w" value="w">Wien</label>
        </div>
              <input type="hidden" name="submit" value="true" />
	      <button type="submit" class="btn">Absenden</button>
	    </form>
			<p><a href="https://www.piratenpartei.at">Zurück zu piratenpartei.at</a></p>
	  </div>
        </div><!--/span-->
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

