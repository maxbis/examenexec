<?php

use yii\bootstrap4\Nav;
use yii\bootstrap4\NavBar;
use yii\helpers\Html;

NavBar::begin([
    'brandLabel' => Html::img('@web/exam.png', ['alt' => 'My logo', 'width' => '40px', 'height' => '40px']) ,
    'brandUrl' => Yii::$app->homeUrl,
    'options' => [
        //'class' => 'navbar-inverse navbar-fixed-top',
        'class' => 'navbar navbar-expand-sm bg-light',
        //'style' => 'font-size: 1.5em',
    ],
]);

echo Nav::widget([
    'options' => ['class' => 'navbar-nav mr-auto'],
    'encodeLabels' => false,
    'items' => [
        [   'label' => 'Admin',
            'visible' => (isset(Yii::$app->user->identity->role) && Yii::$app->user->identity->role == 'admin'),
            'items' => [
                 ['label' => 'Examens', 'url' => ['/examen/index'] ],
                 ['label' => 'Werkproces', 'url' => ['/werkproces/index'] ],
                 ['label' => 'SPL Rubics', 'url' => ['/criterium/index'] ],
                 ['label' => 'Formulieren', 'url' => ['/form']],
                 ['label' => 'Vragen', 'url' => ['/vraag']],
                 ['label' => '-----------------------------------'],
                 ['label' => 'Nieuwe Beoordeling', 'url' => ['/gesprek/create-and-go']],
            ],
            'options' => ['class' => 'nav-item']
        ],
        [   'label' => 'Status',
            'visible' => (isset(Yii::$app->user->identity->role) && Yii::$app->user->identity->role == 'admin'),
            'items' => [
                ['label' => 'Studenten', 'url' => ['/student']],
                ['label' => 'Rolspelers', 'url' => ['/rolspeler']],
                ['label' => 'Alle Gesprekken/beoordelingen', 'url' => ['/gesprek'],],
                ['label' => '-----------------------------------'],
                ['label' => 'Resultaat', 'url' => ['/uitslag/index']],
            ],
            'options' => ['class' => 'nav-item']
        ],
        [
            'visible' => (isset(Yii::$app->user->identity->role) && Yii::$app->user->identity->role == 'admin'),
            'label' => 'Queries',
            'items' => [
                ['label' => 'Gesprekken per kandidaat', 'url' => ['/query/gesprekken-per-kandidaat']],
                ['label' => 'Vrije rolspelers', 'url' => ['/query/vrije-rolspelers']],
                ['label' => 'Rolspelerbelasting', 'url' => ['/query/rolspeler-belasting']],
            ],
        ],
        [
            'visible' => (isset(Yii::$app->user->identity->role) && Yii::$app->user->identity->role == 'admin'),
            'label' => 'Login as...',
            'items' => [
                 ['label' => 'Student Log in', 'url' => ['/student/login']],
                 ['label' => 'Rolspeler Log in', 'url' => ['/rolspeler/login']],
                 ['label' => 'Clear', 'url' => ['/site/clear']],
            ],
        ],

        [   'label' => 'Export',
            'visible' => (isset(Yii::$app->user->identity->role) && Yii::$app->user->identity->role == 'xxx'),
            'items' => [
                ['label' => 'Export Results to File', 'url' => ['/query/export']],
                [  'label' => 'Export (active) Results to KTB',
                    'url' => ['/query/export-results'],
                    'linkOptions' => array('onclick'=>'return confirm("Export all results for active exam and active forms? ")'),
                ],
                [  'label' => 'Export (all) Comments to KTB',
                    'url' => ['/query/export-comments'],
                    'linkOptions' => array('onclick'=>'return confirm("All empty comments for the active exam will be overwitten in KTB, are you sure?")'),
                ],

                ['label' => '-----------------------------------'],
                [
                    'label' => 'Open KTB (Kentaak Beoordelingen)',
                    'url' => 'http://vps789715.ovh.net/KerntaakBeoordelingen/',
                    'template'=> '<a href="{url}" target="_blank">{label}</a>',
                    'linkOptions' => ['target' => '_blank'],
                ],
            ],
            'options' => ['class' => 'nav-item']
        ],

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
