<?php

namespace context\Commerce1C\services;

use context\Commerce1C\interfaces\CommerceImportInterface;
use context\Commerce1C\interfaces\CommerceSessionInterface;
use context\Commerce1C\interfaces\CommerceAuthInterface;
use repositories\Commerce1C\interfaces\Commerce1CSyncRepositoryInterface;
use context\Commerce1C\parsers\CatalogXmlParser;
use context\Commerce1C\parsers\OffersXmlParser;
use context\Commerce1C\enums\ImportFileTypeEnum;
use repositories\Commerce1C\models\CommerceRequest;
use repositories\Commerce1C\models\CommerceResponse;
use context\AbstractService;
use Yii;

class CommerceImportService extends AbstractService implements CommerceImportInterface
{
    private string $filesDirectory;
    
    public function __construct(
        private readonly CommerceSessionInterface          $sessionService,
        private readonly Commerce1CSyncRepositoryInterface $syncRepository,
        private readonly CommerceAuthInterface             $authService
    ) {
        $this->filesDirectory = dirname(\Yii::getAlias('@app')) . '/context/Commerce1C/files';
        if (!is_dir($this->filesDirectory)) {
            mkdir($this->filesDirectory, 0755, true);
        }
    }

    public function initialize(CommerceRequest $request): CommerceResponse
    {
        $sessionId = $this->authService->getSessionIdFromRequest();
        
        if (!$sessionId) {
            return CommerceResponse::failure('Session ID required');
        }

        $session = $this->sessionService->getSession($sessionId);
        if (!$session) {
            return CommerceResponse::failure('Invalid session');
        }

        // Очищаем старые сессии и файлы при инициализации
        $this->cleanupOldSessions();

        // Устанавливаем метаданные для сессии
        $session->setMetadata('initialized_at', new \DateTime());
        $session->setMetadata('zip', 'no');
        $session->setMetadata('file_limit', 52428800); // 50MB в байтах
        
        $this->sessionService->saveSession($session);

        Yii::info("Commerce import session initialized: {$sessionId}", __METHOD__);

        return CommerceResponse::success("zip=no\nfile_limit=52428800");
    }

    public function saveFile(CommerceRequest $request): CommerceResponse
    {
        $sessionId = $this->authService->getSessionIdFromRequest();
        $filename = $request->getFilename();
        $content = $request->getContent();

        if (!$sessionId || !$filename || !$content) {
            return CommerceResponse::failure('Session ID, filename and content required');
        }

        $session = $this->sessionService->getSession($sessionId);
        if (!$session) {
            return CommerceResponse::failure('Invalid session');
        }

        // Очищаем старые XML файлы перед сохранением нового
        $this->cleanupOldXmlFiles($sessionId, $filename);

        // Санитизация относительного пути (защита от directory traversal)
        $safeRelativePath = $this->sanitizeRelativePath($filename);

        $filePath = $this->getFilePathForSession($sessionId, $safeRelativePath);
        
        if (file_put_contents($filePath, $content) === false) {
            return CommerceResponse::failure('Failed to save file');
        }
        
        $session->addUploadedFile($safeRelativePath, $filePath);
        $this->sessionService->saveSession($session);

        Yii::info("File saved successfully: {$filename} (size: " . strlen($content) . " bytes)", __METHOD__);

        return CommerceResponse::success();
    }

    public function importCatalog(CommerceRequest $request): CommerceResponse
    {
        $sessionId = $this->authService->getSessionIdFromRequest();
        $filename = $request->getFilename();

        if (!$sessionId || !$filename) {
            return CommerceResponse::failure('Session ID and filename required');
        }

        $session = $this->sessionService->getSession($sessionId);
        if (!$session) {
            return CommerceResponse::failure('Invalid session');
        }

        if (!$session->isFileUploaded($filename)) {
            return CommerceResponse::failure('File not found in session');
        }

        $filePath = $session->getFileContent($filename);
        
        if (!file_exists($filePath)) {
            return CommerceResponse::failure('File not found on disk');
        }
        
        $xmlContent = file_get_contents($filePath);
        
        try {
            $parser = new CatalogXmlParser();
            $catalogData = $parser->parse($xmlContent);

            $categoriesCount = $this->syncRepository->syncCategories($catalogData['categories']);

            // Обрабатываем изображения товаров: переносим из сессии в uploads/products и подставляем имя файла
            $preparedProducts = [];
            foreach ($catalogData['products'] as $p) {
                if (!empty($p['images']) && is_array($p['images'])) {
                    // Берем первое изображение как основное
                    $main = $p['images'][0];
                    $saved = $this->saveProductImageFromSession($sessionId, $main);
                    if ($saved) {
                        $p['image'] = $saved; // только имя файла (basename) для хранения в БД
                    }
                }
                $preparedProducts[] = $p;
            }

            $productsCount = $this->syncRepository->syncProducts($preparedProducts);
            
            $session->markFileAsImported($filename);
            $this->sessionService->saveSession($session);

            return CommerceResponse::success("Catalog imported: {$categoriesCount} categories, {$productsCount} products");
            
        } catch (\Exception $e) {
            return CommerceResponse::failure('Import failed: ' . $e->getMessage());
        }
    }

