<?php

namespace context\Commerce1C\parsers;

class CatalogXmlParser
{
    public function parse(string $xmlContent): array
    {
        $xml = new \SimpleXMLElement($xmlContent);
        
        // Проверяем структуру XML - должен быть Классификатор для каталога
        if (!isset($xml->Классификатор)) {
            throw new \InvalidArgumentException('Invalid catalog XML structure - missing Классификатор');
        }

        $result = [
            'categories' => [],
            'products' => []
        ];

        // Парсим классификатор (категории)
        if (isset($xml->Классификатор->Группы->Группа)) {
            $result['categories'] = $this->parseCategories($xml->Классификатор->Группы->Группа);
        }

        // Парсим товары из групп (в CommerceML товары могут быть в группах)
        if (isset($xml->Классификатор->Группы->Группа)) {
            $result['products'] = array_merge($result['products'], $this->parseProductsFromGroups($xml->Классификатор->Группы->Группа));
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
    
    private function parseProductsFromGroups($groups): array
    {
        $products = [];
        
        foreach ($groups as $group) {
            // Парсим товары в текущей группе
            if (isset($group->Товары->Товар)) {
                $groupProducts = $this->parseProducts($group->Товары->Товар);
                
                // Устанавливаем category_external_id для всех товаров в группе
                foreach ($groupProducts as &$product) {
                    if (!$product['category_external_id']) {
                        $product['category_external_id'] = (string)$group->Ид;
                    }
                }
                
                $products = array_merge($products, $groupProducts);
            }
            
            // Рекурсивно обрабатываем дочерние группы
            if (isset($group->Группы->Группа)) {
                $childProducts = $this->parseProductsFromGroups($group->Группы->Группа);
                $products = array_merge($products, $childProducts);
            }
        }
        
        return $products;
    }
}
