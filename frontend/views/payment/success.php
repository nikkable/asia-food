<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $order repositories\Order\models\Order */
/* @var $payment repositories\Payment\models\Payment|null */

$this->title = 'Оплата прошла успешно';
?>

<div class="payment-success">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <div class="card">
                    <div class="card-body text-center">
                        <div class="success-icon mb-4">
                            <i class="fas fa-check-circle text-success" style="font-size: 4rem;"></i>
                        </div>
                        
                        <h1 class="h3 mb-3 text-success">Оплата прошла успешно!</h1>
                        
                        <div class="order-info mb-4">
                            <h4>Заказ №<?= Html::encode($order->id) ?></h4>
                            <p class="text-muted">
                                Сумма: <strong><?= number_format($order->total_cost, 0, '.', ' ') ?> ₽</strong>
                            </p>
                            
                            <?php if ($payment): ?>
                                <div class="payment-details mt-3">
                                    <small class="text-muted">
                                        ID платежа: <?= Html::encode($payment->payment_id) ?><br>
                                        Способ оплаты: <?= Html::encode($payment->getPaymentMethodLabel()) ?><br>
                                        Дата оплаты: <?= date('d.m.Y H:i', $payment->updated_at) ?>
                                    </small>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="customer-info mb-4">
                            <h5>Информация о заказе</h5>
                            <p class="mb-1"><strong>Имя:</strong> <?= Html::encode($order->customer_name) ?></p>
                            <p class="mb-1"><strong>Телефон:</strong> <?= Html::encode($order->customer_phone) ?></p>
                            <p class="mb-1"><strong>Email:</strong> <?= Html::encode($order->customer_email) ?></p>
                            <?php if ($order->note): ?>
                                <p class="mb-1"><strong>Комментарий:</strong> <?= Html::encode($order->note) ?></p>
                            <?php endif; ?>
                        </div>
                        
                        <div class="next-steps">
                            <p class="text-muted mb-3">
                                Мы получили ваш платеж и начали обработку заказа. 
                                В ближайшее время с вами свяжется наш менеджер для уточнения деталей доставки.
                            </p>
                            
                            <div class="d-grid gap-2 d-md-flex justify-content-md-center">
                                <?= Html::a('На главную', ['/'], ['class' => 'btn btn-primary']) ?>
                                <?= Html::a('Каталог', ['/catalog'], ['class' => 'btn btn-outline-primary']) ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.payment-success {
    padding: 2rem 0;
    min-height: 60vh;
    display: flex;
    align-items: center;
}

.success-icon {
    animation: successPulse 2s ease-in-out infinite;
}

@keyframes successPulse {
    0% { transform: scale(1); }
    50% { transform: scale(1.05); }
    100% { transform: scale(1); }
}

.card {
    border: none;
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    border-radius: 1rem;
}

.order-info {
    background-color: #f8f9fa;
    padding: 1rem;
    border-radius: 0.5rem;
}

.payment-details {
    border-top: 1px solid #dee2e6;
    padding-top: 0.5rem;
}

.customer-info {
    text-align: left;
    background-color: #f8f9fa;
    padding: 1rem;
    border-radius: 0.5rem;
}

.next-steps {
    border-top: 1px solid #dee2e6;
    padding-top: 1rem;
}
</style>
