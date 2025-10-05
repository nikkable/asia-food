<?php

namespace frontend\controllers;

use yii\web\Controller;
use yii\web\NotFoundHttpException;
use repositories\Category\interfaces\CategoryRepositoryInterface;
use repositories\Product\interfaces\ProductRepositoryInterface;

/**
 * CategoryController для отображения категорий и товаров в них
 */
class CategoryController extends Controller
{
    /**
     * Отображение категории и товаров в ней с пагинацией
     * 
     * @param string $slug
     * @param int $page Номер текущей страницы
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionView($slug, $page = 1)
    {
        $categoryRepository = \Yii::$container->get(CategoryRepositoryInterface::class);
        $productRepository = \Yii::$container->get(ProductRepositoryInterface::class);
        
        $category = $categoryRepository->findBySlug($slug);
        
        if (!$category) {
            throw new NotFoundHttpException('Категория не найдена.');
        }
        
        $pageSize = 12; // Количество товаров на странице
        $offset = ($page - 1) * $pageSize;
        
        $totalProducts = $productRepository->countByCategory($category);
        $totalPages = ceil($totalProducts / $pageSize);
        
        $products = $productRepository->findAllByCategory($category, $pageSize, $offset);
        
        return $this->render('view', [
            'category' => $category,
            'products' => $products,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'pageSize' => $pageSize,
            'totalProducts' => $totalProducts,
        ]);
    }
}
