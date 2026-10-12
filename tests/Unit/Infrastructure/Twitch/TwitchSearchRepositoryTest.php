<?php

namespace Tests\Unit\Infrastructure\Twitch;

use Domain\Entity\Category;
use Infrastructure\Twitch\TwitchSearchRepository;
use PHPUnit\Framework\TestCase;
use Tests\Doubles\TwitchHttpClientStub;

class TwitchSearchRepositoryTest extends TestCase
{
    private TwitchHttpClientStub $httpClient;
    private TwitchSearchRepository $repository;

    protected function setUp(): void
    {
        $this->httpClient = new TwitchHttpClientStub();
        $this->repository = new TwitchSearchRepository($this->httpClient);
    }

    /** @test */
    public function givenApiResponseReturnsMappedCategories(): void
    {
        $this->httpClient->withResponse('/search/categories', [
            ['id' => '1', 'name' => 'Fortnite',  'box_art_url' => 'https://example.com/fortnite.jpg'],
            ['id' => '2', 'name' => 'Minecraft', 'box_art_url' => 'https://example.com/minecraft.jpg'],
        ]);

        $categories = $this->repository->searchCategories('fort');

        $this->assertCount(2, $categories);
        $this->assertInstanceOf(Category::class, $categories[0]);
        $this->assertSame('Fortnite', $categories[0]->getName());
    }

    /** @test */
    public function givenMissingBoxArtUrlDefaultsToEmptyString(): void
    {
        $this->httpClient->withResponse('/search/categories', [
            ['id' => '1', 'name' => 'Fortnite'],
        ]);

        $categories = $this->repository->searchCategories('fort');

        $this->assertSame('', $categories[0]->getBoxArtUrl());
    }

    /** @test */
    public function givenEmptyApiResponseReturnsEmptyArray(): void
    {
        $this->httpClient->withResponse('/search/categories', []);

        $categories = $this->repository->searchCategories('fort');

        $this->assertSame([], $categories);
    }
}
