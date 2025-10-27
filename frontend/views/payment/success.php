<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $order repositories\Order\models\Order */
/* @var $payment repositories\Payment\models\Payment|null */

$this->title = 'Оплата прошла успешно';

// Подключаем FontAwesome для иконок
$this->registerCssFile('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css');

// Подключаем CSS для страницы успешной оплаты
$this->registerCssFile('@web/css/payment-success.css', ['depends' => [\yii\web\YiiAsset::class]]);
?>

<div class="payment-success">
    <div class="container">
        <div class="payment-success-head">
            <div class="success-icon">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="title">Оплата прошла успешно!</div>
            <div class="subtitle">Спасибо за ваш заказ. Мы уже начали его обработку</div>
        </div>
        
        <div class="payment-success-main">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <!-- Информация о заказе -->
                    <div class="order-summary">
                        <div class="order-summary-header">
                            <h3>Заказ №<?= Html::encode($order->id) ?></h3>
                            <div class="order-status">
                                <span class="status-badge status-paid">Оплачен</span>
                            </div>
                        </div>
                        
                        <div class="order-amount-section">
                            <div class="amount-label">Сумма заказа:</div>
                            <div class="amount-value"><?= number_format($order->total_cost, 0, '.', ' ') ?> ₽</div>
                        </div>
                        
                        <?php if ($payment): ?>
                        <div class="payment-info">
                            <h4>Детали платежа</h4>
                            <div class="payment-details">
                                <div class="payment-detail-item">
                                    <span class="label">ID платежа:</span>
                                    <span class="value"><?= Html::encode($payment->payment_id) ?></span>
                                </div>
                                <div class="payment-detail-item">
                                    <span class="label">Способ оплаты:</span>
                                    <span class="value"><?= Html::encode($payment->getPaymentMethodLabel()) ?></span>
                                </div>
                                <div class="payment-detail-item">
                                    <span class="label">Дата оплаты:</span>
                                    <span class="value"><?= date('d.m.Y H:i', $payment->updated_at) ?></span>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>
                        
                        <div class="customer-info">
                            <h4>Информация о получателе</h4>
                            <div class="customer-details">
                                <div class="customer-detail-item">
                                    <div class="detail-icon">
                                        <i class="fas fa-user"></i>
                                    </div>
                                    <div class="detail-content">
                                        <span class="label">Имя:</span>
                                        <span class="value"><?= Html::encode($order->customer_name) ?></span>
                                    </div>
                                </div>
                                
                                <div class="customer-detail-item">
                                    <div class="detail-icon">
                                        <i class="fas fa-phone"></i>
                                    </div>
                                    <div class="detail-content">
                                        <span class="label">Телефон:</span>
                                        <span class="value"><?= Html::encode($order->customer_phone) ?></span>
                                    </div>
                                </div>
                                
                                <div class="customer-detail-item">
                                    <div class="detail-icon">
                                        <i class="fas fa-envelope"></i>
                                    </div>
                                    <div class="detail-content">
                                        <span class="label">Email:</span>
                                        <span class="value"><?= Html::encode($order->customer_email) ?></span>
                                    </div>
                                </div>
                                
                                <?php if ($order->note): ?>
                                <div class="customer-detail-item">
                                    <div class="detail-icon">
                                        <i class="fas fa-comment"></i>
                                    </div>
                                    <div class="detail-content">
                                        <span class="label">Комментарий:</span>
                                        <span class="value"><?= Html::encode($order->note) ?></span>
                                    </div>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Что дальше -->
                    <div class="next-steps">
                        <div class="steps-header">
                            <h4>Что происходит дальше?</h4>
                        </div>
                        
                        <div class="steps-list">
                            <div class="step-item">
                                <div class="step-number">1</div>
                                <div class="step-content">
                                    <h5>Обработка заказа</h5>
                                    <p>Мы получили ваш платеж и начали подготовку заказа</p>
                                </div>
                            </div>
                            
                            <div class="step-item">
                                <div class="step-number">2</div>
                                <div class="step-content">
                                    <h5>Связь с вами</h5>
                                    <p>Наш менеджер свяжется с вами для уточнения деталей доставки</p>
                                </div>
                            </div>
                            
                            <div class="step-item">
                                <div class="step-number">3</div>
                                <div class="step-content">
                                    <h5>Доставка</h5>
                                    <p>Доставим ваш заказ в указанное время и место</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Действия -->
                    <div class="success-actions">
                        <div class="actions-buttons">
                            <?= Html::a('<i class="fas fa-home"></i> На главную', ['/'], [
                                'class' => 'btn btn-primary btn-lg'
                            ]) ?>
                            
                            <?= Html::a('<i class="fas fa-utensils"></i> Каталог', ['/catalog'], [
                                'class' => 'btn btn-outline-primary btn-lg'
                            ]) ?>
                            
                            <?= Html::a('<i class="fas fa-phone"></i> Связаться с нами', ['/contact'], [
                                'class' => 'btn btn-outline-secondary btn-lg'
                            ]) ?>
                        </div>
                    </div>
                    
                    <!-- Благодарность -->
                    <div class="thank-you-section">
                        <div class="thank-you-icon">
                            <i class="fas fa-heart"></i>
                        </div>
                        <div class="thank-you-content">
                            <h5>Спасибо за выбор Азия Фуд!</h5>
                            <p>Мы ценим ваше доверие и стремимся сделать каждый заказ особенным</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
