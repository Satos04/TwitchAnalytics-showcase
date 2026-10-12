<?php

namespace Tests\Doubles;

use Infrastructure\Twitch\TwitchHttpClient;

class TwitchHttpClientStub extends TwitchHttpClient
{
    private array $responses = [];
    private array $defaultResponse = [];
    private ?\Throwable $exception = null;

    public function __construct()
    {
    }

    public function withResponse(string $path, array $data): void
    {
        $this->responses[$path] = $data;
    }

    public function withDefaultResponse(array $data): void
    {
        $this->defaultResponse = $data;
    }

    public function withException(\Throwable $exception): void
    {
        $this->exception = $exception;
    }

    public function get(string $path, array $params = []): array
    {
        if ($this->exception !== null) {
            throw $this->exception;
        }
        return $this->responses[$path] ?? $this->defaultResponse;
    }
}
