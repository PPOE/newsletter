<?php
$display = "#confirm_view {display:none;}";
//Added by Nero for reporting
error_reporting(E_ALL);
//Added by Nero for reson
//session_start();
class db {
private $dbConn_ = null;
public $dbType_ = null;
public function __construct($dbType, $name, $host = null)
{
  $this->dbType_ = $dbType;
  switch ($this->dbType_)
  {
    case 'mysql':
      global $dbUser;
      global $dbPass;
      $this->dbConn_ = mysql_connect($host, $dbUser, $dbPass);
      if (!$this->dbConn_)
      {
        exit('Could not connect: ' . mysql_error());
      }
      mysql_select_db($name, $this->dbConn_);
      break;
    case 'pgsql':
      $this->dbConn_ = pg_connect("dbname=$name")
        or exit('Could not connect: ' . pg_last_error());
      break;
    default:
      exit("invalid dbtype!");
  }
}
public function query($query)
{
  $result_array = array();
  switch ($this->dbType_)
  {
    case 'mysql':
      $result = mysql_query($query);
      if (!$result)
        return $false;
      if ($result === true)
     return true;
      while ($line = mysql_fetch_assoc($result)) {
        $result_array[] = $line;
      }
      break;
    case 'pgsql':
      $result = pg_query($query) or exit('Abfrage fehlgeschlagen: ' . pg_last_error() . '<br><br>' . $query);
      if (!$result)
        return $false;
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
  switch ($this->dbType_)
  {
    case 'mysql':
      mysql_close($this->dbConn_);
      break;
    case 'pgsql':
      pg_close($this->dbConn_);
      break;
    default:
      exit("invalid dbtype!");
  }
}
public function escape($text)
{
  switch ($this->dbType_)
  {
    case 'mysql':
      return "'".mysql_escape_string($text)."'";
    case 'pgsql':
      return pg_escape_literal($text);
    default:
      exit("invalid dbtype!");
  }
}
}
function base64_url_encode($input)
{
    return strtr(base64_encode($input), '+/=', '-_$');
}
function base64_url_decode($input)
{
    return base64_decode(strtr($input, '-_$', '+/='));
}
function change_link($sid,$page = 'change')
{
  global $baseUrl,$key;
  $date = new DateTime();
  $rand = mt_rand();
  return $baseUrl.$page.".php?s=" . $rand . "&q=" . base64_url_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $key , $sid . '|' . $date->getTimestamp(), MCRYPT_MODE_CBC));
}
function mail_utf8($db, $to, $subject, $message, $from, $unsubscribe_link = null)
{
  $subject = "=?UTF-8?B?".base64_encode($subject)."?=";

  $headers = $from;
  $headers .= "MIME-Version: 1.0\r\nContent-type: text/plain; charset=UTF-8\r\n";

  $message .= '

--

Piratenpartei Österreichs

Impressum: https://wiki.piratenpartei.at/wiki/Piratenwiki:Impressum

Kontakt zur TF Newsletter (öffentlich!): tf-newsletter@forum.piratenpartei.at';
if ($unsubscribe_link != null)
{
  $message .= '

Einstellungen ändern bzw. Abmeldung vom Newsletter: ' . $unsubscribe_link;
}
  $to = base64_encode($to);
  $subject = base64_encode($subject);
  $message = base64_encode($message);
  $headers = base64_encode($headers);
  $db->query("INSERT INTO mail_queue (mto,msubject,mbody,mheaders) VALUES ('$to', '$subject', '$message', '$headers');");
  return true;
}
function from_header($n)
{
        switch ($n) {
                case 2:
                        return "From: Piratenpartei Newsletter <lv-burgenland@piratenpartei.at>\r\n";
                case 4:
                        return "From: Piratenpartei Newsletter <lv-kaernten@piratenpartei.at>\r\n";
                case 8:
                        return "From: Piratenpartei Newsletter <lv-noe@piratenpartei.at>\r\n";
                case 16:
                        return "From: Piratenpartei Newsletter <lv-ooe@piratenpartei.at>\r\n";
                case 32:
                        return "From: Piratenpartei Newsletter <lv-sbg@piratenpartei.at>\r\n";
                case 64:
                        return "From: Piratenpartei Newsletter <lv-steiermark@piratenpartei.at>\r\n";
                case 128:
                        return "From: Piratenpartei Newsletter <lv-vorarlberg@piratenpartei.at>\r\n";
                case 256:
                        return "From: Piratenpartei Newsletter <lv-wien@piratenpartei.at>\r\n";
                default:
                        return "From: Piratenpartei Newsletter <newsletter@piratenpartei.at>\r\n";
        }
}

require_once('config.php');
//require_once('db.php');
//require_once('mail.php');

if(isset($_GET['s']) && preg_match('/^-?\d+$/', $_GET['s']) == 1 && isset($_GET['q'])) {
  $s = intval($_GET['s']);
  $q = $_GET['q'];
} else {
  $error = "Ungültige Abfrge 1!";
  goto end;
}
$q_array = explode("|", mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $key, base64_url_decode($q), MCRYPT_MODE_CBC));
if (count($q_array) <= 1 || preg_match('/^-?\d+$/', $q_array[0]) != 1) {
  $error = "Ungültige Abfrge 2!";
  goto end;
}

$sid = $q_array[0];

$db = new db($dbLang, $dbName);

$confirmed = $db->query("SELECT confirmed FROM users WHERE sid = '$sid' LIMIT 1");
if ($confirmed[0]['confirmed'] != 0 && $confirmed[0]['confirmed'] != 'f')
{
    $error = "Diese E-Mail-Adresse wurde bereits bestÃ¤tigt!";
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
    <meta name="description" content="Hier kÃ¶nnen sich Interessenten und Mitglieder fÃ¼r den Newsletter der Piratenpartei Ã–sterreichs anmelden.">
    <meta name="author" content="Piratenpartei Ã–sterreichs">

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
	    <p>Ab sofort erhältst du regelmäßig unseren personalisierten Newsletter. Viel Spaß!</p>
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

