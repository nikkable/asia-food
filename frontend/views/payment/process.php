<?php

use yii\helpers\Html;
use yii\helpers\Url;
use common\helpers\PriceHelper;

/** @var repositories\Order\models\Order $order */
/** @var string $transaction_id */

$this->title = 'Оплата заказа ' . $order->getNumber();
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="payment-process">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0">Оплата заказа <?= Html::encode($order->getNumber()) ?></h4>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info">
                            <p><strong>Это демонстрационная страница оплаты.</strong> В реальном проекте здесь был бы интерфейс платежной системы.</p>
                        </div>
                        
                        <div class="mb-4">
                            <h5>Информация о заказе:</h5>
                            <p><strong>Номер заказа:</strong> <?= Html::encode($order->getNumber()) ?></p>
                            <p><strong>Сумма к оплате:</strong> <?= PriceHelper::formatRub($order->total_cost) ?></p>
                            <p><strong>ID транзакции:</strong> <?= Html::encode($transaction_id) ?></p>
                        </div>
                        
                        <div class="mb-4">
                            <h5>Данные карты:</h5>
                            <div class="row g-3">
                                <div class="col-12">
                                    <label class="form-label">Номер карты</label>
                                    <input type="text" class="form-control" value="4242 4242 4242 4242" readonly>
                                    <div class="form-text">Это демо-номер карты, в реальном проекте здесь будет форма ввода данных карты.</div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Срок действия</label>
                                    <input type="text" class="form-control" value="12/25" readonly>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">CVV</label>
                                    <input type="text" class="form-control" value="123" readonly>
                                </div>
                            </div>
                        </div>
                        
                        <div class="d-flex justify-content-between">
                            <a href="<?= Url::to(['payment/fail', 'transaction_id' => $transaction_id]) ?>" class="btn btn-danger">
                                Отменить платеж
                            </a>
                            <a href="<?= Url::to(['payment/success', 'transaction_id' => $transaction_id]) ?>" class="btn btn-success">
                                Оплатить <?= PriceHelper::formatRub($order->total_cost) ?>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
