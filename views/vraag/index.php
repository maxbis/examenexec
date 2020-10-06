<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $searchModel app\models\VraagSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Vragen';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="vraag-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php // echo $this->render('_search', ['model' => $searchModel]);
        $formList =  ArrayHelper::map($formModel,'id','omschrijving');
    //d($formList);
    ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            [
                'attribute' => 'formid',
                'filter' => $formList,
                'label' => 'Formulier',
                'filterInputOptions' => [
                    'class' => 'form-control',
                    'prompt' => 'Select'
                    ],
                'format' => 'raw',
                'value' => function ($model) use ($formList) {
                    return $formList[$model->formid];
                }
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

<p>
    <?= Html::a('Create Vraag', ['create'], ['class' => 'btn btn-success']) ?>
</p>
