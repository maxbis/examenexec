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
        [   'label' => 'Docent',
            //'class'=>'bootstrap.widgets.BootMenu',
            //'htmlOptions'=>array('style'=>'font-size: 2.5em'),
            'visible' => (isset(Yii::$app->user->identity->role) && Yii::$app->user->identity->role == 'admin'),
            'items' => [
                 ['label' => 'Examens', 'url' => ['/examen/index'] ],
                 ['label' => 'Gesprekssoort', 'url' => ['/gesprek-soort']],
                 ['label' => 'Form', 'url' => ['/form']],
                 ['label' => 'Vragen', 'url' => ['/vraag']],
                 ['label' => 'Rolspelers', 'url' => ['/rolspeler']],
                 ['label' => 'Gesprekken', 'url' => ['/gesprek']],
                 ['label' => 'Planner', 'url' => ['/gesprek/overzicht']],
                 ['label' => 'Help', 'url' => ['/examen/help']],
            ],
            'options' => ['class' => 'nav-item']
        ],
        [
            'label' => 'Student',
            'items' => [
                 ['label' => 'Aanvraag', 'url' => ['/gesprek/create']],
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