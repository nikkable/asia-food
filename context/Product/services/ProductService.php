<?php

namespace context\Product\services;

use context\AbstractService;
use context\Product\interfaces\ProductServiceInterface;

class ProductService extends AbstractService implements ProductServiceInterface
{
    public function __construct(
        private readonly ProductRepositoryInterface $productRepository
    ) {
    }

    // Реализация методов будет здесь
}
