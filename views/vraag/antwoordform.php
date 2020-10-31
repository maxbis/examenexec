<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\LinkPager;
?>

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

    <table class="table">

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
        <td></td>
        <td></td>
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
    echo $date->format('d-m-y - H:i').' uur, door '.$rolspeler->naam."<br><hr>";

    if ( isset($_COOKIE['rolspeler']) ) {
        $action = Url::toRoute(['/gesprek/rolspeler']);
        echo Html::a('Cancel', [$action, 'id'=>$rolspeler->id], ['class'=>'btn btn-primary']);
    }
    
    
    ?>
</div>