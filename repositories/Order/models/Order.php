<?php

namespace repositories\Order\models;

use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "{{%order}}".
 *
 * @property int $id
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
            [['user_id', 'status', 'payment_status', 'created_at', 'updated_at'], 'integer'],
            [['total_cost'], 'number'],
            [['note'], 'string'],
            [['customer_name', 'customer_email', 'customer_phone', 'payment_method', 'payment_transaction_id'], 'string', 'max' => 255],
            [['customer_email'], 'email'],
            ['payment_method', 'in', 'range' => [self::PAYMENT_METHOD_CASH, self::PAYMENT_METHOD_CARD]],
            ['payment_status', 'in', 'range' => [self::PAYMENT_STATUS_PENDING, self::PAYMENT_STATUS_PAID, self::PAYMENT_STATUS_FAILED]],
            ['status', 'in', 'range' => [self::STATUS_NEW, self::STATUS_PROCESSING, self::STATUS_COMPLETED, self::STATUS_CANCELLED]],
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
}
