<?php

namespace repositories\Category\models;

use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\SluggableBehavior;
use yii\web\UploadedFile;
use repositories\Product\models\Product;

/**
 * This is the model class for table "{{%category}}".
 *
 * @property int $id
 * @property int|null $parent_id
 * @property string $name
 * @property string $slug
 * @property string|null $description
 * @property string|null $image
 * @property string|null $external_id
 * @property int $status
 * @property int $created_at
 * @property int $updated_at
 *
 * @property UploadedFile|null $imageFile
 */
class Category extends ActiveRecord
{
    /**
     * @var UploadedFile|null
     */
    public $imageFile;
    public static function tableName()
    {
        return '{{%category}}';
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
            [['name', 'slug'], 'required'],
            [['parent_id', 'status', 'created_at', 'updated_at'], 'integer'],
            [['description'], 'string'],
            [['name', 'slug', 'image', 'external_id'], 'string', 'max' => 255],
            [['slug'], 'unique'],
            [['external_id'], 'unique'],
            [['imageFile'], 'safe'],
        ];
    }

    /**
     * Gets query for [[Parent]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getParent()
    {
        return $this->hasOne(Category::class, ['id' => 'parent_id']);
    }

    /**
     * Gets query for [[Children]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getChildren()
    {
        return $this->hasMany(Category::class, ['parent_id' => 'id']);
    }

    /**
     * Gets query for [[Products]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProducts()
    {
        return $this->hasMany(Product::class, ['category_id' => 'id']);
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'parent_id' => 'Parent ID',
            'name' => 'Name',
            'slug' => 'Slug',
            'description' => 'Description',
            'image' => 'Image',
            'imageFile' => 'Изображение',
            'external_id' => 'Внешний ID (1C)',
            'status' => 'Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
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
                return \Yii::getAlias('@web/uploads/categories/' . $this->image);
            } else {
                // Для фронтенда используем полный URL бэкенда из параметров
                $backendUrl = \Yii::$app->params['backendUrl'] ?? 'http://localhost:8080';
                return rtrim($backendUrl, '/') . '/uploads/categories/' . $this->image;
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
            return \Yii::getAlias('@backend/web/uploads/categories/' . $this->image);
        }
        return null;
    }
}
