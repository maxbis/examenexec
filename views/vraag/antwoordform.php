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

<?php
    if ($student) {
        $studentNaam = $student->naam;
        $studentId = $student->id;
        $dummy = false;
    } else {
        $studentNaam = "DEBUG!";
        $studentId = "";
        $dummy = true;
    }
?>

<div class="Beoordelingsformulier">
    <h1>
    Beoordelingsformulier
    <?= $studentNaam ?>
    </h1>
    <i>Gesprek: <?= $form->nr ?> - <?= $form->omschrijving ?></i>
    <br><br>
    <div style="width: 800px;">
        <?= $form->instructie ?>
    </div>
    <br><br>

    <table class="table" style="width: 100rem;">

    <tr>
        <th scope="col" style="width: 3rem;">Nr.</th>
        <th scope="col" style="width: 35rem;">Vraag</th>
        <th scope="col" style="width: 5rem;">Ja</th>
        <th scope="col" style="width: 5rem;">Soms/Beetje</th>
        <th scope="col" style="width: 5rem;">Nee</th>
    </tr>
        
        <?php $i=0; ?>
        <?php foreach ($vragen as $item): ?>
            <tr>
                <td><?= $item->volgnr ?></td>
                <td><?= $item->vraag ?></td>
                
                <td>
                    <?php 
                        if ( $resultaat['answers'][$i] == 1){
                            echo "&#128505; (".$resultaat['points'][$i].")";
                        } else {
                            echo "&#128454;";
                        }
                    ?>
                </td>

                <?php if ( isset($item->soms) ) : ?>
                    <td>
                        <?php
                            if ( $resultaat['answers'][$i] == 2){
                                echo "&#128505; (".$resultaat['points'][$i].")";
                                } else {
                                    echo "&#128454;";
                                }
                        ?>
                    </td>
                <?php else: ?>
                    <td>nvt</td>
                <?php endif; ?>

                <td>
                    <?php 
                        if ( $resultaat['answers'][$i] == 3){
                            echo "&#128505; (".$resultaat['points'][$i].")";
                        } else {
                            echo "&#128454;";
                        }
                    ?>
                </td>
            </tr>
            <?php $i++; ?>
        <?php endforeach; ?>
        
        <tr>
        <td></td>
        <td><br>Totaal aantal punten</td>
        <td colspan=3><br><?= $resultaat['totaalscore'] ?></td>
        </tr>

    </table>

    <br>

    <div class="card" style="width: 40rem;">
        <div class="card-body">
            <h5 class="card-title">Opmerking</h5>
            <?= $beoordeling->opmerking ?>
        </div>
    </div>

    <br>
    Gesprek op
    <?php
    $date = new DateTime($beoordeling->timestamp);
    echo $date->format('d-m-y - H:i').' uur, door '.$rolspeler->naam;
    ?>
 

</div>