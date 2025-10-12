<?php

namespace frontend\controllers;

use context\Category\interfaces\CategoryServiceInterface;
use context\Product\interfaces\ProductServiceInterface;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class CatalogController extends Controller
{
    public function __construct(
        $id,
        $module,
        private readonly CategoryServiceInterface $categoryService,
        private readonly ProductServiceInterface $productService,
        $config = []
    ) {
        parent::__construct($id, $module, $config);
    }

    public function actionIndex(int $page = 1) :string
    {
        $pageSize = 12; // Количество товаров на странице
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

        return $this->render('product', [
            'product' => $product,
        ]);
    }
}
