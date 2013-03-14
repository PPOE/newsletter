<?
require("db.php");
require("mail.php");
require("config.php");

$submit = isset($_POST['submit']) ? $_POST['submit'] : '';
$name = isset($_POST['name']) ? $_POST['name'] : '';
$pass = isset($_POST['pass']) ? $_POST['pass'] : '';

$db = new db($dbLang, $dbName);

if (checklogin($db) == 0 && $submit == "true"){
  login($db, $name, $pass);
}

if (checklogin($db) > 0)
{
  $display = "#login_view {display:none;}";
}
else
{
  $display = "#subp_view {display:none;}";
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
	  <div id="login_view" class="well">
	    <h1>Piraten-Newsletter</h1>
<?
if($error != "") {
  echo "<div class='alert alert-error'>".$error."</div>";
}
?>
	    <form action="login.php" method="post">
		<div class="input-prepend">
		  <span class="add-on">Name:</span>
		  <input id="inputName" type="text" name="name" placeholder="Admidio Login Name" value="<? echo $name; ?>">
		</div>
                <div class="input-prepend">
                  <span class="add-on">Passwort:</span>
                  <input id="inputPass" type="password" name="pass" placeholder="Admidio Login Passwort">
                </div>
              <input type="hidden" name="submit" value="true" />
	      <button type="submit" class="btn">Login</button>
	    </form>
	  </div>
          <div id="subp_view" class="well">
            <h1>Admin Bereich</h1>
<?
if($error != "") {
  echo "<div class='alert alert-error'>".$error."</div>";
}
?>
            <a href="list.php">Teilnehmerliste anzeigen</a><br />
            <a href="create.php">Newsletter bearbeiten</a><br />
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

