<?php

namespace context\Payment\services;

use context\AbstractService;
use context\Payment\interfaces\PaymentServiceInterface;
use GuzzleHttp\Exception\GuzzleException;
use repositories\Order\interfaces\OrderRepositoryInterface;
use repositories\Order\models\Order;
use repositories\Payment\interfaces\PaymentRepositoryInterface;
use repositories\Payment\models\Payment;
use Yii;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

/**
 * Сервис для работы с ЮKassa (YooMoney)
 */
class YooKassaPaymentService extends AbstractService implements PaymentServiceInterface
{
    private const API_URL = 'https://api.yookassa.ru/v3/';

    // Данные для подключения (боевые)
    private const SHOP_ID = '1199810';
    private const SECRET_KEY = 'test_dlsQnd_IEYdrXEwWziQIOqhYZIgUUd9uOC43Dayc_dE';

    public function __construct(
        private readonly OrderRepositoryInterface $orderRepository,
        private readonly PaymentRepositoryInterface $paymentRepository
    ) {
    }

    /**
     * Нормализация телефона для ЮKassa
     */
    private function normalizePhone(string $phone): string
    {
        // Убираем все символы кроме цифр
        $phone = preg_replace('/[^\d]/', '', $phone);

        // Если номер начинается с 8, заменяем на 7
        if (strlen($phone) === 11 && $phone[0] === '8') {
            $phone = '7' . substr($phone, 1);
        }

        // Если номер начинается с 7 и длина 11 символов
        if (strlen($phone) === 11 && $phone[0] === '7') {
            return '+' . $phone;
        }

        // Если номер 10 символов, добавляем +7
        if (strlen($phone) === 10) {
            return '+7' . $phone;
        }

        // Возвращаем как есть, если не удалось нормализовать
        return $phone;
    }

