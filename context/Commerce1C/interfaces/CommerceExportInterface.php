<?php

namespace context\Commerce1C\interfaces;

use repositories\Commerce1C\models\CommerceRequest;
use repositories\Commerce1C\models\CommerceResponse;

interface CommerceExportInterface
{
    /**
     * Инициализация экспорта заказов
     */
    public function initialize(CommerceRequest $request): CommerceResponse;

    /**
     * Получение списка заказов для экспорта
     */
    public function query(CommerceRequest $request): CommerceResponse;

    /**
     * Подтверждение успешного импорта заказов в 1С
     */
    public function success(CommerceRequest $request): CommerceResponse;

    /**
     * Получение заказов, готовых к экспорту
     */
    public function getOrdersForExport(): array;

    /**
     * Генерация XML с заказами
     */
    public function generateOrdersXml(array $orders): string;

    /**
     * Отметка заказов как экспортированных
     */
    public function markOrdersAsExported(array $orderIds, ?string $externalId = null): void;

    /**
     * Отметка заказов с ошибкой экспорта
     */
    public function markOrdersAsError(array $orderIds, string $errorMessage): void;
}
