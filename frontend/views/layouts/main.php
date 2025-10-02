<?php

/** @var \yii\web\View $this */
/** @var string $content */

use common\widgets\Alert;
use frontend\assets\AppAsset;
use yii\bootstrap5\Breadcrumbs;
use yii\bootstrap5\Html;
use yii\bootstrap5\Nav;
use yii\bootstrap5\NavBar;

AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>" class="h-100">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <?php $this->registerCsrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body class="d-flex flex-column h-100">
<?php $this->beginBody() ?>

<header class="header">
    <div class="container">
        <div class="header-main">
            <a href="/" class="header-logo"><img src="/images/logo.png"></a>
            <div class="header-menu">
                <ul>
                    <li>
                        <a href="<?= \yii\helpers\Url::to(['/catalog/index']) ?>">Каталог</a>
                        <ul>
                            <li><a href="#">Рис и бобовые</a></li>
                            <li><a href="#">Морепродукты</a></li>
                            <li><a href="#">Специи и приправы</a></li>
                            <li><a href="#">Соусы</a></li>
                            <li><a href="#">Сыр</a></li>
                            <li><a href="#">Майонез</a></li>
                            <li><a href="#">Масло</a></li>
                            <li><a href="#">Мясная продукция</a></li>
                            <li><a href="#">Прочее</a></li>
                            <li><a href="#">Товары</a></li>
                            <li><a href="#">Материалы</a></li>
                            <li><a href="#">Наггетсы</a></li>
                            <li><a href="#">Картофель, картофель фри</a></li>
                            <li><a href="#">Рис</a></li>
                        </ul>
                    </li>
                    <li><a href="#">О доставке</a></li>
                    <li><a href="#">Сотрудничество</a></li>
                    <li><a href="#">Контакты</a></li>
                </ul>
            </div>
            <div class="header-work">
                <p>Режим работы:</p>
                <p>Пн-Сб 09:00-18:00</p>
                <p>Вс 09:00-15:00</p>
            </div>
            <div class="header-buttons">
                <button class="btn btn-primary">Войти</button>
            </div>
        </div>
    </div>
</header>

<!--
<header>
    <?php
    NavBar::begin([
        'brandLabel' => Yii::$app->name,
        'brandUrl' => Yii::$app->homeUrl,
        'options' => [
            'class' => 'navbar navbar-expand-md navbar-dark bg-dark fixed-top',
        ],
    ]);
    $menuItems = [
        ['label' => 'Home', 'url' => ['/site/index']],
        ['label' => 'Каталог', 'url' => ['/catalog/index']],
        ['label' => 'About', 'url' => ['/site/about']],
        ['label' => 'Contact', 'url' => ['/site/contact']],
    ];
    if (Yii::$app->user->isGuest) {
        $menuItems[] = ['label' => 'Signup', 'url' => ['/site/signup']];
    }

    echo Nav::widget([
        'options' => ['class' => 'navbar-nav me-auto mb-2 mb-md-0'],
        'items' => $menuItems,
    ]);
    echo \frontend\widgets\CartWidget::widget();
    if (Yii::$app->user->isGuest) {
        echo Html::tag('div',Html::a('Login',['/site/login'],['class' => ['btn btn-link login text-decoration-none']]),['class' => ['d-flex']]);
    } else {
        echo Html::beginForm(['/site/logout'], 'post', ['class' => 'd-flex'])
            . Html::submitButton(
                'Logout (' . Yii::$app->user->identity->username . ')',
                ['class' => 'btn btn-link logout text-decoration-none']
            )
            . Html::endForm();
    }
    NavBar::end();
    ?>
</header>
-->

<?= Breadcrumbs::widget([
    'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
]) ?>
<?= Alert::widget() ?>
<?= $content ?>

<!--
<footer class="footer mt-auto py-3 text-muted">
    <div class="container">
        <p class="float-start">&copy; <?= Html::encode(Yii::$app->name) ?> <?= date('Y') ?></p>
        <p class="float-end"><?= Yii::powered() ?></p>
    </div>
</footer>
-->

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage();
