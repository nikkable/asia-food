<?php

namespace context\Payment\services;

use context\AbstractService;
use context\Payment\interfaces\PaymentServiceInterface;
use repositories\Order\interfaces\OrderRepositoryInterface;
use repositories\Order\models\Order;
use Yii;

/**
 * Заглушка сервиса оплаты для тестирования
 */
class MockPaymentService extends AbstractService implements PaymentServiceInterface
{
    private $orderRepository;
    
    public function __construct(OrderRepositoryInterface $orderRepository)
    {
        $this->orderRepository = $orderRepository;
    }
    
    /**
     * {@inheritdoc}
     */
    public function initPayment(Order $order): array
    {
        // Генерируем уникальный ID транзакции
        $transactionId = 'MOCK_' . time() . '_' . $order->id;
        
        // Обновляем заказ с ID транзакции
        $order->payment_transaction_id = $transactionId;
        $order->payment_status = Order::PAYMENT_STATUS_PENDING;
        $this->orderRepository->save($order);
        
        // В реальном эквайринге здесь был бы запрос к платежному шлюзу
        // и получение URL для редиректа на страницу оплаты
        
        // Формируем URL для страницы оплаты
        $paymentUrl = Yii::$app->urlManager->createAbsoluteUrl([
            '/payment/process', 
            'transaction_id' => $transactionId
        ]);
        
        // Возвращаем данные для перенаправления на страницу оплаты
        return [
            'success' => true,
            'transaction_id' => $transactionId,
            'payment_url' => $paymentUrl,
            'message' => 'Платеж инициализирован'
        ];
    }
    
    /**
     * {@inheritdoc}
     */
    public function handleCallback(array $data): bool
    {
        // Проверяем наличие необходимых параметров
        if (!isset($data['transaction_id']) || !isset($data['status'])) {
            Yii::error('Неверные данные в уведомлении платежной системы: ' . json_encode($data), 'payment');
            return false;
        }
        
        // Находим заказ по ID транзакции
        $order = Order::findOne(['payment_transaction_id' => $data['transaction_id']]);
        if (!$order) {
            Yii::error('Заказ с transaction_id ' . $data['transaction_id'] . ' не найден', 'payment');
            return false;
        }
        
        // Обновляем статус оплаты
        if ($data['status'] === 'success') {
            $order->payment_status = Order::PAYMENT_STATUS_PAID;
            $this->orderRepository->save($order);
            return true;
        } elseif ($data['status'] === 'failed') {
            $order->payment_status = Order::PAYMENT_STATUS_FAILED;
            $this->orderRepository->save($order);
            return true;
        }
        
        return false;
    }
    
    /**
     * {@inheritdoc}
     */
    public function checkStatus(string $transactionId): array
    {
        // Находим заказ по ID транзакции
        $order = Order::findOne(['payment_transaction_id' => $transactionId]);
        if (!$order) {
            return [
                'success' => false,
                'message' => 'Транзакция не найдена'
            ];
        }
        
        // Для заглушки просто возвращаем текущий статус
        $statusText = 'Неизвестный статус';
        switch ($order->payment_status) {
            case Order::PAYMENT_STATUS_PENDING:
                $statusText = 'Ожидает оплаты';
                break;
            case Order::PAYMENT_STATUS_PAID:
                $statusText = 'Оплачен';
                break;
            case Order::PAYMENT_STATUS_FAILED:
                $statusText = 'Ошибка оплаты';
                break;
        }
        
        return [
            'success' => true,
            'transaction_id' => $transactionId,
            'status' => $order->payment_status,
            'status_text' => $statusText,
            'order_id' => $order->id
        ];
    }
}
