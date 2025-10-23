<?php

namespace context\Commerce1C\services;

use context\Commerce1C\interfaces\CommerceImportInterface;
use context\Commerce1C\interfaces\CommerceSessionInterface;
use context\Commerce1C\interfaces\CommerceAuthInterface;
use repositories\Commerce1C\interfaces\Commerce1CSyncRepositoryInterface;
use context\Commerce1C\parsers\CatalogXmlParser;
use context\Commerce1C\parsers\OffersXmlParser;
use repositories\Commerce1C\models\CommerceRequest;
use repositories\Commerce1C\models\CommerceResponse;
use context\AbstractService;

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

        // Устанавливаем метаданные для сессии
        $session->setMetadata('initialized_at', new \DateTime());
        $session->setMetadata('zip', 'no');
        $session->setMetadata('file_limit', 52428800); // 50MB в байтах
        
        $this->sessionService->saveSession($session);

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

        $filePath = $this->getFilePathForSession($sessionId, $filename);
        
        if (file_put_contents($filePath, $content) === false) {
            return CommerceResponse::failure('Failed to save file');
        }
        
        $session->addUploadedFile($filename, $filePath);
        $this->sessionService->saveSession($session);

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
            
            $productsCount = $this->syncRepository->syncProducts($catalogData['products']);
            
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
        
        return $sessionDir . '/' . $filename;
    }

}
