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
        
        // Маппинг полей из формы в модель
        $order->customer_name = $customerData['customerName'] ?? '';
        $order->customer_phone = $customerData['customerPhone'] ?? '';
        $order->customer_email = $customerData['customerEmail'] ?? 'no-email@example.com'; // Значение по умолчанию, если email не указан
        $order->note = isset($customerData['orderComment']) ? $customerData['orderComment'] : '';
        
        // Если есть адрес доставки, добавляем его в примечание
        if (!empty($customerData['deliveryAddress'])) {
            $deliveryInfo = "Адрес доставки: {$customerData['deliveryAddress']}";
            $order->note = empty($order->note) ? $deliveryInfo : $order->note . "\n\n" . $deliveryInfo;
        }
        
        // Устанавливаем способ оплаты
        if (!empty($customerData['paymentMethod'])) {
            $order->payment_method = $customerData['paymentMethod'];
        } else {
            $order->payment_method = Order::PAYMENT_METHOD_CASH; // По умолчанию - наличными
        }
        
        // Устанавливаем статус оплаты
        $order->payment_status = Order::PAYMENT_STATUS_PENDING;
        
        $order->user_id = Yii::$app->user->id;
        $order->total_cost = $cart->getTotalCost();
        $order->status = Order::STATUS_NEW; // Новый заказ

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
