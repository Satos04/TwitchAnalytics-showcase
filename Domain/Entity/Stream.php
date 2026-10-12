<?php

namespace Domain\Entity;

class Stream
{
    private string $userId;
    private string $userName;
    private string $gameId;
    private int $viewerCount;
    public function __construct(string $userId, string $userName, string $gameId, int $viewerCount)
    {
        $this->userId = $userId;
        $this->userName = $userName;
        $this->gameId = $gameId;
        $this->viewerCount = $viewerCount;
    }

    public function getUserId(): string
    {
        return $this->userId;
    }
    public function getUserName(): string
    {
        return $this->userName;
    }
    public function getGameId(): string
    {
        return $this->gameId;
    }
    public function getViewerCount(): int
    {
        return $this->viewerCount;
    }
}
