<?php
use yii\helpers\Url;

$nr=0;
$examenid=12;

if ( $_SERVER['REMOTE_ADDR'] == '::1' ){
    $KTB="http://localhost/KerntaakBeoordelingen/werkproces.php";
} else {
    $KTB="http://vps789715.ovh.net/KerntaakBeoordelingen/werkproces.php";
}

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

<h1>Uitslagen Kerntaak-1</h1>

<p></p>

<div class="card" style="width: 700px">
    <div class="card-body">
        <table class="table">
            <thead>
                <tr>
                    <th></th>
                    <th></th>
                    <th colspan=4>Cijfers</th>
                    <th>&nbsp;</th>
                    <th colspan=4>Resultaten</th>
 
                </tr>    
                <tr>
                    <th></th>
                    <th>Kandidaat</th>
                    <th class="uneven">W1</th>
                    <th class="even">W2</th>
                    <th class="uneven">W3</th>
                    <th class="even"">W4</th>
                    <th>&nbsp;</th>
                    <th class="uneven"">W1</th>
                    <th class="even">W2</th>
                    <th class="uneven">W3</th>
                    <th class="even">W4</th>
                </tr>    
            </thead>
            
            <?php
                foreach($data as $naam => $value) {
                    $nr++;
                    echo "<tr>";
                    echo "<td>".$nr."</td>";
                    echo "<td>".$naam."</td>";
                    echo "<td class=\"uneven\"><a href=".$KTB."?code=B1-K1-W1&examen=".$examenid."&studentnr=".$value['studentnr'].">".$value['B1-K1-W1'][0]."</a></td>";
                    echo "<td class=\"even\"><a href=".$KTB."?code=B1-K1-W2&examen=".$examenid."&studentnr=".$value['studentnr'].">".$value['B1-K1-W2'][0]."</a></td>";
                    echo "<td class=\"uneven\"><a href=".$KTB."?code=B1-K1-W3&examen=".$examenid."&studentnr=".$value['studentnr'].">".$value['B1-K1-W3'][0]."</a></td>";
                    echo "<td class=\"even\"><a href=".$KTB."?code=B1-K1-W4&examen=".$examenid."&studentnr=".$value['studentnr'].">".$value['B1-K1-W4'][0]."</a></td>";
                    echo "<td>&nbsp;</td>";
                    echo "<td class=\"uneven\">".$value['B1-K1-W1'][1]."</td>";
                    echo "<td class=\"even\">".$value['B1-K1-W2'][1]."</td>";
                    echo "<td class=\"uneven\">".$value['B1-K1-W3'][1]."</td>";
                    echo "<td class=\"even\">".$value['B1-K1-W4'][1]."</td>";
                    echo "</tr>";
                }
            ?>

        </table>
    </div>
</div>
<small><hr><i>( berekening SPL score: round(score/maxscore*9+1)+0.049,1) - hiermee wordt altijd omhoog afgerond naar de volgende 0.1 )</i></small>