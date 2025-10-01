<?php

namespace frontend\controllers;

use context\Cart\interfaces\CartServiceInterface;
use context\Product\interfaces\ProductServiceInterface;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class CartController extends Controller
{
    private $cartService;
    private $productService;

    public function __construct($id, $module, CartServiceInterface $cartService, ProductServiceInterface $productService, $config = [])
    {
        $this->cartService = $cartService;
        $this->productService = $productService;
        parent::__construct($id, $module, $config);
    }

    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'add' => ['POST'],
                    'remove' => ['POST'],
                    'clear' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * @return mixed
     */
    public function actionIndex()
    {
        $cart = $this->cartService->getCart();

        return $this->render('index', [
            'cart' => $cart,
        ]);
    }

    /**
     * @param int $id
     * @return Response
     * @throws NotFoundHttpException
     */
    public function actionAdd($id)
    {
        $product = $this->productService->findById($id);
        if (!$product) {
            throw new NotFoundHttpException();
        }

        $this->cartService->getCart()->add($product, 1);
        return $this->redirect(['index']);
    }

    public function actionRemove($id)
    {
        $this->cartService->getCart()->remove($id);
        return $this->redirect(['index']);
    }

    public function actionClear()
    {
        $this->cartService->getCart()->clear();
        return $this->redirect(['index']);
    }
}
