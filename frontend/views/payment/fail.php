<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $order repositories\Order\models\Order */

$this->title = 'Ошибка оплаты';
?>

<div class="payment-fail">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <div class="card">
                    <div class="card-body text-center">
                        <div class="error-icon mb-4">
                            <i class="fas fa-times-circle text-danger" style="font-size: 4rem;"></i>
                        </div>
                        
                        <h1 class="h3 mb-3 text-danger">Ошибка оплаты</h1>
                        
                        <div class="order-info mb-4">
                            <h4>Заказ №<?= Html::encode($order->id) ?></h4>
                            <p class="text-muted">
                                Сумма: <strong><?= number_format($order->total_cost, 0, '.', ' ') ?> ₽</strong>
                            </p>
                        </div>
                        
                        <div class="error-message mb-4">
                            <p class="text-muted">
                                К сожалению, платеж не был завершен. Это могло произойти по следующим причинам:
                            </p>
                            <ul class="text-start text-muted">
                                <li>Недостаточно средств на карте</li>
                                <li>Операция была отменена</li>
                                <li>Технические проблемы банка</li>
                                <li>Превышено время ожидания</li>
                            </ul>
                        </div>
                        
                        <div class="next-steps">
                            <p class="text-muted mb-3">
                                Вы можете попробовать оплатить заказ еще раз или связаться с нами для помощи.
                            </p>
                            
                            <div class="d-grid gap-2 d-md-flex justify-content-md-center">
                                <?= Html::a('Попробовать еще раз', ['/payment/pay', 'orderId' => $order->id], [
                                    'class' => 'btn btn-primary'
                                ]) ?>
                                <?= Html::a('Связаться с нами', ['/contact'], [
                                    'class' => 'btn btn-outline-primary'
                                ]) ?>
                                <?= Html::a('На главную', ['/'], [
                                    'class' => 'btn btn-outline-secondary'
                                ]) ?>
                            </div>
                        </div>
                        
                        <div class="contact-info mt-4">
                            <small class="text-muted">
                                Если у вас возникли вопросы, свяжитесь с нами:<br>
                                <strong>Телефон:</strong> +7 (XXX) XXX-XX-XX<br>
                                <strong>Email:</strong> support@азия-фуд.рф
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.payment-fail {
    padding: 2rem 0;
    min-height: 60vh;
    display: flex;
    align-items: center;
}

.error-icon {
    animation: errorShake 0.5s ease-in-out;
}

@keyframes errorShake {
    0%, 100% { transform: translateX(0); }
    25% { transform: translateX(-5px); }
    75% { transform: translateX(5px); }
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

.error-message ul {
    max-width: 300px;
    margin: 0 auto;
}

.next-steps {
    border-top: 1px solid #dee2e6;
    padding-top: 1rem;
}

.contact-info {
    border-top: 1px solid #dee2e6;
    padding-top: 1rem;
}
</style>
