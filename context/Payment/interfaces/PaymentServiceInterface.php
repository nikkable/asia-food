<?php

namespace context\Payment\interfaces;

use repositories\Order\models\Order;

/**
 * Интерфейс сервиса оплаты
 */
interface PaymentServiceInterface
{
    public function initPayment(Order $order): array;
    
    public function handleCallback(array $data): bool;
    
    public function checkStatus(string $transactionId): array;
}
