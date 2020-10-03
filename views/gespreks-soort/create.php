<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\GespreksSoort */

$this->title = 'Create Gesprekssoort';
$this->params['breadcrumbs'][] = ['label' => 'Gespreks Soorts', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="gespreks-soort-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'examenModel' => $examenModel,
    ]) ?>

</div>
