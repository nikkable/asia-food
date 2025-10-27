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

// Подключаем CSS для личного кабинета
$this->registerCssFile('@web/css/profile-pages.css', ['depends' => [\yii\web\YiiAsset::class]]);
?>

<div class="profile-page">
    <div class="container">
        <div class="profile-page-head">
            <div class="profile-icon">
                <i class="fas fa-user-circle"></i>
            </div>
            <div class="title">Добро пожаловать, <?= Html::encode($user->getDisplayName()) ?>!</div>
            <div class="subtitle">Управляйте своим профилем и заказами</div>
        </div>
        
        <div class="profile-page-main">
            <div class="row">
                <!-- Статистика пользователя -->
                <div class="col-lg-4 mb-4">
                    <div class="profile-stats">
                        <h4>Ваша статистика</h4>
                        
                        <div class="stat-item">
                            <div class="stat-icon">
                                <i class="fas fa-shopping-bag"></i>
                            </div>
                            <div class="stat-content">
                                <div class="stat-number"><?= $totalOrders ?></div>
                                <div class="stat-label">Всего заказов</div>
                            </div>
                        </div>
                        
                        <div class="stat-item">
                            <div class="stat-icon">
                                <i class="fas fa-ruble-sign"></i>
                            </div>
                            <div class="stat-content">
                                <div class="stat-number"><?= number_format($totalSpent, 0, '.', ' ') ?> ₽</div>
                                <div class="stat-label">Потрачено</div>
                            </div>
                        </div>
                        
                        <div class="stat-item">
                            <div class="stat-icon">
                                <i class="fas fa-calendar-alt"></i>
                            </div>
                            <div class="stat-content">
                                <div class="stat-number"><?= date('d.m.Y', $user->created_at) ?></div>
                                <div class="stat-label">Дата регистрации</div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Быстрые действия -->
                <div class="col-lg-8 mb-4">
                    <div class="profile-actions">
                        <h4>Быстрые действия</h4>
                        
                        <div class="actions-grid">
                            <a href="<?= Url::to(['/profile/edit']) ?>" class="action-item">
                                <div class="action-icon">
                                    <i class="fas fa-user-edit"></i>
                                </div>
                                <div class="action-content">
                                    <h5>Редактировать профиль</h5>
                                    <p>Изменить личные данные</p>
                                </div>
                            </a>
                            
                            <a href="<?= Url::to(['/profile/orders']) ?>" class="action-item">
                                <div class="action-icon">
                                    <i class="fas fa-history"></i>
                                </div>
                                <div class="action-content">
                                    <h5>История заказов</h5>
                                    <p>Посмотреть все заказы</p>
                                </div>
                            </a>
                            
                            <a href="<?= Url::to(['/catalog']) ?>" class="action-item">
                                <div class="action-icon">
                                    <i class="fas fa-utensils"></i>
                                </div>
                                <div class="action-content">
                                    <h5>Каталог</h5>
                                    <p>Сделать новый заказ</p>
                                </div>
                            </a>
                            
                            <a href="<?= Url::to(['/contact']) ?>" class="action-item">
                                <div class="action-icon">
                                    <i class="fas fa-phone"></i>
                                </div>
                                <div class="action-content">
                                    <h5>Поддержка</h5>
                                    <p>Связаться с нами</p>
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
                    <a href="<?= Url::to(['/profile/orders']) ?>" class="view-all-link">
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
                            <a href="<?= Url::to(['/profile/order-view', 'id' => $order->id]) ?>" class="btn btn-sm btn-outline-primary">
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
                <a href="<?= Url::to(['/catalog']) ?>" class="btn btn-primary btn-lg">
                    <i class="fas fa-utensils"></i> Перейти в каталог
                </a>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>
