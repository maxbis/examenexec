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
                    <?php
                        for($i=0;$i<count($data['col']);$i++) {
                            echo "<th>".$data['col'][$i]."</th>";
                        }
                    ?>
            </thead>
            
            <?php
                foreach($data['row'] as $item) {
                    $nr++;
                    echo "<tr>";
                    echo "<td>".$nr."</td>";
                    for($i=0;$i<count($data['col']);$i++) {
                        echo "<td>".$item[$data['col'][$i]]."</td>";

                    }
                    echo "</tr>";
                }
            ?>

        </table>
    </div>
</div>