<?php

namespace repositories\Payment\interfaces;

use repositories\Payment\models\Payment;

interface PaymentRepositoryInterface
{
    /**
     * Найти платеж по ID
     */
    public function findById(int $id): ?Payment;
    
    /**
     * Найти платеж по ID платежа в ЮKassa
     */
    public function findByPaymentId(string $paymentId): ?Payment;
    
    /**
     * Найти платежи по ID заказа
     */
    public function findByOrderId(int $orderId): array;
    
    /**
     * Сохранить платеж
     */
    public function save(Payment $payment): bool;
    
    /**
     * Удалить платеж
     */
    public function delete(Payment $payment): bool;
    
    /**
     * Получить все платежи с пагинацией
     */
    public function findAll(int $limit = 20, int $offset = 0): array;
    
    /**
     * Получить платежи по статусу
     */
    public function findByStatus(string $status, int $limit = 20, int $offset = 0): array;
    
    /**
     * Подсчитать общее количество платежей
     */
    public function count(): int;
}
