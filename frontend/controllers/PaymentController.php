<?php

namespace frontend\controllers;

use context\Payment\interfaces\PaymentServiceInterface;
use repositories\Order\interfaces\OrderRepositoryInterface;
use repositories\Order\models\Order;
use repositories\Payment\interfaces\PaymentRepositoryInterface;
use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\web\BadRequestHttpException;

/**
 * PaymentController обрабатывает платежи и уведомления от платежной системы
 */
class PaymentController extends Controller
{
    private $paymentService;
    private $orderRepository;
    private $paymentRepository;

    public function __construct(
        $id, 
        $module, 
        PaymentServiceInterface $paymentService,
        OrderRepositoryInterface $orderRepository,
        PaymentRepositoryInterface $paymentRepository,
        $config = []
    ) {
        $this->paymentService = $paymentService;
        $this->orderRepository = $orderRepository;
        $this->paymentRepository = $paymentRepository;
        parent::__construct($id, $module, $config);
    }
    
    /**
     * Отключаем CSRF для webhook'ов от ЮKassa
     */
    public function beforeAction($action)
    {
        if ($action->id === 'webhook') {
            $this->enableCsrfValidation = false;
        }

        return parent::beforeAction($action);
    }

    /**
     * Страница обработки платежа (заглушка)
     * @throws NotFoundHttpException
     */
    public function actionProcess(string $transaction_id): string
    {
        $paymentStatus = $this->paymentService->checkStatus($transaction_id);
        
        if (!$paymentStatus['success']) {
            throw new NotFoundHttpException('Транзакция не найдена');
        }
        
        $order = Order::findOne(['payment_transaction_id' => $transaction_id]);
        if (!$order) {
            throw new NotFoundHttpException('Заказ не найден');
        }
        
        return $this->render('process', [
            'order' => $order,
            'transaction_id' => $transaction_id,
        ]);
    }
    
    /**
     * Страница успешной оплаты (возврат с ЮKassa)
     */
    public function actionSuccess(): Response|string
    {
        $orderId = Yii::$app->request->get('order_id');
        if (!$orderId) {
            throw new BadRequestHttpException('Не указан ID заказа');
        }
        
        $order = $this->orderRepository->findById($orderId);
        if (!$order) {
            throw new NotFoundHttpException('Заказ не найден');
        }
        
        // Получаем информацию о платеже
        $payments = $this->paymentRepository->findByOrderId($orderId);
        $payment = !empty($payments) ? $payments[0] : null;
        
        // Если есть платеж, проверяем его актуальный статус через API
        if ($payment && $payment->payment_id) {
            $statusResult = $this->paymentService->checkStatus($payment->payment_id);
            
            if ($statusResult['success']) {
                $paymentStatus = $statusResult['status'];
                
                // Если платеж не успешен, перенаправляем на страницу ошибки
                if ($paymentStatus !== 'succeeded') {
                    return $this->redirect(['/payment/fail', 'order_id' => $orderId]);
                }
                
                // Обновляем статус платежа в БД, если он изменился
                if ($payment->status !== $paymentStatus) {
                    $payment->status = $paymentStatus;
                    $payment->webhook_data = json_encode($statusResult['data']);
                    $this->paymentRepository->save($payment);
                    
                    // Обновляем статус заказа
                    if ($paymentStatus === 'succeeded') {
                        $order->payment_status = Order::PAYMENT_STATUS_PAID;
                        $this->orderRepository->save($order);
                    }
                }
            }
        }
        
        return $this->render('success', [
            'order' => $order,
            'payment' => $payment
        ]);
    }
    
    /**
     * Страница неудачной оплаты
     */
    public function actionFail(): string
    {
        $orderId = Yii::$app->request->get('order_id');
        if (!$orderId) {
            throw new BadRequestHttpException('Не указан ID заказа');
        }
        
        $order = $this->orderRepository->findById($orderId);
        if (!$order) {
            throw new NotFoundHttpException('Заказ не найден');
        }
        
        return $this->render('fail', [
            'order' => $order
        ]);
    }
    
    /**
     * Обработка webhook'ов от ЮKassa
     * URL: https://xn----7sbnkf1eg0g.xn--p1ai/checkout
     */
    public function actionWebhook(): Response
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        try {
            // Получаем JSON данные из тела запроса
            $rawBody = Yii::$app->request->getRawBody();
            if (empty($rawBody)) {
                Yii::error('Пустое тело запроса в webhook от ЮKassa', 'payment');
                return $this->asJson(['error' => 'Empty request body']);
            }
            
            $data = json_decode($rawBody, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                Yii::error('Ошибка парсинга JSON в webhook от ЮKassa: ' . json_last_error_msg(), 'payment');
                return $this->asJson(['error' => 'Invalid JSON']);
            }
            
            Yii::info('Получен webhook от ЮKassa: ' . $rawBody, 'payment');
            
            // Обрабатываем уведомление
            $result = $this->paymentService->handleCallback($data);
            
            if ($result) {
                return $this->asJson(['success' => true]);
            } else {
                return $this->asJson(['error' => 'Processing failed']);
            }
            
        } catch (\Exception $e) {
            Yii::error('Исключение в webhook: ' . $e->getMessage(), 'payment');
            return $this->asJson(['error' => 'Server error']);
        }
    }
    
    /**
     * Инициация платежа для заказа
     */
    public function actionPay(int $orderId): Response
    {
        $order = $this->orderRepository->findById($orderId);
        if (!$order) {
            throw new NotFoundHttpException('Заказ не найден');
        }
        
        // Проверяем, что заказ еще не оплачен
        if ($order->payment_status == Order::PAYMENT_STATUS_PAID) {
            Yii::$app->session->setFlash('info', 'Заказ уже оплачен');
            return $this->redirect(['/order/view', 'id' => $orderId]);
        }
        
        // Инициируем платеж
        $paymentResult = $this->paymentService->initPayment($order);
        
        if ($paymentResult['success']) {
            return $this->redirect($paymentResult['payment_url']);
        } else {
            Yii::$app->session->setFlash('error', 'Ошибка инициации платежа: ' . $paymentResult['message']);
            return $this->redirect(['/order/view', 'id' => $orderId]);
        }
    }
}
