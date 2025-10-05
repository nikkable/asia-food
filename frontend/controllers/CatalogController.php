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

    /**
     * Displays the list of all products with pagination.
     * @param int $page Current page number
     * @return string
     */
    public function actionIndex($page = 1)
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
     * Displays products in a specific category with pagination.
     * @param string $slug
     * @param int $page Current page number
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionCategory($slug, $page = 1)
    {
        if (!$category = $this->categoryService->findBySlug($slug)) {
            throw new NotFoundHttpException('The requested page does not exist.');
        }

        $pageSize = 12; // Количество товаров на странице
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
     * Displays a single product.
     * @param string $slug
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionProduct($slug)
    {
        if (!$product = $this->productService->findBySlug($slug)) {
            throw new NotFoundHttpException('The requested page does not exist.');
        }

        return $this->render('product', [
            'product' => $product,
        ]);
    }
}
