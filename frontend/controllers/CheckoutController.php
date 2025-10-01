<?php

namespace frontend\controllers;

use context\Cart\interfaces\CartServiceInterface;
use context\Order\interfaces\OrderServiceInterface;
use yii\web\Controller;
use Yii;

class CheckoutController extends Controller
{
    private $cartService;
    private $orderService;

    public function __construct($id, $module, CartServiceInterface $cartService, OrderServiceInterface $orderService, $config = [])
    {
        $this->cartService = $cartService;
        $this->orderService = $orderService;
        parent::__construct($id, $module, $config);
    }

    public function actionIndex()
    {
        $cart = $this->cartService->getCart();
        if ($cart->getAmount() === 0) {
            return $this->redirect(['/cart/index']);
        }

        $form = new \frontend\models\OrderForm();

        if ($form->load(Yii::$app->request->post()) && $form->validate()) {
            try {
                $order = $this->orderService->create($cart, $form->attributes);
                $cart->clear();
                Yii::$app->session->setFlash('success', 'Ваш заказ #' . $order->id . ' успешно создан.');
                return $this->redirect(['/catalog/index']);
            } catch (\Exception $e) {
                Yii::$app->errorHandler->logException($e);
                Yii::$app->session->setFlash('error', 'Произошла ошибка при оформлении заказа.');
            }
        }

        return $this->render('index', [
            'cart' => $cart,
            'model' => $form,
        ]);
    }
}
