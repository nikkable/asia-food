<?php
namespace console\controllers;

use Yii;
use yii\console\Controller;
use yii\console\ExitCode;
use yii\helpers\Console;
use repositories\Product\models\Product;
use repositories\Category\models\Category;
use context\File\interfaces\FileUploadServiceInterface;
use context\File\models\DownloadedFile;

/**
 * Контроллер для импорта данных из CSV файлов
 */
class ImportController extends Controller
{
    /**
     * @var FileUploadServiceInterface
     */
    private $fileUploadService;

    /**
     * Разделитель CSV по умолчанию
     * @var string
     */
    public $delimiter = ';';

    public function __construct($id, $module, FileUploadServiceInterface $fileUploadService, $config = [])
    {
        $this->fileUploadService = $fileUploadService;
        parent::__construct($id, $module, $config);
    }

    /**
     * Импорт товаров из CSV файла
     * 
     * @param string $filePath Путь к CSV файлу (по умолчанию data/products.csv)
     * @param string $delimiter Разделитель CSV (по умолчанию ';')
     * @return int Код завершения
     */
    public function actionProducts($filePath = null, $delimiter = null)
    {
        if ($delimiter !== null) {
            $this->delimiter = $delimiter;
        }

        if ($filePath === null) {
            $filePath = Yii::getAlias('@app/../data/products.csv');
        } else {
            $filePath = Yii::getAlias($filePath);
        }

        if (!file_exists($filePath)) {
            $this->stderr("Файл не найден: {$filePath}\n", Console::FG_RED);
            return ExitCode::DATAERR;
        }

        $this->stdout("Начинаем импорт товаров из файла: {$filePath}\n", Console::FG_GREEN);
        $this->stdout("Используемый разделитель: '{$this->delimiter}'\n", Console::FG_GREEN);
        
        // Открываем CSV файл
        $handle = fopen($filePath, 'r');
        if (!$handle) {
            $this->stderr("Не удалось открыть файл: {$filePath}\n", Console::FG_RED);
            return ExitCode::IOERR;
        }

        // Определяем кодировку файла и конвертируем при необходимости
        $firstLine = fgets($handle);
        rewind($handle);
        
        // Пытаемся определить BOM (Byte Order Mark) для UTF-8
        $bom = false;
        if (substr($firstLine, 0, 3) == chr(0xEF).chr(0xBB).chr(0xBF)) {
            $bom = true;
            $this->stdout("Обнаружена метка BOM UTF-8, она будет удалена при импорте\n", Console::FG_YELLOW);
        }

        // Читаем заголовки (первая строка)
        $headers = fgetcsv($handle, 0, $this->delimiter);
        if (!$headers) {
            $this->stderr("Не удалось прочитать заголовки CSV файла\n", Console::FG_RED);
            fclose($handle);
            return ExitCode::DATAERR;
        }

        // Удаляем BOM из первого заголовка если он есть
        if ($bom && isset($headers[0])) {
            $headers[0] = substr($headers[0], 3);
        }

        // Преобразуем заголовки в нижний регистр для удобства
        $headers = array_map('trim', $headers);
        $headers = array_map('strtolower', $headers);

        // Определяем индексы нужных нам колонок
        $columnMap = [
            'name' => array_search('title', $headers) !== false ? array_search('title', $headers) : false,
            'description' => array_search('description', $headers) !== false ? array_search('description', $headers) : false,
            'price' => array_search('price', $headers) !== false ? array_search('price', $headers) : false,
            'quantity' => array_search('quantity', $headers) !== false ? array_search('quantity', $headers) : false,
            'article' => array_search('sku', $headers) !== false ? array_search('sku', $headers) : false,
            'image' => array_search('photo', $headers) !== false ? array_search('photo', $headers) : false,
            'category' => array_search('category', $headers) !== false ? array_search('category', $headers) : false,
            'price_old' => array_search('price old', $headers) !== false ? array_search('price old', $headers) : false,
        ];

        // Проверяем наличие обязательных колонок
        if ($columnMap['name'] === false || $columnMap['price'] === false) {
            $this->stderr("В CSV файле отсутствуют обязательные колонки (Title или Price)\n", Console::FG_RED);
            fclose($handle);
            return ExitCode::DATAERR;
        }

        // Счетчики для статистики
        $total = 0;
        $created = 0;
        $updated = 0;
        $skipped = 0;

        // Кэш категорий для оптимизации запросов
        $categoryCache = [];

        // Читаем данные построчно
        while (($data = fgetcsv($handle, 0, $this->delimiter)) !== false) {
            $total++;
            
            // Пропускаем строки с недостаточным количеством колонок
            if (count($data) < count($headers)) {
                $this->stdout("Строка {$total}: Недостаточно данных, пропускаем\n", Console::FG_YELLOW);
                $skipped++;
                continue;
            }

            // Извлекаем данные из CSV
            $name = $columnMap['name'] !== false && isset($data[$columnMap['name']]) ? trim($data[$columnMap['name']]) : null;
            $description = $columnMap['description'] !== false && isset($data[$columnMap['description']]) ? trim($data[$columnMap['description']]) : null;
            $price = $columnMap['price'] !== false && isset($data[$columnMap['price']]) ? str_replace(',', '.', trim($data[$columnMap['price']])) : null;
            $quantity = $columnMap['quantity'] !== false && isset($data[$columnMap['quantity']]) ? (int)trim($data[$columnMap['quantity']]) : 0;
            $article = $columnMap['article'] !== false && isset($data[$columnMap['article']]) ? trim($data[$columnMap['article']]) : null;
            $imageUrl = $columnMap['image'] !== false && isset($data[$columnMap['image']]) ? trim($data[$columnMap['image']]) : null;
            $categoryName = $columnMap['category'] !== false && isset($data[$columnMap['category']]) ? trim($data[$columnMap['category']]) : null;
            $priceOld = $columnMap['price_old'] !== false && isset($data[$columnMap['price_old']]) ? str_replace(',', '.', trim($data[$columnMap['price_old']])) : null;

            // Проверяем обязательные поля
            if (empty($name) || empty($price)) {
                $this->stdout("Строка {$total}: Отсутствуют обязательные данные (название или цена), пропускаем\n", Console::FG_YELLOW);
                $skipped++;
                continue;
            }

            // Ищем или создаем категорию
            $categoryId = null;
            if (!empty($categoryName)) {
                if (!isset($categoryCache[$categoryName])) {
                    // Ищем категорию по имени
                    $category = Category::find()->where(['name' => $categoryName])->one();
                    
                    // Если категории нет, создаем новую
                    if (!$category) {
                        $category = new Category();
                        $category->name = $categoryName;
                        $category->status = 1; // Активная категория
                        if ($category->save()) {
                            $this->stdout("Создана новая категория: {$categoryName}\n", Console::FG_GREEN);
                        } else {
                            $this->stderr("Ошибка создания категории {$categoryName}: " . implode(', ', $category->getErrorSummary(true)) . "\n", Console::FG_RED);
                        }
                    }

                    $categoryCache[$categoryName] = $category->id;
                }

                $categoryId = $categoryCache[$categoryName];
            }

            // Ищем товар по артикулу (если есть) или по названию
            $product = null;
            if (!empty($article)) {
                $product = Product::find()->where(['article' => $article])->one();
            }
            
            if (!$product) {
                $product = Product::find()->where(['name' => $name])->one();
            }

            // Если товар не найден, создаем новый
            if (!$product) {
                $product = new Product();
                $product->name = $name;
                $product->status = 1; // Активный товар
                $isNew = true;
            } else {
                $isNew = false;
            }

            // Заполняем данные товара
            if ($categoryId) {
                $product->category_id = $categoryId;
            } else {
                // Если категория не указана, используем первую доступную или создаем "Без категории"
                if (!isset($categoryCache['Без категории'])) {
                    $defaultCategory = Category::find()->where(['name' => 'Без категории'])->one();
                    if (!$defaultCategory) {
                        $defaultCategory = new Category();
                        $defaultCategory->name = 'Без категории';
                        $defaultCategory->status = 1;
                        if ($defaultCategory->save()) {
                            $this->stdout("Создана категория 'Без категории'\n", Console::FG_GREEN);
                        }
                    }
                    $categoryCache['Без категории'] = $defaultCategory->id;
                }
                $product->category_id = $categoryCache['Без категории'];
            }

            $product->description = $description;
            $product->price = $price;
            $product->quantity = $quantity;
            
            // Устанавливаем скидочную цену, если она есть
            if (!empty($priceOld) && is_numeric($priceOld)) {
                $product->price_discount = $priceOld;
            }
            
            if (!empty($article)) {
                $product->article = $article;
            }
            
            // Загружаем изображение, если есть URL
            if (!empty($imageUrl)) {
                $imageName = $this->uploadImageFromUrl($imageUrl, $product->image);
                if ($imageName) {
                    $product->image = $imageName;
                } else {
                    $this->stdout("Строка {$total}: Не удалось загрузить изображение по URL: {$imageUrl}\n", Console::FG_YELLOW);
                }
            }

            // Сохраняем товар
            if ($product->save()) {
                if ($isNew) {
                    $this->stdout("Строка {$total}: Создан новый товар: {$name}\n", Console::FG_GREEN);
                    $created++;
                } else {
                    $this->stdout("Строка {$total}: Обновлен товар: {$name}\n", Console::FG_BLUE);
                    $updated++;
                }
            } else {
                $this->stderr("Строка {$total}: Ошибка сохранения товара {$name}: " . implode(', ', $product->getErrorSummary(true)) . "\n", Console::FG_RED);
                $skipped++;
            }
        }

        fclose($handle);

        $this->stdout("\nИмпорт завершен.\n", Console::FG_GREEN);
        $this->stdout("Всего обработано: {$total}\n", Console::FG_GREEN);
        $this->stdout("Создано новых товаров: {$created}\n", Console::FG_GREEN);
        $this->stdout("Обновлено товаров: {$updated}\n", Console::FG_GREEN);
        $this->stdout("Пропущено: {$skipped}\n", Console::FG_YELLOW);

        return ExitCode::OK;
    }

