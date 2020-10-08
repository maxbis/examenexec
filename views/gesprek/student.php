<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\LinkPager;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;

$status = ['wachten', 'loopt', 'klaar']
?>

<meta http-equiv="refresh" content="60">
<div class="text-right">
  <script> document.write(new Date().toLocaleTimeString('en-GB')); </script>
</div>


<h1>Gespreksoverzicht
    <?= $student->naam ?>
</h1>

<?php if(count($gesprekken)==0): ?>
  <br>
  Je hebt nog geen gesprekken aangevraagd
  <br>
<?php else: ?>

  <div class="gesprek-overzicht">

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

<hr>
<br>

<h1>Nieuw Gesprek aanvragen
</h1>

<?php
  $studentId = $student->id;
?>

<div class="gesprek-form">

    <?php $form = ActiveForm::begin(['action' => 'create',]);?>

    <?= $form->field($newGesprek, 'studentid')->hiddenInput(['value' => $student->id])->label(false) ?>
    <?= $form->field($newGesprek, 'studentmummer')->hiddenInput(['value' => $student->nummer])->label(false) ?>

    <?php
        $itemList=ArrayHelper::map($formModel,'id','omschrijving');
        echo $form->field($newGesprek, 'formid')->dropDownList($itemList,[ 'style'=>'width:400px', 'prompt'=>'Please select'])->label('Kies Gesprek');
    ?>

    <?= $form->field($newGesprek, 'opmerking')->textArea( ['style'=>'width:400px'] ) ?>

    <div class="form-group">
      <?= Html::a( 'Cancel', Yii::$app->request->referrer , ['class'=>'btn btn-primary']); ?>
      &nbsp;&nbsp;&nbsp;
      <?= Html::submitButton('New', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>
 
</div>