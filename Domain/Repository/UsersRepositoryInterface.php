<?php

namespace Domain\Repository;

use Domain\Entity\User;

interface UsersRepositoryInterface
{
    public function getUser(string $email, string $apiKey): User|false;
    public function createUser(string $email, string $apiKey): bool;
    public function emailExists(string $email): bool;
}
