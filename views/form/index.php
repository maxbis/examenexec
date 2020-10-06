<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\FormSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Formulieren';

?>
<div class="form-index">

    <h1><?= Html::encode($this->title) ?></h1>


    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            [
                'attribute'=>'nr',
                'contentOptions' => ['style' => 'width:40px; white-space: normal;'],
            ],
            [
                'attribute'=>'actief',
                'contentOptions' => ['style' => 'width:40px; white-space: normal;'],
                'format' => 'raw',
                'filter' => [''=> 'alles', '0'=>'Inactief','1'=>'Actief'],
                'value' => function ($data) {
                  $status = $data->actief ? '<span class="glyphicon glyphicon-ok"></span>' : '<span class="glyphicon glyphicon-minus"></span>';
                  return Html::a($status, ['toggle-actief?id='.$data->id], ['title' => 'Actief <-> Inactief',]);
                }
            ],
            [
                'attribute'=>'omschrijving',
                'format' => 'raw',
                'value' => function ($data) {
                  return Html::a($data->omschrijving, ['/vraag/form?formid='.$data->id],['title' => 'Edit',]);
                },  
            ],
            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>


</div>

<p>
    <?= Html::a('New Form', ['create'], ['class' => 'btn btn-success']) ?>
</p>