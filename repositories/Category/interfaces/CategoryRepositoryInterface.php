<?php

namespace repositories\Category\interfaces;

use repositories\Category\models\Category;

interface CategoryRepositoryInterface
{
    public function findBySlug(string $slug): ?Category;

    public function getRoot(): array;
}
