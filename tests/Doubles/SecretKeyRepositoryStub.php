<?php

namespace Tests\Doubles;

use Domain\Repository\SecretKeyRepositoryInterface;

class SecretKeyRepositoryStub implements SecretKeyRepositoryInterface
{
    public function createSecretKey(): void
    {
    }

    public function getSecretKey(): false|string
    {
        return str_repeat('a', 32);
    }
}
