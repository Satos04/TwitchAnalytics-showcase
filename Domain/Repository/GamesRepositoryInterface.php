<?php

namespace Domain\Repository;

interface GamesRepositoryInterface
{
    public function getTopGames(int $limit): array;

    public function getStreamsByGameIds(array $gameIds): array;
}
