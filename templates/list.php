<div class="row">
    <div class="col-md-12">
        <div id="list_view" class="well">
            <h1>Teilnehmer-Liste</h1>
            <table class="table">
                <tr>
                    <td>Adresse</td>
                    <td>Präferenzen</td>
                    <td>Bestätigt</td>
                    <td>Optionen</td>
                </tr>
                <?php
                foreach ($users as $user)
                {
                ?>
                <tr>
                    <td>
                        <a href="mailto:<?php echo $user['email']; ?>"><?php echo $user['email']; ?></a>
                    </td>
                    <td><?php echo implode(', ', decodePrefs($user['prefs'])); ?></td>
                    <td><?php echo ($user['confirmed'] !== 'f' && $user['confirmed'] != 0 ? 'Ja' : 'Nein'); ?></td>
                    <td>
                        <form action="list.php" method="POST">
                            <input type="hidden" name="email" value="<?php echo $user['email']; ?>" />
                            <input type="submit" name="delete" value="Abmelden" class="btn btn-danger" />
                        </form>
                    </td>
                </tr>
                <?php
                }
                ?>
            </table>
        </div>
    </div>
</div>
