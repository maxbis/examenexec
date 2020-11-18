<?php

use yii\bootstrap4\Nav;
use yii\bootstrap4\NavBar;
use yii\helpers\Html;

NavBar::begin([
    'brandLabel' => Html::img('@web/planner.jpg', ['alt' => 'My logo']) ,
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
        [   'label' => 'Admin',
            //'class'=>'bootstrap.widgets.BootMenu',
            //'htmlOptions'=>array('style'=>'font-size: 2.5em'),
            'visible' => (isset(Yii::$app->user->identity->role) && Yii::$app->user->identity->role == 'admin'),
            'items' => [
                 ['label' => 'Examens', 'url' => ['/examen/index'] ],
                 ['label' => 'Formulieren', 'url' => ['/form']],
                 ['label' => 'Vragen', 'url' => ['/vraag']],
                 ['label' => '-----------------------------------'],
                 ['label' => 'Nieuwe Beoordeling', 'url' => ['/gesprek/create-and-go']],
                 ['label' => '-----------------------------------'],
                 ['label' => 'Export Resultaten to File', 'url' => ['/beoordeling/export']],
                 ['label' => 'Export Resultaten to Query', 'url' => ['/beoordeling/export3']],
                 ['label' => '-----------------------------------'],
                 [
                    'label' => 'Kentaak Beoordelingen',
                    'url' => 'http://vps789715.ovh.net/KerntaakBeoordelingen/',
                    'template'=> '<a href="{url}" target="_blank">{label}</a>',
                    'linkOptions' => ['target' => '_blank'],
                 ],

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
            ['label' => 'Vrije rolspelers', 'url' => ['/beoordeling/export4']],
            ['label' => 'Alle Gesprekken', 'url' => ['/gesprek']],
            ['label' => 'Overzicht Gesprekken', 'url' => ['/student/status']],
        ],
        'options' => ['class' => 'nav-item']
    ],

        [
            'visible' => (isset(Yii::$app->user->identity->role) && Yii::$app->user->identity->role == 'admin'),
            'label' => 'Student',
            'items' => [
                 ['label' => 'Log in', 'url' => ['/student/login']],
                 ['label' => 'Logout', 'url' => ['/site/clear']],
            ],
        ],

        [
            'visible' => (isset(Yii::$app->user->identity->role) && Yii::$app->user->identity->role == 'admin'),
            'label' => 'Rolspeler',
            'items' => [
                 ['label' => 'Log in', 'url' => ['/rolspeler/login']],
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