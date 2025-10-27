<?php

namespace repositories\Payment\models;

use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;
use repositories\Order\models\Order;

/**
 * This is the model class for table "{{%payment}}".
 *
 * @property int $id
 * @property int $order_id
 * @property string $payment_id Идентификатор платежа в ЮKassa
 * @property string $status Статус платежа в ЮKassa
 * @property float $amount Сумма платежа
 * @property string $currency Валюта платежа
 * @property string|null $payment_method_type Тип платежного средства
 * @property string|null $payment_method_id Идентификатор платежного средства
 * @property string|null $description Описание платежа
 * @property array|null $metadata Метаданные платежа
 * @property string|null $confirmation_url URL для подтверждения платежа
 * @property string|null $refund_id Идентификатор возврата
 * @property float|null $refund_amount Сумма возврата
 * @property string|null $refund_status Статус возврата
 * @property string|null $webhook_data Данные последнего webhook
 * @property int $created_at
 * @property int $updated_at
 *
 * @property Order $order
 */
class Payment extends ActiveRecord
{
    // Статусы платежа в ЮKassa
    const STATUS_PENDING = 'pending';
    const STATUS_WAITING_FOR_CAPTURE = 'waiting_for_capture';
    const STATUS_SUCCEEDED = 'succeeded';
    const STATUS_CANCELED = 'canceled';
    
    // Типы платежных средств
    const PAYMENT_METHOD_BANK_CARD = 'bank_card';
    const PAYMENT_METHOD_YOO_MONEY = 'yoo_money';
    const PAYMENT_METHOD_QIWI = 'qiwi';
    const PAYMENT_METHOD_WEBMONEY = 'webmoney';
    const PAYMENT_METHOD_ALFABANK = 'alfabank';
    const PAYMENT_METHOD_APPLE_PAY = 'apple_pay';
    const PAYMENT_METHOD_GOOGLE_PAY = 'google_pay';
    const PAYMENT_METHOD_SAMSUNG_PAY = 'samsung_pay';
    
    // Статусы возврата
    const REFUND_STATUS_PENDING = 'pending';
    const REFUND_STATUS_SUCCEEDED = 'succeeded';
    const REFUND_STATUS_CANCELED = 'canceled';

    public static function tableName(): string
    {
        return '{{%payment}}';
    }

    public function behaviors(): array
    {
        return [
            TimestampBehavior::class,
        ];
    }

    public function rules(): array
    {
        return [
            [['order_id', 'payment_id', 'status', 'amount', 'currency'], 'required'],
            [['order_id', 'created_at', 'updated_at'], 'integer'],
            [['amount', 'refund_amount'], 'number'],
            [['metadata', 'webhook_data'], 'safe'],
            [['payment_id', 'payment_method_id', 'refund_id'], 'string', 'max' => 50],
            [['status', 'payment_method_type', 'refund_status'], 'string', 'max' => 50],
            [['currency'], 'string', 'max' => 3],
            [['description', 'confirmation_url'], 'string', 'max' => 500],
            [['payment_id'], 'unique'],
            ['status', 'in', 'range' => [
                self::STATUS_PENDING,
                self::STATUS_WAITING_FOR_CAPTURE,
                self::STATUS_SUCCEEDED,
                self::STATUS_CANCELED
            ]],
            ['currency', 'in', 'range' => ['RUB', 'USD', 'EUR']],
            ['payment_method_type', 'in', 'range' => [
                self::PAYMENT_METHOD_BANK_CARD,
                self::PAYMENT_METHOD_YOO_MONEY,
                self::PAYMENT_METHOD_QIWI,
                self::PAYMENT_METHOD_WEBMONEY,
                self::PAYMENT_METHOD_ALFABANK,
                self::PAYMENT_METHOD_APPLE_PAY,
                self::PAYMENT_METHOD_GOOGLE_PAY,
                self::PAYMENT_METHOD_SAMSUNG_PAY
            ]],
            ['refund_status', 'in', 'range' => [
                self::REFUND_STATUS_PENDING,
                self::REFUND_STATUS_SUCCEEDED,
                self::REFUND_STATUS_CANCELED
            ]],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'order_id' => 'ID заказа',
            'payment_id' => 'ID платежа в ЮKassa',
            'status' => 'Статус платежа',
            'amount' => 'Сумма платежа',
            'currency' => 'Валюта',
            'payment_method_type' => 'Тип платежного средства',
            'payment_method_id' => 'ID платежного средства',
            'description' => 'Описание',
            'metadata' => 'Метаданные',
            'confirmation_url' => 'URL подтверждения',
            'refund_id' => 'ID возврата',
            'refund_amount' => 'Сумма возврата',
            'refund_status' => 'Статус возврата',
            'webhook_data' => 'Данные webhook',
            'created_at' => 'Дата создания',
            'updated_at' => 'Дата обновления',
        ];
    }

