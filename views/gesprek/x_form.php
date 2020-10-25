<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $model app\models\Gesprek */
/* @var $form yii\widgets\ActiveForm */
?>

<?php
    if( isset($student->naam) ) { // new gesprek, student naam is passed via object $student
        $studentNaam = $student->naam;
        $studentId = $student->id;
    } else { // modify existing student
        $studentNaam = $model->student->naam;
        $studentId = $model->student->id;
    }
?>

<h2>
<?= $studentNaam ?>
</h2>

<div class="gesprek-form">


    <?php $form = ActiveForm::begin();
                    
    ?>

    <?= $form->field($model, 'studentid')->textInput(['readonly' => true, 'value' => $studentId, 'style'=>'width:100px' ]) ?>

    <?php
        $itemList=ArrayHelper::map($formModel,'id','omschrijving');
        echo $form->field($model, 'formid')->dropDownList($itemList,['prompt'=>'Please select'])->label('Kies Gesprek');
    ?>

    <?= $form->field($model, 'opmerking')->textArea() ?>


    <div class="form-group">
      <?= Html::a('Cancel', ['/gesprek/student', 'id' => $studentId, 'nummer' => $student->nummer], [ 'class'=>'btn btn-primary']) ?>
      &nbsp;&nbsp;&nbsp;
      <?= Html::submitButton('New', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>
    <hr>
    Je kunt hier een nieuw gesprek aanvragen, kies het juiste gesprek en druk op de knop new.<br>
    Als je op de knop cancel drukt dan krijg je een <a href="/gesprek/student/?id=<?= $studentId ?>">overzicht</a> van al jouw gesprekken.<br>

</div>
