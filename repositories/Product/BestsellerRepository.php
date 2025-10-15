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
     * @return Product[] Массив товаров
     */
    public function getBestsellers(int $limit = 20): array
    {
        $topProductIds = OrderItem::find()
            ->select(['product_id', new Expression('SUM(quantity) as total_sold')])
            ->where(['IS NOT', 'product_id', null])
            ->groupBy('product_id')
            ->orderBy(['total_sold' => SORT_DESC])
            ->limit($limit)
            ->asArray()
            ->all();
        
        if (empty($topProductIds)) {
            return [];
        }
        
        $productIds = array_column($topProductIds, 'product_id');
        
        $products = Product::find()
            ->where(['id' => $productIds, 'status' => 1])
            ->all();
        
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
