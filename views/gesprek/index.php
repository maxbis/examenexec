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

// counts[0] heeft aantal wachtende, counts[1] in gesprek, counts[2] klaar 
$counts = array_count_values(array_column($alleGesprekken, 'status'));

if ( !isset($counts[0]) ) $counts[0]=0;
if ( !isset($counts[1]) ) $counts[1]=0;

$barlen1 = max(5,$counts[0]*2);
$barlen2 = max(5,$counts[1]*2);

?>
	
<script>
    function changeStatus(id, status, rolspelerid) {
        // console.log(val, id);
        $.ajax({
        url: "<?= Url::to(['/gesprek/update-status']) ?>",
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
        $statusIcon = ['&#128347;', ' 	&#128172;', '&#128504;'];
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
                'label' => 'Gespreksnaam',
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
                'attribute' => 'student.naam',
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
                        // return("???");
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
                    if ( $model->status != 9 ){ // replace != 9 into ==2 in order to enabel  edit only for status 2
                        return Html::dropDownList('status', $model->status, ['0'=>'Wachten','1'=>'Loopt','2'=>'Klaar'],
                        ['onchange' => "changeStatus('$model->id', $(this).val(), '$model->rolspelerid')"]);
                    } else {
                        return  ['0'=>'Wachten','1'=>'Loopt','2'=>'Klaar'][$model->status];
                    }
                    
                     }
            ],

            ['class' => 'yii\grid\ActionColumn', 'template' => '{view}',],
            ],
        ]);
    ?>


</div>
