<?php

namespace repositories\Order\models;

use yii\db\ActiveRecord;
use repositories\Product\models\Product;

/**
 * This is the model class for table "{{%order_item}}".
 *
 * @property int $id
 * @property int $order_id
 * @property int|null $product_id
 * @property string $product_name
 * @property float $price
 * @property int $quantity
 * @property float $cost
 *
 * @property Order $order
 * @property Product $product
 */
class OrderItem extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%order_item}}';
    }

    public function rules()
    {
        return [
            [['order_id', 'product_name', 'price', 'quantity', 'cost'], 'required'],
            [['order_id', 'product_id', 'quantity'], 'integer'],
            [['price', 'cost'], 'number'],
            [['product_name'], 'string', 'max' => 255],
        ];
    }

    public function getOrder()
    {
        return $this->hasOne(Order::class, ['id' => 'order_id']);
    }

    public function getProduct()
    {
        return $this->hasOne(Product::class, ['id' => 'product_id']);
    }
}
