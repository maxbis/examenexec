<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\LinkPager;
?>
<script>
    function result(){
    var val;
        // get list of radio buttons with specified name
        var radios = document.getElementById("myForm");
        var totaalString = "";
        var statusString = "";

        // loop through list of radio buttons
        for (var i=0; i < radios.length; i++) {
            if ( radios[i].checked ) { // radio checked?
                totaalString += radios[i].value + "|";
                statusString += radios[i].id.split("-",1) + "|"; // value of radiobutton is answer-vraagnr
            }
        }
        totaalString = totaalString.slice(0,-1);
        statusString = statusString.slice(0,-1);
        //alert(statusString);
        document.getElementById("totaalString").value = totaalString;
        document.getElementById("statusString").value = statusString;
        return true; // don't submit the form
    }
</script>

<script>
    function CopyToClipboard(containerid) {
        if (document.selection) {
            var range = document.body.createTextRange();
            range.moveToElementText(document.getElementById(containerid));
            range.select().createTextRange();
            document.execCommand("copy");
        } else if (window.getSelection) {
            var range = document.createRange();
            range.selectNode(document.getElementById(containerid));
            window.getSelection().addRange(range);
            document.execCommand("copy");
        }
    }
</script>

<?php
    if ($student) {
        $studentNaam = $student->naam;
        $studentId = $student->id;
        $studentNr = $student->nummer;
        $dummy = false;
    } else {
        $studentNaam = "DEBUG!";
        $studentId = "";
        $dummy = true;
    }
    $action = Url::toRoute(['beoordeling/formpost']);
?>

<!-- hightlighting for niet ingevulde radios werkt niet
<style>
    input[type=radio] {
        display: none;
    }
    input[type=radio] + label::before {
        content: '';
        display: inline-block;
        border: 1px solid #000;
        border-color: gray;
        border-radius: 50%;
        vertical-align: middle;
        margin: 0 0.5em;
        width: 1.2em;
        height: 1.20em;
    }
    input[type=radio]:checked + label::before {
        background-color: #ff6060;
    }
</style>
-->

<!-- this is the real form that needs to be filled in -->

<div class="Beoordelingsformulier">
    <h1>
    Beoordelingsformulier
    <a href="sip:<?=$studentNr?>@talnet.nl" title="Open in MS Teams"><?= $studentNaam ?></a>
    </h1>
    <i>Gesprek: <?= $form->nr ?> - <?= $form->omschrijving ?></i>
    <br><br>
    <div style="width: 800px;">
        <?= $form->instructie ?>
    </div>
    <br><br>

    <form action=<?= $action ?> onsubmit="return result()" method="get" id="myForm">

        <input type="hidden" id="totaalString" name="totaalString" value="-">
        <input type="hidden" id="statusString" name="statusString" value="-">
        <input type="hidden" id="studentid" name="studentid" value=<?= $studentId ?> >
        <input type="hidden" id="studentnr" name="studentnr" value=<?= $studentNr ?> >
        <input type="hidden" id="rolspelerid" name="rolspelerid" value=<?= $rolspeler->id ?> >
        <input type="hidden" id="gesprekid" name="gesprekid" value="<?= $gesprek->id ?>">
        <input type="hidden" id="formId" name="formId" value="<?= $form->id ?>">

        <div class="custom-control custom-radio">
        <table class="table">

        <tr>
            <th scope="col" style="width: 1px;"></th>
            <th scope="col" style="width: 1px;" colspan=2>Vragen</th>
            <th scope="col" style="width: 80px;">Ja</th>
            <th scope="col" style="width: 80px;">Soms/Beetje</th>
            <th scope="col" style="width: 80px;">Nee</th>
        </tr>
            
            <?php foreach ($vragen as $item): ?>
                <tr>
                    <td><?= $item->volgnr ?></td>
                    <td colspan=2><?= $item->vraag ?></td>
                    
                    <td><input type="radio" id="1-<?=$item->volgnr?>" name="<?= $item->volgnr ?>" value="<?= $item->ja ?>" required><label for="1-<?=$item->volgnr?>"></label></td>
                        <?php if ( isset($item->soms) ) : ?>
                            <td><input type="radio" id="2-<?=$item->volgnr?>" name="<?= $item->volgnr ?>" value="<?= $item->soms ?>"><label for="2-<?=$item->volgnr?>"></label></td>
                        <?php else: ?>
                            <td>nvt</td>
                        <?php endif; ?>
                    <td><input type="radio" id="3-<?=$item->volgnr?>" name="<?= $item->volgnr ?>" value="<?= $item->nee ?>"><label for="3-<?=$item->volgnr?>"></label></td>
                </tr>
                <?php if ( $item->toelichting != "" ): ?>
                    <tr>
                        <td>&nbsp;</td><td>&nbsp;</td>
                        <td><?= $item->toelichting ?></td>
                        <td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>
                    <tr>
                <?php endif; ?>
            <?php endforeach; ?>
            

        </table>
        <div class="custom-control custom-radio">

        <b>Opmerkingen</b><br>

        <textarea rows="4" cols="100" name="opmerking" form="myForm"></textarea>

        <br>
        
        <div class="form-group">
        <br>
        <?php
            if ($dummy) {
                echo Html::a('Cancel', ['form'], ['class'=>'btn btn-primary']);
            } else {    
                echo Html::a('Cancel', ['gesprek/rolspeler' , 'id'=>$rolspeler->id, 'gesprekid'=>$gesprek->id], ['class'=>'btn btn-primary']);
                echo " &nbsp;&nbsp;&nbsp;";
                echo Html::submitButton('&nbsp;Save&nbsp;', ['class' => 'btn btn-success']);
               
            }
        ?>
        </div>
    </form>

    <?php $bestandsNaam = 'F'.$form->nr.'_'.str_replace(' ', '_', trim($studentNaam)) ?>

    <br />

    <svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-clipboard" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
        <path fill-rule="evenodd" d="M4 1.5H3a2 2 0 0 0-2 2V14a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V3.5a2 2 0 0 0-2-2h-1v1h1a1 1 0 0 1 1 1V14a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1V3.5a1 1 0 0 1 1-1h1v-1z"/>
        <path fill-rule="evenodd" d="M9.5 1h-3a.5.5 0 0 0-.5.5v1a.5.5 0 0 0 .5.5h3a.5.5 0 0 0 .5-.5v-1a.5.5 0 0 0-.5-.5zm-3-1A1.5 1.5 0 0 0 5 1.5v1A1.5 1.5 0 0 0 6.5 4h3A1.5 1.5 0 0 0 11 2.5v-1A1.5 1.5 0 0 0 9.5 0h-3z"/>
    </svg>

    <a href="#" id="div1" title="Copy" onclick="CopyToClipboard('div1')"><small><?=$bestandsNaam?></small></a>
    <small> (bestandsnaam voor audio-opname van externe rolspelers)</small>
    

</div>