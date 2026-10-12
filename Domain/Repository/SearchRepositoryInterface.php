<?php

namespace Domain\Repository;

use Domain\Entity\Category;

interface SearchRepositoryInterface
{
    /**
     * @return Category[]
     */
    public function searchCategories(string $query): array;
}
