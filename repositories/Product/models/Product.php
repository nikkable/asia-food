<?php

namespace repositories\Product\models;

use repositories\Category\models\Category;
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
            [['name', 'image', 'article'], 'string', 'max' => 255],
            [['article'], 'unique'],
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
        if ($this->image) {
            // Определяем, в каком приложении мы находимся
            if (\Yii::$app->id === 'app-backend') {
                return \Yii::getAlias('@web/uploads/products/' . $this->image);
            } else {
                // Для фронтенда используем полный URL бэкенда из параметров
                $backendUrl = \Yii::$app->params['backendUrl'] ?? 'http://localhost:8080';
                return rtrim($backendUrl, '/') . '/uploads/products/' . $this->image;
            }
        }
        return null;
    }

    /**
     * Получить полный путь к изображению
     *
     * @return string|null
     */
    public function getImagePath(): ?string
    {
        if ($this->image) {
            return \Yii::getAlias('@backend/web/uploads/products/' . $this->image);
        }
        return null;
    }
}
