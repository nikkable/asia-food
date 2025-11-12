<?php

namespace repositories\Order\models;

use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "{{%order}}".
 *
 * @property int $id
 * @property string $uuid
 * @property int|null $user_id
 * @property string $customer_name
 * @property string $customer_email
 * @property string $customer_phone
 * @property float $total_cost
 * @property string|null $note
 * @property int $status
 * @property string $payment_method
 * @property int $payment_status
 * @property string|null $payment_transaction_id
 * @property string|null $external_id
 * @property int|null $exported_at
 * @property int $export_status
 * @property int $created_at
 * @property int $updated_at
 *
 * @property OrderItem[] $orderItems
 */
class Order extends ActiveRecord
{
    // Способы оплаты
    const PAYMENT_METHOD_CASH = 'cash';
    const PAYMENT_METHOD_CARD = 'card';
    
    // Статусы оплаты
    const PAYMENT_STATUS_PENDING = 0;
    const PAYMENT_STATUS_PAID = 1;
    const PAYMENT_STATUS_FAILED = 2;
    
    // Статусы заказа
    const STATUS_NEW = 0;
    const STATUS_PROCESSING = 1;
    const STATUS_COMPLETED = 2;
    const STATUS_CANCELLED = 3;
    
    // Статусы экспорта в 1С
    const EXPORT_STATUS_NOT_EXPORTED = 0;
    const EXPORT_STATUS_EXPORTED = 1;
    const EXPORT_STATUS_ERROR = 2;

    public static function tableName(): string
    {
        return '{{%order}}';
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
            [['customer_name', 'customer_email', 'customer_phone', 'total_cost'], 'required'],
            [['user_id', 'status', 'payment_status', 'exported_at', 'export_status', 'created_at', 'updated_at'], 'integer'],
            [['total_cost'], 'number'],
            [['note'], 'string'],
            [['uuid'], 'string', 'max' => 36],
            [['uuid'], 'unique'],
            [['customer_name', 'customer_email', 'customer_phone', 'payment_method', 'payment_transaction_id'], 'string', 'max' => 255],
            [['external_id'], 'string', 'max' => 50],
            [['customer_email'], 'email'],
            ['payment_method', 'in', 'range' => [self::PAYMENT_METHOD_CASH, self::PAYMENT_METHOD_CARD]],
            ['payment_status', 'in', 'range' => [self::PAYMENT_STATUS_PENDING, self::PAYMENT_STATUS_PAID, self::PAYMENT_STATUS_FAILED]],
            ['status', 'in', 'range' => [self::STATUS_NEW, self::STATUS_PROCESSING, self::STATUS_COMPLETED, self::STATUS_CANCELLED]],
            ['export_status', 'in', 'range' => [self::EXPORT_STATUS_NOT_EXPORTED, self::EXPORT_STATUS_EXPORTED, self::EXPORT_STATUS_ERROR]],
            ['export_status', 'default', 'value' => self::EXPORT_STATUS_NOT_EXPORTED],
        ];
    }

    public function attributeLabels(): array
    {
        return [
          'user_id' => 'Id пользователя',
          'customer_name' => 'Имя клиента',
          'customer_email' => 'Email клиента',
          'customer_phone' => 'Телефон клиента',
          'total_cost' => 'Итоговая цена',
          'note' => 'Заметка',
          'status' => 'Статус',
          'created_at' => 'Дата создания',
          'updated_at' => 'Дата обновления',
        ];
    }

    public function getOrderItems(): ActiveQuery
    {
        return $this->hasMany(OrderItem::class, ['order_id' => 'id']);
    }

    public static function getStatusInfo(int $status): array
    {
        $statuses = [
            self::STATUS_NEW => ['text' => 'Новый', 'class' => 'new', 'icon' => 'fa-file-alt'],
            self::STATUS_PROCESSING => ['text' => 'В обработке', 'class' => 'processing', 'icon' => 'fa-cogs'],
            self::STATUS_COMPLETED => ['text' => 'Выполнен', 'class' => 'completed', 'icon' => 'fa-check-circle'],
            self::STATUS_CANCELLED => ['text' => 'Отменен', 'class' => 'cancelled', 'icon' => 'fa-times-circle'],
        ];
        return $statuses[$status] ?? ['text' => 'Неизвестно', 'class' => 'unknown', 'icon' => 'fa-question-circle'];
    }

    public static function getPaymentStatusInfo(int $status): array
    {
        $statuses = [
            self::PAYMENT_STATUS_PENDING => ['text' => 'Ожидает оплаты', 'class' => 'pending'],
            self::PAYMENT_STATUS_PAID => ['text' => 'Оплачен', 'class' => 'paid'],
            self::PAYMENT_STATUS_FAILED => ['text' => 'Ошибка оплаты', 'class' => 'failed'],
        ];
        return $statuses[$status] ?? ['text' => 'Неизвестно', 'class' => 'unknown'];
    }

    public static function getPaymentMethodText(string $method): string
    {
        $methods = [
            self::PAYMENT_METHOD_CASH => 'Наличными',
            self::PAYMENT_METHOD_CARD => 'Картой онлайн',
        ];
        return $methods[$method] ?? 'Неизвестно';
    }

    /**
     * Генерирует UUID перед сохранением
     */
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if ($insert && empty($this->uuid)) {
                $this->uuid = $this->generateUuid();
            }
            return true;
        }
        return false;
    }

    /**
     * Генерирует UUID v4 с использованием Yii2 Security
     */
    private function generateUuid(): string
    {
        return \Yii::$app->security->generateRandomString(36);
    }

    public function getNumber(): string
    {
        $year = $this->created_at ? (int)date('Y', (int)$this->created_at) : (int)date('Y');
        $calc = $year * 3 + (int)$this->id;
        return 'AF-' . $calc;
    }
}
