<?php

namespace frontend\controllers;

use context\Payment\interfaces\PaymentServiceInterface;
use repositories\Order\models\Order;
use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * PaymentController обрабатывает платежи и уведомления от платежной системы
 */
class PaymentController extends Controller
{
    private $paymentService;

    public function __construct($id, $module, PaymentServiceInterface $paymentService, $config = [])
    {
        $this->paymentService = $paymentService;
        parent::__construct($id, $module, $config);
    }

    /**
     * Страница обработки платежа (заглушка)
     * 
     * @param string $transaction_id ID транзакции
     * @return mixed
     * @throws NotFoundHttpException
     */
    public function actionProcess($transaction_id)
    {
        // Проверяем статус платежа
        $paymentStatus = $this->paymentService->checkStatus($transaction_id);
        
        if (!$paymentStatus['success']) {
            throw new NotFoundHttpException('Транзакция не найдена');
        }
        
        // Получаем заказ
        $order = Order::findOne(['payment_transaction_id' => $transaction_id]);
        if (!$order) {
            throw new NotFoundHttpException('Заказ не найден');
        }
        
        // Рендерим страницу оплаты
        return $this->render('process', [
            'order' => $order,
            'transaction_id' => $transaction_id,
        ]);
    }
    
    /**
     * Имитация успешной оплаты
     * 
     * @param string $transaction_id ID транзакции
     * @return mixed
     */
    public function actionSuccess($transaction_id)
    {
        // Обрабатываем успешный платеж
        $this->paymentService->handleCallback([
            'transaction_id' => $transaction_id,
            'status' => 'success'
        ]);
        
        Yii::$app->session->setFlash('success', 'Платеж успешно выполнен!');
        return $this->redirect(['/site/index']);
    }
    
    /**
     * Имитация неудачной оплаты
     * 
     * @param string $transaction_id ID транзакции
     * @return mixed
     */
    public function actionFail($transaction_id)
    {
        // Обрабатываем неудачный платеж
        $this->paymentService->handleCallback([
            'transaction_id' => $transaction_id,
            'status' => 'failed'
        ]);
        
        Yii::$app->session->setFlash('error', 'Оплата не выполнена. Пожалуйста, попробуйте еще раз или выберите другой способ оплаты.');
        return $this->redirect(['/site/index']);
    }
    
    /**
     * Обработка уведомлений от платежной системы
     * 
     * @return mixed
     */
    public function actionCallback()
    {
        // В реальном проекте здесь будет обработка уведомлений от платежной системы
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        $data = Yii::$app->request->post();
        if (empty($data)) {
            return [
                'success' => false,
                'message' => 'Нет данных'
            ];
        }
        
        $result = $this->paymentService->handleCallback($data);
        
        return [
            'success' => $result,
            'message' => $result ? 'Уведомление обработано' : 'Ошибка обработки уведомления'
        ];
    }
}
