<?php

/** @var yii\web\View $this */
/** @var common\models\Order $order */

use yii\helpers\Html;
use yii\helpers\Url;
use common\helpers\PriceHelper;
use repositories\Order\models\Order as OrderModel;

$this->title = 'Заказ №' . $order->id;

// Подключаем FontAwesome для иконок
$this->registerCssFile('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css');

?>

<div class="profile-page order-view-page">
    <div class="container">
        <div class="profile-page-head">
            <div class="title">Заказ №<?= $order->id ?></div>
        </div>

        <div class="profile-page-main">
            <div class="row">
                <div class="col-lg-8">
                    <!-- Детали заказа -->
                    <div class="card-style mb-4">
                        <div class="card-header">
                            <h5>Детали заказа</h5>
                        </div>
                        <div class="card-body">
                            <div class="info-grid-new">
                                <div class="info-item"><i class="fas fa-hashtag"></i> <strong>Номер:</strong> <span>#<?= $order->id ?></span></div>
                                <div class="info-item"><i class="fas fa-calendar-alt"></i> <strong>Дата:</strong> <span><?= date('d.m.Y H:i', $order->created_at) ?></span></div>
                                <div class="info-item"><i class="fas fa-money-bill-wave"></i> <strong>Сумма:</strong> <span class="total-amount"><?= PriceHelper::formatRub($order->total_cost) ?></span></div>
                                <div class="info-item"><i class="fas fa-credit-card"></i> <strong>Оплата:</strong> <span><?= $paymentMethodText ?></span></div>
                                <div class="info-item"><i class="fas fa-info-circle"></i> <strong>Статус заказа:</strong> <span class="status-badge status-<?= $order->status ?>"><?= $statusInfo['text'] ?></span></div>
                                <div class="info-item"><i class="fas fa-credit-card-alt"></i> <strong>Статус оплаты:</strong> <span class="payment-badge payment-<?= $order->payment_status ?>"><?= $paymentStatusInfo['text'] ?></span></div>
                            </div>
                        </div>
                    </div>

                    <!-- Состав заказа -->
                    <div class="card-style mb-4">
                        <div class="card-header">
                            <h5>Состав заказа</h5>
                        </div>
                        <div class="card-body">
                            <div class="order-items-list">
                                <?php foreach ($order->orderItems as $item): ?>
                                    <div class="order-item">
                                        <div class="item-image">
                                            <?php if ($item->product && $item->product->image): ?>
                                                <img src="<?= $item->product->getCroppedImageUrl(80, 80) ?>" alt="<?= Html::encode($item->product_name) ?>">
                                            <?php else: ?>
                                                <div class="no-image"><i class="fas fa-image"></i></div>
                                            <?php endif; ?>
                                        </div>
                                        <div class="item-info">
                                            <div class="item-name">
                                                <?php if ($item->product): ?>
                                                    <a href="<?= Url::to(['/product/view', 'slug' => $item->product->slug]) ?>"><?= Html::encode($item->product_name) ?></a>
                                                <?php else: ?>
                                                    <?= Html::encode($item->product_name) ?>
                                                <?php endif; ?>
                                            </div>
                                            <div class="item-details">
                                                <span><?= $item->quantity ?> шт. × <?= PriceHelper::formatRub($item->price) ?></span>
                                            </div>
                                        </div>
                                        <div class="item-total">
                                            <strong><?= PriceHelper::formatRub($item->price * $item->quantity) ?></strong>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <!-- Получатель -->
                    <div class="card-style mb-4">
                        <div class="card-header">
                            <h5>Получатель</h5>
                        </div>
                        <div class="card-body">
                            <div class="contact-item"><i class="fas fa-user"></i> <span><?= Html::encode($order->customer_name) ?></span></div>
                            <div class="contact-item"><i class="fas fa-phone"></i> <span><?= Html::encode($order->customer_phone) ?></span></div>
                            <?php if ($order->customer_email): ?>
                                <div class="contact-item"><i class="fas fa-envelope"></i> <span><?= Html::encode($order->customer_email) ?></span></div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Действия -->
                    <div class="card-style mb-4">
                        <div class="card-header">
                            <h5>Действия</h5>
                        </div>
                        <div class="card-body">
                            <div class="action-buttons">
                                <?php if ($order->payment_status == OrderModel::PAYMENT_STATUS_PENDING): ?>
                                    <a href="<?= Url::to(['/payment/pay', 'orderId' => $order->id]) ?>" class="but but-three"><i class="fas fa-credit-card"></i> Оплатить</a>
                                <?php endif; ?>
                                <a href="<?= Url::to(['/catalog/index']) ?>" class="but but-secondary"><i class="fas fa-store"></i> В каталог</a>
                                <?php if (Yii::$app->user->isGuest): ?>
                                    <a href="<?= Url::to(['/site/login']) ?>" class="but but-secondary"><i class="fas fa-sign-in-alt"></i> Войти в профиль</a>
                                <?php else: ?>
                                     <a href="<?= Url::to(['/profile/orders']) ?>" class="but but-secondary"><i class="fas fa-history"></i> Мои заказы</a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <?php if (trim($order->note)): ?>
                        <div class="card-style mb-4">
                            <div class="card-header">
                                <h5>Комментарий к заказу</h5>
                            </div>
                            <div class="card-body">
                                <p><?= Html::encode($order->note) ?></p>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

