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
     * Корзина доступна только через модальное окно
     * Редирект на главную страницу
     * 
     * @return mixed
     */
    public function actionIndex()
    {
        return $this->redirect(['/site/index']);
    }

    /**
     * @param int $id
     * @throws NotFoundHttpException
     */
    public function actionAdd($id)
    {
        if (\Yii::$app->request->isAjax) {
            \Yii::$app->response->format = Response::FORMAT_JSON;
            
            $quantity = (int) \Yii::$app->request->post('quantity', 1);
            $success = $this->cartService->addProduct($id, $quantity);
            
            if ($success) {
                $cart = $this->cartService->getCart();
                return [
                    'success' => true,
                    'message' => 'Товар добавлен в корзину',
                    'cartAmount' => $cart->getAmount(),
                    'cartTotal' => $cart->getTotalCost()
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Не удалось добавить товар в корзину'
                ];
            }
        }
        
        $product = $this->productService->findById($id);
        if (!$product) {
            throw new NotFoundHttpException();
        }

        $this->cartService->getCart()->add($product, 1);

        return $this->redirect(['/site/index']);
    }

    public function actionRemove()
    {
        // Получаем ID из GET или POST параметров
        $id = \Yii::$app->request->get('id');
        if (!$id) {
            $id = \Yii::$app->request->post('id');
        }
        
        if (!$id) {
            if (\Yii::$app->request->isAjax) {
                \Yii::$app->response->format = Response::FORMAT_JSON;
                return [
                    'success' => false,
                    'message' => 'Не указан ID товара'
                ];
            }
            return $this->redirect(['/site/index']);
        }

        if (\Yii::$app->request->isAjax) {
            \Yii::$app->response->format = Response::FORMAT_JSON;

            $this->cartService->getCart()->remove($id);
            $cart = $this->cartService->getCart();

            return [
                'success' => true,
                'message' => 'Товар удален из корзины',
                'cartAmount' => $cart->getAmount(),
                'cartTotal' => $cart->getTotalCost(),
                'removedProductId' => $id
            ];
        }

        $this->cartService->getCart()->remove($id);
        return $this->redirect(['/site/index']);
    }

    public function actionClear()
    {
        $this->cartService->getCart()->clear();
        return $this->redirect(['/site/index']);
    }

    /**
     * Обновление количества товара в корзине через AJAX
     */
    public function actionUpdateQuantity()
    {
        if (\Yii::$app->request->isAjax) {
            \Yii::$app->response->format = Response::FORMAT_JSON;

            $productId = (int) \Yii::$app->request->post('productId');
            $action = \Yii::$app->request->post('action'); // 'increase' или 'decrease'

            $cart = $this->cartService->getCart();

            if (isset($cart->getItems()[$productId])) {
                $item = $cart->getItems()[$productId];
                $oldQuantity = $item->getQuantity();

                if ($action === 'increase') {
                    $newQuantity = $oldQuantity + 1;
                } elseif ($action === 'decrease') {
                    $newQuantity = $oldQuantity - 1;
                } else {
                    return [
                        'success' => false,
                        'message' => 'Неверное действие'
                    ];
                }

                if ($newQuantity <= 0) {
                    $cart->remove($productId);
                    return [
                        'success' => true,
                        'message' => 'Товар удален из корзины',
                        'cartAmount' => $cart->getAmount(),
                        'cartTotal' => $cart->getTotalCost(),
                        'removedProductId' => $productId
                    ];
                } else {
                    $cart->set($productId, $newQuantity);
                    $updatedItem = $cart->getItems()[$productId];
                    
                    return [
                        'success' => true,
                        'message' => 'Количество обновлено',
                        'cartAmount' => $cart->getAmount(),
                        'cartTotal' => $cart->getTotalCost(),
                        'updatedProductId' => $productId,
                        'newQuantity' => $newQuantity,
                        'itemTotal' => $updatedItem->getCost()
                    ];
                }
            }

            return [
                'success' => false,
                'message' => 'Товар не найден в корзине'
            ];
        }

        return $this->redirect(['/site/index']);
    }
    
    /**
     * Возвращает HTML-содержимое модального окна корзины
     * 
     * @return string
     */
    public function actionModalContent()
    {
        $cart = $this->cartService->getCart();
        
        return $this->renderPartial('_modal_content', [
            'cart' => $cart,
        ]);
    }
}
