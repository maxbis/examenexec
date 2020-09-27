<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Examen */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="examen-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'naam')->textInput(['maxlength' => true]) ?>

    <?php $model->actief=0 ?>
    <?= $form->field($model, 'actief')->checkbox() ?>

    <?= $form->field($model, 'datum_start')->textInput() ?>

    <?= $form->field($model, 'datum_eind')->textInput() ?>

    <?= HTMLInclude('formSave') ?>

    <?php ActiveForm::end(); ?>

</div>
