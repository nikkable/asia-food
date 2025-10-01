<?php

namespace context\Order\services;

use context\AbstractService;
use context\Cart\models\Cart;
use context\Order\interfaces\OrderServiceInterface;
use repositories\Order\interfaces\OrderRepositoryInterface;
use repositories\Order\models\Order;
use repositories\Order\models\OrderItem;
use Yii;

class OrderService extends AbstractService implements OrderServiceInterface
{
    public function __construct(
        private readonly OrderRepositoryInterface $orderRepository
    ) {}

    public function create(Cart $cart, array $customerData): Order
    {
        $order = new Order();
        $order->load($customerData, '');
        $order->user_id = Yii::$app->user->id;
        $order->total_cost = $cart->getTotalCost();

        $transaction = Yii::$app->db->beginTransaction();
        try {
            $this->orderRepository->save($order);

            foreach ($cart->getItems() as $item) {
                $orderItem = new OrderItem();
                $orderItem->order_id = $order->id;
                $orderItem->product_id = $item->getProduct()->id;
                $orderItem->product_name = $item->getProduct()->name;
                $orderItem->price = $item->getPrice();
                $orderItem->quantity = $item->getQuantity();
                $orderItem->cost = $item->getCost();
                if (!$orderItem->save()) {
                    throw new \RuntimeException('Order item saving error.');
                }
            }

            $transaction->commit();
            return $order;
        } catch (\Exception $e) {
            $transaction->rollBack();
            throw $e;
        }
    }
}
