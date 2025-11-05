<?php

/** @var yii\web\View $this */
/** @var common\models\User $user */
/** @var repositories\Order\models\Order[] $recentOrders */
/** @var int $totalOrders */
/** @var float $totalSpent */

use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'Личный кабинет';

// Подключаем FontAwesome для иконок
$this->registerCssFile('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css');
?>

<div class="profile-page">
    <div class="container">
        <div class="profile-page-head">
            <div class="title">Профиль</div>
        </div>
        
        <div class="profile-page-main">
            <div class="row">
                <!-- Статистика пользователя -->
                <div class="col-lg-4 mb-4">
                    <div class="profile-stats">
                        <div class="profile-stats-title">Ваша статистика</div>
                        
                        <div class="profile-stats-item">
                            <div class="profile-stats-item-icon">
                                <i class="fas fa-shopping-bag"></i>
                            </div>
                            <div class="profile-stats-item-content">
                                <div class="profile-stats-item-number"><?= $totalOrders ?></div>
                                <div class="profile-stats-item-label">Всего заказов</div>
                            </div>
                        </div>
                        
                        <div class="profile-stats-item">
                            <div class="profile-stats-item-icon">
                                <i class="fas fa-ruble-sign"></i>
                            </div>
                            <div class="profile-stats-item-content">
                                <div class="profile-stats-item-number"><?= number_format($totalSpent, 0, '.', ' ') ?> ₽</div>
                                <div class="profile-stats-item-label">Потрачено</div>
                            </div>
                        </div>
                        
                        <div class="profile-stats-item">
                            <div class="profile-stats-item-icon">
                                <i class="fas fa-calendar-alt"></i>
                            </div>
                            <div class="profile-stats-item-content">
                                <div class="profile-stats-item-number"><?= date('d.m.Y', $user->created_at) ?></div>
                                <div class="profile-stats-item-label">Дата регистрации</div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Быстрые действия -->
                <div class="col-lg-8 mb-4">
                    <div class="profile-actions">
                        <div class="profile-actions-title">Быстрые действия</div>
                        
                        <div class="profile-actions-items">
                            <a href="<?= Url::to(['/profile/edit']) ?>" class="profile-actions-item">
                                <div class="profile-actions-item-icon">
                                    <i class="fas fa-user-edit"></i>
                                </div>
                                <div class="profile-actions-item-content">
                                    <div class="profile-actions-item-title">Редактировать</div>
                                    <div class="profile-actions-item-desc">Изменить личные данные</div>
                                </div>
                            </a>
                            
                            <a href="<?= Url::to(['/profile/orders']) ?>" class="profile-actions-item">
                                <div class="profile-actions-item-icon">
                                    <i class="fas fa-history"></i>
                                </div>
                                <div class="profile-actions-item-content">
                                    <div class="profile-actions-item-title">История заказов</div>
                                    <div class="profile-actions-item-desc">Посмотреть все заказы</div>
                                </div>
                            </a>
                            
                            <a href="<?= Url::to(['/catalog']) ?>" class="profile-actions-item">
                                <div class="profile-actions-item-icon">
                                    <i class="fas fa-utensils"></i>
                                </div>
                                <div class="profile-actions-item-content">
                                    <div class="profile-actions-item-title">Каталог</div>
                                    <div class="profile-actions-item-desc">Сделать новый заказ</div>
                                </div>
                            </a>
                            
                            <a href="<?= Url::to(['/contact']) ?>" class="profile-actions-item">
                                <div class="profile-actions-item-icon">
                                    <i class="fas fa-phone"></i>
                                </div>
                                <div class="profile-actions-item-content">
                                    <div class="profile-actions-item-title">Поддержка</div>
                                    <div class="profile-actions-item-desc">Связаться с нами</div>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Последние заказы -->
            <?php if (!empty($recentOrders)): ?>
            <div class="recent-orders">
                <div class="section-header">
                    <h4>Последние заказы</h4>
                    <a href="<?= Url::to(['/profile/orders']) ?>" class="but but-link">
                        Посмотреть все <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
                
                <div class="orders-list">
                    <?php foreach ($recentOrders as $order): ?>
                    <div class="order-item">
                        <div class="order-info">
                            <div class="order-number">
                                <strong>Заказ №<?= $order->id ?></strong>
                            </div>
                            <div class="order-date">
                                <?= date('d.m.Y H:i', $order->created_at) ?>
                            </div>
                        </div>
                        
                        <div class="order-status">
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
                        
                        <div class="order-amount">
                            <strong><?= number_format($order->total_cost, 0, '.', ' ') ?> ₽</strong>
                        </div>
                        
                        <div class="order-actions">
                            <a href="<?= Url::to(['/order/view', 'uuid' => $order->uuid]) ?>" class="but but-secondary">
                                <i class="fas fa-eye"></i> Подробнее
                            </a>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php else: ?>
            <div class="no-orders">
                <div class="no-orders-icon">
                    <i class="fas fa-shopping-cart"></i>
                </div>
                <h4>У вас пока нет заказов</h4>
                <p>Самое время сделать первый заказ!</p>
                <a href="<?= Url::to(['/catalog']) ?>" class="but but-primary">
                    <i class="fas fa-utensils"></i> Перейти в каталог
                </a>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>
