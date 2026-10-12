<?php

namespace Infrastructure\Twitch;

use Domain\Entity\Category;
use Domain\Repository\SearchRepositoryInterface;

class TwitchSearchRepository implements SearchRepositoryInterface
{
    private const string SEARCH_CATEGORIES_PATH = '/search/categories';

    public function __construct(private TwitchHttpClient $httpClient)
    {
    }

    public function searchCategories(string $query): array
    {
        $data = $this->httpClient->get(
            self::SEARCH_CATEGORIES_PATH,
            ['query' => $query]
        );

        return array_map(fn ($item) => $this->buildCategory($item), $data);
    }

    private function buildCategory(array $item): Category
    {
        return new Category(
            $item['id'],
            $item['name'],
            $this->normalizeBoxArtUrl($item['box_art_url'] ?? '')
        );
    }

    private function normalizeBoxArtUrl(string $url): string
    {
        $url = str_replace(
            ['{width}', '{height}'],
            ['285', '380'],
            $url
        );

        return preg_replace(
            '/-\d+x\d+/',
            '-285x380',
            $url
        ) ?? $url;
    }
}
