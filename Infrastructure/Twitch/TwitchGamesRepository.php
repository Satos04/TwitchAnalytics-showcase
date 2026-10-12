<?php

namespace Infrastructure\Twitch;

use Domain\Entity\Game;
use Domain\Entity\Stream;
use Domain\Repository\GamesRepositoryInterface;

class TwitchGamesRepository implements GamesRepositoryInterface
{
    private const string BOX_ART_WIDTH  = '285';
    private const string BOX_ART_HEIGHT = '380';
    private const int    MAX_STREAMS    = 100;

    public function __construct(private TwitchHttpClient $httpClient)
    {
    }

    public function getTopGames(int $limit): array
    {
        $data = $this->httpClient->get('/games/top', ['first' => $limit]);

        return array_map(fn ($game) => $this->buildGame($game), $data);
    }

    public function getStreamsByGameIds(array $gameIds): array
    {
        $params = $this->buildStreamsParams($gameIds);

        $data   = $this->httpClient->get('/streams', $params);

        return array_map(fn ($stream) => $this->buildStream($stream), $data);
    }

    private function buildGame(array $game): Game
    {
        return new Game(
            $game['id'],
            $game['name'],
            $this->normalizeBoxArtUrl($game['box_art_url'] ?? '')
        );
    }

    private function normalizeBoxArtUrl(string $url): string
    {
        return str_replace(
            ['{width}', '{height}'],
            [self::BOX_ART_WIDTH, self::BOX_ART_HEIGHT],
            $url
        );
    }

    private function buildStreamsParams(array $gameIds): array
    {
        return [
            'first'   => self::MAX_STREAMS,
            'game_id' => $gameIds
        ];
    }

    private function buildStream(array $stream): Stream
    {
        return new Stream(
            $stream['user_id'],
            $stream['user_name'],
            $stream['game_id'],
            $stream['viewer_count']
        );
    }
}
