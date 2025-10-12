<?php

namespace context\Commerce1C\parsers;

class CatalogXmlParser
{
    public function parse(string $xmlContent): array
    {
        $xml = new \SimpleXMLElement($xmlContent);
        
        // Проверяем структуру XML
        if (!isset($xml->Каталог)) {
            throw new \InvalidArgumentException('Invalid catalog XML structure');
        }

        $result = [
            'categories' => [],
            'products' => []
        ];

        // Парсим классификатор (категории)
        if (isset($xml->Классификатор->Группы->Группа)) {
            $result['categories'] = $this->parseCategories($xml->Классификатор->Группы->Группа);
        }

        // Парсим товары
        if (isset($xml->Каталог->Товары->Товар)) {
            $result['products'] = $this->parseProducts($xml->Каталог->Товары->Товар);
        }

        return $result;
    }

    private function parseCategories($groups): array
    {
        $categories = [];

        foreach ($groups as $group) {
            $category = [
                'external_id' => (string)$group->Ид,
                'name' => (string)$group->Наименование,
                'description' => (string)($group->Описание ?? ''),
                'parent_external_id' => null
            ];

            $categories[] = $category;

            // Рекурсивно обрабатываем дочерние группы
            if (isset($group->Группы->Группа)) {
                $childCategories = $this->parseCategories($group->Группы->Группа);
                
                // Устанавливаем родительский ID для дочерних категорий
                foreach ($childCategories as &$childCategory) {
                    if ($childCategory['parent_external_id'] === null) {
                        $childCategory['parent_external_id'] = $category['external_id'];
                    }
                }
                
                $categories = array_merge($categories, $childCategories);
            }
        }

        return $categories;
    }

    private function parseProducts($products): array
    {
        $result = [];

        foreach ($products as $product) {
            $item = [
                'external_id' => (string)$product->Ид,
                'name' => (string)$product->Наименование,
                'description' => (string)($product->Описание ?? ''),
                'article' => (string)($product->Артикул ?? ''),
                'category_external_id' => null
            ];

            // Получаем группы товара (категории)
            if (isset($product->Группы->Ид)) {
                $item['category_external_id'] = (string)$product->Группы->Ид[0];
            }

            // Получаем изображения
            if (isset($product->Картинка)) {
                $item['images'] = [];
                foreach ($product->Картинка as $image) {
                    $item['images'][] = (string)$image;
                }
            }

            $result[] = $item;
        }

        return $result;
    }
}
