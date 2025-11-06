<?php

namespace frontend\widgets;

use repositories\Product\interfaces\ProductRepositoryInterface;
use yii\base\Widget;

class PopularProductWidget extends Widget
{
    public string $slug = 'sous-poke-tamaki-047-ml6stup';
    public string $title = 'Популярное';

    public function __construct(
        private readonly ProductRepositoryInterface $productRepository,
        $config = []
    )
    {
        parent::__construct($config);
    }

    public function run()
    {
        if (!$this->slug) {
            return '';
        }

        $product = $this->productRepository->findBySlug($this->slug);
        
        if (!$product || $product->quantity <= 0) {
            return '';
        }

        return $this->render('popular-product-widget', [
            'product' => $product,
            'title' => $this->title,
        ]);
    }
}
