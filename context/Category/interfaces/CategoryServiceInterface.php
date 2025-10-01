<?php

namespace context\Category\interfaces;

use repositories\Category\models\Category;

interface CategoryServiceInterface
{
    public function findBySlug(string $slug): ?Category;

    public function getRoot(): array;
}
