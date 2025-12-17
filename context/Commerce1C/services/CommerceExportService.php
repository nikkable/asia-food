<?php

namespace context\Commerce1C\services;

use context\Commerce1C\interfaces\CommerceExportInterface;
use context\Commerce1C\interfaces\CommerceAuthInterface;
use context\Commerce1C\interfaces\CommerceSessionInterface;
use context\Commerce1C\generators\OrderXmlGenerator;
use context\Commerce1C\parsers\OrderXmlParser;
use repositories\Order\interfaces\OrderRepositoryInterface;
use repositories\Order\models\Order;
use repositories\Commerce1C\models\CommerceRequest;
use repositories\Commerce1C\models\CommerceResponse;
use context\AbstractService;
use Yii;

class CommerceExportService extends AbstractService implements CommerceExportInterface
{
    public function __construct(
        private readonly CommerceAuthInterface $authService,
        private readonly CommerceSessionInterface $sessionService,
        private readonly OrderRepositoryInterface $orderRepository,
        private readonly OrderXmlGenerator $xmlGenerator,
        private readonly OrderXmlParser $orderParser
    ) {}

    public function initialize(CommerceRequest $request): CommerceResponse
    {
        $sessionId = $this->authService->getSessionIdFromRequest();
        
        if (!$sessionId) {
            return CommerceResponse::failure('Session ID required');
        }

        if (!$this->authService->validateSession($sessionId)) {
            return CommerceResponse::failure('Invalid or expired session', 401);
        }

        Yii::info("Commerce export session initialized: {$sessionId}", __METHOD__);

        return CommerceResponse::success("zip=no\nfile_limit=52428800");
    }

    public function query(CommerceRequest $request): CommerceResponse
    {
        $sessionId = $this->authService->getSessionIdFromRequest();
        
        if (!$sessionId || !$this->authService->validateSession($sessionId)) {
            return CommerceResponse::failure('Invalid or expired session', 401);
        }

        try {
            $orders = $this->getOrdersForExport();
            
            if (empty($orders)) {
                Yii::info("No orders found for export", __METHOD__);
                return CommerceResponse::success("success\nНет заказов для экспорта");
            }

            $session = $this->sessionService->getSession($sessionId);
            if (!$session) {
                return CommerceResponse::failure('Invalid session');
            }

            $orderIds = [];
            foreach ($orders as $order) {
                if ($order instanceof Order) {
                    $orderIds[] = $order->id;
                }
            }

            $session->setMetadata('last_exported_order_ids', $orderIds);
            $this->sessionService->saveSession($session);

            $xml = $this->generateOrdersXml($orders);
            
            Yii::info("Generated XML for " . count($orders) . " orders", __METHOD__);
            
            return CommerceResponse::success($xml);
            
        } catch (\Exception $e) {
            Yii::error("Export query failed: " . $e->getMessage(), __METHOD__);
            return CommerceResponse::failure('Export failed: ' . $e->getMessage());
        }
    }

    public function success(CommerceRequest $request): CommerceResponse
    {
        $sessionId = $this->authService->getSessionIdFromRequest();
        
        if (!$sessionId || !$this->authService->validateSession($sessionId)) {
            return CommerceResponse::failure('Invalid or expired session', 401);
        }

        try {
            $session = $this->sessionService->getSession($sessionId);
            if (!$session) {
                return CommerceResponse::failure('Invalid session');
            }

            $orderIds = $session->getMetadataValue('last_exported_order_ids') ?? [];
            
            if (empty($orderIds)) {
                Yii::info("No order IDs stored in session for export confirmation", __METHOD__);
                return CommerceResponse::success("success\nНет заказов для подтверждения");
            }

            // Отмечаем заказы как экспортированные
            $this->markOrdersAsExported($orderIds);
            $session->setMetadata('last_exported_order_ids', []);
            $this->sessionService->saveSession($session);
            
            Yii::info("Marked " . count($orderIds) . " orders as exported", __METHOD__);
            
            return CommerceResponse::success("success\nЗаказы успешно экспортированы");
            
        } catch (\Exception $e) {
            Yii::error("Export success confirmation failed: " . $e->getMessage(), __METHOD__);
            return CommerceResponse::failure('Success confirmation failed: ' . $e->getMessage());
        }
    }

    public function getOrdersForExport(): array
    {
        return $this->orderRepository->findForExport();
    }

