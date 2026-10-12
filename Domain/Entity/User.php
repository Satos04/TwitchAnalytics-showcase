<?php

namespace Domain\Entity;

class User
{
    public function __construct(
        private string $email,
        private string $apiKey
    ) {
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getApiKey(): string
    {
        return $this->apiKey;
    }
}
