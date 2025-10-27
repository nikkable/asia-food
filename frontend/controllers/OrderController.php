<?php

namespace frontend\controllers;

use context\Cart\interfaces\CartServiceInterface;
use context\Order\interfaces\OrderServiceInterface;
use context\Payment\interfaces\PaymentServiceInterface;
use frontend\models\QuickOrderForm;
use Yii;
use yii\web\Controller;
use yii\web\Response;

/**
 * OrderController обрабатывает быстрые заказы из модального окна корзины
 */
class OrderController extends Controller
{
    private $cartService;
    private $orderService;
    private $paymentService;

    public function __construct(
        $id, 
        $module, 
        CartServiceInterface $cartService, 
        OrderServiceInterface $orderService, 
        PaymentServiceInterface $paymentService, 
        $config = []
    ) {
        $this->cartService = $cartService;
        $this->orderService = $orderService;
        $this->paymentService = $paymentService;
        parent::__construct($id, $module, $config);
    }

    /**
     * Создание заказа из модального окна корзины
     */
    public function actionCreate()
    {
        $model = new QuickOrderForm();
        $cart = $this->cartService->getCart();
        
        // Проверяем, что корзина не пуста
        if ($cart->getAmount() === 0) {
            if (Yii::$app->request->isAjax) {
                Yii::$app->response->format = Response::FORMAT_JSON;
                return [
                    'success' => false,
                    'message' => 'Корзина пуста'
                ];
            }
            return $this->redirect(['/site/index']);
        }
        
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            try {
                $order = $this->orderService->create($cart, $model->attributes);
                
                $cart->clear();
                
                if ($model->paymentMethod === QuickOrderForm::PAYMENT_METHOD_CARD) {
                    $paymentResult = $this->paymentService->initPayment($order);
                    
                    if ($paymentResult['success']) {
                        if (Yii::$app->request->isAjax) {
                            Yii::$app->response->format = Response::FORMAT_JSON;
                            return [
                                'success' => true,
                                'message' => 'Заказ создан, перенаправление на страницу оплаты',
                                'orderId' => $order->id,
                                'paymentUrl' => $paymentResult['payment_url'],
                                'requiresRedirect' => true
                            ];
                        }
                        
                        return $this->redirect($paymentResult['payment_url']);
                    } else {
                        if (Yii::$app->request->isAjax) {
                            Yii::$app->response->format = Response::FORMAT_JSON;
                            return [
                                'success' => false,
                                'message' => 'Ошибка инициации платежа: ' . $paymentResult['message']
                            ];
                        }
                        
                        return $this->redirect(['/site/index']);
                    }
                } else {
                    if (Yii::$app->request->isAjax) {
                        Yii::$app->response->format = Response::FORMAT_JSON;
                        return [
                            'success' => true,
                            'message' => 'Заказ успешно оформлен',
                            'orderId' => $order->id,
                            'requiresRedirect' => false
                        ];
                    }
                    
                    Yii::$app->session->setFlash('success', 'Ваш заказ #' . $order->id . ' успешно создан.');
                    return $this->redirect(['/catalog/index']);
                }
            } catch (\Exception $e) {
                if (Yii::$app->request->isAjax) {
                    Yii::$app->response->format = Response::FORMAT_JSON;
                    
                    $errorMessage = $e->getMessage();
                    if (strpos($errorMessage, 'Details:') !== false) {
                        $errorMessage = 'Произошла ошибка при оформлении заказа. Пожалуйста, проверьте введенные данные.';
                    }
                    
                    return [
                        'success' => false,
                        'message' => $errorMessage
                    ];
                }
                
                Yii::$app->session->setFlash('error', 'Произошла ошибка при оформлении заказа.');
            }
        } elseif (Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return [
                'success' => false,
                'message' => 'Проверьте правильность заполнения формы',
                'errors' => $model->errors
            ];
        }
        
        return $this->redirect(['/site/index']);
    }
}