    public function generateOrdersXml(array $orders): string
    {
        return $this->xmlGenerator->generate($orders);
    }

    public function markOrdersAsExported(array $orderIds, ?string $externalId = null): void
    {
        $currentTime = time();
        
        foreach ($orderIds as $orderId) {
            $order = $this->orderRepository->findById($orderId);
            if ($order) {
                $order->export_status = Order::EXPORT_STATUS_EXPORTED;
                $order->exported_at = $currentTime;
                
                if ($externalId) {
                    $order->external_id = $externalId;
                }
                
                $this->orderRepository->save($order);
            }
        }
    }

    public function markOrdersAsError(array $orderIds, string $errorMessage): void
    {
        foreach ($orderIds as $orderId) {
            $order = $this->orderRepository->findById($orderId);
            if ($order) {
                $order->export_status = Order::EXPORT_STATUS_ERROR;
                $order->note = empty($order->note) 
                    ? "Ошибка экспорта: {$errorMessage}"
                    : $order->note . "\n\nОшибка экспорта: {$errorMessage}";
                
                $this->orderRepository->save($order);
            }
        }
    }

    /**
     * Парсит ID заказов из запроса
     */
    private function parseOrderIdsFromRequest(CommerceRequest $request): array
    {
        $orderIds = [];
        
        // Пытаемся получить ID заказов из параметров
        $idsParam = $request->getParam('order_ids');
        if ($idsParam) {
            if (is_string($idsParam)) {
                $orderIds = explode(',', $idsParam);
            } elseif (is_array($idsParam)) {
                $orderIds = $idsParam;
            }
        }

        // Альтернативно, можем получить из содержимого запроса
        $content = $request->getContent();
        if (empty($orderIds) && !empty($content)) {
            // Парсим XML или JSON с ID заказов
            if (strpos($content, '<') === 0) {
                $orderIds = $this->parseOrderIdsFromXml($content);
            } else {
                $data = json_decode($content, true);
                if (isset($data['order_ids'])) {
                    $orderIds = $data['order_ids'];
                }
            }
        }

        return array_filter(array_map('intval', $orderIds));
    }

    /**
     * Парсит ID заказов из XML
     */
    private function parseOrderIdsFromXml(string $xml): array
    {
        $orderIds = [];
        
        try {
            $dom = new \DOMDocument();
            $dom->loadXML($xml);
            
            $xpath = new \DOMXPath($dom);
            $documents = $xpath->query('//Документ/Номер');
            
            foreach ($documents as $document) {
                $orderId = (int)$document->textContent;
                if ($orderId > 0) {
                    $orderIds[] = $orderId;
                }
            }
        } catch (\Exception $e) {
            Yii::warning("Failed to parse order IDs from XML: " . $e->getMessage(), __METHOD__);
        }
        
        return $orderIds;
    }

    public function saveFile(CommerceRequest $request): CommerceResponse
    {
        $sessionId = $this->authService->getSessionIdFromRequest();
        
        if (!$sessionId || !$this->authService->validateSession($sessionId)) {
            return CommerceResponse::failure('Invalid or expired session', 401);
        }

        try {
            $filename = $request->getFilename();
            if (empty($filename)) {
                return CommerceResponse::failure('Filename is required');
            }

            $content = $request->getContent();
            if (empty($content)) {
                return CommerceResponse::failure('File content is empty');
            }

            $session = $this->sessionService->getSession($sessionId);
            if (!$session) {
                return CommerceResponse::failure('Invalid session');
            }

            $savedPath = $this->sessionService->saveFile($session, $filename, $content);
            
            if (!$savedPath) {
                return CommerceResponse::failure('Failed to save file');
            }

            Yii::info("Saved orders file: {$filename} ({$savedPath})", __METHOD__);
            
            return CommerceResponse::success("success");
            
        } catch (\Exception $e) {
            Yii::error("Failed to save orders file: " . $e->getMessage(), __METHOD__);
            return CommerceResponse::failure('Failed to save file: ' . $e->getMessage());
        }
    }

