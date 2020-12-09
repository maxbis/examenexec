<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\CriteriumSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = $werkproces['id'].' '.$werkproces['titel'];
$this->params['breadcrumbs'][] = $this->title;
//dd($student);
?>
<div class="criterium-index">

    <h1><?= substr($werkproces['id'],0,5).' '.$examen['titel']; ; ?></h1>
    <h1><?= Html::encode($this->title) ?></h1>

    <br>

    <div class="card" style="width: 40rem;"><div class="card-body">
    <h4 class="card-header">Persoonsgegevens</h4>
        <table border=0 class="table">
            <tr> <td>Datum</td ><td><?= $examen['datum_start'].' t/m '.$examen['datum_eind'] ?></td> </tr>
            <tr> <td>Kandidaat</td> <td><?= $student['naam'] ?></td> </tr>
            <tr> <td>Leerlingnummer</td> <td><?=$student['nummer'] ?></td> </tr>
            <tr> <td>Klas</td> <td><?=$student['klas'] ?></td></tr>
            <tr><td>Beoordelaar1</td><td></td></tr>
            <tr><td>Beoordelaar2</td><td></td></tr>
        </table>
    </div>
    </div>
    
    <br><hr>

    <table border=0 class="table">
            <thead>
                <tr>
                    <th></th>
                    <th></th>
                    <th class="text-center">0</th>
                    <th class="text-center">1</th>
                    <th class="text-center">2</th>
                    <th class="text-center">3</th
                </tr> 
            </thead>

        <?php

        // klopt niet: count forms versus count SPL vragen!
        //if ( $formWpCount[$werkproces['id']] != count($results) ) {
        //    dd([ $formWpCount[$werkproces['id']], count($results) ]);
        //}

        $total=0;
        foreach($results as $item) {

            $uitslag=round($item['score']/10);
            $total+=$uitslag;
            $uitslag=max(0,$uitslag);

            if ($item['cruciaal']) {
                $bgcolor=['#F0F0F0','#F0F0F0','#F0F0F0','#F0F0F0','#F0F0F0'];
            } else {
                $bgcolor=['#FFFFFF','#FFFFFF','#FFFFFF','#FFFFFF','#FFFFFF'];
            }
            if ($item['cruciaal'] && $uitslag==0 ) {
                $bgcolor[$uitslag]='#ff9e9e'; 
            } else {
                $bgcolor[$uitslag]='#d5f7ba'; 
            }
           

            echo "<tr>";
            echo "<td width=30px>".$item['score']."</td>";
            echo "<td width=80px bgcolor=".$bgcolor[4].">".$item['cnaam']."<hr>".$item['fnaam']."</td>";

            echo "<td width=80px bgcolor=".$bgcolor[0]." >".$item['nul']."</td>";
            echo "<td width=80px bgcolor=".$bgcolor[1]." >".$item['een']."</td>";
            echo "<td width=80px bgcolor=".$bgcolor[2]." >".$item['twee']."</td>";
            echo "<td width=80px bgcolor=".$bgcolor[3]." >".$item['drie']."</td>";
            echo "</tr>";
        }
        ?>
   </table>

    <hr>
    Totale score: <?= $total; ?>
    <br>
    Werkproces maximale score: <?= $werkproces['maxscore']; ?>
    <br>
    <hr>
    
    <div class="card" style="width: 18rem;"><div class="card-body">
    <h5 class="card-header"><u>Cijfertabel</u></h5>
    <table border=0 class="table">
        <thead>
            <tr>
                <th>punten</th>
                <th>cijfer</th>

            </tr> 
        </thead>
        
        <?php

        $total=max(0,$total);
        for($i=0; $i<=$werkproces['maxscore']; $i++) {
            if ( $total == $i) {
                $bgcolor="#d5f7ba";
            } else {
                $bgcolor="#FFFFFF";
            }
            $cijfer=number_format(intval(10.99+90*$i/$werkproces['maxscore'])/10,1);
            echo "<tr>";
            echo "<td width=80px bgcolor=".$bgcolor.">".$i."</td>";
            echo "<td width=80px bgcolor=".$bgcolor.">".$cijfer."</td>";
            echo "</tr>";
        }

        ?>
    </table>
    </div></div>

    <br>
    <br>
    motivatie<br>
    drop down Beoordelaar1<br>
    drop down Beoordelaar2<br>
    <br>
    button ready for print<br>

</div>

<br><br>
