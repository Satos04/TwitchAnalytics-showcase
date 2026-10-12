<?php

namespace Tests\Integration;

use Infrastructure\Twitch\TwitchHttpClient;
use Laravel\Lumen\Testing\DatabaseMigrations;
use RuntimeException;
use Tests\Doubles\TwitchHttpClientStub;
use Tests\TestCase;

class SearchIntegrationTest extends TestCase
{
    use DatabaseMigrations;

    private function getAuthToken(): string
    {
        $this->post('/register', ['email' => 'test@test.com']);
        $registerResponse = json_decode($this->response->getContent(), true);
        $apiKey = $registerResponse['api_key'];

        $this->post('/token', [
            'email'   => 'test@test.com',
            'api_key' => $apiKey,
        ]);
        $tokenResponse = json_decode($this->response->getContent(), true);
        return $tokenResponse['token'];
    }

    /**
     * @test
     */
    public function returns200WithValidTokenAndLimit(): void
    {
        $token = $this->getAuthToken();

        $httpClientStub = new TwitchHttpClientStub();
        $httpClientStub->withResponse('/search/categories', [
            ['id' => '1', 'name' => 'Fortnite',  'box_art_url' => 'https://example.com/fortnite.jpg'],
            ['id' => '2', 'name' => 'Minecraft', 'box_art_url' => 'https://example.com/minecraft.jpg'],
        ]);

        $this->app->instance(TwitchHttpClient::class, $httpClientStub);

        $response = $this->get('/analytics/search?query=fort', [
            'Authorization' => 'Bearer ' . $token,
        ]);

        $response->assertResponseStatus(200);
        $response->seeJsonStructure([
            ['id', 'name', 'box_art_url']
        ]);
    }

    /**
     * @test
     */
    public function returns200AndEmptyResponseWhenNoResultsAreFound(): void
    {
        $token = $this->getAuthToken();

        $httpClientStub = new TwitchHttpClientStub();
        $httpClientStub->withResponse('/search/categories', []);

        $this->app->instance(TwitchHttpClient::class, $httpClientStub);

        $response = $this->get('/analytics/search?query=fort', [
            'Authorization' => 'Bearer ' . $token,
        ]);

        $response->assertResponseStatus(200);
        $response->seeJsonEquals([]);
    }

    /**
     * @test
     */
    public function returns400WhenQueryIsMissing(): void
    {
        $token = $this->getAuthToken();

        $httpClientStub = new TwitchHttpClientStub();
        $httpClientStub->withResponse('/search/categories', []);

        $this->app->instance(TwitchHttpClient::class, $httpClientStub);

        $response = $this->get('/analytics/search?', [
            'Authorization' => 'Bearer ' . $token,
        ]);

        $response->assertResponseStatus(400);
        $response->seeJsonEquals(["error" => "Invalid or missing 'query' parameter."]);
    }

    /**
     * @test
     */
    public function returns401WhenNoTokenProvided(): void
    {
        $response = $this->get('/analytics/search?query=fort');

        $response->assertResponseStatus(401);
        $response->seeJsonEquals(["error" => "Unauthorized. Token is invalid or has expired."]);
    }

    /**
     * @test
     */
    public function returns500WhenTwitchFails(): void
    {
        $token = $this->getAuthToken();

        $httpClientStub = new TwitchHttpClientStub();
        $httpClientStub->withException(new RuntimeException("Connection timeout"));
        $this->app->instance(TwitchHttpClient::class, $httpClientStub);

        $response = $this->get('/analytics/search?query=fort', [
            'Authorization' => 'Bearer ' . $token,
        ]);

        $response->assertResponseStatus(500);
        $response->seeJsonEquals(['error' => 'Internal server error.']);
    }
}
