<?php

namespace frontend\controllers;

use context\Category\interfaces\CategoryServiceInterface;
use context\Product\interfaces\ProductServiceInterface;
use Yii;
use yii\base\InvalidConfigException;
use yii\di\NotInstantiableException;
use yii\web\NotFoundHttpException;

class CatalogController extends BaseSeoController
{
    private $categoryService;
    private $productService;

    /**
     * @throws NotInstantiableException
     * @throws InvalidConfigException
     */
    public function init(): void
    {
        parent::init();
        $this->categoryService = Yii::$container->get(CategoryServiceInterface::class);
        $this->productService = Yii::$container->get(ProductServiceInterface::class);
    }

    public function actionIndex(int $page = 1) :string
    {
        $this->setCatalogSeoData();
        
        $pageSize = 12;
        $offset = ($page - 1) * $pageSize;
        
        $totalProducts = $this->productService->countAll();
        $totalPages = ceil($totalProducts / $pageSize);
        
        $products = $this->productService->findAll($pageSize, $offset);
        $categories = $this->categoryService->getRoot();

        return $this->render('index', [
            'products' => $products,
            'categories' => $categories,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'pageSize' => $pageSize,
            'totalProducts' => $totalProducts,
        ]);
    }

    /**
     * @throws NotFoundHttpException
     */
    public function actionCategory(string $slug, int $page = 1) :string
    {
        if (!$category = $this->categoryService->findBySlug($slug)) {
            throw new NotFoundHttpException('The requested page does not exist.');
        }

        $this->setCategorySeoData($category->name, $category->slug);

        $pageSize = 12;
        $offset = ($page - 1) * $pageSize;
        
        $totalProducts = $this->productService->countByCategory($category);
        $totalPages = ceil($totalProducts / $pageSize);
        
        $products = $this->productService->findAllByCategory($category, $pageSize, $offset);

        return $this->render('category', [
            'category' => $category,
            'products' => $products,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'pageSize' => $pageSize,
            'totalProducts' => $totalProducts,
        ]);
    }

    /**
     * @throws NotFoundHttpException
     */
    public function actionProduct(string $slug) :string
    {
        if (!$product = $this->productService->findBySlug($slug)) {
            throw new NotFoundHttpException('The requested page does not exist.');
        }

        $categoryName = $product->category ? $product->category->name : null;
        $this->setProductSeoData($product->name, $product->slug, $categoryName);

        return $this->render('product', [
            'product' => $product,
        ]);
    }
}