    /**
     * Инициация платежа в ЮKassa
     */
    public function initPayment(Order $order): array
    {
        try {
            if (!$order->id) {
                return [
                    'success' => false,
                    'message' => 'Заказ не сохранен в базе данных'
                ];
            }

            $client = new Client();

            $idempotenceKey = uniqid('order_' . $order->id . '_', true);

            $returnUrl = Yii::$app->urlManager->createAbsoluteUrl(['/order/view', 'uuid' => $order->uuid]);

            // Валидация обязательных полей
            if (empty($order->customer_phone)) {
                return [
                    'success' => false,
                    'message' => 'Не указан телефон клиента'
                ];
            }

            if (empty($order->orderItems) || count($order->orderItems) === 0) {
                return [
                    'success' => false,
                    'message' => 'В заказе нет товаров'
                ];
            }

            // Нормализуем телефон для ЮKassa
            $normalizedPhone = $this->normalizePhone($order->customer_phone);

            // Проверяем корректность нормализованного телефона
            if (!preg_match('/^\+7\d{10}$/', $normalizedPhone)) {
                return [
                    'success' => false,
                    'message' => 'Некорректный формат телефона: ' . $order->customer_phone
                ];
            }

            // Формируем чек для 54-ФЗ
            $receiptItems = [];
            foreach ($order->orderItems as $item) {
                $receiptItems[] = [
                    'description' => mb_substr($item->product_name, 0, 128), // Ограничиваем длину
                    'quantity' => (string)$item->quantity,
                    'amount' => [
                        'value' => number_format($item->price, 2, '.', ''),
                        'currency' => 'RUB'
                    ],
                    'vat_code' => 1, // НДС не облагается
                    'payment_mode' => 'full_payment',
                    'payment_subject' => 'commodity'
                ];
            }

            $paymentData = [
                'amount' => [
                    'value' => number_format($order->total_cost, 2, '.', ''),
                    'currency' => 'RUB'
                ],
                'confirmation' => [
                    'type' => 'redirect',
                    'return_url' => $returnUrl
                ],
                'capture' => true,
                'description' => 'Заказ №' . $order->id . ' на сайте азия-фуд.рф',
                'receipt' => [
                    'customer' => array_filter([
                        'email' => !empty($order->customer_email) && $order->customer_email !== 'no-email@example.com' ? $order->customer_email : null,
                        'phone' => $normalizedPhone
                    ]),
                    'items' => $receiptItems
                ],
                'metadata' => [
                    'order_id' => $order->id,
                    'customer_email' => $order->customer_email,
                    'customer_phone' => $order->customer_phone
                ]
            ];

            // Отправляем запрос к API ЮKassa
            $response = $client->post(self::API_URL . 'payments', [
                'headers' => [
                    'Authorization' => 'Basic ' . base64_encode(self::SHOP_ID . ':' . self::SECRET_KEY),
                    'Idempotence-Key' => $idempotenceKey,
                    'Content-Type' => 'application/json'
                ],
                'json' => $paymentData
            ]);

            if ($response->getStatusCode() !== 200) {
                $errorBody = $response->getBody()->getContents();
                $errorData = json_decode($errorBody, true);

                $errorMessage = 'Ошибка инициации платежа';
                if (isset($errorData['description'])) {
                    $errorMessage .= ': ' . $errorData['description'];
                }

                Yii::error('Ошибка ЮKassa API: ' . $errorBody, 'payment');

                return [
                    'success' => false,
                    'message' => $errorMessage
                ];
            }

            $responseData = json_decode($response->getBody()->getContents(), true);

            // Сохраняем ID платежа в заказе
            $order->payment_transaction_id = $responseData['id'];
            $order->payment_status = Order::PAYMENT_STATUS_PENDING;
            $order->payment_method = Order::PAYMENT_METHOD_CARD;
            $this->orderRepository->save($order);

            // Создаем запись о платеже
            $payment = Payment::createFromYooKassaData($order->id, $responseData);
            $this->paymentRepository->save($payment);

            return [
                'success' => true,
                'transaction_id' => $responseData['id'],
                'payment_url' => $responseData['confirmation']['confirmation_url'],
                'message' => 'Платеж успешно инициализирован'
            ];

        } catch (RequestException $e) {
            return [
                'success' => false,
                'message' => 'Ошибка соединения с платежной системой: ' . $e->getMessage()
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Ошибка инициации платежа: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Обработка уведомлений (webhook) от ЮKassa
     */
    public function handleCallback(array $data): bool
    {
        try {
            // Проверяем структуру данных
            if (!isset($data['event']) || !isset($data['object'])) {
                Yii::error('Неверная структура webhook от ЮKassa: ' . json_encode($data), 'payment');
                return false;
            }

            $event = $data['event'];
            $payment = $data['object'];

            // Проверяем, что это уведомление о платеже
            if (!isset($payment['id']) || !isset($payment['metadata']['order_id'])) {
                Yii::error('Отсутствуют обязательные поля в webhook: ' . json_encode($data), 'payment');
                return false;
            }

            $paymentId = $payment['id'];
            $orderId = $payment['metadata']['order_id'];

            // Находим заказ
            $order = $this->orderRepository->findById($orderId);
            if (!$order) {
                Yii::error('Заказ с ID ' . $orderId . ' не найден для платежа ' . $paymentId, 'payment');
                return false;
            }

            // Находим запись о платеже
            $paymentRecord = $this->paymentRepository->findByPaymentId($paymentId);
            if (!$paymentRecord) {
                Yii::error('Запись о платеже с ID ' . $paymentId . ' не найдена', 'payment');
                return false;
            }

            // Проверяем, что ID платежа совпадает
            if ($order->payment_transaction_id !== $paymentId) {
                Yii::error('ID платежа не совпадает для заказа ' . $orderId . ': ожидался ' . $order->payment_transaction_id . ', получен ' . $paymentId, 'payment');
                return false;
            }

            // Обновляем данные платежа
            $paymentRecord->updateFromYooKassaData($payment);
            $paymentRecord->webhook_data = $data;

            // Обрабатываем различные события
            switch ($event) {
                case 'payment.succeeded':
                    $order->payment_status = Order::PAYMENT_STATUS_PAID;
                    $order->status = Order::STATUS_PROCESSING;
                    $paymentRecord->status = Payment::STATUS_SUCCEEDED;
                    break;

                case 'payment.waiting_for_capture':
                    $paymentRecord->status = Payment::STATUS_WAITING_FOR_CAPTURE;
                    $this->capturePayment($paymentId, $payment['amount']);
                    break;

                case 'payment.canceled':
                    $order->payment_status = Order::PAYMENT_STATUS_FAILED;
                    $paymentRecord->status = Payment::STATUS_CANCELED;
                    break;

                case 'refund.succeeded':
                    if (isset($data['object']['refund_id'])) {
                        $paymentRecord->refund_id = $data['object']['refund_id'];
                        $paymentRecord->refund_status = Payment::REFUND_STATUS_SUCCEEDED;
                        if (isset($data['object']['amount']['value'])) {
                            $paymentRecord->refund_amount = (float)$data['object']['amount']['value'];
                        }
                    }
                    break;

                default:
                    return true;
            }

            $this->orderRepository->save($order);
            $this->paymentRepository->save($paymentRecord);
            return true;

        } catch (\Exception $e) {
            Yii::error('Ошибка обработки webhook от ЮKassa: ' . $e->getMessage(), 'payment');
            return false;
        }
    }

    /**
     * Проверка статуса платежа
     * @throws GuzzleException
     */
    public function checkStatus(string $transactionId): array
    {
        try {
            $client = new Client();

            $response = $client->get(self::API_URL . 'payments/' . $transactionId, [
                'headers' => [
                    'Authorization' => 'Basic ' . base64_encode(self::SHOP_ID . ':' . self::SECRET_KEY),
                    'Content-Type' => 'application/json'
                ]
            ]);

            if ($response->getStatusCode() !== 200) {
                return [
                    'success' => false,
                    'message' => 'Ошибка получения статуса платежа'
                ];
            }

            $paymentData = json_decode($response->getBody()->getContents(), true);

            return [
                'success' => true,
                'transaction_id' => $transactionId,
                'status' => $paymentData['status'],
                'amount' => $paymentData['amount']['value'],
                'currency' => $paymentData['amount']['currency'],
                'created_at' => $paymentData['created_at'],
                'paid' => $paymentData['paid'] ?? false
            ];

        } catch (RequestException $e) {
            Yii::error('HTTP ошибка при проверке статуса платежа: ' . $e->getMessage(), 'payment');
            return [
                'success' => false,
                'message' => 'Ошибка соединения с платежной системой'
            ];
        } catch (\Exception $e) {
            Yii::error('Ошибка проверки статуса платежа: ' . $e->getMessage(), 'payment');
            return [
                'success' => false,
                'message' => 'Ошибка проверки статуса платежа'
            ];
        }
    }

    /**
     * Подтверждение платежа (capture)
     */
    private function capturePayment(string $paymentId, array $amount): void
    {
        try {
            $client = new Client();

            $captureData = [
                'amount' => $amount
            ];

            $response = $client->post(self::API_URL . 'payments/' . $paymentId . '/capture', [
                'headers' => [
                    'Authorization' => 'Basic ' . base64_encode(self::SHOP_ID . ':' . self::SECRET_KEY),
                    'Idempotence-Key' => uniqid('capture_' . $paymentId . '_', true),
                    'Content-Type' => 'application/json'
                ],
                'json' => $captureData
            ]);

            if ($response->getStatusCode() === 200) {
                Yii::info('Платеж успешно подтвержден: ' . $paymentId, 'payment');
                return;
            } else {
                $errorBody = $response->getBody()->getContents();
                Yii::error('Ошибка подтверждения платежа ' . $paymentId . ': ' . $errorBody, 'payment');
                return;
            }

        } catch (RequestException $e) {
            Yii::error('HTTP ошибка при подтверждении платежа: ' . $e->getMessage(), 'payment');
            return;
        } catch (\Exception $e) {
            Yii::error('Исключение при подтверждении платежа: ' . $e->getMessage(), 'payment');
            return;
        } catch (GuzzleException $e) {
        }
    }

    /**
     * Создание возврата
     */
    public function createRefund(string $paymentId, float $amount, string $reason = ''): array
    {
        try {
            $client = new Client();

            $refundData = [
                'amount' => [
                    'value' => number_format($amount, 2, '.', ''),
                    'currency' => 'RUB'
                ],
                'payment_id' => $paymentId
            ];

            if ($reason) {
                $refundData['description'] = $reason;
            }

            $response = $client->post(self::API_URL . 'refunds', [
                'headers' => [
                    'Authorization' => 'Basic ' . base64_encode(self::SHOP_ID . ':' . self::SECRET_KEY),
                    'Idempotence-Key' => uniqid('refund_' . $paymentId . '_', true),
                    'Content-Type' => 'application/json'
                ],
                'json' => $refundData
            ]);

            if ($response->getStatusCode() === 200) {
                $responseData = json_decode($response->getBody()->getContents(), true);

                return [
                    'success' => true,
                    'refund_id' => $responseData['id'],
                    'status' => $responseData['status'],
                    'message' => 'Возврат успешно создан'
                ];
            } else {
                $errorBody = $response->getBody()->getContents();
                Yii::error('Ошибка создания возврата для платежа ' . $paymentId . ': ' . $errorBody, 'payment');
                return [
                    'success' => false,
                    'message' => 'Ошибка создания возврата'
                ];
            }

        } catch (RequestException $e) {
            Yii::error('HTTP ошибка при создании возврата: ' . $e->getMessage(), 'payment');
            return [
                'success' => false,
                'message' => 'Ошибка соединения с платежной системой'
            ];
        } catch (\Exception $e) {
            Yii::error('Исключение при создании возврата: ' . $e->getMessage(), 'payment');
            return [
                'success' => false,
                'message' => 'Ошибка создания возврата'
            ];
        }
    }
}
