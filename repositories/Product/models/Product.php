<?php

namespace repositories\Product\models;

use repositories\Category\models\Category;
use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\SluggableBehavior;
use yii\web\UploadedFile;

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
 * @property string|null $external_id
 * @property int $status
 * @property int $created_at
 * @property int $updated_at
 *
 * @property UploadedFile|null $imageFile
 */
class Product extends ActiveRecord
{
    /**
     * @var UploadedFile|null
     */
    public $imageFile;
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
            [['name', 'image', 'article', 'external_id'], 'string', 'max' => 255],
            [['article'], 'unique'],
            [['external_id'], 'unique'],
            [['imageFile'], 'safe'],
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
            'category_id' => 'Категория',
            'name' => 'Название',
            'description' => 'Описание',
            'image' => 'Изображение',
            'imageFile' => 'Изображение',
            'price' => 'Цена',
            'price_discount' => 'Цена со скидкой',
            'quantity' => 'Количество',
            'article' => 'Артикул',
            'external_id' => 'Внешний ID (1C)',
            'status' => 'Статус',
            'created_at' => 'Создано',
            'updated_at' => 'Обновлено',
        ];
    }

    /**
     * Получить полный URL изображения
     *
     * @return string|null
     */
    public function getImageUrl(): ?string
    {
        $basePath = Yii::$app->id === 'app-backend' ? '@web/uploads/products/' : rtrim(Yii::$app->params['backendUrl'], '/') . '/uploads/products/';

        if ($this->image) {
            if (Yii::$app->id === 'app-backend') {
                return Yii::getAlias($basePath . $this->image);
            } else {
                return $basePath . $this->image;
            }
        }

        return $basePath . 'default.png';
    }
}
