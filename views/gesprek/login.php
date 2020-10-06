<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Gesprek */
/* @var $form yii\widgets\ActiveForm */

$this->title = 'Log in';
?>

<div class="gesprek-form">

    <div class="col-sm-4">

        <h1><?= Html::encode($this->title) ?></h1>
        <hr>

        <form action="/gesprek/student">
            <label for="exampleFormControlSelect1">Studentennummer:</label>
            <input class="form-control form-control-lg" type="text" id="nummer" name="nummer" placeholder="">
            <div class="form-group">
            <br>
            <?= Html::submitButton('Login', ['class' => 'btn btn-success']) ?>
            </div>
        </form>

    </div>
</div>