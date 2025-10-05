<?php

namespace repositories\Product;

use repositories\Product\interfaces\BestsellerRepositoryInterface;
use repositories\Product\models\Product;
use repositories\Order\models\OrderItem;
use yii\db\Expression;

/**
 * Репозиторий для работы с хитами продаж
 */
class BestsellerRepository implements BestsellerRepositoryInterface
{
    /**
     * Получить список хитов продаж
     * 
     * @param int $limit Ограничение количества товаров
     * @return Product[] Массив товаров
     */
    public function getBestsellers(int $limit = 20): array
    {
        // Получаем ID товаров, отсортированных по количеству продаж
        $topProductIds = OrderItem::find()
            ->select(['product_id', new Expression('SUM(quantity) as total_sold')])
            ->where(['IS NOT', 'product_id', null])
            ->groupBy('product_id')
            ->orderBy(['total_sold' => SORT_DESC])
            ->limit($limit)
            ->asArray()
            ->all();
        
        // Если нет проданных товаров, возвращаем пустой массив
        if (empty($topProductIds)) {
            return [];
        }
        
        // Извлекаем только ID товаров
        $productIds = array_column($topProductIds, 'product_id');
        
        // Получаем товары по ID и проверяем, что они активны
        $products = Product::find()
            ->where(['id' => $productIds, 'status' => 1])
            ->all();
        
        // Сортируем товары в том же порядке, что и в запросе
        $sortedProducts = [];
        foreach ($productIds as $id) {
            foreach ($products as $product) {
                if ($product->id == $id) {
                    $sortedProducts[] = $product;
                    break;
                }
            }
        }
        
        return $sortedProducts;
    }
}
