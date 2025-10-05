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
     * Отображение категории и товаров в ней
     * 
     * @param string $slug
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionView($slug)
    {
        $categoryRepository = \Yii::$container->get(CategoryRepositoryInterface::class);
        $category = $categoryRepository->findBySlug($slug);
        
        if (!$category) {
            throw new NotFoundHttpException('Категория не найдена.');
        }
        
        // Получаем товары категории
        $products = $category->getProducts()->where(['status' => 1])->all();
        
        return $this->render('view', [
            'category' => $category,
            'products' => $products,
        ]);
    }
}
