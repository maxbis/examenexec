<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\LinkPager;

$status = ['wachten', 'loopt', 'klaar']
?>

<meta http-equiv="refresh" content="60">
<script> document.write(new Date().toLocaleTimeString('en-GB')); </script>


<div class="gesprek-overzicht">
    <h1>Gespreksoverzicht
    <?= $rolspeler->naam ?>
    </h1>

    <table class="table" style="width: 100rem;">

    <tr>
      <th scope="col" style="width: 15rem;">Student;</th>
      <th scope="col" style="width: 15rem;">Gesprek</th>
      <th scope="col" style="width: 15rem;">Opmerking</th>
      <th scope="col" style="width: 20rem;">Status</th>
      <th scope="col" style="width: 20rem;">Time</th>
      <th scope="col" style="width: 10rem;">&nbsp;</th>
    </tr>
   
    <?php foreach ($gesprekken as $item): ?>
        <tr>
        <td><?= $item->student->naam ?> </td>
        <td><?= $item->form->omschrijving ?> </td>
        <td><?= $item->opmerking ?> </td>
        <td><?= $status[$item->status] ?> </td>
        <td><?= Yii::$app->formatter->asTime($item->created) ?> </td>
        <td><?php if ($item->status == 0): ?>
                <?= Html::a('Start Gesprek', ['/vraag/form', 'gesprekid' => $item->id, 'formid' => $item->form->id, 
                                            'studentid' => $item->student->id, 'rolspelerid' => $rolspeler->id]) ?>
            <?php else: ?>
                &nbsp;
            <?php endif; ?> </td>
        </tr>
    <?php endforeach; ?>

    </table>