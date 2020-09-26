<?php

/* @var $this \yii\web\View */
/* @var $content string */

use app\widgets\Alert;
use yii\helpers\Html;
use yii\bootstrap4\Nav;
use yii\bootstrap4\NavBar;
use yii\widgets\Breadcrumbs;
use app\assets\AppAsset;

AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php $this->registerCsrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head> 
<body>

<?php $this->beginBody() ?>

<?php

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
                 ['label' => 'Gesprekssoort', 'url' => ['/gesprek-soort/index']],
                 ['label' => 'Rolspelers', 'url' => ['/rolspeler/index']],
                 ['label' => 'Gesprekken', 'url' => ['/gesprek/index']],
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


<div class="container">
    <!--
        <?= Breadcrumbs::widget([
            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
        ]) ?>
    <?= Alert::widget() ?>
    -->
    <hr>
    <?= $content ?>
</div>

<br>

<footer class="footer" >
    <div class="container">
        <p class="pull-left">&copy; ROCvA <?= date('Y') ?></p>
        <p class="pull-right"><?= Yii::powered() ?></p>
    </div>
</footer>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>

