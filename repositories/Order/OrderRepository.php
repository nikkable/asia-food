<?php

namespace repositories\Order;

use repositories\Order\interfaces\OrderRepositoryInterface;
use repositories\Order\models\Order;
use yii\db\Exception;

class OrderRepository implements OrderRepositoryInterface
{
    /**
     * @throws Exception
     */
    public function save(Order $order): void
    {
        if (!$order->save()) {
            $errors = json_encode($order->getErrors());
            \Yii::error("Ошибка сохранения заказа: {$errors}", 'order');
            throw new \RuntimeException("Saving error. Details: {$errors}");
        }
    }
}
