<?php

namespace Tests\Integration;

use Laravel\Lumen\Testing\DatabaseMigrations;
use Tests\Doubles\TwitchHttpClientStub;
use Infrastructure\Twitch\TwitchHttpClient;
use Tests\TestCase;

class GamesIntegrationTest extends TestCase
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
        $httpClientStub->withResponse('/games/top', [
            ['id' => '509658', 'name' => 'Just Chatting', 'box_art_url' => 'https://example.com/art-{width}x{height}.jpg'],
        ]);
        $httpClientStub->withResponse('/streams', []);

        $this->app->instance(TwitchHttpClient::class, $httpClientStub);

        $response = $this->get('/analytics/games?limit=1', [
            'Authorization' => 'Bearer ' . $token,
        ]);

        $response->assertResponseStatus(200);
        $response->seeJsonStructure([
            ['id', 'name', 'box_art_url', 'total_streams', 'total_viewers', 'average_viewers', 'top_streamer']
        ]);
    }

    /**
     * @test
     */
    public function returns401WhenNoTokenProvided(): void
    {
        $response = $this->get('/analytics/games?limit=1');

        $response->assertResponseStatus(401);
    }

    /**
     * @test
     */
    public function returns400WhenLimitIsOutOfRange(): void
    {
        $token = $this->getAuthToken();

        $httpClientStub = new TwitchHttpClientStub();
        $this->app->instance(TwitchHttpClient::class, $httpClientStub);

        $response = $this->get('/analytics/games?limit=5', [
            'Authorization' => 'Bearer ' . $token,
        ]);

        $response->assertResponseStatus(400);
        $response->seeJsonEquals([
            'error' => "Invalid 'limit' parameter. Must be between 1 and 3."
        ]);
    }

    /**
     * @test
     */
    public function returns500WhenTwitchFails(): void
    {
        $token = $this->getAuthToken();

        $httpClientStub = new TwitchHttpClientStub();
        $httpClientStub->withException(new \RuntimeException("Connection timeout"));
        $this->app->instance(TwitchHttpClient::class, $httpClientStub);

        $response = $this->get('/analytics/games?limit=1', [
            'Authorization' => 'Bearer ' . $token,
        ]);

        $response->assertResponseStatus(500);
        $response->seeJsonEquals(['error' => 'Internal server error.']);
    }
}
