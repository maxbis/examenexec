<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\LinkPager;

$status = ['wachten', 'loopt', 'klaar']
?>

<meta http-equiv="refresh" content="60">
<script> document.write(new Date().toLocaleTimeString('en-GB')); </script>

<?php if(count($gesprekken)==0): ?>
  <br>
  <h1>Je hebt nog geen gesprekken aangevraagd</h1>
  <br>
  <?= Html::a('Nieuw Gesprek', ['/gesprek/login', 'nummer' => $_GET['nummer']], [ 'class'=>'btn btn-primary']) ?>
<?php else: ?>

  <div class="gesprek-overzicht">
    <h1>Gespreksoverzicht
    <?= $gesprekken[0]->student->naam ?>
    </h1>

    <table class="table" style="width: 100rem;">

      <tr>
        <th scope="col" style="width: 15rem;">Tijd</th>
        <th scope="col" style="width: 15rem;">Gesprek</th>
        <th scope="col" style="width: 20rem;">Status</th>
        <th scope="col" style="width: 15rem;">Opmerking</th>
      </tr>
    
      <?php foreach ($gesprekken as $item): ?>
          <tr>
          <td><?= Yii::$app->formatter->asTime($item->created) ?> </td>
          <td><?= $item->form->omschrijving ?> </td>
          <td><?= $status[$item->status] ?> </td>
          <td><?= $item->opmerking ?> </td>
          </tr>
      <?php endforeach; ?>

    </table>
  </div>
<?php endif; ?>