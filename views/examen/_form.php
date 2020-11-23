<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;

use nex\datepicker\DatePicker;

/* @var $this yii\web\View */
/* @var $model app\models\Examen */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="examen-form">

    <?php $form = ActiveForm::begin(); ?>

      <div class="row">

        <div class="col-sm-4">
          <?= $form->field($model, 'naam')->textInput(['maxlength' => true]) ?>
        </div>
      </div>

      <div class="row">
        <div class="col-sm-2">
          <?= $form->field($model, 'datum_start')->widget(
              DatePicker::className(), [
                'clientOptions' => [
                  'format' => 'Y-MM-D',
                  'stepping' => 30,
                  'minDate' => '2020-01-01',
                  'maxDate' => '2025-12-31',
                ],
              ]);
          ?>
        </div>

        <div class="col-sm-2">
          <?= $form->field($model, 'datum_eind')->widget(
              DatePicker::className(), [
                'clientOptions' => [
                  'format' => 'Y-MM-D',
                  'stepping' => 30,
                  'minDate' => '2020-01-01',
                  'maxDate' => '2025-12-31',
                ],
              ]);
          ?>
        </div>
      </div>

      <div class="row">
        <div class="col-sm-4">
          <?= $form->field($model, 'otherid')->textInput(['maxlength' => true])->label('Examenid for export to KTB') ?>
          </div>
      </div>
    
    <?= $form->field($model, 'actief')->hiddenInput()->label(false); ?>

    <div class="form-group">
      <?= Html::a('Cancel', [Url::toRoute(['examen/index'])], ['class'=>'btn btn-primary']) ?>
      &nbsp;&nbsp;&nbsp;
      <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

  </div>
  <?php ActiveForm::end(); ?>

</div>

<br>
Examenesprekken kunnen worden aangemaakt vanuit het <?= Html::a('gesprekkenoverzicht', ['/gespreks-soort/index']) ?>.