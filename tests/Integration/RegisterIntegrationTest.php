<?php

namespace Tests\Integration;

use Tests\TestCase;
use Laravel\Lumen\Testing\DatabaseMigrations;

class RegisterIntegrationTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function registerCreatesUserInDatabase(): void
    {
        $response = $this->call('POST', '/register', ['email' => 'nuevo@test.com']);

        $this->assertEquals(201, $response->status());
        $this->seeInDatabase('users', ['email' => 'nuevo@test.com']);
    }

    /** @test */
    public function registerReturnsBadRequestWhenEmailMissing(): void
    {
        $response = $this->call('POST', '/register', []);

        $this->assertEquals(400, $response->status());
    }

    /** @test */
    public function registerReturnsConflictWhenEmailAlreadyRegistered(): void
    {
        $this->call('POST', '/register', ['email' => 'duplicado@test.com']);
        $response = $this->call('POST', '/register', ['email' => 'duplicado@test.com']);

        $this->assertEquals(409, $response->status());
    }
}
