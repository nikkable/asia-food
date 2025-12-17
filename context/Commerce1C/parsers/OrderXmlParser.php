<?php

namespace context\Commerce1C\parsers;

use Yii;

class OrderXmlParser
{
    /**
     * Парсит XML с обновлениями заказов из 1С
     * 
     * @param string $xmlContent
     * @return array Массив с данными заказов
     */
    public function parse(string $xmlContent): array
    {
        $orders = [];
        
        try {
            $xmlContent = str_replace('xmlns="urn:1C.ru:commerceml_2"', '', $xmlContent);
            
            $xml = simplexml_load_string($xmlContent);
            if ($xml === false) {
                Yii::error("Failed to parse orders XML", __METHOD__);
                return [];
            }

            if (!isset($xml->Документ)) {
                Yii::warning("No documents found in orders XML", __METHOD__);
                return [];
            }

            foreach ($xml->Документ as $document) {
                $orderData = $this->parseOrderDocument($document);
                if ($orderData) {
                    $orders[] = $orderData;
                }
            }

            Yii::info("Parsed " . count($orders) . " orders from XML", __METHOD__);
            
        } catch (\Exception $e) {
            Yii::error("Error parsing orders XML: " . $e->getMessage(), __METHOD__);
        }

        return $orders;
    }

    /**
     * Парсит отдельный документ заказа
     */
    private function parseOrderDocument(\SimpleXMLElement $document): ?array
    {
        try {
            $externalId = (string)($document->Ид ?? '');
            $orderNumber = (string)($document->Номер ?? '');
            
            if (empty($externalId) && empty($orderNumber)) {
                Yii::warning("Order without ID or number found", __METHOD__);
                return null;
            }

            $orderData = [
                'external_id' => $externalId,
                'order_number' => $orderNumber,
                'date' => $this->parseDate($document->Дата ?? ''),
                'status' => $this->parseStatus($document->Статус ?? $document->ЗначениеСтатуса ?? ''),
                'comment' => (string)($document->Комментарий ?? ''),
                'sum' => (float)($document->Сумма ?? 0),
            ];

            if (isset($document->ЗначенияРеквизитов)) {
                $orderData['requisites'] = $this->parseRequisites($document->ЗначенияРеквизитов);
            }

            if (isset($document->Товары)) {
                $orderData['items'] = $this->parseOrderItems($document->Товары);
            }

            return $orderData;
            
        } catch (\Exception $e) {
            Yii::error("Error parsing order document: " . $e->getMessage(), __METHOD__);
            return null;
        }
    }

    /**
     * Парсит дату из формата 1С
     */
    private function parseDate(string $dateString): ?int
    {
        if (empty($dateString)) {
            return null;
        }

        try {
            $timestamp = strtotime($dateString);
            return $timestamp !== false ? $timestamp : null;
        } catch (\Exception $e) {
            Yii::warning("Failed to parse date: {$dateString}", __METHOD__);
            return null;
        }
    }

    /**
     * Парсит статус заказа из 1С и преобразует в статус системы
     */
    private function parseStatus($status): ?string
    {
        if (empty($status)) {
            return null;
        }

        $statusString = (string)$status;
        $statusLower = mb_strtolower($statusString);

        return match (true) {
            str_contains($statusLower, 'новый') || str_contains($statusLower, 'new') => 'new',
            str_contains($statusLower, 'обработ') || str_contains($statusLower, 'processing') => 'processing',
            str_contains($statusLower, 'готов') || str_contains($statusLower, 'cooking') => 'cooking',
            str_contains($statusLower, 'выполн') || str_contains($statusLower, 'completed') => 'completed',
            str_contains($statusLower, 'отмен') || str_contains($statusLower, 'cancel') => 'cancelled',
            default => $statusString,
        };
    }

    /**
     * Парсит реквизиты заказа
     */
    private function parseRequisites(\SimpleXMLElement $requisites): array
    {
        $result = [];
        
        if (isset($requisites->ЗначениеРеквизита)) {
            foreach ($requisites->ЗначениеРеквизита as $requisite) {
                $name = (string)($requisite->Наименование ?? '');
                $value = (string)($requisite->Значение ?? '');
                
                if (!empty($name)) {
                    $result[$name] = $value;
                }
            }
        }

        return $result;
    }

    /**
     * Парсит товары заказа
     */
    private function parseOrderItems(\SimpleXMLElement $items): array
    {
        $result = [];
        
        if (isset($items->Товар)) {
            foreach ($items->Товар as $item) {
                $itemData = [
                    'external_id' => (string)($item->Ид ?? ''),
                    'name' => (string)($item->Наименование ?? ''),
                    'quantity' => (float)($item->Количество ?? 0),
                    'price' => (float)($item->ЦенаЗаЕдиницу ?? 0),
                    'sum' => (float)($item->Сумма ?? 0),
                ];
                
                $result[] = $itemData;
            }
        }

        return $result;
    }
}
