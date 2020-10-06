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

    <script> document.write(new Date().toLocaleTimeString('en-GB')); </script>


    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?php $rolspelerList = ArrayHelper::map($rolspeler,'id','naam');
        $statusIcon = ['&#9749;', '&#128490;', '&#128505;'];
        $rolspelerList[0]="Select...";
    ?>

<hr
<!-- &#9749;&#128490;&#128505; -->

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'form.nr',
            
            'form.omschrijving',

            'student.naam',

            [
                'attribute' => 'rolspelerid',
                'filter' => $rolspelerList,
                'filterInputOptions' => [
                    'class' => 'form-control',
                    'prompt' => 'Select'
                    ],
                'format' => 'raw',
                'value' => function ($model) use ($rolspelerList) {
                    if ($model->status==0) {
                        return Html::dropDownList('status', $model->rolspelerid, $rolspelerList,
                        ['onchange' => "changeStatus('$model->id', '$model->status', $(this).val())"]);
                    } else {
                        return $model->rolspeler->naam;
                    }
                }
            ],

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
                    ['onchange' => "changeStatus('$model->id', $(this).val(), '$model->rolspelerid')"])."&nbsp;&nbsp;&nbsp;(".$model->status.")";
                     }
            ],

            ['class' => 'yii\grid\ActionColumn', 'template' => '{delete}',],
            ],
        ]);
    ?>


</div>
