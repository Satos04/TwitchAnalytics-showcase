<?php

namespace Infrastructure\Http\Middleware;

use Illuminate\Http\Request;

class HttpTokenRequestHandler implements TokenRequestHandlerInterface
{
    private Request $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function getRequestBodyStructure(): array
    {
        $method   = $this->request->method();
        $uri      = $this->request->getRequestUri();
        $clientIp = $this->request->ip();
        return [$method, $uri, $clientIp];
    }

    public function getRequestBodyData(): mixed
    {
        return $this->request->all();
    }
}
