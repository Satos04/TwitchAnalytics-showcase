<?php

namespace Tests\Unit\Application\UseCase;

use Domain\Entity\Game;
use Domain\Entity\Stream;
use Domain\Repository\GamesRepositoryInterface;
use Application\UseCase\GamesUseCase;
use PHPUnit\Framework\TestCase;
use Tests\Doubles\GamesRepositoryStub;

class GamesUseCaseTest extends TestCase
{
    private function makeGame(string $id, string $name): Game
    {
        return new Game($id, $name, "https://example.com/{$id}-{width}x{height}.jpg");
    }

    private function makeStream(string $gameId, string $userId, string $userName, int $viewers): Stream
    {
        return new Stream($userId, $userName, $gameId, $viewers);
    }

    /**
     * @test
     */
    public function givenOneGameWithNoStreamsReturnsZeroStats(): void
    {
        $repository = $this->createStub(GamesRepositoryInterface::class);
        $repository->method('getTopGames')->willReturn([
            $this->makeGame('1', 'Just Chatting')
        ]);
        $repository->method('getStreamsByGameIds')->willReturn([]);

        $useCase = new GamesUseCase($repository);
        $result  = $useCase->getTopGamesWithStats(1);

        $expected = [
            'id' => '1',
            'name' => 'Just Chatting',
            'box_art_url' => 'https://example.com/1-{width}x{height}.jpg',
            'total_streams' => 0,
            'total_viewers' => 0,
            'average_viewers' => 0,
            'top_streamer' => null,
        ];
        $this->assertEquals($expected, $result[0]);
    }

    /**
     * @test
     */
    public function givenOneGameWithOneStreamReturnsCorrectStats(): void
    {
        $repository = $this->createStub(GamesRepositoryInterface::class);
        $repository->method('getTopGames')->willReturn([
            $this->makeGame('1', 'Just Chatting')
        ]);
        $repository->method('getStreamsByGameIds')->willReturn([
            $this->makeStream('1', 'u1', 'Streamer1', 10000)
        ]);

        $useCase = new GamesUseCase($repository);
        $result  = $useCase->getTopGamesWithStats(1);

        $expected = [
            'id' => '1',
            'name' => 'Just Chatting',
            'box_art_url' => 'https://example.com/1-{width}x{height}.jpg',
            'total_streams' => 1,
            'total_viewers' => 10000,
            'average_viewers' => 10000,
            'top_streamer' => [
                'user_id' => 'u1',
                'user_name' => 'Streamer1',
                'viewer_count' => 10000,
            ],
        ];
        $this->assertEquals($expected, $result[0]);
    }

    /**
     * @test
     */
    public function givenOneGameWithMultiplesStreamsCalculatesTotalsAndAverage(): void
    {
        $repository = $this->createStub(GamesRepositoryInterface::class);
        $repository->method('getTopGames')->willReturn([
            $this->makeGame('42', 'GTA V')
        ]);
        $repository->method('getStreamsByGameIds')->willReturn([
            $this->makeStream('42', 'u1', 'Streamer1', 30000),
            $this->makeStream('42', 'u2', 'Streamer2', 10000),
        ]);

        $useCase = new GamesUseCase($repository);
        $result  = $useCase->getTopGamesWithStats(1);

        $expected = [
            'id' => '42',
            'name' => 'GTA V',
            'box_art_url' => 'https://example.com/42-{width}x{height}.jpg',
            'total_streams' => 2,
            'total_viewers' => 40000,
            'average_viewers' => 20000,
            'top_streamer' => [
                'user_id' => 'u1',
                'user_name' => 'Streamer1',
                'viewer_count' => 30000,
            ],
        ];
        $this->assertEquals($expected, $result[0]);
    }

    /**
     * @test
     */
    public function giveOneGameWithMultipleStreamsReturnsStreamerWithMostViewersAsTop(): void
    {
        $repository = $this->createStub(GamesRepositoryInterface::class);
        $repository->method('getTopGames')->willReturn([
            $this->makeGame('42', 'GTA V')
        ]);
        $repository->method('getStreamsByGameIds')->willReturn([
            $this->makeStream('42', 'u1', 'Streamer1', 30000),
            $this->makeStream('42', 'u2', 'Streamer2', 10000),
        ]);

        $useCase = new GamesUseCase($repository);
        $result  = $useCase->getTopGamesWithStats(1);

        $expected = [
            'id' => '42',
            'name' => 'GTA V',
            'box_art_url' => 'https://example.com/42-{width}x{height}.jpg',
            'total_streams' => 2,
            'total_viewers' => 40000,
            'average_viewers' => 20000,
            'top_streamer' => [
                'user_id' => 'u1',
                'user_name' => 'Streamer1',
                'viewer_count' => 30000,
            ],
        ];
        $this->assertEquals($expected, $result[0]);
    }

    /**
     * @test
     */
    public function givenTwoGamesTheStreamsAreNotMixedBetweenGames(): void
    {
        $repository = $this->createStub(GamesRepositoryInterface::class);
        $repository->method('getTopGames')->willReturn([
            $this->makeGame('1', 'Game A'),
            $this->makeGame('2', 'Game B'),
        ]);
        $repository->method('getStreamsByGameIds')->willReturn([
            $this->makeStream('1', 'u1', 'StreamerA', 5000),
            $this->makeStream('2', 'u2', 'StreamerB', 9000),
        ]);

        $useCase = new GamesUseCase($repository);
        $result  = $useCase->getTopGamesWithStats(2);

        $expected = [
            [
                'id' => '1',
                'name' => 'Game A',
                'box_art_url' => 'https://example.com/1-{width}x{height}.jpg',
                'total_streams' => 1,
                'total_viewers' => 5000,
                'average_viewers' => 5000,
                'top_streamer' => [
                    'user_id' => 'u1',
                    'user_name' => 'StreamerA',
                    'viewer_count' => 5000,
                ],
            ],
            [
                'id' => '2',
                'name' => 'Game B',
                'box_art_url' => 'https://example.com/2-{width}x{height}.jpg',
                'total_streams' => 1,
                'total_viewers' => 9000,
                'average_viewers' => 9000,
                'top_streamer' => [
                    'user_id' => 'u2',
                    'user_name' => 'StreamerB',
                    'viewer_count' => 9000,
                ],
            ],
        ];
        $this->assertEquals($expected, $result);
    }

