<?php

namespace context\Commerce1C\services;

use context\Commerce1C\interfaces\CommerceExportInterface;
use context\Commerce1C\interfaces\CommerceAuthInterface;
use context\Commerce1C\generators\OrderXmlGenerator;
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
        private readonly OrderRepositoryInterface $orderRepository,
        private readonly OrderXmlGenerator $xmlGenerator
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
            // Получаем список ID заказов из параметров запроса
            $orderIds = $this->parseOrderIdsFromRequest($request);
            
            if (empty($orderIds)) {
                return CommerceResponse::failure('No order IDs provided');
            }

            // Отмечаем заказы как экспортированные
            $this->markOrdersAsExported($orderIds);
            
            Yii::info("Marked " . count($orderIds) . " orders as exported", __METHOD__);
            
            return CommerceResponse::success("success\nЗаказы успешно экспортированы");
            
        } catch (\Exception $e) {
            Yii::error("Export success confirmation failed: " . $e->getMessage(), __METHOD__);
            return CommerceResponse::failure('Success confirmation failed: ' . $e->getMessage());
        }
    }

    public function getOrdersForExport(): array
    {
        // Получаем заказы, которые еще не экспортированы
        return $this->orderRepository->findByExportStatus(Order::EXPORT_STATUS_NOT_EXPORTED);
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
}
