<?php

namespace Tests\Doubles;

use Domain\Entity\User;
use Domain\Repository\UsersRepositoryInterface;
use Exception;

class UsersRepositoryDummy implements UsersRepositoryInterface
{
    /**
     * @throws Exception
     */
    public function getUser(string $email, string $apiKey): User|false
    {
        throw new Exception();
    }

    public function createUser(string $email, string $apiKey): bool
    {
        throw new Exception();
    }

    public function emailExists(string $email): bool
    {
        throw new Exception();
    }
}
