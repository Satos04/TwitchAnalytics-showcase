<?php

namespace Infrastructure\Authentication;

use Domain\Authentication\JWTCipherInterface;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class JWTCipher implements JWTCipherInterface
{
    public function encode(array $payload, string $key, string $hashAlgorithm = 'HS256'): string
    {
        return JWT::encode($payload, $key, $hashAlgorithm);
    }

    public function decode(string $token, string $key, string $hashAlgorithm = 'HS256'): array
    {
        return (array) JWT::decode($token, new Key($key, $hashAlgorithm));
    }
}
