<?php

use common\helpers\PriceHelper;
use repositories\Order\models\Order;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;

/** @var yii\web\View $this */
/** @var backend\models\search\OrderSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Заказы';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="order-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Создать заказ', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

//            'id',
            [
                'header' => 'Номер',
                'format' => 'raw',
                'value' => function (Order $model) {
                    return $model->getNumber();
                },
            ],
            [
                'attribute' => 'customer_name',
                'header' => 'Имя',
                'format' => 'raw',
                'value' => function (Order $model) {
                    return Html::a(
                        Html::encode($model->customer_name),
                        ['view', 'id' => $model->id],
                        ['data-pjax' => 0]
                    );
                },
            ],
            [
                    'header' => 'Телефон',
                    'attribute' => 'customer_phone',
            ],
            [
                'header' => 'Email',
                'attribute' => 'customer_email',
            ],

            [
                'header' => 'Итоговая цена',
                'attribute' => 'total_cost',
                'format' => 'raw',
                'value' => function (Order $model) {
                    return PriceHelper::formatRub($model->total_cost);
                },
                'contentOptions' => ['class' => 'text-right'],
            ],
            [
                'header' => 'Способ оплаты',
                'attribute' => 'payment_method',
                'filter' => [
                    Order::PAYMENT_METHOD_CASH => 'Наличными',
                    Order::PAYMENT_METHOD_CARD => 'Картой',
                ],
                'value' => function (Order $model) {
                    return $model->payment_method === Order::PAYMENT_METHOD_CARD ? 'Картой' : 'Наличными';
                },
            ],
            [
                'header' => 'Статус оплаты',
                'attribute' => 'payment_status',
                'filter' => Order::getPaymentStatusLabels(),
                'format' => 'raw',
                'value' => function (Order $model) {
                    $statuses = Order::getPaymentStatusBadgeMap();
                    $status = $statuses[$model->payment_status] ?? ['Неизвестно', 'default'];
                    return Html::tag('span', $status[0], ['class' => 'badge text-bg-' . $status[1]]);
                },
            ],
            [
                'header' => 'Статус заказа',
                'attribute' => 'status',
                'filter' => Order::getOrderStatusLabels(),
                'format' => 'raw',
                'value' => function (Order $model) {
                    $statuses = Order::getOrderStatusBadgeMap();
                    $status = $statuses[$model->status] ?? ['Неизвестно', 'default'];
                    return Html::tag('span', $status[0], ['class' => 'badge text-bg-' . $status[1]]);
                },
            ],
            [
                'header' => 'Время создания',
                'attribute' => 'created_at',
                'format' => 'datetime',
            ],
            [
                'class' => ActionColumn::className(),
                'template' => '{view} {update}',
                'urlCreator' => function ($action, Order $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'id' => $model->id]);
                 }
            ],
        ],
    ]); ?>


</div>
