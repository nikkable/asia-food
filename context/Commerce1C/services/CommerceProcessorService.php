<?php

namespace context\Commerce1C\services;

use context\Commerce1C\interfaces\CommerceProcessorInterface;
use context\Commerce1C\interfaces\CommerceAuthInterface;
use context\Commerce1C\interfaces\CommerceImportInterface;
use context\Commerce1C\enums\ImportFileTypeEnum;
use repositories\Commerce1C\models\CommerceRequest;
use repositories\Commerce1C\models\CommerceResponse;
use context\AbstractService;

class CommerceProcessorService extends AbstractService implements CommerceProcessorInterface
{
    public function __construct(
        private readonly CommerceAuthInterface   $authService,
        private readonly CommerceImportInterface $importService
    ) {}

    public function processRequest(CommerceRequest $request): CommerceResponse
    {
        if (!$this->isRequestSupported($request)) {
            return CommerceResponse::failure('Unsupported request type or mode');
        }

        return match ([$request->getType()->value, $request->getMode()->value]) {
            ['catalog', 'checkauth'] => $this->authService->checkAuth($request),
            ['catalog', 'init'] => $this->handleAuthenticatedRequest($request, fn() => $this->importService->initialize($request)),
            ['catalog', 'file'] => $this->handleAuthenticatedRequest($request, fn() => $this->importService->saveFile($request)),
            ['catalog', 'import'] => $this->handleImportRequest($request),
            default => CommerceResponse::failure('Request handler not implemented')
        };
    }

    private function isRequestSupported(CommerceRequest $request): bool
    {
        $supportedRequests = [
            ['catalog', 'checkauth'],
            ['catalog', 'init'],
            ['catalog', 'file'],
            ['catalog', 'import']
        ];

        return in_array([$request->getType()->value, $request->getMode()->value], $supportedRequests, true);
    }

    private function handleAuthenticatedRequest(CommerceRequest $request, callable $handler): CommerceResponse
    {
        $sessionId = $this->authService->getSessionIdFromRequest();

        if (!$sessionId || !$this->authService->validateSession($sessionId)) {
            return CommerceResponse::failure('Invalid or expired session', 401);
        }

        return $handler();
    }

    private function handleImportRequest(CommerceRequest $request): CommerceResponse
    {
        return $this->handleAuthenticatedRequest($request, function() use ($request) {
            $filename = $request->getFilename();
            
            return match ($filename) {
                ImportFileTypeEnum::CATALOG->value => $this->importService->importCatalog($request),
                ImportFileTypeEnum::OFFERS->value => $this->importService->importOffers($request),
                default => CommerceResponse::failure('Unknown file type for import')
            };
        });
    }
}
