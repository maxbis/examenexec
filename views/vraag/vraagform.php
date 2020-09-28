<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\LinkPager;
?>
<script>
function result(){
   // Do your stuff here
   //return true; // submit the form

   var val;
    // get list of radio buttons with specified name
    var radios = document.getElementById("myForm");
    var totaalString = "";
    var statusString = "";

    // loop through list of radio buttons
    for (var i=0; i < radios.length; i++) {
        if ( radios[i].checked ) { // radio checked?
            totaalString += radios[i].value + "-";
            statusString += radios[i].id + "-";
        }
    }
    totaalString = totaalString.slice(0,-1);
    statusString = statusString.slice(0,-1);
    alert(statusString);
    document.getElementById("totaalString").value = totaalString;
    document.getElementById("statusString").value = statusString;
    return true; // don't submit the form
}
</script>

<div class="Beoordelingsformulier">
    <h1>Beoordelingsformulier</h1>

    <form action="formpost" onsubmit="return result()" method="get" id="myForm">

        <input type="hidden" id="totaalString" name="totaalString" value="-">
        <input type="hidden" id="statusString" name="statusString" value="-">
        <input type="hidden" id="formId" name="formId" value="<?= $vragen[0]->formid ?>">

        <table class="table" style="width: 100rem;">

        <tr>
            <th scope="col" style="width: 3rem;">Nr.</th>
            <th scope="col" style="width: 35rem;">Vraag</th>
            <th scope="col" style="width: 5rem;">Ja</th>
            <th scope="col" style="width: 5rem;">Soms/Beetje</th>
            <th scope="col" style="width: 5rem;">Nee</th>
        </tr>
            
            <?php foreach ($vragen as $item): ?>
                <tr>
                    <td><?= $item->volgnr ?></td>
                    <td><?= $item->vraag ?></td>
                    
                    <td><input type="radio" id="1" name="<?= $item->volgnr ?>" value="<?= $item->ja ?>" required></td>
                    <?php if ( isset($item->soms) ) : ?>
                        <td><input type="radio" id="2" name="<?= $item->volgnr ?>" value="<?= $item->soms ?>"></td>
                    <?php else: ?>
                        <td>nvt</td>
                    <?php endif; ?>
                    <td><input type="radio" id="3" name="<?= $item->volgnr ?>" value="<?= $item->nee ?>"></td>
                </tr>
            <?php endforeach; ?>


        </table>
        <?= HTMLInclude('formSave') ?>
    </form>

</div>