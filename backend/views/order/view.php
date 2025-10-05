<?php

use common\helpers\PriceHelper;
use repositories\Order\models\Order;
use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\grid\GridView;
use yii\data\ArrayDataProvider;

/** @var yii\web\View $this */
/** @var repositories\Order\models\Order $model */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Orders', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="order-view">

    <h1>Заказ #<?= Html::encode($model->id) ?></h1>
    
    <div class="row mb-4">
        <div class="col-md-6">
            <p>
                <?= Html::a('Вернуться к списку', ['index'], ['class' => 'btn btn-secondary']) ?>
                <?= Html::a('Редактировать', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
                <?php if ($model->status !== Order::STATUS_CANCELLED): ?>
                    <?= Html::a('Отменить заказ', ['cancel', 'id' => $model->id], [
                        'class' => 'btn btn-danger',
                        'data' => [
                            'confirm' => 'Вы уверены, что хотите отменить этот заказ?',
                            'method' => 'post',
                        ],
                    ]) ?>
                <?php endif; ?>
            </p>
        </div>
        <div class="col-md-6 text-right">
            <div class="order-status-badges">
                <?php
                $orderStatuses = [
                    Order::STATUS_NEW => ['Новый', 'info'],
                    Order::STATUS_PROCESSING => ['В обработке', 'primary'],
                    Order::STATUS_COMPLETED => ['Выполнен', 'success'],
                    Order::STATUS_CANCELLED => ['Отменен', 'danger'],
                ];
                $paymentStatuses = [
                    Order::PAYMENT_STATUS_PENDING => ['Ожидает оплаты', 'warning'],
                    Order::PAYMENT_STATUS_PAID => ['Оплачен', 'success'],
                    Order::PAYMENT_STATUS_FAILED => ['Ошибка оплаты', 'danger'],
                ];
                
                $orderStatus = $orderStatuses[$model->status] ?? ['Неизвестно', 'default'];
                $paymentStatus = $paymentStatuses[$model->payment_status] ?? ['Неизвестно', 'default'];
                ?>
                <span class="badge badge-<?= $orderStatus[1] ?> mr-2">Статус заказа: <?= $orderStatus[0] ?></span>
                <span class="badge badge-<?= $paymentStatus[1] ?>">Статус оплаты: <?= $paymentStatus[0] ?></span>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Информация о заказе</h5>
                </div>
                <div class="card-body">
                    <?= DetailView::widget([
                        'model' => $model,
                        'attributes' => [
                            'id',
                            [
                                'attribute' => 'created_at',
                                'format' => 'datetime',
                            ],
                            [
                                'attribute' => 'status',
                                'format' => 'raw',
                                'value' => function ($model) {
                                    $statuses = [
                                        Order::STATUS_NEW => ['Новый', 'info'],
                                        Order::STATUS_PROCESSING => ['В обработке', 'primary'],
                                        Order::STATUS_COMPLETED => ['Выполнен', 'success'],
                                        Order::STATUS_CANCELLED => ['Отменен', 'danger'],
                                    ];
                                    $status = $statuses[$model->status] ?? ['Неизвестно', 'default'];
                                    return Html::tag('span', $status[0], ['class' => 'badge badge-' . $status[1]]);
                                },
                            ],
                            [
                                'attribute' => 'total_cost',
                                'format' => 'raw',
                                'value' => function ($model) {
                                    return PriceHelper::formatRub($model->total_cost);
                                },
                            ],
                        ],
                    ]) ?>
                    
                    <div class="mt-4">
                        <h6>Изменить статус заказа:</h6>
                        <div class="btn-group">
                            <?php if ($model->status !== Order::STATUS_NEW): ?>
                                <?= Html::a('Новый', ['update-status', 'id' => $model->id, 'status' => Order::STATUS_NEW], [
                                    'class' => 'btn btn-outline-info',
                                    'data' => [
                                        'confirm' => 'Изменить статус заказа на "Новый"?',
                                        'method' => 'post',
                                    ],
                                ]) ?>
                            <?php endif; ?>
                            
                            <?php if ($model->status !== Order::STATUS_PROCESSING): ?>
                                <?= Html::a('В обработке', ['update-status', 'id' => $model->id, 'status' => Order::STATUS_PROCESSING], [
                                    'class' => 'btn btn-outline-primary',
                                    'data' => [
                                        'confirm' => 'Изменить статус заказа на "В обработке"?',
                                        'method' => 'post',
                                    ],
                                ]) ?>
                            <?php endif; ?>
                            
                            <?php if ($model->status !== Order::STATUS_COMPLETED): ?>
                                <?= Html::a('Выполнен', ['update-status', 'id' => $model->id, 'status' => Order::STATUS_COMPLETED], [
                                    'class' => 'btn btn-outline-success',
                                    'data' => [
                                        'confirm' => 'Изменить статус заказа на "Выполнен"?',
                                        'method' => 'post',
                                    ],
                                ]) ?>
                            <?php endif; ?>
                            
                            <?php if ($model->status !== Order::STATUS_CANCELLED): ?>
                                <?= Html::a('Отменен', ['update-status', 'id' => $model->id, 'status' => Order::STATUS_CANCELLED], [
                                    'class' => 'btn btn-outline-danger',
                                    'data' => [
                                        'confirm' => 'Изменить статус заказа на "Отменен"?',
                                        'method' => 'post',
                                    ],
                                ]) ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Информация о клиенте</h5>
                </div>
                <div class="card-body">
                    <?= DetailView::widget([
                        'model' => $model,
                        'attributes' => [
                            'customer_name',
                            'customer_phone',
                            'customer_email:email',
                            [
                                'attribute' => 'user_id',
                                'format' => 'raw',
                                'value' => function ($model) {
                                    return $model->user_id ? Html::a(
                                        $model->user_id,
                                        ['/user/view', 'id' => $model->user_id],
                                        ['target' => '_blank']
                                    ) : '<span class="text-muted">Гость</span>';
                                },
                            ],
                        ],
                    ]) ?>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Информация об оплате</h5>
                </div>
                <div class="card-body">
                    <?= DetailView::widget([
                        'model' => $model,
                        'attributes' => [
                            [
                                'attribute' => 'payment_method',
                                'format' => 'raw',
                                'value' => function ($model) {
                                    $methods = [
                                        Order::PAYMENT_METHOD_CASH => 'Наличными при получении',
                                        Order::PAYMENT_METHOD_CARD => 'Оплата картой онлайн',
                                    ];
                                    return $methods[$model->payment_method] ?? $model->payment_method;
                                },
                            ],
                            [
                                'attribute' => 'payment_status',
                                'format' => 'raw',
                                'value' => function ($model) {
                                    $statuses = [
                                        Order::PAYMENT_STATUS_PENDING => ['Ожидает оплаты', 'warning'],
                                        Order::PAYMENT_STATUS_PAID => ['Оплачен', 'success'],
                                        Order::PAYMENT_STATUS_FAILED => ['Ошибка оплаты', 'danger'],
                                    ];
                                    $status = $statuses[$model->payment_status] ?? ['Неизвестно', 'default'];
                                    return Html::tag('span', $status[0], ['class' => 'badge badge-' . $status[1]]);
                                },
                            ],
                            [
                                'attribute' => 'payment_transaction_id',
                                'format' => 'raw',
                                'value' => function ($model) {
                                    return $model->payment_transaction_id ?: '<span class="text-muted">Нет данных</span>';
                                },
                            ],
                        ],
                    ]) ?>
                    
                    <div class="mt-4">
                        <h6>Изменить статус оплаты:</h6>
                        <div class="btn-group">
                            <?php if ($model->payment_status !== Order::PAYMENT_STATUS_PENDING): ?>
                                <?= Html::a('Ожидает оплаты', ['update-payment-status', 'id' => $model->id, 'status' => Order::PAYMENT_STATUS_PENDING], [
                                    'class' => 'btn btn-outline-warning',
                                    'data' => [
                                        'confirm' => 'Изменить статус оплаты на "Ожидает оплаты"?',
                                        'method' => 'post',
                                    ],
                                ]) ?>
                            <?php endif; ?>
                            
                            <?php if ($model->payment_status !== Order::PAYMENT_STATUS_PAID): ?>
                                <?= Html::a('Оплачен', ['update-payment-status', 'id' => $model->id, 'status' => Order::PAYMENT_STATUS_PAID], [
                                    'class' => 'btn btn-outline-success',
                                    'data' => [
                                        'confirm' => 'Изменить статус оплаты на "Оплачен"?',
                                        'method' => 'post',
                                    ],
                                ]) ?>
                            <?php endif; ?>
                            
                            <?php if ($model->payment_status !== Order::PAYMENT_STATUS_FAILED): ?>
                                <?= Html::a('Ошибка оплаты', ['update-payment-status', 'id' => $model->id, 'status' => Order::PAYMENT_STATUS_FAILED], [
                                    'class' => 'btn btn-outline-danger',
                                    'data' => [
                                        'confirm' => 'Изменить статус оплаты на "Ошибка оплаты"?',
                                        'method' => 'post',
                                    ],
                                ]) ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Комментарий к заказу</h5>
                </div>
                <div class="card-body">
                    <?php if ($model->note): ?>
                        <div class="note-content">
                            <?= nl2br(Html::encode($model->note)) ?>
                        </div>
                    <?php else: ?>
                        <div class="text-muted">Комментарий отсутствует</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Позиции заказа</h5>
        </div>
        <div class="card-body">
            <?php
            $orderItems = $model->orderItems;
            
            if (empty($orderItems)) {
                echo '<div class="alert alert-warning">Позиции заказа не найдены</div>';
            } else {
                $dataProvider = new ArrayDataProvider([
                    'allModels' => $orderItems,
                    'pagination' => false,
                ]);
                
                echo GridView::widget([
                    'dataProvider' => $dataProvider,
                    'summary' => '',
                    'tableOptions' => ['class' => 'table table-striped table-bordered'],
                    'columns' => [
                        ['class' => 'yii\grid\SerialColumn'],
                        [
                            'attribute' => 'product_id',
                            'label' => 'ID товара',
                            'format' => 'raw',
                            'value' => function ($model) {
                                return Html::a(
                                    $model->product_id,
                                    ['/product/view', 'id' => $model->product_id],
                                    ['target' => '_blank']
                                );
                            },
                        ],
                        [
                            'attribute' => 'product_name',
                            'label' => 'Название товара',
                        ],
                        [
                            'attribute' => 'price',
                            'label' => 'Цена',
                            'format' => 'raw',
                            'value' => function ($model) {
                                return PriceHelper::formatRub($model->price);
                            },
                            'contentOptions' => ['class' => 'text-right'],
                        ],
                        [
                            'attribute' => 'quantity',
                            'label' => 'Количество',
                            'contentOptions' => ['class' => 'text-center'],
                        ],
                        [
                            'attribute' => 'cost',
                            'label' => 'Стоимость',
                            'format' => 'raw',
                            'value' => function ($model) {
                                return PriceHelper::formatRub($model->cost);
                            },
                            'contentOptions' => ['class' => 'text-right'],
                        ],
                    ],
                ]);
                
                echo '<div class="text-right mt-3">';
                echo '<h4>Итого: <strong>' . PriceHelper::formatRub($model->total_cost) . '</strong></h4>';
                echo '</div>';
            }
            ?>
        </div>
    </div>

</div>
