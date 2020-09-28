<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\VraagSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Vragen';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="vraag-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Vraag', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>


    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            [   'attribute' => 'form.omschrijving',
                'label' => 'form',
            ],
            [   'attribute' => 'volgnr',
                'contentOptions' => ['style' => 'width:40px;'],
            ],
            [
                'attribute' => 'vraag',
                'format' => 'raw',
                'value' => function ($data) {
                    return Html::a($data->vraag, ['update?id='.$data->id],['title' => 'Edit',]);
                },
            ],
            [   'attribute' => 'ja',
            'contentOptions' => ['style' => 'width:80px;'],
            ],
            [   'attribute' => 'soms',
                'contentOptions' => ['style' => 'width:80px;'],
            ],
            [   'attribute' => 'nee',
            'contentOptions' => ['style' => 'width:80px;'],
            ],

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>


</div>
