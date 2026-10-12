<?php

namespace Tests\Doubles;

use Domain\Entity\Category;
use Domain\Repository\SearchRepositoryInterface;

class SearchRepositoryFake implements SearchRepositoryInterface
{
    /**
     * @inheritDoc
     */
    public function searchCategories(string $query): array
    {
        if ($query === '') {
            return [];
        }
        return [
            new Category('1', 'Test Category', 'http://example.com/boxart.jpg'),
        ];
    }
}
