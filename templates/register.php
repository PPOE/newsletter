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
            if ($error) {
                echo "<div class='alert alert-error'>" . $error . "</div>";
            }
            ?>
            <p>Hier kannst du dich zum Newsletter der Piratenpartei Österreichs schnell und einfach anmelden.<br>
                Unsere aktuellen Datenschutzrichtlinien findest du hier: <a href="register.php?dse=1">Datenschutzrichtlinien</a></p>
            <form action="register.php" method="post">
                <h4>Bitte trage hier deine E-Mail-Adresse ein:</h4>
                <div class="input-prepend">
                    <span class="add-on">@</span>
                    <input id="inputEmail" type="email" name="email" placeholder="E-Mail-Adresse" value="<?php echo $email; ?>">
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
            <p><a href="http://www.piratenpartei.at">Zurück zu piratenpartei.at</a></p>
        </div>
    </div><!--/span-->
</div><!--/row-->