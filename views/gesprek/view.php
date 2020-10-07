<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Gesprek */

$this->title = "Gesprek";
$this->params['breadcrumbs'][] = ['label' => 'Gespreks', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="gesprek-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'formid',
            'rolspelerid',
            'studentid',
            'opmerking',
        ],
    ]) ?>

</div>

<br>

<?php
    echo Html::a('Delete', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
    ]);

    echo " &nbsp;&nbsp;&nbsp;";

    echo Html::a('Cancel', ['/gesprek'], ['class'=>'btn btn-primary']);
    

?>