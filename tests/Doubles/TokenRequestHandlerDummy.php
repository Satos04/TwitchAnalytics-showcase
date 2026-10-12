<?php

namespace Tests\Doubles;

use Exception;
use Infrastructure\Http\Middleware\TokenRequestHandlerInterface;

class TokenRequestHandlerDummy implements TokenRequestHandlerInterface
{
    /**
     * @throws Exception
     */
    public function getRequestBodyStructure(): array
    {
        throw new Exception();
    }

    /**
     * @throws Exception
     */
    public function getRequestBodyData(): mixed
    {
        throw new Exception();
    }
}