    public function importOffers(CommerceRequest $request): CommerceResponse
    {
        $sessionId = $this->authService->getSessionIdFromRequest();
        $filename = $request->getFilename();

        if (!$sessionId || !$filename) {
            return CommerceResponse::failure('Session ID and filename required');
        }

        $session = $this->sessionService->getSession($sessionId);
        if (!$session) {
            return CommerceResponse::failure('Invalid session');
        }

        if (!$session->isFileUploaded($filename)) {
            return CommerceResponse::failure('File not found in session');
        }

        $filePath = $session->getFileContent($filename); // Теперь это путь к файлу
        
        if (!file_exists($filePath)) {
            return CommerceResponse::failure('File not found on disk');
        }
        
        $xmlContent = file_get_contents($filePath);
        
        try {
            $parser = new OffersXmlParser();
            $offersData = $parser->parse($xmlContent);
            
            $offersCount = $this->syncRepository->syncOffers($offersData);
            
            $session->markFileAsImported($filename);
            $this->sessionService->saveSession($session);

            return CommerceResponse::success("Offers imported: {$offersCount} offers updated");
            
        } catch (\Exception $e) {
            return CommerceResponse::failure('Import failed: ' . $e->getMessage());
        }
    }

    
    private function getFilePathForSession(string $sessionId, string $filename): string
    {
        $sessionDir = $this->filesDirectory . '/' . $sessionId;
        if (!is_dir($sessionDir)) {
            mkdir($sessionDir, 0755, true);
        }

        // Поддержка вложенных директорий (например import_files/02/xxx.jpg)
        $relativeDir = dirname($filename);
        if ($relativeDir !== '.' && $relativeDir !== '/') {
            $targetDir = $sessionDir . '/' . $relativeDir;
            if (!is_dir($targetDir)) {
                mkdir($targetDir, 0755, true);
            }
        }

        return $sessionDir . '/' . $filename;
    }

    /**
     * Очищает старые XML файлы, оставляя только новые catalog и offers файлы
     * 
     * @param string $sessionId ID текущей сессии
     * @param string $newFilename Имя нового загружаемого файла
     */
    private function cleanupOldXmlFiles(string $sessionId, string $newFilename): void
    {
        try {
            $sessionDir = $this->filesDirectory . '/' . $sessionId;
            
            if (!is_dir($sessionDir)) {
                return;
            }
            
            // Определяем тип нового файла
            $newFileType = $this->getFileType($newFilename);
            if (!$newFileType) {
                return; // Если это не catalog или offers файл, не очищаем
            }
            
            $files = scandir($sessionDir);
            if ($files === false) {
                Yii::warning("Cannot read directory: {$sessionDir}", __METHOD__);
                return;
            }
            
            $filesToDelete = [];
            
            foreach ($files as $file) {
                if ($file === '.' || $file === '..' || $file === $newFilename) {
                    continue;
                }
                
                $filePath = $sessionDir . '/' . $file;
                if (!is_file($filePath)) {
                    continue;
                }
                
                $existingFileType = $this->getFileType($file);
                
                // Удаляем файлы того же типа (catalog или offers)
                if ($existingFileType === $newFileType) {
                    $filesToDelete[] = $filePath;
                }
                
                // Также удаляем старые XML файлы, которые могут быть от предыдущих загрузок
                if ($this->isXmlFile($file) && $this->isOldFile($filePath)) {
                    $filesToDelete[] = $filePath;
                }
            }
            
            // Удаляем найденные файлы
            foreach ($filesToDelete as $fileToDelete) {
                if (unlink($fileToDelete)) {
                    Yii::info("Deleted old XML file: {$fileToDelete}", __METHOD__);
                } else {
                    Yii::warning("Failed to delete old XML file: {$fileToDelete}", __METHOD__);
                }
            }
            
        } catch (\Exception $e) {
            Yii::error("Error during XML files cleanup: " . $e->getMessage(), __METHOD__);
        }
    }

    /**
     * Санитизация относительного пути, запрещаем выход из директории (..), абсолютные пути и NULL байты
     */
    private function sanitizeRelativePath(string $path): string
    {
        $path = str_replace("\0", '', $path);
        $path = ltrim($path, "\\/");
        // Нормализуем разделители
        $parts = [];
        foreach (explode('/', str_replace('\\', '/', $path)) as $seg) {
            if ($seg === '' || $seg === '.') { continue; }
            if ($seg === '..') { continue; } // запрещаем подниматься вверх
            $parts[] = $seg;
        }
        return implode('/', $parts);
    }

