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
     * Displays the list of root categories.
     * @return string
     */
    public function actionIndex()
    {
        $categories = $this->categoryService->getRoot();

        return $this->render('index', [
            'categories' => $categories,
        ]);
    }

    /**
     * Displays products in a specific category.
     * @param string $slug
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionCategory($slug)
    {
        if (!$category = $this->categoryService->findBySlug($slug)) {
            throw new NotFoundHttpException('The requested page does not exist.');
        }

        $products = $this->productService->findAllByCategory($category);

        return $this->render('category', [
            'category' => $category,
            'products' => $products,
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
