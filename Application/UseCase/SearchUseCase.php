<?php

namespace Application\UseCase;

use Domain\Entity\Category;
use Domain\Repository\SearchRepositoryInterface;

class SearchUseCase
{
    private SearchRepositoryInterface $repository;

    public function __construct(SearchRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function searchCategories(string $query): array
    {
        $categories = $this->repository->searchCategories($query);

        return array_map(
            fn (Category $category) => [
                'id'          => $category->getId(),
                'name'        => $category->getName(),
                'box_art_url' => $category->getBoxArtUrl(),
            ],
            $categories
        );
    }
}
