<?php

/** @var yii\web\View $this */

use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'Административная панель';

// Определяем доступные разделы
$sections = [
    [
        'title' => 'Заказы',
        'description' => 'Управление заказами, изменение статуса и информации об оплате',
        'icon' => 'shopping-cart',
        'url' => ['/order/index'],
        'color' => 'primary',
        'count' => \repositories\Order\models\Order::find()->count(),
    ],
    [
        'title' => 'Категории',
        'description' => 'Управление категориями товаров, создание и редактирование',
        'icon' => 'list',
        'url' => ['/category/index'],
        'color' => 'success',
        'count' => \repositories\Category\models\Category::find()->count(),
    ],
    [
        'title' => 'Товары',
        'description' => 'Управление товарами, цены, наличие, описания и изображения',
        'icon' => 'box',
        'url' => ['/product/index'],
        'color' => 'info',
        'count' => \repositories\Product\models\Product::find()->count(),
    ],
];
?>

<div class="site-index">
    <div class="jumbotron text-center bg-transparent mt-4 mb-5">
        <h1 class="display-4">Административная панель</h1>
        <p class="lead">Добро пожаловать в панель управления интернет-магазином Asia Food</p>
    </div>

    <div class="body-content">
        <div class="row">
            <?php foreach ($sections as $section): ?>
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="card dashboard-card h-100 border-<?= $section['color'] ?>">
                        <div class="card-header bg-<?= $section['color'] ?> text-white">
                            <h4 class="mb-0">
                                <i class="fas fa-<?= $section['icon'] ?> me-2"></i>
                                <?= $section['title'] ?>
                            </h4>
                        </div>
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h5 class="card-title">Всего: <?= $section['count'] ?></h5>
                                <span class="badge bg-<?= $section['color'] ?> rounded-pill"><?= $section['count'] ?></span>
                            </div>
                            <p class="card-text"><?= $section['description'] ?></p>
                        </div>
                        <div class="card-footer bg-transparent border-top">
                            <?= Html::a(
                                '<i class="fas fa-arrow-right"></i> Перейти в раздел',
                                $section['url'],
                                ['class' => 'btn btn-outline-' . $section['color'] . ' w-100']
                            ) ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        
        <div class="row mt-5">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-dark text-white">
                        <h4 class="mb-0">Последние заказы</h4>
                    </div>
                    <div class="card-body">
                        <?php 
                        $latestOrders = \repositories\Order\models\Order::find()
                            ->orderBy(['created_at' => SORT_DESC])
                            ->limit(5)
                            ->all();
                        
                        if ($latestOrders): ?>
                            <div class="list-group">
                                <?php foreach ($latestOrders as $order): ?>
                                    <a href="<?= Url::to(['/order/view', 'id' => $order->id]) ?>" class="list-group-item list-group-item-action">
                                        <div class="d-flex w-100 justify-content-between">
                                            <h5 class="mb-1">Заказ #<?= $order->id ?></h5>
                                            <small><?= Yii::$app->formatter->asDatetime($order->created_at) ?></small>
                                        </div>
                                        <p class="mb-1"><?= Html::encode($order->customer_name) ?> - <?= Yii::$app->formatter->asCurrency($order->total_cost) ?></p>
                                    </a>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <p class="text-muted">Нет заказов</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-dark text-white">
                        <h4 class="mb-0">Статистика</h4>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-6 mb-3">
                                <div class="card bg-light">
                                    <div class="card-body text-center">
                                        <h3 class="display-4"><?= \repositories\Order\models\Order::find()->count() ?></h3>
                                        <p class="mb-0">Заказов</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6 mb-3">
                                <div class="card bg-light">
                                    <div class="card-body text-center">
                                        <h3 class="display-4"><?= \repositories\Product\models\Product::find()->count() ?></h3>
                                        <p class="mb-0">Товаров</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="card bg-light">
                                    <div class="card-body text-center">
                                        <h3 class="display-4"><?= \repositories\Category\models\Category::find()->count() ?></h3>
                                        <p class="mb-0">Категорий</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="card bg-light">
                                    <div class="card-body text-center">
                                        <h3 class="display-4"><?= \repositories\Order\models\Order::find()->where(['status' => \repositories\Order\models\Order::STATUS_NEW])->count() ?></h3>
                                        <p class="mb-0">Новых заказов</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
