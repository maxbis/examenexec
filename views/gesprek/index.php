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

    <?php

        $rolspelerList = ArrayHelper::map($rolspeler,'id','naam');
        $statusIcon = ['&#128347;', '&#128172;', '&#128504;'];
        $rolspelerList = [ ''=> '...'] + $rolspelerList;
        $formlist =  ArrayHelper::map($form,'id','omschrijving');  // gespreksnaam
        // dd($rolspelerList);
    ?>


    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            [   'attribute'=>'created',
                'format' => 'raw',
                'value' => function ($alleGesprekken) use ($statusIcon) {
                    $date = new DateTime($alleGesprekken->created);
                    $text = $date->format('H:i')."&nbsp;&nbsp;&nbsp;".$statusIcon[$alleGesprekken->status];
                    if ( $alleGesprekken->status == 2 ) {
                        return Html::a($text, ['/vraag/form', 'gesprekid'=>$alleGesprekken->id,'compleet'=>'1']);
                    } else {
                        return $text;
                    }
                   
                }
            ],
            
            //'form.nr',
            //'beoordeling.id',
            //[
            //    'label' => 'cnt',
            //    'value' => function($alleGesprekken) {
            //        return $alleGesprekken->getBeoordeling()->count();
            //    }
            //],
            
            [
                'attribute' => 'formid',
                'label' => 'Gespreksnaam',
                'filter' => $formlist,
                'filterInputOptions' => [
                    'class' => 'form-control',
                    'prompt' => '...'
                ],
                'format' => 'raw',
                'value' => function ($alleGesprekken)  {
                    return $alleGesprekken->form->omschrijving;
                }
            ],

            [
                'attribute' => 'student.naam',
                'format' => 'raw',
                'value' => function ($alleGesprekken) {
                    //return $alleGesprekken->student->naam;
                    return Html::a($alleGesprekken->student->naam, ['/gesprek/student', 'id'=>$alleGesprekken->studentid]);
                }
            ],

            [
                'attribute' => 'rolspelerid',
                'filter' => $rolspelerList,
                'filterInputOptions' => [
                    'class' => 'form-control',
                    'prompt' => '...'
                    ],
                'format' => 'raw',
                'value' => function ($alleGesprekken) use ($rolspelerList) {
                    if ($alleGesprekken->status==0) {
                        return Html::dropDownList('status', $alleGesprekken->rolspelerid, $rolspelerList,
                        ['onchange' => "changeStatus('$alleGesprekken->id', '$alleGesprekken->status', $(this).val())"]);
                    } else {
                        // return $alleGesprekken->rolspeler->naam;
                        return("???");
                    }
                }
            ],

            [   'attribute' => 'opmerking',
                'contentOptions' => ['style' => 'width:80px;'],
                'format' => 'raw',
                'value' => function ($alleGesprekken) {
                    return substr($alleGesprekken->opmerking, 0, 10);
                }
            ],


            [
                'attribute' => 'status',
                'filter' => ['0'=>'Wachten','1'=>'Loopt','2'=>'Klaar'],
                'filterInputOptions' => [
                    'class' => 'form-control',
                    'prompt' => '...'
                    ],
                'format' => 'raw',
                'value' => function ($alleGesprekken) {
                    //$test = Html::dropDownList('status', 3, $rolspelerList);
                    if ( $alleGesprekken->status == 9 ){ // replace != 9 into ==2 in order to enabel  edit only for status 2
                        return Html::dropDownList('status', $alleGesprekken->status, ['0'=>'Wachten','1'=>'Loopt','2'=>'Klaar'],
                        ['onchange' => "changeStatus('$alleGesprekken->id', $(this).val(), '$alleGesprekken->rolspelerid')"]);
                    } else {
                        return  ['0'=>'Wachten','1'=>'Loopt','2'=>'Klaar'][$alleGesprekken->status];
                    }
                    
                     }
            ],

            [   'class' => 'yii\grid\ActionColumn', 'template' => '{view} {delete}',
            'visibleButtons'=>[
                'delete'=> function($alleGesprekken){
                      return 0;
                 },
            ]
            ],


        ],
    ]);
?>


</div>