    public function importOrders(CommerceRequest $request): CommerceResponse
    {
        $sessionId = $this->authService->getSessionIdFromRequest();
        
        if (!$sessionId || !$this->authService->validateSession($sessionId)) {
            return CommerceResponse::failure('Invalid or expired session', 401);
        }

        try {
            $filename = $request->getFilename();
            
            $session = $this->sessionService->getSession($sessionId);
            if (!$session) {
                return CommerceResponse::failure('Invalid session');
            }

            $file = $session->getFile($filename);
            if (!$file) {
                return CommerceResponse::failure("File not found: {$filename}");
            }

            $xmlContent = file_get_contents($file['path']);
            if ($xmlContent === false) {
                return CommerceResponse::failure("Failed to read file: {$filename}");
            }

            $orders = $this->orderParser->parse($xmlContent);
            
            if (empty($orders)) {
                Yii::info("No orders found in file: {$filename}", __METHOD__);
                return CommerceResponse::success("success\nНет заказов для обновления");
            }

            $updatedCount = $this->updateOrdersFromData($orders);
            
            Yii::info("Updated {$updatedCount} orders from file: {$filename}", __METHOD__);
            
            return CommerceResponse::success("success\nОбновлено заказов: {$updatedCount}");
            
        } catch (\Exception $e) {
            Yii::error("Failed to import orders: " . $e->getMessage(), __METHOD__);
            return CommerceResponse::failure('Import failed: ' . $e->getMessage());
        }
    }

    /**
     * Обновляет заказы на основе данных из 1С
     */
    private function updateOrdersFromData(array $ordersData): int
    {
        $updatedCount = 0;
        
        foreach ($ordersData as $orderData) {
            try {
                $order = null;
                
                if (!empty($orderData['external_id'])) {
                    $order = $this->orderRepository->findByExternalId($orderData['external_id']);
                }
                
                if (!$order && !empty($orderData['order_number'])) {
                    $order = $this->orderRepository->findById((int)$orderData['order_number']);
                }
                
                if (!$order) {
                    Yii::warning("Order not found: " . json_encode([
                        'external_id' => $orderData['external_id'] ?? null,
                        'order_number' => $orderData['order_number'] ?? null
                    ]), __METHOD__);
                    continue;
                }

                $updated = false;

                if (!empty($orderData['external_id']) && $order->external_id !== $orderData['external_id']) {
                    $order->external_id = $orderData['external_id'];
                    $updated = true;
                }

                if (isset($orderData['status'])) {
                    $newStatus = $this->mapStatusToOrderStatus($orderData['status']);
                    if ($newStatus !== null && $order->status !== $newStatus) {
                        $order->status = $newStatus;
                        $updated = true;
                        Yii::info("Order #{$order->id} status changed to: {$newStatus}", __METHOD__);
                    }
                }

                if (!empty($orderData['comment'])) {
                    $commentPrefix = "Комментарий из 1С: ";
                    if (!str_contains($order->note ?? '', $commentPrefix)) {
                        $order->note = trim(($order->note ?? '') . "\n\n" . $commentPrefix . $orderData['comment']);
                        $updated = true;
                    }
                }

                if (isset($orderData['requisites'])) {
                    foreach ($orderData['requisites'] as $name => $value) {
                        if ($name === 'Оплачен' || $name === 'Paid') {
                            $isPaid = mb_strtolower($value) === 'да' || mb_strtolower($value) === 'yes' || $value === '1';
                            if ($isPaid && $order->payment_status !== Order::PAYMENT_STATUS_PAID) {
                                $order->payment_status = Order::PAYMENT_STATUS_PAID;
                                $updated = true;
                                Yii::info("Order #{$order->id} marked as paid from 1C", __METHOD__);
                            }
                        }
                    }
                }

                if ($updated) {
                    $this->orderRepository->save($order);
                    $updatedCount++;
                    Yii::info("Order #{$order->id} updated from 1C", __METHOD__);
                }
                
            } catch (\Exception $e) {
                Yii::error("Failed to update order: " . $e->getMessage() . " Data: " . json_encode($orderData), __METHOD__);
            }
        }
        
        return $updatedCount;
    }

    /**
     * Преобразует статус из 1С в статус заказа системы
     */
    private function mapStatusToOrderStatus(string $status1c): ?int
    {
        return match ($status1c) {
            'new' => Order::STATUS_NEW,
            'processing' => Order::STATUS_PROCESSING,
            'cooking' => Order::STATUS_COOKING,
            'completed' => Order::STATUS_COMPLETED,
            'cancelled' => Order::STATUS_CANCELLED,
            default => null,
        };
    }
}
