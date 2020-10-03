<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\grid\GridView;

use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel app\models\GesprekSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Gesprekken';
$this->params['breadcrumbs'][] = $this->title;
?>
	
<script>
    function changeStatus(id, status, rolspelerid) {
        // console.log(val, id);
        $.ajax({
        url: "<?= Url::to(['update-status']) ?>",
            data: {id: id, 'status': status, 'rolspelerid': rolspelerid },
            cache: false
        }).done(function (html) {
            location.reload();
        });
    }
</script>

<div class="gesprek-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Gesprek', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?php $rolspelerList = ArrayHelper::map($rolspeler,'id','naam'); ?>

<hr>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'form.nr',
            'form.omschrijving',
            'student.naam',
            [
                'attribute' => 'Rolspeler',
                'filter' => ['0'=>'Wachten','1'=>'Loopt','2'=>'Klaar'],
                'filterInputOptions' => [
                    'class' => 'form-control',
                    'prompt' => 'Select'
                ],
                'format' => 'raw',
                'value' => function ($model) use ($rolspelerList) {
                    return Html::dropDownList('status', $model->rolspelerid, $rolspelerList,
                    ['onchange' => "changeStatus('$model->id', '$model->status', $(this).val())"]);
                }],
            'opmerking',
            [
            'attribute' => 'status',
            'filter' => ['0'=>'Wachten','1'=>'Loopt','2'=>'Klaar'],
            'filterInputOptions' => [
                'class' => 'form-control',
                'prompt' => 'Select'
            ],
            'format' => 'raw',
            'value' => function ($model) {
                //$test = Html::dropDownList('status', 3, $rolspelerList);
                return Html::dropDownList('status', $model->status, ['0'=>'Wachten','1'=>'Loopt','2'=>'Klaar'],
                ['onchange' => "changeStatus('$model->id', $(this).val(), '$model->rolspelerid')"]);
            }],

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>


</div>
