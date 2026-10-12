<?php

namespace Tests\Unit\Infrastructure\Twitch;

use Domain\Entity\Game;
use Infrastructure\Twitch\TwitchGamesRepository;
use PHPUnit\Framework\TestCase;
use Tests\Doubles\TwitchHttpClientStub;

class TwitchGamesRepositoryTest extends TestCase
{
    private TwitchHttpClientStub $httpClient;
    private TwitchGamesRepository $repository;

    protected function setUp(): void
    {
        $this->httpClient = new TwitchHttpClientStub();
        $this->repository = new TwitchGamesRepository($this->httpClient);
    }

    /** @test */
    public function givenApiResponseReturnsMappedGames(): void
    {
        $this->httpClient->withResponse('/games/top', [
            ['id' => '1', 'name' => 'Just Chatting', 'box_art_url' => 'https://example.com/{width}x{height}.jpg'],
            ['id' => '2', 'name' => 'GTA V',         'box_art_url' => 'https://example.com/{width}x{height}.jpg'],
        ]);

        $games = $this->repository->getTopGames(2);

        $this->assertCount(2, $games);
        $this->assertInstanceOf(Game::class, $games[0]);
        $this->assertSame('Just Chatting', $games[0]->getName());
        $this->assertSame('GTA V', $games[1]->getName());
    }

    /** @test */
    public function givenApiResponseBoxArtUrlIsNormalized(): void
    {
        $this->httpClient->withResponse('/games/top', [
            ['id' => '1', 'name' => 'Just Chatting', 'box_art_url' => 'https://example.com/{width}x{height}.jpg'],
        ]);

        $games = $this->repository->getTopGames(1);

        $this->assertSame('https://example.com/285x380.jpg', $games[0]->getBoxArtUrl());
    }

    /** @test */
    public function givenEmptyApiResponseReturnsEmptyArray(): void
    {
        $this->httpClient->withResponse('/games/top', []);

        $games = $this->repository->getTopGames(1);

        $this->assertSame([], $games);
    }
}
