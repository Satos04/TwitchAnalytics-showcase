<?php

namespace Tests\Doubles;

use Domain\Repository\SecretKeyRepositoryInterface;
use Exception;

class SecretKeyRepositoryDummy implements SecretKeyRepositoryInterface
{
    public function createSecretKey(): void
    {
        // TODO: Implement createSecretKey() method.
        throw new Exception("SecretKeyRepository error");
    }

    public function getSecretKey(): false|string
    {
        // TODO: Implement getSecretKey() method.
        throw new Exception("SecretKeyRepository error");
    }
}
