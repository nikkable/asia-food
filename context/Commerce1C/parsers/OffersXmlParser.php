<?php

namespace context\Commerce1C\parsers;

use SimpleXMLElement;

class OffersXmlParser
{
    public function parse(string $xmlContent): array
    {
        $xml = new SimpleXMLElement($xmlContent);
        
        // Проверяем структуру XML
        if (!isset($xml->ПакетПредложений)) {
            throw new \InvalidArgumentException('Invalid offers XML structure');
        }

        $offers = [];

        // Парсим предложения
        if (isset($xml->ПакетПредложений->Предложения->Предложение)) {
            foreach ($xml->ПакетПредложений->Предложения->Предложение as $offer) {
                $offerData = [
                    'external_id' => (string)$offer->Ид,
                    'quantity' => 0,
                    'prices' => []
                ];

                // Парсим количество
                if (isset($offer->Количество)) {
                    $offerData['quantity'] = (int)$offer->Количество;
                }

                // Парсим цены
                if (isset($offer->Цены->Цена)) {
                    foreach ($offer->Цены->Цена as $price) {
                        $priceData = [
                            'price_type' => (string)($price->ИдТипаЦены ?? 'base'),
                            'value' => (float)($price->ЦенаЗаЕдиницу ?? 0),
                            'currency' => (string)($price->Валюта ?? 'RUB')
                        ];
                        
                        $offerData['prices'][] = $priceData;
                    }
                }

                // Парсим характеристики товара
                if (isset($offer->ХарактеристикиТовара->ХарактеристикаТовара)) {
                    $offerData['characteristics'] = [];
                    foreach ($offer->ХарактеристикиТовара->ХарактеристикаТовара as $char) {
                        $offerData['characteristics'][] = [
                            'name' => (string)$char->Наименование,
                            'value' => (string)$char->Значение
                        ];
                    }
                }

                $offers[] = $offerData;
            }
        }

        return $offers;
    }
}
