<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;

use yii\helpers\Url;

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

    <?= $form->field($model, 'volgnr')->textInput(['style'=>'width:200px']) ?>

    <?= $form->field($model, 'vraag')->textInput() ?>

    <?= $form->field($model, 'toelichting')->textInput(['maxlength' => true])->textArea( ['style'=>'width:800px'] ) ?>

    <div class="row">
        <div class="col-sm-5">
            <?= $form->field($model, 'mappingid')->dropDownList(ArrayHelper::map($criterium,'id','omschrijving'), ['prompt'=>'...', 'style'=>'width:300px'])->label('Vraag hoort bij SPL Rubic'); ?>
        </div>
    </div>

    <br>
    <div class="row">
        <div class="col-sm-2"> 
            <?= $form->field($model, 'ja')->textInput(['style'=>'width:120px'])->label('Ja-punten') ?>
        </div>
        <div class="col-sm-2"> 
            <?= $form->field($model, 'soms')->textInput(['style'=>'width:120px'])->label('Soms-punten') ?>
        </div>
        <div class="col-sm-2"> 
            <?= $form->field($model, 'nee')->textInput(['style'=>'width:120px'])->label('Nee-punten') ?>
        </div>
    </div>
    <?= HTMLInclude('formSave') ?>

    <?php ActiveForm::end(); ?>
 
</div>

