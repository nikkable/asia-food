<?php

namespace context\Category\services;

use context\AbstractService;
use context\Category\interfaces\CategoryServiceInterface;
use repositories\Category\interfaces\CategoryRepositoryInterface;
use repositories\Category\models\Category;

class CategoryService extends AbstractService implements CategoryServiceInterface
{
    public function __construct(
        private readonly CategoryRepositoryInterface $categoryRepository
    ) {
    }

    public function findBySlug(string $slug): ?Category
    {
        return $this->categoryRepository->findBySlug($slug);
    }

    public function getRoot(): array
    {
        return $this->categoryRepository->getRoot();
    }
}
