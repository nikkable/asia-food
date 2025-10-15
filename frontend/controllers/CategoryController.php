<?php

namespace frontend\controllers;

use Yii;
use yii\base\InvalidConfigException;
use yii\di\NotInstantiableException;
use yii\web\NotFoundHttpException;
use repositories\Category\interfaces\CategoryRepositoryInterface;
use repositories\Product\interfaces\ProductRepositoryInterface;

class CategoryController extends BaseSeoController
{
    protected $categoryRepository;
    protected $productRepository;

    /**
     * @throws NotInstantiableException
     * @throws InvalidConfigException
     */
    public function init()
    {
        parent::init();
        $this->categoryRepository = Yii::$container->get(CategoryRepositoryInterface::class);
        $this->productRepository = Yii::$container->get(ProductRepositoryInterface::class);
    }

    /**
     * Отображение категории и товаров в ней с пагинацией
     * @throws NotFoundHttpException
     */
    public function actionView(string $slug, int $page = 1) :string
    {
        $category = $this->categoryRepository->findBySlug($slug);
        
        if (!$category) {
            throw new NotFoundHttpException('Категория не найдена.');
        }
        
        $this->setCategorySeoData($category->name, $category->slug);
        
        $pageSize = 12;
        $offset = ($page - 1) * $pageSize;
        
        $totalProducts = $this->productRepository->countByCategory($category);
        $totalPages = ceil($totalProducts / $pageSize);
        
        $products = $this->productRepository->findAllByCategory($category, $pageSize, $offset);
        
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
