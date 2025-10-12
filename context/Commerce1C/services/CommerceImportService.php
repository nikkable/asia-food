<?php

namespace context\Commerce1C\services;

use context\Commerce1C\interfaces\CommerceImportInterface;
use context\Commerce1C\interfaces\CommerceSessionInterface;
use repositories\Commerce1C\interfaces\Commerce1CSyncRepositoryInterface;
use context\Commerce1C\enums\ImportFileTypeEnum;
use context\Commerce1C\parsers\CatalogXmlParser;
use context\Commerce1C\parsers\OffersXmlParser;
use context\Commerce1C\models\CommerceRequest;
use context\Commerce1C\models\CommerceResponse;
use context\AbstractService;

class CommerceImportService extends AbstractService implements CommerceImportInterface
{
    public function __construct(
        private CommerceSessionInterface $sessionService,
        private Commerce1CSyncRepositoryInterface $syncRepository
    ) {}

    public function initialize(CommerceRequest $request): CommerceResponse
    {
        $sessionId = $this->getSessionIdFromRequest();
        
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
        $session->setMetadata('file_limit', 1024000); // 1MB лимит
        
        $this->sessionService->saveSession($session);

        return CommerceResponse::progressSuccess($sessionId);
    }

    public function saveFile(CommerceRequest $request): CommerceResponse
    {
        $sessionId = $this->getSessionIdFromRequest();
        $filename = $request->getFilename();
        $content = $request->getContent();

        if (!$sessionId || !$filename || !$content) {
            return CommerceResponse::failure('Session ID, filename and content required');
        }

        $session = $this->sessionService->getSession($sessionId);
        if (!$session) {
            return CommerceResponse::failure('Invalid session');
        }

        // Проверяем размер файла
        $fileLimit = $session->getMetadataValue('file_limit') ?? 1024000;
        if (strlen($content) > $fileLimit) {
            return CommerceResponse::failure('File too large');
        }

        // Сохраняем файл в сессии
        $session->addUploadedFile($filename, $content);
        $this->sessionService->saveSession($session);

        return CommerceResponse::success('File uploaded successfully');
    }

    public function importCatalog(CommerceRequest $request): CommerceResponse
    {
        $sessionId = $this->getSessionIdFromRequest();
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

        // Получаем содержимое файла
        $xmlContent = $session->getFileContent($filename);
        
        try {
            // Парсим XML каталога
            $parser = new CatalogXmlParser();
            $catalogData = $parser->parse($xmlContent);
            
            // Импортируем категории
            $categoriesCount = $this->syncRepository->syncCategories($catalogData['categories']);
            
            // Импортируем товары
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
        $sessionId = $this->getSessionIdFromRequest();
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

        // Получаем содержимое файла
        $xmlContent = $session->getFileContent($filename);
        
        try {
            // Парсим XML предложений
            $parser = new OffersXmlParser();
            $offersData = $parser->parse($xmlContent);
            
            // Импортируем остатки и цены
            $offersCount = $this->syncRepository->syncOffers($offersData);
            
            $session->markFileAsImported($filename);
            $this->sessionService->saveSession($session);

            return CommerceResponse::success("Offers imported: {$offersCount} offers updated");
            
        } catch (\Exception $e) {
            return CommerceResponse::failure('Import failed: ' . $e->getMessage());
        }
    }

    private function getSessionIdFromRequest(): ?string
    {
        return $_GET['session_id'] ?? $_POST['session_id'] ?? null;
    }

}
