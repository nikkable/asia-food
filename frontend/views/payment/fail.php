<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $order repositories\Order\models\Order */

$this->title = 'Ошибка оплаты';

// Подключаем FontAwesome для иконок
$this->registerCssFile('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css');

?>

<div class="payment-fail">
    <div class="container">
        <div class="payment-fail-head">
            <div class="error-icon">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <div class="title">Оплата не прошла</div>
            <div class="subtitle">Не переживайте, мы поможем решить проблему</div>
        </div>
        
        <div class="payment-fail-main">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <!-- Информация о заказе -->
                    <div class="order-summary">
                        <div class="order-summary-header">
                            <h3>Заказ <?= Html::encode($order->getNumber()) ?></h3>
                            <div class="order-amount"><?= number_format($order->total_cost, 0, '.', ' ') ?> ₽</div>
                        </div>
                        
                        <div class="order-details">
                            <div class="order-detail-item">
                                <span class="label">Получатель:</span>
                                <span class="value"><?= Html::encode($order->customer_name) ?></span>
                            </div>
                            <div class="order-detail-item">
                                <span class="label">Телефон:</span>
                                <span class="value"><?= Html::encode($order->customer_phone) ?></span>
                            </div>
                            <div class="order-detail-item">
                                <span class="label">Email:</span>
                                <span class="value"><?= Html::encode($order->customer_email) ?></span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Причины ошибки -->
                    <div class="error-reasons">
                        <h4>Возможные причины:</h4>
                        <div class="reasons-list">
                            <div class="reason-item">
                                <div class="reason-icon">
                                    <i class="fas fa-credit-card"></i>
                                </div>
                                <div class="reason-text">
                                    <strong>Недостаточно средств</strong>
                                    <p>На карте недостаточно средств для оплаты</p>
                                </div>
                            </div>
                            
                            <div class="reason-item">
                                <div class="reason-icon">
                                    <i class="fas fa-ban"></i>
                                </div>
                                <div class="reason-text">
                                    <strong>Операция отменена</strong>
                                    <p>Платеж был отменен пользователем или банком</p>
                                </div>
                            </div>
                            
                            <div class="reason-item">
                                <div class="reason-icon">
                                    <i class="fas fa-clock"></i>
                                </div>
                                <div class="reason-text">
                                    <strong>Превышено время</strong>
                                    <p>Время ожидания подтверждения истекло</p>
                                </div>
                            </div>
                            
                            <div class="reason-item">
                                <div class="reason-icon">
                                    <i class="fas fa-exclamation-circle"></i>
                                </div>
                                <div class="reason-text">
                                    <strong>Технические проблемы</strong>
                                    <p>Временные неполадки в работе банка</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Действия -->
                    <div class="payment-actions">
                        <div class="actions-header">
                            <h4>Что делать дальше?</h4>
                            <p>Выберите один из вариантов ниже</p>
                        </div>
                        
                        <div class="actions-buttons">
                            <?= Html::a('<i class="fas fa-redo"></i> Попробовать еще раз', ['/payment/pay', 'orderId' => $order->id], [
                                'class' => 'btn btn-primary btn-lg'
                            ]) ?>
                            
                            <?= Html::a('<i class="fas fa-phone"></i> Связаться с нами', ['/contact'], [
                                'class' => 'btn btn-outline-primary btn-lg'
                            ]) ?>
                            
                            <?= Html::a('<i class="fas fa-home"></i> На главную', ['/'], [
                                'class' => 'btn btn-outline-secondary btn-lg'
                            ]) ?>
                        </div>
                    </div>
                    
                    <!-- Контактная информация -->
                    <div class="help-section">
                        <div class="help-icon">
                            <i class="fas fa-headset"></i>
                        </div>
                        <div class="help-content">
                            <h5>Нужна помощь?</h5>
                            <p>Наша служба поддержки готова помочь вам 24/7</p>
                            <div class="help-contacts">
                                <a href="tel:+79228111503" class="help-contact">
                                    <i class="fas fa-phone"></i>
                                    +7 922 811 15 03
                                </a>
                                <a href="mailto:albekovn@bk.ru" class="help-contact">
                                    <i class="fas fa-envelope"></i>
                                    albekovn@bk.ru
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
