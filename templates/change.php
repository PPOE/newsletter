<div class="row">
    <div class="span8">
        <div id="delete_view" class="well">
            <h1>Abmeldung erfolgreich!</h1>
            <p>Ab sofort wurde der Versand von Newslettern an dich gestoppt und deine Daten unwiderbringlich gelöscht!</p>
        </div>
        <div id="confirm_view" class="well">
            <h1>Deine Einstellungen wurden erfolgreich geändert!</h1>
        </div>
        <?php
        if($error != "") {
            echo "<div class='alert alert-error'>".$error."</div>";
        }
        ?>
        <div id="change_view" class="well">
            <h1>Newsletter-Einstellungen bearbeiten</h1>
            <p>Hier kannst du deine Newsletter-Einstellungen bearbeiten:</p>
            <?php echo "<form action=\"".change_link($sid)."\" method=\"post\">";?>
            <div>
                <h4>Für welche Teile des Newsletters willst du dich registieren?</h4>
                <?php
                echo '			<input type="hidden" name="bund" value="bund"/>';
                echo '                  <label class="checkbox"><input type="checkbox" name="" value="" checked="checked" disabled>Bundesweite Informationen</label>';
                echo '                  <label class="checkbox"><input type="checkbox" name="bgld" value="bgld" '.($prefs & 2 ? 'checked="checked"' : '').'>Burgenland</label>';
                echo '                  <label class="checkbox"><input type="checkbox" name="ktn" value="ktn" '.($prefs & 4 ? 'checked="checked"' : '').'>Kärnten</label>';
                echo '                  <label class="checkbox"><input type="checkbox" name="noe" value="noe" '.($prefs & 8 ? 'checked="checked"' : '').'>Niederösterreich</label>';
                echo '                  <label class="checkbox"><input type="checkbox" name="ooe" value="ooe" '.($prefs & 16 ? 'checked="checked"' : '').'>Oberösterreich</label>';
                echo '                  <label class="checkbox"><input type="checkbox" name="sbg" value="sbg" '.($prefs & 32 ? 'checked="checked"' : '').'>Salzburg</label>';
                echo '                  <label class="checkbox"><input type="checkbox" name="stmk" value="stmk" '.($prefs & 64 ? 'checked="checked"' : '').'>Steiermark</label>';
                echo '                  <label class="checkbox"><input type="checkbox" name="vlbg" value="vlbg" '.($prefs & 128 ? 'checked="checked"' : '').'>Vorarlberg</label>';
                echo '                  <label class="checkbox"><input type="checkbox" name="w" value="w" '.($prefs & 256 ? 'checked="checked"' : '').'>Wien</label>';
                ?>
            </div>
            <input type="hidden" name="submit" value="true" />
            <button type="submit" class="btn">Absenden</button>
            </form>
            <?php echo "<form action=\"".change_link($sid)."\" method=\"post\">";?>
            <h4>Willst du den Newsletter abbestellen?</h4>
            <input type="hidden" name="submit" value="true" />
            <input type="hidden" name="delete" value="true" />
            <button type="submit" class="btn btn-danger">Newsletter abbestellen</button>
            </form>
        </div>
    </div><!--/span-->
</div><!--/row-->
