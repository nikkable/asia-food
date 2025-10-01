<?php

namespace repositories\Order\models;

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
 * @property int $created_at
 * @property int $updated_at
 *
 * @property OrderItem[] $orderItems
 */
class Order extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%order}}';
    }

    public function behaviors()
    {
        return [
            TimestampBehavior::class,
        ];
    }

    public function rules()
    {
        return [
            [['customer_name', 'customer_email', 'customer_phone', 'total_cost'], 'required'],
            [['user_id', 'status', 'created_at', 'updated_at'], 'integer'],
            [['total_cost'], 'number'],
            [['note'], 'string'],
            [['customer_name', 'customer_email', 'customer_phone'], 'string', 'max' => 255],
            [['customer_email'], 'email'],
        ];
    }

    public function getOrderItems()
    {
        return $this->hasMany(OrderItem::class, ['order_id' => 'id']);
    }
}
