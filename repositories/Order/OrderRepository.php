<?php

namespace repositories\Order;

use repositories\Order\interfaces\OrderRepositoryInterface;
use repositories\Order\models\Order;
use yii\db\Exception;
use yii\db\Expression;

class OrderRepository implements OrderRepositoryInterface
{
    /**
     * @throws Exception
     */
    public function save(Order $order): void
    {
        if (!$order->save()) {
            $errors = json_encode($order->getErrors());
            throw new \RuntimeException("Saving error. Details: {$errors}");
        }
    }
    
    public function findById(int $id): ?Order
    {
        return Order::find()
            ->where(['id' => $id])
            ->with('orderItems')
            ->one();
    }
    
    public function findByUuid(string $uuid): ?Order
    {
        return Order::find()
            ->where(['uuid' => $uuid])
            ->with('orderItems')
            ->one();
    }
    
    public function findByExternalId(string $externalId): ?Order
    {
        return Order::find()
            ->where(['external_id' => $externalId])
            ->with('orderItems')
            ->one();
    }
    
    public function findByExportStatus(int $exportStatus): array
    {
        return Order::find()
            ->where(['export_status' => $exportStatus])
            ->with(['orderItems.product'])
            ->all();
    }

    public function findForExport(): array
    {
        return Order::find()
            ->where(['export_status' => Order::EXPORT_STATUS_NOT_EXPORTED])
            ->orWhere([
                'and',
                ['export_status' => Order::EXPORT_STATUS_EXPORTED],
                ['>', 'updated_at', new Expression('exported_at')]
            ])
            ->with(['orderItems.product'])
            ->all();
    }
}
