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
                    return Html::a($formList[$model->formid],['/form/form','id'=>$model->formid],['title'=> 'Show Form',]);
                }
            ],
            [   'attribute' => 'volgnr',
                'label' => '#',
                'contentOptions' => ['style' => 'width:20px;'],
                'value' => function ($model) use ($formList) {
                    return $model->form->nr.'-'.$model->volgnr;
                }
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
            [   'attribute' => 'mappingid',
                'label' => 'mapping',
                'contentOptions' => ['style' => 'width:80px;'],
            ],
            [   'class' => 'yii\grid\ActionColumn',
                'contentOptions' => ['style' => 'width:80px;'],
            ],
        ],
    ]); ?>


</div>

<p>
    <?= Html::a('Create Vraag', ['create'], ['class' => 'btn btn-success']) ?>
</p>