    /**
     * Загружает изображение из URL, сохраняет его и возвращает новое имя файла.
     *
     * @param string $url URL изображения
     * @param string|null $oldFileName Имя старого файла для удаления
     * @return string|null Новое имя файла или null в случае ошибки
     */
    private function uploadImageFromUrl(string $url, ?string $oldFileName = null): ?string
    {
        // Создаем временный файл
        $tempPath = tempnam(sys_get_temp_dir(), 'import_img_');
        if (!$tempPath) {
            $this->stderr("Не удалось создать временный файл.\n", Console::FG_RED);
            return null;
        }

        try {
            // Скачиваем содержимое файла
            $fileContent = @file_get_contents($url);
            if ($fileContent === false) {
                $this->stderr("Не удалось скачать файл по URL: {$url}\n", Console::FG_RED);
                return null;
            }

            // Записываем содержимое во временный файл
            if (file_put_contents($tempPath, $fileContent) === false) {
                $this->stderr("Не удалось записать данные во временный файл: {$tempPath}\n", Console::FG_RED);
                return null;
            }

            // Получаем информацию о файле
            $fileSize = filesize($tempPath);
            $fileType = mime_content_type($tempPath);
            // Убираем GET-параметры из URL, чтобы получить чистое имя файла
            $urlPath = parse_url($url, PHP_URL_PATH);
            $originalName = basename($urlPath);

            // Создаем наш объект для скачанного файла
            $downloadedFile = new DownloadedFile(
                $originalName,
                $tempPath,
                $fileType,
                $fileSize,
                UPLOAD_ERR_OK
            );

            // Используем сервис для загрузки
            $newFileName = $this->fileUploadService->uploadImage($downloadedFile, 'products', $oldFileName);

            if (!$newFileName) {
                $error = $this->fileUploadService->getLastError();
                $this->stderr("Сервис загрузки не смог обработать файл '{$originalName}'. Причина: {$error}\n", Console::FG_RED);
                return null;
            }

            return $newFileName;

        } catch (\Exception $e) {
            $this->stderr("Исключение при загрузке файла: " . $e->getMessage() . "\n", Console::FG_RED);
            return null;
        } finally {
            // Удаляем временный файл
            if (file_exists($tempPath)) {
                unlink($tempPath);
            }
        }
    }
}
