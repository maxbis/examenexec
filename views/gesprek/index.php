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

$counts = array_count_values(array_column($alleGesprekken, 'status'));
$queue = $counts[0]+$counts[1];
$barlen1 = max(1,$counts[0]*2);
$barlen2 = max(1,$counts[1]*2);

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

    <div class="row">

        <div class="col-8">
            <h1>Gespreksoverzicht</h1>
        </div>

        <div class="col bg-light">
            <font size="2" >
                <table border=0 width="100%" class="table-sm">
                    <tr>

                    <td>&nbsp;</td>
                    <td>Drukte</td>
                    <td><script> document.write(new Date().toLocaleTimeString('en-GB')); </script></td>
                    </tr>

                    <tr>
                    <td style="width: 100px;">
                        Wachtende:
                    </td>

                    <td style="width: 600px;">
                        <div class="progress-bar bg-info" style="width:<?= $barlen1 ?>%">
                        <font size="1" ><?= $counts[0] ?></font>
                        </div>
                    </td>
                    <td>&nbsp;</td>

                    </tr>

                    <tr>
                    <td style="width: 100px;">
                        loopt:
                        </td>

                        <td style="width: 600px;">
                            <div class="progress-bar bg-success" style="width:<?= $barlen2 ?>%">
                            <?= $counts[1] ?>
                            </div>
                        </td>
                        <td>&nbsp;</td>

                    </tr>
                </table>
            </font>
        </div>
    </div>



    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?php $rolspelerList = ArrayHelper::map($rolspeler,'id','naam');
        $statusIcon = ['&#128347;', '&#128490;', '&#128504;'];
        $rolspelerList = [ ''=> '...'] + $rolspelerList;
        $formlist =  ArrayHelper::map($form,'id','omschrijving');
        // dd($rolspelerList);
    ?>


    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            [   'attribute'=>'created',
            'format' => 'raw',
                'value' => function ($model) use ($statusIcon) {
                    $date = new DateTime($model->created);
                    $text = $date->format('H:i')."&nbsp;&nbsp;&nbsp;".$statusIcon[$model->status];
                    if ( $model->status == 2 ) {
                        return Html::a($text, ['/vraag/form', 'gesprekid'=>$model->id,'compleet'=>'1']);
                    } else {
                        return $text;
                    }
                   
                }
            ],
            'form.nr',
            
            [
                'attribute' => 'formid',
                'filter' => $formlist,
                'filterInputOptions' => [
                    'class' => 'form-control',
                    'prompt' => '...'
                ],
                'format' => 'raw',
                'value' => function ($model)  {
                    return $model->form->omschrijving;
                }
            ],

            [
                'attribute' => 'formid',
                'format' => 'raw',
                'value' => function ($model) {
                    //return $model->student->naam;
                    return Html::a($model->student->naam, ['/gesprek/student', 'id'=>$model->studentid]);
                }
            ],

            [
                'attribute' => 'rolspelerid',
                'filter' => $rolspelerList,
                'filterInputOptions' => [
                    'class' => 'form-control',
                    'prompt' => '  '
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

            [   'attribute' => 'opmerking',
                'contentOptions' => ['style' => 'width:80px;'],
                'format' => 'raw',
                'value' => function ($model) {
                    return substr($model->opmerking, 0, 10);
                }
            ],


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
                     }
            ],

            ['class' => 'yii\grid\ActionColumn', 'template' => '{view}',],
            ],
        ]);
    ?>


</div>
