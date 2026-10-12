<?php

namespace Tests\Doubles;

use Domain\Authentication\JWTCipherInterface;

class JWTCipherStub implements JWTCipherInterface
{
    public function encode(array $payload, string $key): string
    {
        return "12345";
    }

    public function decode(string $token, string $key, string $hashAlgorithm = 'HS256'): array
    {
        return [];
    }
}
