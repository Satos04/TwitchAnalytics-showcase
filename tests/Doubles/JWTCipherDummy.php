<?php

namespace Tests\Doubles;

use Domain\Authentication\JWTCipherInterface;
use Exception;

class JWTCipherDummy implements JWTCipherInterface
{
    /**
     * @throws Exception
     */
    public function encode(array $payload, string $key): string
    {
        throw new Exception();
    }

    public function decode(string $token, string $key, string $hashAlgorithm = 'HS256'): array
    {
        throw new Exception();
    }
}
