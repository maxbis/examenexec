<?php

use yii\bootstrap4\Nav;
use yii\bootstrap4\NavBar;
use yii\helpers\Html;

NavBar::begin([
    'brandLabel' => Html::img(['@web/planner.jpg']),
    'brandUrl' => Yii::$app->homeUrl,
    'options' => [
        //'class' => 'navbar-inverse navbar-fixed-top',
        'class' => 'navbar navbar-expand-sm bg-light',
        //'style' => 'font-size: 1.5em',
    ],
]);

echo Nav::widget([
    //'options' => ['class' => 'navbar-nav navbar-right'],
    'options' => ['class' => 'navbar-nav mr-auto'],
    'encodeLabels' => false,
    'items' => [
        [   'label' => 'Set-up',
            //'class'=>'bootstrap.widgets.BootMenu',
            //'htmlOptions'=>array('style'=>'font-size: 2.5em'),
            'visible' => (isset(Yii::$app->user->identity->role) && Yii::$app->user->identity->role == 'admin'),
            'items' => [
                 //['label' => 'Examens (unused)', 'url' => ['/examen/index'] ],
                 ['label' => 'Formulieren', 'url' => ['/form']],
                 ['label' => 'Vragen', 'url' => ['/vraag']],
            ],
            'options' => ['class' => 'nav-item']
        ],
        [   'label' => 'Status',
        //'class'=>'bootstrap.widgets.BootMenu',
        //'htmlOptions'=>array('style'=>'font-size: 2.5em'),
        'visible' => (isset(Yii::$app->user->identity->role) && Yii::$app->user->identity->role == 'admin'),
        'items' => [
            ['label' => 'Studenten', 'url' => ['/student']],
            ['label' => 'Rolspelers', 'url' => ['/rolspeler']],
            ['label' => 'Gesprekken', 'url' => ['/gesprek']],
        ],
        'options' => ['class' => 'nav-item']
    ],

        [
            'label' => 'Student',
            'items' => [
                 ['label' => 'Log in', 'url' => ['/gesprek/login']],
            ],
        ],

        [
            'label' => 'Rolspeler',
            'items' => [
                 ['label' => 'Log in', 'url' => ['/gesprek/rolspeler']],
            ],
        ],
        // ['label' => 'Home', 'url' => ['/site/index'], 'options' => ['class' => 'nav-item'] ],
        // ['label' => 'About', 'url' => ['/site/about'], 'options' => ['class' => 'nav-item'] ],
        // ['label' => 'Contact', 'url' => ['/site/contact'], 'options' => ['class' => 'nav-item'] ],
    ],
    
]);

echo Nav::widget([
    'options' => ['class' => 'navbar-nav ml-auto'],
    'items' => [
        Yii::$app->user->isGuest ? (
            ['label' => 'Login', 'url' => ['/site/login'], 'options' => ['class' => 'nav-item']]
        ) : (
            ['label' => 'Logout', 'url' => ['/site/logout'], 'options' => ['class' => 'nav-item'],]
        )
    ],
]);

NavBar::end();
?>