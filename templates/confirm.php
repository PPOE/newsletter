<div class="row">
    <div class="span8">
        <div id="confirm_view" class="well">
            <h1>Du bist erfolgreich angemeldet!</h1>
            <p>Ab sofort erhältst du regelmäßig unseren personalisierten Newsletter. Viel Spa&szlig;!</p>
            <p>Mit einem Klick auf den folgenden Link kannst du die Inhaltseinstellungen deines Newsletters verändern oder den Newsletter abbestellen:<br><a href="<?php echo change_link($sid);?>"><?php echo change_link($sid);?></a>
            <p><a href="http://www.piratenpartei.at">Zurück zu piratenpartei.at</a></p>
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
