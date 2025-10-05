<?php

namespace context\Product\services;

use context\AbstractService;
use context\Product\interfaces\BestsellerServiceInterface;
use repositories\Product\interfaces\BestsellerRepositoryInterface;
use repositories\Product\models\Product;

/**
 * Сервис для работы с хитами продаж
 */
class BestsellerService extends AbstractService implements BestsellerServiceInterface
{
    private BestsellerRepositoryInterface $bestsellerRepository;
    
    /**
     * @param BestsellerRepositoryInterface $bestsellerRepository
     */
    public function __construct(BestsellerRepositoryInterface $bestsellerRepository)
    {
        $this->bestsellerRepository = $bestsellerRepository;
    }
    
    /**
     * {@inheritdoc}
     */
    public function getBestsellers(int $limit = 20): array
    {
        return $this->bestsellerRepository->getBestsellers($limit);
    }
}
