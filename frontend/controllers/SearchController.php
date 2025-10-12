<?php

namespace frontend\controllers;

use context\Product\interfaces\ProductServiceInterface;
use yii\web\Controller;
use yii\web\Response;
use yii\helpers\Url;
use common\helpers\PriceHelper;

/**
 * Контроллер для поиска товаров
 */
class SearchController extends BaseSeoController
{
    public function __construct(
        $id,
        $module,
        private readonly ProductServiceInterface $productService,
        $config = []
    ) {
        parent::__construct($id, $module, $config);
    }

    /**
     * AJAX поиск товаров по названию
     */
    public function actionIndex(string $q = '') :array
    {
        \Yii::$app->response->format = Response::FORMAT_JSON;
        
        if (empty(trim($q))) {
            return ['products' => []];
        }
        
        $products = $this->productService->searchByName($q, 8);
        $result = [];
        
        foreach ($products as $product) {
            $result[] = [
                'id' => $product->id,
                'name' => $product->name,
                'price' => $product->price,
                'priceFormatted' => PriceHelper::formatRub($product->price),
                'image' => $product->image ? $product->getImageUrl() : '/images/products/default.png',
                'slug' => $product->slug,
                'url' => Url::to(['/catalog/product', 'slug' => $product->slug]),
                'quantity' => $product->quantity,
                'inStock' => $product->quantity > 0
            ];
        }
        
        return ['products' => $result];
    }

    /**
     * Страница результатов поиска
     */
    public function actionResults(string $q = '') :string
    {
        $query = trim($q);
        
        if (empty($query)) {
            return $this->redirect(['/catalog/index']);
        }
        
        $this->setSearchSeoData($query);
        
        $products = $this->productService->searchByName($query, 20);
        
        return $this->render('results', [
            'products' => $products,
            'query' => $query,
        ]);
    }
}
