<?php
/**
 * Debug function
 * d($var);
 */
function _d($var,$caller=null)
{
    if(!isset($caller)){
        $caller = array_shift(debug_backtrace(1));
    }
    echo '<code>File: '.$caller['file'].' / Line: '.$caller['line'].'</code>';
    echo '<pre>';
    yii\helpers\VarDumper::dump($var, 10, true);
    echo '</pre>';
}

/**
 * Debug function with die() after
 * dd($var);
 */
function _dd($var)
{
    $caller = array_shift(debug_backtrace(1));
    d($var,$caller);
    die();
}

function d($var)
{
    echo '<pre>';
    yii\helpers\VarDumper::dump($var, 10, true);
    echo '</pre>';
}

function dd($var)
{
    d($var);
    die();
}

function HTMLInclude($file)
{
    return \Yii::$app->view->renderFile('@app/views/layouts/'.$file.'.php');
}

function writeLog($msg="")
{
    $log  = date("j-m-Y, H:i")." "
            .$_SERVER['REMOTE_ADDR']." "
            .Yii::$app->controller->id."Controller "
            ."action".Yii::$app->controller->action->id." "
            .$msg;
    $result = file_put_contents('./log_'.date("j-m-Y").'.log', $log.PHP_EOL, FILE_APPEND);
    //d("writeLog: ".$log);
}

function ipRange($cidr)
{
    $range = array();
    $cidr = explode('/', $cidr);
    $range[0] = long2ip((ip2long($cidr[0])) & ((-1 << (32 - (int)$cidr[1]))));
    $range[1] = long2ip((ip2long($range[0])) + pow(2, (32 - (int)$cidr[1])) - 1);
    return $range;
}

function areWeOK()
{
    if ( $_SERVER['REMOTE_ADDR'] == '::1' ) return; // php yii server

    $ipAllowed= [   '145.100.74.0/24',
                    '82.217.135.0/24',
                    '86.89.180.0/24',
                    '127.0.0.1/32',
                ];
    $weAreOK=false;
    foreach ($ipAllowed as $item) {
        $ipRange = ipRange($item);
        //echo $_SERVER['REMOTE_ADDR']." in ".$ipRange[0]." - ".$ipRange[1]."<br>";
        if ( (int)$_SERVER['REMOTE_ADDR'] >= (int)$ipRange[0] && (int)$_SERVER['REMOTE_ADDR'] <= (int)$ipRange[1] ) {
                $weAreOK=true;
        }
    }
    if ( $weAreOK == false ) {
        $string = "Permission denied for ". $_SERVER['REMOTE_ADDR'];
        writeLog($string);
        echo $string;
        exit;
    }
}



