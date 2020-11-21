<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\ExamenSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Examens';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="examen-index">

  <h1><?= Html::encode($this->title) ?></h1>
  <?php $examenNaam = $examen->naam ?? "-"; ?>
  Open voor gespreksaanvragen: <span style="text-decoration:underline;" class="" > <?= $examenNaam ?> </span>
  <hr>

  <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

  <?= GridView::widget([
      'dataProvider' => $dataProvider,
      //'filterModel' => $searchModel,
      'columns' => [
          // ['class' => 'yii\grid\SerialColumn'],
          [
            'attribute'=>'id',
            'contentOptions' => ['style' => 'width:40px; white-space: normal;'],
          ],
          [
            'attribute'=>'actief',
            'contentOptions' => ['style' => 'width:20px;'],
            'format' => 'raw',
            'value' => function ($data) {
              $status = $data->actief ? '<span class="glyphicon glyphicon-ok"></span>' : '<span class="glyphicon glyphicon-minus"></span>';
              return Html::a($status, ['/examen/toggle-actief?id='.$data->id],['title'=> 'Toggle Status',]);
            }
          ],
          [
            'attribute'=>'datum_start',
            'contentOptions' => ['style' => 'width:40px; white-space: normal;'],
          ],
          [
            'attribute'=>'datum_eind',
            'contentOptions' => ['style' => 'width:40px; white-space: normal;'],
          ],

          [
            'attribute'=>'naam',
            'contentOptions' => ['style' => 'width:600px; white-space: normal;'],
            'format' => 'raw',
            'value' => function ($data) {
              return Html::a($data->naam, ['/examen/update?id='.$data->id],['title'=> 'Edit',]);
            },
          ],
          [
            'attribute'=>'otherid',
            'contentOptions' => ['style' => 'width:100px; white-space: normal;'],
          ],
          [
            'attribute'=>'',
            'contentOptions' => ['style' => 'width:20px; white-space: normal;'],
            'format' => 'raw',
            'value' => function ($data) {
              return Html::a('<span class="glyphicon glyphicon-play"></span>',
              ['/gesprek/index?GesprekSearch[statusNaam]=&GesprekSearch[student_naam]=&GesprekSearch[lokaal]=
              &GesprekSearch[examen_id]='.$data->id],['title'=> 'Naar Gesprekken',]);
            },
          ],
          [
            'class' => 'yii\grid\ActionColumn',
            'contentOptions' => ['style' => 'width:60px;'],
            'template' => '{view} {update}', 
            'visibleButtons'=>[
              'delete'=> function($model){
                    return $model->actief!=1;
               },
              ],
            'buttons' => [
              'delete' => function($url, $model){
                  return Html::a('<span class="glyphicon glyphicon-trash"></span>', ['delete', 'id' => $model->id], [
                      'class' => '',
                      'title'=> 'Delete',
                      'data' => [
                          'confirm' => 'Weet je het zeker?',
                          'method' => 'post',
                      ],
                  ]);
              },
            ],
            
          ],
      ],
  ]); ?>


</div>
<br>
<p>

  <?= Html::a('Nieuw Examen', ['create'], ['class' => 'btn btn-success']) ?>
  &nbsp;
  <?= Html::a('<span>Planner</span>', ['/gesprek'], ['class' => 'btn btn-secondary', 'title' => 'Naar examenplanner']) ?>
  &nbsp;
  <?= Html::a('<span>Forms</span>', ['/form'], ['class' => 'btn btn-secondary', 'title' => 'Naar examenplanner']) ?>
</p>
<br>
<hr>
<p>
Examenesprekken kunnen worden aangemaakt vanuit het <?= Html::a('gesprekkenoverzicht', ['/gespreks-soort/index']) ?>.
</p>
