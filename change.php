<?
$display = "#confirm_view {display:none;}\n#delete_view {display:none;}";

$email = isset($_POST['email']) ? $_POST['email'] : '';
$bund = isset($_POST['bund']) ? $_POST['bund'] : '';
$bgld = isset($_POST['bgld']) ? $_POST['bgld'] : '';
$ktn = isset($_POST['ktn']) ? $_POST['ktn'] : '';
$noe = isset($_POST['noe']) ? $_POST['noe'] : '';
$ooe = isset($_POST['ooe']) ? $_POST['ooe'] : '';
$sbg = isset($_POST['sbg']) ? $_POST['sbg'] : '';
$stmk = isset($_POST['stmk']) ? $_POST['stmk'] : '';
$graz = isset($_POST['graz']) ? $_POST['graz'] : '';
$vlbg = isset($_POST['vlbg']) ? $_POST['vlbg'] : '';
$w = isset($_POST['w']) ? $_POST['w'] : '';
$submit = isset($_POST['submit']) ? $_POST['submit'] : '';
$s_get = isset($_GET['s']) ? $_GET['s'] : '';
$q_get = isset($_GET['q']) ? $_GET['q'] : '';

$mailqueue = true;

require("db.php");
require("config.php");
require("mail.php");

$db = new db($dbLang,$dbName);

if(isset($_GET['s']) && preg_match('/^-?\d+$/', $_GET['s']) == 1 && isset($_GET['q'])) {
  $s = intval($_GET['s']);
  $q = $_GET['q'];
} else {
  $error = "Ungültige Abfrage, Error-Code: A!";
  $display = "#confirm_view {display:none;}\n#change_view {display:none;}\n#delete_view {display:none;}";
  goto end;
}

$q_array = explode("|", mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $key . $s, base64_url_decode($q), MCRYPT_MODE_CBC));
if (count($q_array) <= 1 || preg_match('/^-?\d+$/', $q_array[0]) != 1) {
  $error = "Ungültige Abfrage, Error-Code: B!";
  goto end;
}

$sid = $q_array[0];

if($submit != "true"){
  goto end;
}

if($_POST['delete'] == "true") {
  $delete = "true";
}

if($bund == "bund") {$prefs += 1;}
if($bgld == "bgld") {$prefs += 2;}
if($ktn == "ktn") {$prefs += 4;}
if($noe == "noe") {$prefs += 8;}
if($ooe == "ooe") {$prefs += 16;}
if($sbg == "sbg") {$prefs += 32;}
if($stmk == "stmk") {$prefs += 64;}
if($vlbg == "vlbg") {$prefs += 128;}
if($w == "w") {$prefs += 256;}
if($graz == "graz") {$prefs += 512;}

$id = $db->query("SELECT id FROM presse_users WHERE sid = '$sid' LIMIT 1;");
$email = $db->query("SELECT email FROM presse_users WHERE sid = '$sid' LIMIT 1;");
$email = $email[0]['email'];

if (count($id[0]['id']) == '')
{
  $error = "Ungültige Abfrage, Error-Code: C!";
  goto end;
}

if ($delete == "true") {
  $db->query("DELETE FROM presse_users WHERE sid = $sid;");
  $display = "#confirm_view {display:none;}\n#change_view {display:none;}";
  mail_utf8($db, $email, "$tag Presseverteiler abbestellt", "Ab sofort erhalten Sie von uns keine Pressemitteilungen mehr. Ihre Daten wurden aus unserer Datenbank unwiderbringlich gelöscht.",from_header(1));
  goto end;
}

$db->query("UPDATE presse_users SET prefs = $prefs WHERE sid = $sid;");
mail_utf8($db, $email, "$tag Einstellung geändert", "Ihre Presseverteiler-Einstellungen wurden geändert. Mit einem Klick auf den folgenden Klick können die Einstellungen noch einmal geändert werden:\n".change_link($sid),from_header(1));

$prefs = $db->query("SELECT prefs FROM presse_users WHERE sid = $sid;");
$prefs = $prefs[0]['prefs'];

$display = "#change_view {display:none;}\n#delete_view {display:none;}";
end:
if ($sid != null)
{
	$prefs = $db->query("SELECT prefs FROM presse_users WHERE sid = $sid;");
	$prefs = $prefs[0]['prefs'];
}
$db->close();
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
<?echo $display;?>
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
        <div class="span8">
          <div id="delete_view" class="well">
            <h1>Abmeldung erfolgreich!</h1>
	    <p>Ab sofort wurde der Versand von Presseinformationen der Piratenpartei an Sie gestoppt und ihre Daten unwiderbringlich gelöscht!</p> 
          </div>
	  <div id="confirm_view" class="well">
	    <h1>Ihre Einstellungen wurden erfolgreich geändert!</h1>
	  </div>
<?
if($error != "") {
  echo "<div class='alert alert-error'>".$error."</div>";
}
?>
	  <div id="change_view" class="well">
	    <h1>Presseverteiler-Einstellungen bearbeiten</h1>
	    <p>Hier können Sie ihre Presseverteiler-Einstellungen bearbeiten:</p>
	    <?echo "<form action=\"".change_link($sid)."\" method=\"post\">";?>
                <div>
                  <h4>Für welche Presseinformationen möchten Sie sich registieren?</h4>
<?
echo '                  <label class="checkbox"><input type="checkbox" name="bund" value="bund" checked="checked">Bundesweite Informationen</label>';
echo '                  <label class="checkbox"><input type="checkbox" name="bgld" value="bgld" '.($prefs & 2 ? 'checked="checked"' : '').'>Burgenland</label>';
echo '                  <label class="checkbox"><input type="checkbox" name="ktn" value="ktn" '.($prefs & 4 ? 'checked="checked"' : '').'>Kärnten</label>';
echo '                  <label class="checkbox"><input type="checkbox" name="noe" value="noe" '.($prefs & 8 ? 'checked="checked"' : '').'>Niederösterreich</label>';
echo '                  <label class="checkbox"><input type="checkbox" name="ooe" value="ooe" '.($prefs & 16 ? 'checked="checked"' : '').'>Oberösterreich</label>';
echo '                  <label class="checkbox"><input type="checkbox" name="sbg" value="sbg" '.($prefs & 32 ? 'checked="checked"' : '').'>Salzburg</label>';
echo '                  <label class="checkbox"><input type="checkbox" name="stmk" value="stmk" '.($prefs & 64 ? 'checked="checked"' : '').'>Steiermark</label>';
echo '                  <label class="checkbox"><input type="checkbox" name="vlbg" value="vlbg" '.($prefs & 128 ? 'checked="checked"' : '').'>Vorarlberg</label>';
echo '                  <label class="checkbox"><input type="checkbox" name="w" value="w" '.($prefs & 256 ? 'checked="checked"' : '').'>Wien</label>';
echo '                  <label class="checkbox"><input type="checkbox" name="graz" value="graz" '.($prefs & 512 ? 'checked="checked"' : '').'>Graz</label>';
?>
                </div>
              <input type="hidden" name="submit" value="true" />
              <button type="submit" class="btn">Absenden</button>
            </form>
	    <?echo "<form action=\"".change_link($sid)."\" method=\"post\">";?>
              <h4>Möchten Sie unsere Presseinformationen ganz abbestellen?</h4>
	      <input type="hidden" name="submit" value="true" />
	      <input type="hidden" name="delete" value="true" />
	      <button type="submit" class="btn btn-danger">Presseinformationen abbestellen</button>
	    </form>
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