    /**
     * @test
     */
    public function giveOneGameWithMultipleStreamsWithSameViewersReturnsFirstStreamerWithMostViewersAsTop(): void
    {
        $repository = $this->createStub(GamesRepositoryInterface::class);
        $repository->method('getTopGames')->willReturn([
            $this->makeGame('42', 'GTA V')
        ]);
        $repository->method('getStreamsByGameIds')->willReturn([
            $this->makeStream('42', 'u1', 'Streamer1', 20000),
            $this->makeStream('42', 'u2', 'Streamer2', 20000),
        ]);

        $useCase = new GamesUseCase($repository);
        $result  = $useCase->getTopGamesWithStats(1);

        $expected = [
            'id' => '42',
            'name' => 'GTA V',
            'box_art_url' => 'https://example.com/42-{width}x{height}.jpg',
            'total_streams' => 2,
            'total_viewers' => 40000,
            'average_viewers' => 20000,
            'top_streamer' => [
                'user_id' => 'u1',
                'user_name' => 'Streamer1',
                'viewer_count' => 20000,
            ],
        ];
        $this->assertEquals($expected, $result[0]);
    }

    /**
     * @test
     */
    public function givenNoStreamsReturnsEmptyListOfStreams(): void
    {
        $gamesRepositoryStub = new GamesRepositoryStub();
        $useCase = new GamesUseCase($gamesRepositoryStub);

        $result  = $useCase->getTopGamesWithStats(1);

        $this->assertEquals([], $result);
    }

    /**
     * @test
     */
    public function givenThreeGamesWithLimit3ReturnsStatsOfAllThreeGames(): void
    {
        $repository = $this->createStub(GamesRepositoryInterface::class);
        $repository->method('getTopGames')->willReturn([
            $this->makeGame('1', 'Game A'),
            $this->makeGame('2', 'Game B'),
            $this->makeGame('3', 'Game C'),
        ]);
        $repository->method('getStreamsByGameIds')->willReturn([
            $this->makeStream('1', 'u1', 'StreamerA', 5000),
            $this->makeStream('2', 'u2', 'StreamerB', 9000),
            $this->makeStream('3', 'u3', 'StreamerC', 7000),
            $this->makeStream('2', 'u4', 'StreamerD', 10000),
        ]);
        $useCase = new GamesUseCase($repository);

        $result  = $useCase->getTopGamesWithStats(3);

        $expected = [
            [
                'id' => '1',
                'name' => 'Game A',
                'box_art_url' => 'https://example.com/1-{width}x{height}.jpg',
                'total_streams' => 1,
                'total_viewers' => 5000,
                'average_viewers' => 5000,
                'top_streamer' => [
                    'user_id' => 'u1',
                    'user_name' => 'StreamerA',
                    'viewer_count' => 5000,
                ],
            ],
            [
                'id' => '2',
                'name' => 'Game B',
                'box_art_url' => 'https://example.com/2-{width}x{height}.jpg',
                'total_streams' => 2,
                'total_viewers' => 19000,
                'average_viewers' => 9500,
                'top_streamer' => [
                    'user_id' => 'u4',
                    'user_name' => 'StreamerD',
                    'viewer_count' => 10000,
                ],
            ],
            [
                'id' => '3',
                'name' => 'Game C',
                'box_art_url' => 'https://example.com/3-{width}x{height}.jpg',
                'total_streams' => 1,
                'total_viewers' => 7000,
                'average_viewers' => 7000,
                'top_streamer' => [
                    'user_id' => 'u3',
                    'user_name' => 'StreamerC',
                    'viewer_count' => 7000,
                ],
            ],
        ];
        $this->assertEquals($expected, $result);
    }
    /**
     * @test
     */
    public function givenThreeGamesButNoStreamsReturnsStatsAt0(): void
    {
        $repository = $this->createStub(GamesRepositoryInterface::class);
        $repository->method('getTopGames')->willReturn([
            $this->makeGame('1', 'Game A'),
            $this->makeGame('2', 'Game B'),
            $this->makeGame('3', 'Game C'),
        ]);
        $repository->method('getStreamsByGameIds')->willReturn([]);
        $useCase = new GamesUseCase($repository);

        $result  = $useCase->getTopGamesWithStats(3);

        $expected = [
            [
                'id' => '1',
                'name' => 'Game A',
                'box_art_url' => 'https://example.com/1-{width}x{height}.jpg',
                'total_streams' => 0,
                'total_viewers' => 0,
                'average_viewers' => 0,
                'top_streamer' => null,
            ],
            [
                'id' => '2',
                'name' => 'Game B',
                'box_art_url' => 'https://example.com/2-{width}x{height}.jpg',
                'total_streams' => 0,
                'total_viewers' => 0,
                'average_viewers' => 0,
                'top_streamer' => null,
            ],
            [
                'id' => '3',
                'name' => 'Game C',
                'box_art_url' => 'https://example.com/3-{width}x{height}.jpg',
                'total_streams' => 0,
                'total_viewers' => 0,
                'average_viewers' => 0,
                'top_streamer' => null,
            ],
        ];
        $this->assertEquals($expected, $result);
    }
}
