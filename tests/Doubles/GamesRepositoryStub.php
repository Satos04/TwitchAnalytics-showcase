<?php

namespace Tests\Doubles;

use Domain\Repository\GamesRepositoryInterface;

class GamesRepositoryStub implements GamesRepositoryInterface
{
    public function getTopGames(int $limit): array
    {
        return [];
    }

    public function getStreamsByGameIds(array $gameIds): array
    {
        return [];
    }
}
