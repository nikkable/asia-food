<?php

namespace context\Order\services;

use context\AbstractService;
use context\Cart\models\Cart;
use context\Order\interfaces\OrderServiceInterface;
use context\Notification\NotificationEvents;
use context\Notification\events\OrderEvent;
use repositories\Order\interfaces\OrderRepositoryInterface;
use repositories\Order\models\Order;
use repositories\Order\models\OrderItem;
use Yii;
use yii\db\Exception;

class OrderService extends AbstractService implements OrderServiceInterface
{
    public function __construct(
        private readonly OrderRepositoryInterface $orderRepository
    ) {}

    public function findOrderByUuid(string $uuid): ?Order
    {
        return $this->orderRepository->findByUuid($uuid);
    }

    public function prepareViewData(Order $order): array
    {
        return [
            'statusInfo' => Order::getStatusInfo($order->status),
            'paymentStatusInfo' => Order::getPaymentStatusInfo($order->payment_status),
            'paymentMethodText' => Order::getPaymentMethodText($order->payment_method),
        ];
    }

    /**
     * @throws Exception
     */
    public function create(Cart $cart, array $customerData): Order
    {
        $order = new Order();
        
        $order->customer_name = $customerData['customerName'] ?? '';
        $order->customer_phone = $customerData['customerPhone'] ?? '';
        $order->customer_email = $customerData['customerEmail'] ?? 'no-email@example.com';
        $order->note = $customerData['orderComment'] ?? '';
        
        if (!empty($customerData['deliveryAddress'])) {
            $deliveryInfo = "Адрес доставки: {$customerData['deliveryAddress']}";
            $order->note = empty($order->note) ? $deliveryInfo : $order->note . "\n\n" . $deliveryInfo;
        }
        
        if (!empty($customerData['paymentMethod'])) {
            $order->payment_method = $customerData['paymentMethod'];
        } else {
            $order->payment_method = Order::PAYMENT_METHOD_CASH;
        }
        
        $order->payment_status = Order::PAYMENT_STATUS_PENDING;
        
        $order->user_id = Yii::$app->user->id;
        $order->total_cost = $cart->getTotalCost();
        $order->status = Order::STATUS_NEW;

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
            Yii::$app->trigger(NotificationEvents::ORDER_CREATED, new OrderEvent(['order' => $order]));
            return $order;
        } catch (\Exception $e) {
            $transaction->rollBack();
            throw $e;
        }
    }
}
