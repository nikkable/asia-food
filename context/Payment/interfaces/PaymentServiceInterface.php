<?php

namespace context\Payment\interfaces;

use repositories\Order\models\Order;

/**
 * Интерфейс сервиса оплаты
 */
interface PaymentServiceInterface
{
    /**
     * Инициализация платежа
     * 
     * @param Order $order Заказ
     * @return array Результат инициализации платежа
     */
    public function initPayment(Order $order): array;
    
    /**
     * Обработка уведомления от платежной системы
     * 
     * @param array $data Данные уведомления
     * @return bool Результат обработки
     */
    public function handleCallback(array $data): bool;
    
    /**
     * Проверка статуса платежа
     * 
     * @param string $transactionId ID транзакции
     * @return array Информация о статусе платежа
     */
    public function checkStatus(string $transactionId): array;
}
