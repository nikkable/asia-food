<?php

/** @var yii\web\View $this */
/** @var yii\data\ActiveDataProvider $dataProvider */

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\LinkPager;

$this->title = 'История заказов';

// Подключаем FontAwesome для иконок
$this->registerCssFile('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css');

// Подключаем CSS для личного кабинета
$this->registerCssFile('@web/css/profile-pages.css', ['depends' => [\yii\web\YiiAsset::class]]);
?>

<div class="profile-page">
    <div class="container">
        <div class="profile-page-head">
            <div class="title">История заказов</div>
        </div>
        
        <div class="profile-page-main">
            <div class="orders-history">
                <div class="section-header">
                    <h4>Ваши заказы</h4>
                    <a href="<?= Url::to(['/profile']) ?>" class="back-link">
                        <i class="fas fa-arrow-left"></i> Назад в профиль
                    </a>
                </div>
                
                <?php if ($dataProvider->getTotalCount() > 0): ?>
                    <div class="orders-list-full">
                        <?php foreach ($dataProvider->getModels() as $order): ?>
                        <div class="order-card">
                            <div class="order-card-header">
                                <div class="order-info">
                                    <h5>Заказ №<?= $order->id ?></h5>
                                    <div class="order-meta">
                                        <span class="order-date">
                                            <i class="fas fa-calendar"></i>
                                            <?= date('d.m.Y H:i', $order->created_at) ?>
                                        </span>
                                        <span class="order-amount">
                                            <i class="fas fa-ruble-sign"></i>
                                            <?= number_format($order->total_cost, 0, '.', ' ') ?> ₽
                                        </span>
                                    </div>
                                </div>
                                
                                <div class="order-status-section">
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
                            </div>
                            
                            <div class="order-card-body">
                                <div class="order-details">
                                    <div class="detail-item">
                                        <i class="fas fa-user"></i>
                                        <span><?= Html::encode($order->customer_name) ?></span>
                                    </div>
                                    <div class="detail-item">
                                        <i class="fas fa-phone"></i>
                                        <span><?= Html::encode($order->customer_phone) ?></span>
                                    </div>
                                    <div class="detail-item">
                                        <i class="fas fa-envelope"></i>
                                        <span><?= Html::encode($order->customer_email) ?></span>
                                    </div>
                                    <?php if ($order->note): ?>
                                    <div class="detail-item">
                                        <i class="fas fa-comment"></i>
                                        <span><?= Html::encode($order->note) ?></span>
                                    </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            
                            <div class="order-card-footer">
                                <div class="order-actions">
                                    <a href="<?= Url::to(['/order/view', 'uuid' => $order->uuid]) ?>" class="btn btn-secondary">
                                        <i class="fas fa-eye"></i> Подробнее
                                    </a>
                                    
                                    <?php if ($order->payment_status == 0): ?>
                                        <a href="<?= Url::to(['/payment/pay', 'orderId' => $order->id]) ?>" class="btn btn-three">
                                            <i class="fas fa-credit-card"></i> Оплатить
                                        </a>
                                    <?php endif; ?>
                                    
                                    <?php if ($order->status == 2): ?>
                                        <button class="btn btn-three" onclick="reorderItems(<?= $order->id ?>)">
                                            <i class="fas fa-redo"></i> Повторить заказ
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <!-- Пагинация -->
                    <div class="pagination-wrapper">
                        <?= LinkPager::widget([
                            'pagination' => $dataProvider->pagination,
                            'options' => ['class' => 'pagination justify-content-center'],
                            'linkOptions' => ['class' => 'page-link'],
                            'activePageCssClass' => 'active',
                            'disabledPageCssClass' => 'disabled',
                            'prevPageLabel' => '<i class="fas fa-chevron-left"></i>',
                            'nextPageLabel' => '<i class="fas fa-chevron-right"></i>',
                        ]) ?>
                    </div>
                    
                <?php else: ?>
                    <div class="no-orders">
                        <div class="no-orders-icon">
                            <i class="fas fa-shopping-cart"></i>
                        </div>
                        <h4>У вас пока нет заказов</h4>
                        <p>Самое время сделать первый заказ!</p>
                        <a href="<?= Url::to(['/catalog']) ?>" class="btn btn-primary btn-lg">
                            <i class="fas fa-utensils"></i> Перейти в каталог
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
function reorderItems(orderId) {
    if (confirm('Добавить товары из этого заказа в корзину?')) {
        alert('Функция в разработке');
    }
}
</script>
