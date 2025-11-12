<?php

namespace repositories\Product\models;

use repositories\Category\models\Category;
use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\SluggableBehavior;
use yii\web\UploadedFile;
use context\File\interfaces\ImageResizeServiceInterface;

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
 * @property mixed|null $category
 */
class Product extends ActiveRecord
{
    public $imageFile;

    public static function tableName(): string
    {
        return '{{%product}}';
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
            [['name', 'price', 'slug'], 'required'],
            [['category_id', 'quantity', 'status', 'created_at', 'updated_at'], 'integer'],
            [['description'], 'string'],
            [['price', 'price_discount'], 'number'],
            [['name', 'image', 'article', 'external_id'], 'string', 'max' => 255],
            [['external_id'], 'unique'],
            [['imageFile'], 'safe'],
        ];
    }

    public function getCategory(): ActiveQuery
    {
        return $this->hasOne(Category::class, ['id' => 'category_id']);
    }

    public function attributeLabels(): array
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
            $basePath = Yii::$app->id === 'app-backend' ? '@web/uploads/products/' : rtrim(Yii::$app->params['backendUrl'], '/') . '/uploads/products/';
            return $basePath . 'default.png';
        }

        // Во фронтенде используем ImageController для динамической генерации
        if (Yii::$app->id === 'app-frontend') {
            $originalPath = 'products/' . $this->image;
            return "/image/resize?path=" . urlencode($originalPath) . "&w={$width}&h={$height}&mode={$mode}";
        }

        // В бэкенде используем сервис напрямую
        /** @var ImageResizeServiceInterface $imageService */
        $imageService = Yii::$container->get(ImageResizeServiceInterface::class);
        
        $originalPath = 'products/' . $this->image;
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
        
        $originalPath = 'products/' . $this->image;
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
        
        $originalPath = 'products/' . $this->image;
        return $imageService->deleteThumbnails($originalPath);
    }
}
