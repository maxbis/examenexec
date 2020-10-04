<?php

use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Rolspeler */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="rolspeler-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'naam')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'token')->textInput(['maxlength' => true]) ?>


    <?php $model->actief=1 ?>
    <?= $form->field($model, 'actief')->checkbox() ?>

    <?= HTMLInclude('formSave') ?>

    <?php ActiveForm::end(); ?>

</div>
