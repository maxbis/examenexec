<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\LinkPager;
?>

<div class="Beoordelingsformulier">
    <h1>
    Beoordelingsformulier
    </h1>
    <i>Gesprek: <?= $form->nr ?> - <?= $form->omschrijving ?></i>
    <br><br>
    <div style="width: 800px;">
        <?= $form->instructie ?>
    </div>
    <br><br>

    <form action="/beoordeling/formpost" onsubmit="return result()" method="get" id="myForm">

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

        <b>Opmerkingen</b><br>

        <textarea rows="4" cols="100" name="opmerking" form="myForm"></textarea>

        <br>
        
        <div class="form-group">
        <br>
        <?php
            echo Html::a('Cancel', ['/form'], ['class'=>'btn btn-primary']);
        ?>
        </div>
    </form>

</div>