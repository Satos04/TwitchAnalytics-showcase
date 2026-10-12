<?php

namespace Domain\Authentication;

interface JWTCipherInterface
{
    public function encode(array $payload, string $key): string;

    public function decode(string $token, string $key, string $hashAlgorithm = 'HS256'): array;
}
