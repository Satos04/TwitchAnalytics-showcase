<?php

namespace Tests\Doubles;

use Domain\Entity\User;
use Domain\Repository\UsersRepositoryInterface;

class UsersRepositoryFake implements UsersRepositoryInterface
{
    public function getUser(string $email, string $apiKey): User|false
    {
        if ($email === "example@gmail.com" && $apiKey === "api_key") {
            return new User('example@gmail.com', 'api_key');
        }
        return false;
    }

    public function createUser(string $email, string $apiKey): bool
    {
        return true;
    }

    public function emailExists(string $email): bool
    {
        if ($email === 'registered@gmail.com') {
            return true;
        }
        return false;
    }
}
