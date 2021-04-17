<?php
use yii\helpers\Url;
use yii\helpers\Html;

$nr=0;

$output='';

header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="sample.csv"');

$fp = fopen('php://output', 'wb');

foreach($dataSet as $naam => $value) {
    $line=[];

    if ($value['studentid']=='') continue; // if beoordeling is not yet specified skip this record
    $nr++;

    $output.= '"'.$nr.'",';
    $output.= '"'.$value['groep'].'",';
    $output.= '"'.$naam.'",';

    array_push($line,$nr, $value['groep'],$naam);

    foreach($wp as $thisWp) {
        $output.= '"'.$value[$thisWp]['result'][0].'",';
        array_push($line,$value[$thisWp]['result'][0]);
    }
    foreach($wp as $thisWp) {
        $output.= '"'.$value[$thisWp]['result'][1].'"';
        array_push($line,$value[$thisWp]['result'][1]);
    }
    
    $output.= '<br>\n';

    fputcsv($fp, $line, ',', '"', "\\");

}



fclose($fp);

// exit;

// echo "<pre>";
// echo $output;
// echo "</pre>";