<?php

namespace repositories\Product\models;

use repositories\Category\models\Category;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\SluggableBehavior;

/**
 * This is the model class for table "{{%product}}".
 *
 * @property int $id
 * @property int $category_id
 * @property string $name
 * @property string $slug
 * @property string|null $description
 * @property string|null $image
 * @property float $price
 * @property float|null $price_discount
 * @property int $quantity
 * @property string|null $article
 * @property int $status
 * @property int $created_at
 * @property int $updated_at
 */
class Product extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%product}}';
    }

    public function behaviors()
    {
        return [
            TimestampBehavior::class,
            [
                'class' => SluggableBehavior::class,
                'attribute' => 'name',
                'slugAttribute' => 'slug',
                'ensureUnique' => true,
            ],
        ];
    }

    public function rules()
    {
        return [
            [['category_id', 'name', 'price', 'slug'], 'required'],
            [['category_id', 'quantity', 'status', 'created_at', 'updated_at'], 'integer'],
            [['description'], 'string'],
            [['price', 'price_discount'], 'number'],
            [['name', 'image', 'article'], 'string', 'max' => 255],
            [['article'], 'unique'],
        ];
    }

    /**
     * Gets query for [[Category]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCategory()
    {
        return $this->hasOne(Category::class, ['id' => 'category_id']);
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'category_id' => 'Category ID',
            'name' => 'Name',
            'description' => 'Description',
            'image' => 'Image',
            'price' => 'Price',
            'price_discount' => 'Price Discount',
            'quantity' => 'Quantity',
            'article' => 'Article',
            'status' => 'Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }
}
