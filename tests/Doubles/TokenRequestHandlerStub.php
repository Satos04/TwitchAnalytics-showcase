<?php

namespace Tests\Doubles;

use Exception;
use Infrastructure\Http\Middleware\TokenRequestHandlerInterface;

class TokenRequestHandlerStub implements TokenRequestHandlerInterface
{
    public function getRequestBodyStructure(): array
    {
        return array("POST", "localhost", "127.0.0.1");
    }

    /**
     * @throws Exception
     */
    public function getRequestBodyData(): mixed
    {
        $bodyData = [];
        $bodyData['email'] = 'example@gmail.com';
        $bodyData['api_key'] = 'api_key';
        return $bodyData;
    }
}
