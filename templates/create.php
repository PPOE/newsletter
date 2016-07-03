<div class="row">
    <?php
    foreach ($articles as $article) {
        $pid = (int) $article['pref_id'];
        if ($pid > 0) {
            continue;
        }
        if ($pid < 0 && !(-$pid & $rights)) {
            continue;
        }
        if (isset($article['first_eyes_usr_id'])) {
            $admins[] = $article['first_eyes_usr_id'];
        }
        if (isset($article['second_eyes_usr_id'])) {
            $admins[] = $article['second_eyes_usr_id'];
        }
        $admins = getAdminNames($admins);
        echo '
            <div class="col-md-12">
                <div class="well">
                    <h3>Betreff bearbeiten</h3>
                    <div><form action="create.php" method="POST">
                        <input type="hidden" name="pref_id" value="'.$article['pref_id'].'" />
                        <textarea style="width:60%;" rows="1" name="content" onclick="document.getElementById(\'publish'.$article['pref_id'].'\').style.display=\'none\';">'.stripslashes($article['content']).'</textarea><br />
                        <input type="submit" class="btn" name="save" value="Speichern (ohne Versandfreigabe)" />
                        <input type="submit" class="btn" id="publish'.$article['pref_id'].'" name="publish" value="Versandfreigabe (ohne Speichern)" />
                        <p>Versandfreigabe erfolgt durch (2 Personen): '.implode(", ", $admins).'</p>
                    </form></div>
                </div>
            </div><!--/col-md--->
        ';
    }
    foreach ($articles as $article) {
        $pid = (int) $article['pref_id'];
        if ($pid > 0 && !($pid & $rights) && $rights != 1) {
            continue;
        }
        if ($pid < 0) {
            continue;
        }
        $admins = '';
        $prefs = decodePrefs($article['pref_id']);
        if (isset($article['first_eyes_usr_id'])) {
            $admins[] = $article['first_eyes_usr_id'];
        }
        if (isset($article['second_eyes_usr_id'])) {
            $admins[] = $article['second_eyes_usr_id'];
        }
        $send_btn = '';
        $admins = getAdminNames($admins);
        $area_note = '';
        $send_btn = "<p><a href='preview.php' class='btn btn-success'>Vorschau</a></p>";
        if ($article['pref_id'] == 1) {
            $area_note = "<br />Beachte dass der Text die Zeichenfolge <code>%%LO CONTENT%%</code> enthalten muss. An dieser Stelle wird der LO-spezifische Inhalt eingefügt.";
        }
        echo '
            <div class="col-md-12">
                <div class="well">
                    '.$send_btn.'
                    <h3>Text bearbeiten</h3>
                    <div>
                        <form action="create.php" method="POST">
                            <input type="hidden" name="pref_id" value="'.$article['pref_id'].'" />
                            <input type="hidden" name="id" value="'.$article['id'].'" />
                            <p>Bereich: '.$prefs[0].$area_note.'</p>
                            <textarea style="width:60%;" rows="5" name="content" onclick="document.getElementById(\'publish'.$article['pref_id'].'\').style.display=\'none\';">'.stripslashes($article['content']).'</textarea><br />
                            <input type="submit" class="btn" name="save" value="Speichern (ohne Versandfreigabe)" />
                            <input type="submit" class="btn" id="publish'.$article['pref_id'].'" name="publish" value="Versandfreigabe (ohne Speichern)" />
                            <p>Versandfreigabe erfolgt durch (2 Personen): '.implode(", ", $admins).'</p>
                        </form>
                    </div>
                </div>
            </div><!--/col-md--->
        ';
    }
    ?>
</div><!--/row-->
