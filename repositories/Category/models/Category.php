<?php

namespace repositories\Category\models;

use Yii;
use yii\db\ActiveQuery;
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
    public ?UploadedFile $imageFile;

    public static function tableName(): string
    {
        return '{{%category}}';
    }

    public function behaviors(): array
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

    public function rules(): array
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

    public function getParent(): ActiveQuery
    {
        return $this->hasOne(Category::class, ['id' => 'parent_id']);
    }

    public function getChildren(): ActiveQuery
    {
        return $this->hasMany(Category::class, ['parent_id' => 'id']);
    }

    public function getProducts(): ActiveQuery
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
     */
    public function getImageUrl(): ?string
    {
        $basePath = Yii::$app->id === 'app-backend' ? '@web/uploads/categories/' : rtrim(Yii::$app->params['backendUrl'], '/') . '/uploads/categories/';

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
