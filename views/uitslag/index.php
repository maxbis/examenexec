<?php
use yii\helpers\Url;
use yii\helpers\Html;

$nr=0;

?>

<style>
.uneven {
    text-align:center;
    background-color:#F0F0F0;
    width:50px;
}
.even {
    text-align:center;
    background-color:#F4F4F4;
    width: 50px;
}
</style>

<h1>Uitslagen</h1>

<p></p>

<div class="card" style="width: 1000px">
    <div class="card-body">
        <table class="table">
            <thead>
                <tr>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th colspan=4>Cijfers</th>
                    <th>&nbsp;</th>
                    <th colspan=4>Resultaten</th>
                    <th>&nbsp;</th>
                    <th colspan=4>Print Ready</th>
                    <th>&nbsp;</span></th>
                </tr>    
                <tr>
                <?php
                    echo "<th></th>";
                    echo "<th></th>";
                    echo "<th>Kandidaat</th>";
                   
                    $teller=1;
                    foreach($wp as $thisWp) {
                        echo "<th class=\"even\">W".$teller++."</th>";
                    }
                    echo "<th>&nbsp;</th>";

                    $teller=1;
                    foreach($wp as $thisWp) {
                        echo "<th class=\"even\">W".$teller++."</th>";
                    }
                    echo "<th>&nbsp;</th>";
                    $teller=1;
                    foreach($wp as $thisWp) {
                        echo "<th class=\"even\">W".$teller++."</th>";
                    }
                    echo "<th>";
                    echo Html::a("<span style=\"color:#D0D0F0\" class=\"glyphicon glyphicon-print\"></span>",
                        ['print/index', 'id'=>-99],
                        [   'title' => 'Print ALL',
                            'data' => [
                            'confirm' => 'Let op ALLE examens worden in één PDF gezet. Dit kan even duren. Weet je het zeker?',
                            'method' => 'post',
                            ],
                        ] );
                    echo "</th>";
                ?>
                </tr>    
            </thead>
            
            <?php
                foreach($dataSet as $naam => $value) {
                    $nr++;
                    echo "<tr>";
                    echo "<td class=\"text-muted\">".$nr."</td>";
                    echo "<td>".$value['groep']."</td>";
                    $onvoldoende=false;
                    foreach($wp as $thisWp) {
                        if ( $value[$thisWp]['result'][1] == 'O' ) {
                            $onvoldoende=true;
                            break;
                        }
                    }
                    if ($onvoldoende) {
                        echo "<td style=\"color:red\">";
                    } else {
                        echo "<td>";
                    }
                    echo $naam."</td>";

                    foreach($wp as $thisWp) {
                        echo "<td class=\"even\">"; 
                        echo Html::a($value[$thisWp]['result'][0], ['/uitslag/result', 'studentid'=>$value['studentid'], 'wp'=>$thisWp ] );
                        echo "</td>";
                    }
                    echo "<td>&nbsp;</td>";
                    foreach($wp as $thisWp) {
                        echo "<td class=\"even\">".$value[$thisWp]['result'][1]."</td>";
                    }
                    echo "<td>&nbsp;</td>";
                    foreach($wp as $thisWp) {
                        echo "<td class=\"even\">"; 
                        //if ( $value[$thisWp]['status']==$formWpCount[$thisWp] ) echo "<div class=\"text-success\"><b>".$value[$thisWp]['status']."</b></div>";
                        if ( $value[$thisWp]['status']==$formWpCount[$thisWp] ) echo  Html::a( "<div class=\"text-success\"><b>".$value[$thisWp]['status']."</b></div>" , ['/uitslag/result', 'studentid'=>$value['studentid'], 'wp'=>$thisWp ] );
                        elseif ( $value[$thisWp]['status']==99 ) echo "<span class=\"glyphicon glyphicon-check\"></span>";
                        else echo "<div class=\"text-info\">".$value[$thisWp]['status']."</div>";
                    }
                    echo "</td>";
                    echo "<td>";
                    $print=true;
                    foreach($wp as $thisWp) {
                        if ( $value[$thisWp]['status'] != 99 ) {
                            $print=false;
                            break;
                        }
                    }

                    if ( $print ) {
                        echo Html::a("<span class=\"glyphicon glyphicon-print\"></span>", ['/print/index', 'id'=>$dataSet[$naam]['studentid'] ]);
                    } else {
                        echo "<span title=\"Print beschikbaar als alle vier de werkprocessen print-klaar zijn.\" class=\"glyphicon glyphicon-print text-muted\"></span>";
                    }
                    echo "</td>";
                    echo "</tr>";
                }
            ?>

        </table>
    </div>
</div>
<small><hr><i>( berekening SPL score: round(score/maxscore*9+1)+0.049,1) - hiermee wordt altijd omhoog afgerond naar de volgende 0.1 )</i></small>