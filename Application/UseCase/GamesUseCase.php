<?php

namespace Application\UseCase;

use Domain\Entity\Game;
use Domain\Entity\Stream;
use Domain\Repository\GamesRepositoryInterface;

class GamesUseCase
{
    private GamesRepositoryInterface $repository;

    public function __construct(GamesRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function getTopGamesWithStats(int $limit): array
    {
        $games   = $this->repository->getTopGames($limit);
        $gameIds = array_map([$this, 'extractGameId'], $games);
        $streams = $this->repository->getStreamsByGameIds($gameIds);

        return array_map(fn (Game $game) => $this->buildGameStats($game, $streams), $games);
    }

    private function buildGameStats(Game $game, array $streams): array
    {
        $gameStreams  = $this->filterStreamsByGame($streams, $game);
        $totalStreams = count($gameStreams);
        $totalViewers = array_sum(array_map([$this, 'extractViewerCount'], $gameStreams));
        $avgViewers  = $totalStreams > 0 ? intval($totalViewers / $totalStreams) : 0;
        $topStream   = $this->findTopStream($gameStreams);

        return [
            'id' => $game->getId(),
            'name' => $game->getName(),
            'box_art_url' => $game->getBoxArtUrl(),
            'total_streams' => $totalStreams,
            'total_viewers' => $totalViewers,
            'average_viewers' => $avgViewers,
            'top_streamer' => $topStream ? $this->formatTopStreamer($topStream) : null,

        ];
    }

    private function findTopStream(array $streams): ?Stream
    {
        return array_reduce(
            $streams,
            fn (?Stream $carry, Stream $stream) =>
                ($carry === null || $stream->getViewerCount() > $carry->getViewerCount()) ? $stream : $carry,
            null
        );
    }

    private function extractGameId(Game $game): string
    {
        return $game->getId();
    }

    private function filterStreamsByGame(array $streams, Game $game): array
    {
        return array_values(
            array_filter($streams, fn (Stream $stream) => $stream->getGameId() === $game->getId())
        );
    }

    private function extractViewerCount(Stream $stream): int
    {
        return $stream->getViewerCount();
    }

    private function formatTopStreamer(Stream $stream): array
    {
        return [
            'user_id' => $stream->getUserId(),
            'user_name' => $stream->getUserName(),
            'viewer_count' => $stream->getViewerCount(),
        ];
    }
}
