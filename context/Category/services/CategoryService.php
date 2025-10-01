<?php

namespace context\Category\services;

use context\AbstractService;
use context\Category\interfaces\CategoryServiceInterface;

class CategoryService extends AbstractService implements CategoryServiceInterface
{
    public function __construct(
        private readonly CategoryRepositoryInterface $categoryRepository
    ) {
    }
}
