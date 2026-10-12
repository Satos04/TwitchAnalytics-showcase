<?php

namespace Infrastructure\Persistence;

use Domain\Entity\User;
use Domain\Repository\UsersRepositoryInterface;

class EloquentUsersRepository implements UsersRepositoryInterface
{
    public function getUser(string $email, string $apiKey): User|false
    {
        /** @var EloquentUser|null $record */
        $record = EloquentUser::where('email', $email)
            ->where('api_key', $apiKey)
            ->first();

        if (!$record) {
            return false;
        }

        return new User($record->email, $record->api_key);
    }

    public function createUser(string $email, string $apiKey): bool
    {
        return (bool) EloquentUser::create([
            'email' => $email,
            'api_key' => $apiKey
        ]);
    }

    public function emailExists(string $email): bool
    {
        return EloquentUser::where('email', $email)->exists();
    }
}
