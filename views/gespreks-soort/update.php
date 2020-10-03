<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\GespreksSoort */

$this->title = 'Update Gespreks Soort: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Gespreks Soorts', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="gespreks-soort-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'examenModel' => $examenModel,
    ]) ?>

</div>
