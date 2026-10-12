<?php

namespace Tests\Doubles;

use Domain\Authentication\ApiKeyGeneratorInterface;

class ApiKeyGeneratorStub implements ApiKeyGeneratorInterface
{
    public function generateApiKey(): string
    {
        return "123456";
    }
}
