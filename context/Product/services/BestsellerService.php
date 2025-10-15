<?php

namespace context\Product\services;

use context\AbstractService;
use context\Product\interfaces\BestsellerServiceInterface;
use repositories\Product\interfaces\BestsellerRepositoryInterface;

/**
 * Сервис для работы с хитами продаж
 */
class BestsellerService extends AbstractService implements BestsellerServiceInterface
{
    public function __construct(
        private readonly BestsellerRepositoryInterface $bestsellerRepository
    )
    {
    }
    
    public function getBestsellers(int $limit = 20): array
    {
        return $this->bestsellerRepository->getBestsellers($limit);
    }
}
