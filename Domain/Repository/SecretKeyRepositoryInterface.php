<?php

namespace Domain\Repository;

interface SecretKeyRepositoryInterface
{
    public function createSecretKey(): void;
    public function getSecretKey(): false|string;
}
