<?php

namespace frontend\controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use repositories\Product\interfaces\ProductRepositoryInterface;
use context\Favorite\interfaces\FavoriteServiceInterface;

/**
 * ProductController для отображения товаров
 */
class ProductController extends BaseSeoController
{
    private ProductRepositoryInterface $productRepository;

    public function __construct($id, $module, ProductRepositoryInterface $productRepository, $config = [])
    {
        $this->productRepository = $productRepository;
        parent::__construct($id, $module, $config);
    }

    /**
     * Отображение товара по slug
     */
    public function actionView($slug)
    {
        $product = $this->productRepository->findBySlug($slug);
        
        if (!$product || $product->status !== 1) {
            throw new NotFoundHttpException('Товар не найден.');
        }

        /** @var FavoriteServiceInterface $favoriteService */
        $favoriteService = Yii::$container->get(FavoriteServiceInterface::class);
        $isInFavorites = $favoriteService->isInFavorites($product->id);

        return $this->render('view', [
            'product' => $product,
            'isInFavorites' => $isInFavorites,
        ]);
    }
}
