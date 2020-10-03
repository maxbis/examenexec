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