<?php

namespace frontend\controllers;

use context\Cart\interfaces\CartServiceInterface;
use context\Order\interfaces\OrderServiceInterface;
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

    public function __construct($id, $module, CartServiceInterface $cartService, OrderServiceInterface $orderService, $config = [])
    {
        $this->cartService = $cartService;
        $this->orderService = $orderService;
        parent::__construct($id, $module, $config);
    }

    /**
     * Создание заказа из модального окна корзины
     * 
     * @return mixed
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
            return $this->redirect(['/cart/index']);
        }
        
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            try {
                // Детальное логирование данных формы для отладки
                Yii::info('QuickOrderForm data: ' . json_encode($model->attributes), 'order');
                
                // Создаем заказ через сервис
                $order = $this->orderService->create($cart, $model->attributes);
                
                // Очищаем корзину после успешного оформления заказа
                $cart->clear();
                
                if (Yii::$app->request->isAjax) {
                    Yii::$app->response->format = Response::FORMAT_JSON;
                    return [
                        'success' => true,
                        'message' => 'Заказ успешно оформлен',
                        'orderId' => $order->id
                    ];
                }
                
                Yii::$app->session->setFlash('success', 'Ваш заказ #' . $order->id . ' успешно создан.');
                return $this->redirect(['/catalog/index']);
            } catch (\Exception $e) {
                // Детальное логирование ошибки
                Yii::error('Order creation error: ' . $e->getMessage() . '\nTrace: ' . $e->getTraceAsString(), 'order');
                
                if (Yii::$app->request->isAjax) {
                    Yii::$app->response->format = Response::FORMAT_JSON;
                    
                    // Получаем детали ошибки для отображения пользователю
                    $errorMessage = $e->getMessage();
                    // Удаляем технические детали из сообщения об ошибке
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
            // Если есть ошибки валидации и запрос через AJAX
            Yii::$app->response->format = Response::FORMAT_JSON;
            return [
                'success' => false,
                'message' => 'Проверьте правильность заполнения формы',
                'errors' => $model->errors
            ];
        }
        
        // Если что-то пошло не так, перенаправляем на страницу корзины
        return $this->redirect(['/cart/index']);
    }
}
