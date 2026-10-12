<?php

namespace Domain\Entity;

class Category
{
    private string $categoryId;
    private string $name;
    private string $boxArtUrl;

    public function __construct(string $categoryId, string $name, string $boxArtUrl)
    {
        $this->categoryId = $categoryId;
        $this->name = $name;
        $this->boxArtUrl = $boxArtUrl;
    }

    public function getId(): string
    {
        return $this->categoryId;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getBoxArtUrl(): string
    {
        return $this->boxArtUrl;
    }
}
