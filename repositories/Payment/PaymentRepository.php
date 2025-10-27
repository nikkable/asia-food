<?php

namespace repositories\Payment;

use repositories\Payment\interfaces\PaymentRepositoryInterface;
use repositories\Payment\models\Payment;

class PaymentRepository implements PaymentRepositoryInterface
{
    public function findById(int $id): ?Payment
    {
        return Payment::findOne($id);
    }
    
    public function findByPaymentId(string $paymentId): ?Payment
    {
        return Payment::findOne(['payment_id' => $paymentId]);
    }
    
    public function findByOrderId(int $orderId): array
    {
        return Payment::find()
            ->where(['order_id' => $orderId])
            ->orderBy(['created_at' => SORT_DESC])
            ->all();
    }
    
    public function save(Payment $payment): bool
    {
        if (!$payment->validate()) {
            return false;
        }
        
        return $payment->save();
    }
    
    public function delete(Payment $payment): bool
    {
        try {
            return $payment->delete() !== false;
        } catch (\Exception $e) {
            return false;
        }
    }
    
    public function findAll(int $limit = 20, int $offset = 0): array
    {
        return Payment::find()
            ->orderBy(['created_at' => SORT_DESC])
            ->limit($limit)
            ->offset($offset)
            ->all();
    }
    
    public function findByStatus(string $status, int $limit = 20, int $offset = 0): array
    {
        return Payment::find()
            ->where(['status' => $status])
            ->orderBy(['created_at' => SORT_DESC])
            ->limit($limit)
            ->offset($offset)
            ->all();
    }
    
    public function count(): int
    {
        return Payment::find()->count();
    }
}
