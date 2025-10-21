<?php

namespace repositories\Category\models;

use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\SluggableBehavior;
use yii\web\UploadedFile;
use repositories\Product\models\Product;
use context\File\interfaces\ImageResizeServiceInterface;

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
    public $imageFile;

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
            'name' => 'Название',
            'slug' => 'Slug',
            'description' => 'Описание',
            'image' => 'Изображение',
            'imageFile' => 'Изображение',
            'external_id' => 'Внешний ID (1C)',
            'status' => 'Статус',
            'created_at' => 'Дата создания',
            'updated_at' => 'Дата обновления',
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

    /**
     * Получить URL кропленного изображения
     * @param int $width Ширина
     * @param int $height Высота
     * @param string $mode Режим кропа: 'crop', 'fit', 'stretch'
     * @return string
     */
    public function getCroppedImageUrl(int $width, int $height, string $mode = 'crop'): string
    {
        if (!$this->image) {
            // Возвращаем URL дефолтного изображения для указанных размеров
            $basePath = Yii::$app->id === 'app-backend' ? '@web/uploads/categories/' : rtrim(Yii::$app->params['backendUrl'], '/') . '/uploads/categories/';
            return $basePath . 'default.png';
        }

        // Во фронтенде используем ImageController для динамической генерации
        if (Yii::$app->id === 'app-frontend') {
            $originalPath = 'categories/' . $this->image;
            return "/image/resize?path=" . urlencode($originalPath) . "&w={$width}&h={$height}&mode={$mode}";
        }

        // В бэкенде используем сервис напрямую
        /** @var ImageResizeServiceInterface $imageService */
        $imageService = Yii::$container->get(ImageResizeServiceInterface::class);
        
        $originalPath = 'categories/' . $this->image;
        return $imageService->getResizedImageUrl($originalPath, $width, $height, $mode);
    }

    /**
     * Генерирует миниатюру изображения если она не существует
     * @param int $width Ширина
     * @param int $height Высота
     * @param string $mode Режим кропа
     * @return bool
     */
    public function generateThumbnail(int $width, int $height, string $mode = 'crop'): bool
    {
        if (!$this->image) {
            return false;
        }

        /** @var ImageResizeServiceInterface $imageService */
        $imageService = Yii::$container->get(ImageResizeServiceInterface::class);
        
        $originalPath = 'categories/' . $this->image;
        $thumbnailUrl = $imageService->getResizedImageUrl($originalPath, $width, $height, $mode);
        
        // Проверяем, существует ли миниатюра
        $thumbnailPath = str_replace('/uploads/', '', parse_url($thumbnailUrl, PHP_URL_PATH));
        if ($imageService->thumbnailExists($thumbnailPath)) {
            return true;
        }

        // Создаем миниатюру
        $sourceFile = Yii::getAlias('@backend/web/uploads/' . $originalPath);
        $targetFile = Yii::getAlias('@backend/web/uploads/' . $thumbnailPath);
        
        return $imageService->resize($sourceFile, $targetFile, $width, $height, $mode);
    }

    /**
     * Удаляет все миниатюры при удалении изображения
     */
    public function deleteThumbnails(): bool
    {
        if (!$this->image) {
            return true;
        }

        /** @var ImageResizeServiceInterface $imageService */
        $imageService = Yii::$container->get(ImageResizeServiceInterface::class);
        
        $originalPath = 'categories/' . $this->image;
        return $imageService->deleteThumbnails($originalPath);
    }
}
