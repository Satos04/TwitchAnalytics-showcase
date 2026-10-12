<?php

namespace Infrastructure\Http\Middleware;

interface TokenRequestHandlerInterface
{
    public function getRequestBodyStructure(): array;
    public function getRequestBodyData(): mixed;
}
