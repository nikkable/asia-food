<?php

namespace frontend\widgets;

use repositories\Product\interfaces\ProductRepositoryInterface;
use yii\base\Widget;
use yii\helpers\Html;

class PopularProductWidget extends Widget
{
    public $slug;
    public $title = 'Популярное';
    public $imagePath = '/images/popular/1.png';

    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;

    public function __construct($config = [])
    {
        $this->productRepository = \Yii::$container->get(ProductRepositoryInterface::class);
        parent::__construct($config);
    }

    public function run()
    {
        if (!$this->slug) {
            return '';
        }

        $product = $this->productRepository->findBySlug($this->slug);
        
        if (!$product) {
            return '';
        }

        return $this->render('popular-product-widget', [
            'product' => $product,
            'title' => $this->title,
            'imagePath' => $this->imagePath,
        ]);
    }
}