    public function getOrder(): ActiveQuery
    {
        return $this->hasOne(Order::class, ['id' => 'order_id']);
    }
    
    /**
     * Проверяет, успешно ли завершен платеж
     */
    public function isSucceeded(): bool
    {
        return $this->status === self::STATUS_SUCCEEDED;
    }
    
    /**
     * Проверяет, отменен ли платеж
     */
    public function isCanceled(): bool
    {
        return $this->status === self::STATUS_CANCELED;
    }
    
    /**
     * Проверяет, ожидает ли платеж подтверждения
     */
    public function isWaitingForCapture(): bool
    {
        return $this->status === self::STATUS_WAITING_FOR_CAPTURE;
    }
    
    /**
     * Проверяет, в процессе ли платеж
     */
    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }
    
    /**
     * Возвращает статус платежа
     */
    public function getStatusLabel(): string
    {
        return match($this->status) {
            self::STATUS_PENDING => 'Ожидает оплаты',
            self::STATUS_WAITING_FOR_CAPTURE => 'Ожидает подтверждения',
            self::STATUS_SUCCEEDED => 'Оплачен',
            self::STATUS_CANCELED => 'Отменен',
            default => 'Неизвестный статус'
        };
    }
    
    /**
     * Возвращает человекочитаемый тип платежного средства
     */
    public function getPaymentMethodLabel(): string
    {
        return match($this->payment_method_type) {
            self::PAYMENT_METHOD_BANK_CARD => 'Банковская карта',
            self::PAYMENT_METHOD_YOO_MONEY => 'ЮMoney',
            self::PAYMENT_METHOD_QIWI => 'QIWI Кошелек',
            self::PAYMENT_METHOD_WEBMONEY => 'WebMoney',
            self::PAYMENT_METHOD_ALFABANK => 'Альфа-Клик',
            self::PAYMENT_METHOD_APPLE_PAY => 'Apple Pay',
            self::PAYMENT_METHOD_GOOGLE_PAY => 'Google Pay',
            self::PAYMENT_METHOD_SAMSUNG_PAY => 'Samsung Pay',
            default => 'Неизвестный способ'
        };
    }
    
    /**
     * Обновляет данные платежа из ответа ЮKassa
     */
    public function updateFromYooKassaData(array $paymentData): void
    {
        $this->status = $paymentData['status'];
        $this->amount = (float)$paymentData['amount']['value'];
        $this->currency = $paymentData['amount']['currency'];
        
        if (isset($paymentData['payment_method']['type'])) {
            $this->payment_method_type = $paymentData['payment_method']['type'];
        }
        
        if (isset($paymentData['payment_method']['id'])) {
            $this->payment_method_id = $paymentData['payment_method']['id'];
        }
        
        if (isset($paymentData['description'])) {
            $this->description = $paymentData['description'];
        }
        
        if (isset($paymentData['metadata'])) {
            $this->metadata = $paymentData['metadata'];
        }
        
        if (isset($paymentData['confirmation']['confirmation_url'])) {
            $this->confirmation_url = $paymentData['confirmation']['confirmation_url'];
        }
    }
    
    /**
     * Создает новый платеж из данных ЮKassa
     */
    public static function createFromYooKassaData(int $orderId, array $paymentData): self
    {
        $payment = new self();
        $payment->order_id = $orderId;
        $payment->payment_id = $paymentData['id'];
        $payment->updateFromYooKassaData($paymentData);
        
        return $payment;
    }
}
