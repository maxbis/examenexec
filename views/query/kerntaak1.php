<?php
use yii\helpers\Url;
$nr=0;
?>

<h1><?= $data['title'] ?></h1>

<p></p>

<div class="card"  style="width: 600px">
    <div class="card-body">
        <table class="table">
            <thead>
                <tr>
                    <td>#</td>
                    <td>Naam</td>
                    <td>W1</td>
                    <td>W2</td>
                    <td>W3</td>
                    <td>W4</td>
            </thead>
            
            <?php
                if ( $data['row'] ) {
                    foreach($data['row'] as $item) {
                        $nr++;
                        echo "<tr>";
                        echo "<td>".$nr."</td>";
                        echo "<td>".$item['naam']."</td>";

                        if ( $item['formnaam']=='B1-K1-W1' ) {
                            echo "<td>".$item['cijfer']."</td>";
                        } else {
                            echo "<td>&nbsp</td>";
                        }
                        if ( $item['formnaam']=='B1-K1-W2' ) {
                            echo "<td>".$item['cijfer']."</td>";
                        } else {
                            echo "<td>&nbsp</td>";
                        }
                        if ( $item['formnaam']=='B1-K1-W3' ) {
                            echo "<td>".$item['cijfer']."</td>";
                        } else {
                            echo "<td>&nbsp</td>";
                        }
                        if ( $item['formnaam']=='B1-K1-W4' ) {
                            echo "<td>".$item['cijfer']."</td>";
                        } else {
                            echo "<td>&nbsp</td>";
                        }

                        echo "</tr>";
                    }
                }

            ?>

        </table>
    </div>
</div>