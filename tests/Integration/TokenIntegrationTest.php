<?php

namespace Tests\Integration;

use Tests\TestCase;
use Laravel\Lumen\Testing\DatabaseMigrations;

class TokenIntegrationTest extends TestCase
{
    use DatabaseMigrations;

    public const int TOKEN_DURATION_IN_SECONDS = 259200;

    private function registerAndGetApiKey(string $email): string
    {
        $response = $this->call('POST', '/register', ['email' => $email]);
        $data = json_decode($response->getContent(), true);
        return $data['api_key'];
    }

    /** @test */
    public function tokenIsReturnedWithValidCredentials(): void
    {
        $email  = 'usuario@test.com';
        $apiKey = $this->registerAndGetApiKey($email);

        $response = $this->call('POST', '/token', [
            'email'   => $email,
            'api_key' => $apiKey,
        ]);

        $data = json_decode($response->getContent(), true);

        $expectedData = [
            'token' => $data['token'],
            'expires_in' => self::TOKEN_DURATION_IN_SECONDS,
        ];
        $this->assertEquals(200, $response->status());
        $this->assertEquals($expectedData, $data);
    }

    /** @test */
    public function tokenReturnsUnauthorizedWithWrongApiKey(): void
    {
        $email = 'usuario@test.com';
        $this->registerAndGetApiKey($email);

        $response = $this->call('POST', '/token', [
            'email'   => $email,
            'api_key' => 'clave-incorrecta',
        ]);

        $this->assertEquals(401, $response->status());
    }

    /** @test */
    public function tokenReturnsBadRequestWhenParametersMissing(): void
    {
        $response = $this->call('POST', '/token', []);

        $this->assertEquals(400, $response->status());
    }
}
