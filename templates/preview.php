<div class="row">
    <?php
    if ($sendmails)
    {
        echo '
        <div class="span12">
          <div class="well">
            <h3 id="please_wait">Bitte warten...</h3>
              <p>
';
        $db = new db($dbLang, $dbName);
        if (!$testmail)
        {
            if ($rights == 1)
                $db->query("UPDATE content SET first_eyes_usr_id = NULL, second_eyes_usr_id = NULL;");
            else
                $db->query("UPDATE content SET first_eyes_usr_id = NULL, second_eyes_usr_id = NULL WHERE pref_id = $rights OR pref_id = -$rights;");
        }
        $user_count = count($users);
        $nth = 10;
        $lo_real_id = 1;
        foreach ($users as $user)
        {
            if ($rights != 1)
            {
                $user_mailtext = stripslashes($sendbo[0]['content']);
                $lo_real_id = $sendbo[0]['pref_id'];
            }
            else
            {
                $lo_mailtext = '';
                foreach ($sendlos as $sendlo)
                {
                    $lo = decodePrefs($sendlo['pref_id']);
                    $pre = "--------------- Information der LO " . $lo[0] . " ";
                    $pre .= str_repeat("-", 72 - strlen(mb_convert_encoding($pre,'ISO-8859-15','UTF-8'))) . "\n";
                    $post = "\n" . str_repeat("-", strlen(mb_convert_encoding($pre,'ISO-8859-15','UTF-8')) - 1) . "\n";
                    if (strlen(stripslashes($article['content'])) > 10 && intval($sendlo['pref_id']) & intval($user['prefs']))
                        $lo_mailtext .= $pre . stripslashes($sendlo['content']) . $post;
                }

                $user_mailtext = str_replace('%%LO CONTENT%%',$lo_mailtext,$mailtext);
                $lo_real_id = 1;
            }

            mail_utf8($db,$user['email'], "$subject", $user_mailtext, from_header($lo_real_id), change_link($user['sid']));

            if ($testmail)
            {
                echo '<p>Versand an ' . $user['email'] . " erfolgt.</p>\n";
            }

            $i++;
            if ($i % ($user_count / $nth) == 0) {
                echo $i . " / " . $user_count . "<br />";
            }
        }
        echo '
              </p>
';
        if ($i > 0)
            echo '
            <h3>Versand wird in den nächsten 10 Minuten abgeschlossen.</h3>
';
        echo '
            <script type="text/javascript">document.getElementById("please_wait").style.display="none";</script>
          </div>
        </div><!--/span-->
';
        if (!$testmail)
        {
            if ($rights == 1)
                $db->query("UPDATE content SET first_eyes_usr_id = NULL, second_eyes_usr_id = NULL;");
            else
                $db->query("UPDATE content SET first_eyes_usr_id = NULL, second_eyes_usr_id = NULL WHERE pref_id = $rights OR pref_id = -$rights;");
        }
        $db->close();
    }?>
    <div class="span8">
        <div class="well">
            <h1>Vorschau</h1>
            <p><form action="preview.php" method="POST">
                <?php
                if ($may_send_mails)
                    echo '
	      <input type="hidden" name="sendmails" value="true" />
	      <input type="submit" class="btn btn-success" value="Newsletter aussenden" />
';
                ?>
                <a class="btn" href="create.php">Newsletter bearbeiten</a>
                <textarea style="width:180px;" rows="1" name="testmail"></textarea>
                <input type="submit" class="btn btn-success" name="test" value="Test an diese Mailadresse aussenden" />
            </form></p>
            <p>Betreff: <?php echo $subject;?></p>
            <p><?php echo "<pre>".$preview_text."</pre>";?></p>
        </div>
    </div>
    <div class="span4">
        <?php
        $article = $subject_r[0];
        $admins = "";
        $prefs = decodePrefs($article['pref_id']);
        $send = true;
        if (isset($article['first_eyes_usr_id'])) {$admins[] = $article['first_eyes_usr_id'];}
        if (isset($article['second_eyes_usr_id'])) {$admins[] = $article['second_eyes_usr_id'];} else {$send = false;}
        $admins = getAdminNames($admins);
        if ($send) {$send_color = "alert-success";} else {$send_color = "alert-danger";}
        echo '
            <div class="alert '.$send_color.'">
              <p>Betreff '.$prefs[0].'</br>
              Versandfreigabe erfolgt durch: '.implode(", ", $admins).'</p>
            </div>
';
        foreach ($articles as $article)
        {
            if ($article['pref_id'] < 0) {continue;}
            if ($rights != 1 && !(intval($article['pref_id']) & $rights))
                continue;
            $admins = "";
            $prefs = decodePrefs($article['pref_id']);
            $send = true;
            if (isset($article['first_eyes_usr_id'])) {$admins[] = $article['first_eyes_usr_id'];}
            if (isset($article['second_eyes_usr_id'])) {$admins[] = $article['second_eyes_usr_id'];} else {$send = false;}
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
    </div><!--/span-->
</div><!--/row-->
