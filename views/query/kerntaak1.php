<?php
use yii\helpers\Url;
$nr=0;
?>

<h1>Uitslagen Kerntaak-1</h1>
<i>(Cijfers zijn berekend en kunnen maximaal 0.1 punt verschillen van de SPL cijfertabellen)</i>

<p></p>

<div class="card"  style="width: 800px">
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
                    <th>#</th>
                    <th>Naam</th>
                    <th style="width: 50px;background-color:#F0F0F0;">W1</th>
                    <th style="width: 50px;background-color:#F0F0F0;">W2</th>
                    <th style="width: 50px;background-color:#F0F0F0;">W3</th>
                    <th style="width: 50px;background-color:#F0F0F0;">W4</th>
                    <th>&nbsp;</th>
                    <th style="width: 50px;">W1</th>
                    <th style="width: 50px;">W2</th>
                    <th style="width: 50px;">W3</th>
                    <th style="width: 50px;">W4</th>
                </tr>    
            </thead>
            
            <?php
                foreach($data as $naam => $value) {
                    $nr++;
                    echo "<tr>";
                    echo "<td>".$nr."</td>";
                    echo "<td>".$naam."</td>";
                    echo "<td style=\"background-color:#F0F0F0;\">".$value['B1-K1-W1'][0]."</td>";
                    echo "<td style=\"background-color:#F0F0F0;\">".$value['B1-K1-W2'][0]."</td>";
                    echo "<td style=\"background-color:#F0F0F0;\">".$value['B1-K1-W3'][0]."</td>";
                    echo "<td style=\"background-color:#F0F0F0;\">".$value['B1-K1-W4'][0]."</td>";
                    echo "<td>&nbsp;</td>";
                    echo "<td>".$value['B1-K1-W1'][1]."</td>";
                    echo "<td>".$value['B1-K1-W2'][1]."</td>";
                    echo "<td>".$value['B1-K1-W3'][1]."</td>";
                    echo "<td>".$value['B1-K1-W4'][1]."</td>";

                    echo "</tr>";
                }
            ?>

        </table>
    </div>
</div>