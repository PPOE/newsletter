<!doctype html>
<html lang="de">
    <head>
        <meta charset="utf-8">
        <title>Piraten-Newsletter</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="Hier können sich Interessenten und Mitglieder für den Newsletter der Piratenpartei Österreichs anmelden.">
        <meta name="author" content="Piratenpartei Österreichs">

        <!-- Le styles -->
        <link href="bootstrap/css/bootstrap.min.css" rel="stylesheet" />
        <link href="bootstrap/css/bootstrap-theme.min.css" rel="stylesheet" />
        <link href="css/newsletter.css" rel="stylesheet" />

        <style type="text/css">
            <?php echo $display; ?>
        </style>
    </head>

    <body>

        <div class="container">

            <?php echo $pageContent; ?>

            <?php require_once('footer.php'); ?>

        </div>

        <!-- Le javascript
        ================================================== -->
        <!-- Placed at the end of the document so the pages load faster -->
        <script src="js/jquery.min.js"></script>
        <script src="bootstrap/js/bootstrap.min.js"></script>
    </body>
</html>
