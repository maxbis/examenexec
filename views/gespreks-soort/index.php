<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\GesprekSoortSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Gesprekssoort';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="gesprek-soort-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <hr>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        //'filterModel' => $searchModel,
        'columns' => [
            // ['class' => 'yii\grid\SerialColumn'],
            [
              'attribute'=>'volgnummer',
              'contentOptions' => ['style' => 'width:10px;'],
            ],
            [
              'attribute'=>'naam',
              'contentOptions' => ['style' => 'width:600px;'],
              'format' => 'raw',
              'value' => function ($data) {
                return Html::a($data->naam, ['update?id='.$data->id],['title' => 'Edit',]);
              },
            ],
            [
              'attribute'=>'naam',
              'contentOptions' => ['style' => 'width:300px;'],
            ],
            [
              'class' => 'yii\grid\ActionColumn',
              'contentOptions' => ['style' => 'width:80px;'],
              'template' => '{copy} - {delete}',
              'buttons' => [
                'delete' => function($url, $model){
                    return Html::a('<span class="glyphicon glyphicon-trash"></span>', ['delete', 'id' => $model->id], [
                        'class' => '',
                        'title' => 'Delete',
                        'data' => [
                            'confirm' => 'LET OP! Alle gesprekken (gesprekaanvragen) voor dit type gepsrek zullen ook worden verwijderd, Weet je het heel zeker?',
                            'method' => 'post',
                        ],
                    ]); // end return statement
                }, // end function
                'copy' => function ($url, $model, $key) {
                    return Html::a('<span class="glyphicon glyphicon-copy">C</span>', ['copy', 'id'=>$model->id],['title'=>'Copy']);
                },
              ],
              
            ],
            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>

<br>
<p>
  <?= Html::a('Nieuw Gespreksoort', ['create'], ['class' => 'btn btn-success']) ?>
</p>
<br>
<hr>
<p>Gesprekken worden gekoppeld aan examens door in het <?= Html::a('examenoverzicht', ['/examen/index']) ?> op de
examennaam te klikken.</p>
<p>Gesprekken kunnen worden gekopieerd. Klik op het copy-icoontje voor de delete.</p>
<p>Let op: bij het verwijderen van gesprekken worden alle <?= Html::a('gesprekken', ['/gesprek/overzicht']) ?>
 van dit gesprekstype ook verwijderd!</p>
