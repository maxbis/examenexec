<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Gesprek */

?>
<div class="gesprek-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'student' => $student,
        'formModel' => $formModel,
    ]) ?>

</div>
