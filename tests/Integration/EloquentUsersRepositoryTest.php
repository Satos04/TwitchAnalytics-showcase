<?php

namespace Tests\Integration;

use Infrastructure\Persistence\EloquentUsersRepository;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Tests\TestCase;

class EloquentUsersRepositoryTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function givenEmailAndApiKeyCreatesNewUSer(): void
    {
        $repository = new EloquentUsersRepository();
        $email = 'email@example.com';
        $apiKey = 'apiKey';

        $repository->createUser($email, $apiKey);
        $result = $repository->getUser($email, $apiKey);

        $this->assertNotFalse($result);
    }

    /** @test */
    public function givenEmailOfRegisteredUserEmailExists(): void
    {
        $repository = new EloquentUsersRepository();
        $email = 'email@example.com';
        $apiKey = 'apiKey';

        $repository->createUser($email, $apiKey);
        $result = $repository->emailExists($email);

        $this->assertTrue($result);
    }
}
