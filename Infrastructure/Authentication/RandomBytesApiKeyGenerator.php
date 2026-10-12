<?php

namespace Infrastructure\Authentication;

use Domain\Authentication\ApiKeyGeneratorInterface;
use Random\RandomException;

class RandomBytesApiKeyGenerator implements ApiKeyGeneratorInterface
{
    /**
     * @throws RandomException
     */
    public function generateApiKey(): string
    {
        return bin2hex(random_bytes(16));
    }
}
