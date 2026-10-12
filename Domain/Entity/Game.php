<?php

namespace Domain\Entity;

class Game
{
    private string $gameId;
    private string $name;
    private string $boxArtUrl;

    public function __construct(string $gameId, string $name, string $boxArtUrl)
    {
        $this->gameId = $gameId;
        $this->name = $name;
        $this->boxArtUrl = $boxArtUrl;
    }

    public function getId(): string
    {
        return $this->gameId;
    }

    public function getBoxArtUrl(): string
    {
        return $this->boxArtUrl;
    }

    public function getName(): string
    {
        return $this->name;
    }
}
