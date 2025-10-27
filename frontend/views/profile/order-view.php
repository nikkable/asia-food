<?php

/** @var yii\web\View $this */
/** @var repositories\Order\models\Order $order */

use yii\helpers\Html;
use yii\helpers\Url;
use common\helpers\PriceHelper;

$this->title = 'Заказ №' . $order->id;

// Подключаем FontAwesome для иконок
$this->registerCssFile('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css');

// Подключаем CSS для личного кабинета
$this->registerCssFile('@web/css/profile-pages.css', ['depends' => [\yii\web\YiiAsset::class]]);
?>

<div class="profile-page">
    <div class="container">
        <div class="profile-page-head">
            <div class="profile-icon">
                <i class="fas fa-receipt"></i>
            </div>
            <div class="title">Заказ №<?= $order->id ?></div>
            <div class="subtitle">Детальная информация о заказе</div>
        </div>
        
        <div class="profile-page-main">
            <div class="order-details-container">
                <div class="section-header">
                    <h4>Информация о заказе</h4>
                    <a href="<?= Url::to(['/profile/orders']) ?>" class="back-link">
                        <i class="fas fa-arrow-left"></i> Назад к заказам
                    </a>
                </div>
                
                <div class="row">
                    <!-- Основная информация -->
                    <div class="col-lg-8">
                        <div class="order-info-card">
                            <div class="card-header">
                                <h5><i class="fas fa-info-circle"></i> Основная информация</h5>
                            </div>
                            <div class="card-body">
                                <div class="info-grid">
                                    <div class="info-row">
                                        <span class="info-label">Номер заказа:</span>
                                        <span class="info-value">#<?= $order->id ?></span>
                                    </div>
                                    <div class="info-row">
                                        <span class="info-label">Дата создания:</span>
                                        <span class="info-value"><?= date('d.m.Y H:i', $order->created_at) ?></span>
                                    </div>
                                    <div class="info-row">
                                        <span class="info-label">Статус заказа:</span>
                                        <span class="status-badge status-<?= $order->status ?>">
                                            <?php
                                            switch ($order->status) {
                                                case 0: echo 'Новый'; break;
                                                case 1: echo 'В обработке'; break;
                                                case 2: echo 'Выполнен'; break;
                                                case 3: echo 'Отменен'; break;
                                                default: echo 'Неизвестно';
                                            }
                                            ?>
                                        </span>
                                    </div>
                                    <div class="info-row">
                                        <span class="info-label">Статус оплаты:</span>
                                        <span class="payment-badge payment-<?= $order->payment_status ?>">
                                            <?php
                                            switch ($order->payment_status) {
                                                case 0: echo 'Ожидает оплаты'; break;
                                                case 1: echo 'Оплачен'; break;
                                                case 2: echo 'Ошибка оплаты'; break;
                                                default: echo 'Неизвестно';
                                            }
                                            ?>
                                        </span>
                                    </div>
                                    <div class="info-row">
                                        <span class="info-label">Общая сумма:</span>
                                        <span class="info-value total-amount"><?= PriceHelper::formatRub($order->total_cost) ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Товары в заказе -->
                        <?php if (!empty($order->orderItems)): ?>
                        <div class="order-items-card">
                            <div class="card-header">
                                <h5><i class="fas fa-shopping-bag"></i> Товары в заказе</h5>
                            </div>
                            <div class="card-body">
                                <div class="order-items-list">
                                    <?php foreach ($order->orderItems as $item): ?>
                                    <div class="order-item">
                                        <div class="item-image">
                                            <?php if ($item->product && $item->product->image): ?>
                                                <img src="<?= $item->product->getImageUrl() ?>" 
                                                     alt="<?= Html::encode($item->product_name) ?>">
                                            <?php else: ?>
                                                <div class="no-image">
                                                    <i class="fas fa-image"></i>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                        
                                        <div class="item-info">
                                            <div class="item-name">
                                                <?php if ($item->product): ?>
                                                    <a href="<?= Url::to(['/catalog/product', 'slug' => $item->product->slug]) ?>">
                                                        <?= Html::encode($item->product_name) ?>
                                                    </a>
                                                <?php else: ?>
                                                    <?= Html::encode($item->product_name) ?>
                                                <?php endif; ?>
                                            </div>
                                            <div class="item-details">
                                                <span class="item-price"><?= PriceHelper::formatRub($item->price) ?></span>
                                                <span class="item-quantity">× <?= $item->quantity ?></span>
                                                <span class="item-total"><?= PriceHelper::formatRub($item->price * $item->quantity) ?></span>
                                            </div>
                                        </div>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Боковая панель -->
                    <div class="col-lg-4">
                        <!-- Контактная информация -->
                        <div class="contact-info-card">
                            <div class="card-header">
                                <h5><i class="fas fa-user"></i> Контактная информация</h5>
                            </div>
                            <div class="card-body">
                                <div class="contact-item">
                                    <i class="fas fa-user"></i>
                                    <span><?= Html::encode($order->customer_name) ?></span>
                                </div>
                                <div class="contact-item">
                                    <i class="fas fa-phone"></i>
                                    <span><?= Html::encode($order->customer_phone) ?></span>
                                </div>
                                <?php if ($order->customer_email): ?>
                                <div class="contact-item">
                                    <i class="fas fa-envelope"></i>
                                    <span><?= Html::encode($order->customer_email) ?></span>
                                </div>
                                <?php endif; ?>
                                <?php 
                                // Извлекаем адрес доставки из комментария
                                $deliveryAddress = '';
                                if ($order->note && strpos($order->note, 'Адрес доставки:') !== false) {
                                    $lines = explode("\n", $order->note);
                                    foreach ($lines as $line) {
                                        if (strpos($line, 'Адрес доставки:') !== false) {
                                            $deliveryAddress = trim(str_replace('Адрес доставки:', '', $line));
                                            break;
                                        }
                                    }
                                }
                                ?>
                                <?php if ($deliveryAddress): ?>
                                <div class="contact-item">
                                    <i class="fas fa-map-marker-alt"></i>
                                    <span><?= Html::encode($deliveryAddress) ?></span>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <!-- Действия -->
                        <div class="order-actions-card">
                            <div class="card-header">
                                <h5><i class="fas fa-cogs"></i> Действия</h5>
                            </div>
                            <div class="card-body">
                                <div class="action-buttons">
                                    <?php if ($order->payment_status == 0): ?>
                                    <a href="<?= Url::to(['/payment/pay', 'orderId' => $order->id]) ?>" 
                                       class="btn btn-success btn-block">
                                        <i class="fas fa-credit-card"></i> Оплатить заказ
                                    </a>
                                    <?php endif; ?>
                                    
                                    <?php if ($order->status == 2): ?>
                                    <button class="btn btn-outline-primary btn-block" onclick="reorderItems(<?= $order->id ?>)">
                                        <i class="fas fa-redo"></i> Повторить заказ
                                    </button>
                                    <?php endif; ?>
                                    
                                    <a href="<?= Url::to(['/contact']) ?>" class="btn btn-outline-secondary btn-block">
                                        <i class="fas fa-phone"></i> Связаться с нами
                                    </a>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Комментарий -->
                        <?php 
                        // Убираем адрес доставки из комментария для отображения
                        $displayNote = $order->note;
                        if ($displayNote && strpos($displayNote, 'Адрес доставки:') !== false) {
                            $lines = explode("\n", $displayNote);
                            $filteredLines = [];
                            foreach ($lines as $line) {
                                if (strpos($line, 'Адрес доставки:') === false && trim($line) !== '') {
                                    $filteredLines[] = $line;
                                }
                            }
                            $displayNote = implode("\n", $filteredLines);
                        }
                        ?>
                        <?php if ($displayNote && trim($displayNote)): ?>
                        <div class="order-comment-card">
                            <div class="card-header">
                                <h5><i class="fas fa-comment"></i> Комментарий к заказу</h5>
                            </div>
                            <div class="card-body">
                                <p><?= Html::encode($displayNote) ?></p>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function reorderItems(orderId) {
    if (confirm('Добавить товары из этого заказа в корзину?')) {
        // Здесь можно добавить AJAX запрос для повторного заказа
        alert('Функция в разработке');
    }
}
</script>
