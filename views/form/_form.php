<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $model app\models\Form */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="form-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'nr')->textInput() ?> 
    
    <?php
        $itemList=ArrayHelper::map($examenModel,'id','naam');
        echo $form->field($model, 'examenid')->dropDownList($itemList,['prompt'=>'Please select'])->label('examen');
    ?>

    <?= $form->field($model, 'omschrijving')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'instructie')->textInput(['maxlength' => true])->textArea( ['style'=>'width:800px'] ) ?>


    <?= HTMLInclude('formSave') ?>

    <?php ActiveForm::end(); ?>

</div>
