<?php

namespace context\Product\services;

use context\AbstractService;
use context\Product\interfaces\ProductServiceInterface;
use repositories\Product\interfaces\ProductRepositoryInterface;
use repositories\Product\models\Product;
use repositories\Category\models\Category;

class ProductService extends AbstractService implements ProductServiceInterface
{
    public function __construct(
        private readonly ProductRepositoryInterface $productRepository
    ) {
    }

    public function findById(int $id): ?Product
    {
        return $this->productRepository->findById($id);
    }

    public function findBySlug(string $slug): ?Product
    {
        return $this->productRepository->findBySlug($slug);
    }

    public function findAllByCategory(Category $category): array
    {
        return $this->productRepository->findAllByCategory($category);
    }
}
