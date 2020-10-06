<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $model app\models\vraag */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="vraag-form">

    <?php $form = ActiveForm::begin(); ?>

    <?php
        $itemList=ArrayHelper::map($formModel,'id','omschrijving');
        echo $form->field($model, 'formid')->dropDownList($itemList,['prompt'=>'Please select']);
    ?>

    <?= $form->field($model, 'volgnr')->textInput() ?>

    <?= $form->field($model, 'vraag')->textInput() ?>

    <?= $form->field($model, 'ja')->textInput()->label('Ja-punten') ?>

    <?= $form->field($model, 'soms')->textInput()->label('Soms-punten') ?>

    <?= $form->field($model, 'nee')->textInput()->label('Nee-punten') ?>

    <?= HTMLInclude('formSave') ?>

    <?php ActiveForm::end(); ?>

</div>