    /**
     * Копирует файл изображения из сессионной директории в uploads/products и возвращает сохраненное имя файла
     */
    private function saveProductImageFromSession(string $sessionId, string $relativeImagePath): ?string
    {
        $safeRel = $this->sanitizeRelativePath($relativeImagePath);
        $sourcePath = $this->getFilePathForSession($sessionId, $safeRel);
        if (!is_file($sourcePath)) {
            Yii::warning("Product image not found in session: {$safeRel}", __METHOD__);
            return null;
        }

        $ext = strtolower(pathinfo($sourcePath, PATHINFO_EXTENSION));
        $allowed = ['jpg','jpeg','png','gif','webp'];
        if (!in_array($ext, $allowed, true)) {
            Yii::warning("Unsupported image extension: .{$ext}", __METHOD__);
            return null;
        }

        // Папка назначения
        $targetDir = \Yii::getAlias('@backend/web/uploads/products');
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0755, true);
        }

        // Генерация уникального имени файла
        $baseName = pathinfo($sourcePath, PATHINFO_FILENAME);
        $hash = substr(sha1($baseName . '|' . $sessionId . '|' . filesize($sourcePath)), 0, 12);
        $fileName = $hash . '.' . $ext;
        $targetPath = rtrim($targetDir, '/').'/'.$fileName;

        // Если файл уже существует (идемпотентность), не переписываем
        if (!is_file($targetPath)) {
            if (!copy($sourcePath, $targetPath)) {
                Yii::error("Failed to copy product image to uploads: {$sourcePath} -> {$targetPath}", __METHOD__);
                return null;
            }
        }

        return $fileName;
    }
    
    /**
     * Определяет тип файла (catalog или offers)
     * 
     * @param string $filename
     * @return string|null
     */
    private function getFileType(string $filename): ?string
    {
        // Проверяем точные имена файлов
        if ($filename === ImportFileTypeEnum::CATALOG->value) {
            return 'catalog';
        }
        
        if ($filename === ImportFileTypeEnum::OFFERS->value) {
            return 'offers';
        }
        
        // Проверяем по паттернам для файлов с номерами (import0_1.xml, import0_2.xml и т.д.)
        if (preg_match('/^import\d+_\d+\.xml$/i', $filename)) {
            return 'catalog';
        }
        
        if (preg_match('/^offers\d+_\d+\.xml$/i', $filename)) {
            return 'offers';
        }
        
        return null;
    }
    
    /**
     * Проверяет, является ли файл XML файлом
     * 
     * @param string $filename
     * @return bool
     */
    private function isXmlFile(string $filename): bool
    {
        return strtolower(pathinfo($filename, PATHINFO_EXTENSION)) === 'xml';
    }
    
    /**
     * Проверяет, является ли файл старым (старше 1 часа)
     * 
     * @param string $filePath
     * @return bool
     */
    private function isOldFile(string $filePath): bool
    {
        if (!file_exists($filePath)) {
            return false;
        }
        
        $fileTime = filemtime($filePath);
        $currentTime = time();
        
        // Считаем файл старым, если он создан более часа назад
        return ($currentTime - $fileTime) > 3600;
    }

    /**
     * Очищает старые директории сессий и их файлы
     */
    private function cleanupOldSessions(): void
    {
        try {
            if (!is_dir($this->filesDirectory)) {
                return;
            }
            
            $sessions = scandir($this->filesDirectory);
            if ($sessions === false) {
                Yii::warning("Cannot read files directory: {$this->filesDirectory}", __METHOD__);
                return;
            }
            
            $currentTime = time();
            $maxSessionAge = 24 * 3600; // 24 часа
            
            foreach ($sessions as $sessionDir) {
                if ($sessionDir === '.' || $sessionDir === '..') {
                    continue;
                }
                
                $sessionPath = $this->filesDirectory . '/' . $sessionDir;
                if (!is_dir($sessionPath)) {
                    continue;
                }
                
                $sessionTime = filemtime($sessionPath);
                if (($currentTime - $sessionTime) > $maxSessionAge) {
                    $this->removeDirectoryRecursively($sessionPath);
                    Yii::info("Removed old session directory: {$sessionPath}", __METHOD__);
                }
            }
            
        } catch (\Exception $e) {
            Yii::error("Error during old sessions cleanup: " . $e->getMessage(), __METHOD__);
        }
    }
    
    /**
     * Рекурсивно удаляет директорию и все её содержимое
     * 
     * @param string $dir
     * @return bool
     */
    private function removeDirectoryRecursively(string $dir): bool
    {
        if (!is_dir($dir)) {
            return false;
        }
        
        $files = scandir($dir);
        if ($files === false) {
            return false;
        }
        
        foreach ($files as $file) {
            if ($file === '.' || $file === '..') {
                continue;
            }
            
            $filePath = $dir . '/' . $file;
            if (is_dir($filePath)) {
                $this->removeDirectoryRecursively($filePath);
            } else {
                unlink($filePath);
            }
        }
        
        return rmdir($dir);
    }

}
