<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $model app\models\GespreksSoort */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="gespreks-soort-form">

    <?php $form = ActiveForm::begin(); ?>

    <?php
        $itemList=ArrayHelper::map($examenModel,'id','naam');
        echo $form->field($model, 'examenid')->dropDownList($itemList,['prompt'=>'Please select']);
    ?>

    <?= $form->field($model, 'volgnummer')->textInput() ?>

    <?= $form->field($model, 'naam')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
