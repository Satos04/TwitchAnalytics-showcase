<?php

namespace Tests\Unit\Infrastructure\Http\Controllers;

use Application\UseCase\GamesUseCase;
use Illuminate\Http\Request;
use Infrastructure\Http\Controllers\GamesController;
use Infrastructure\Http\Validators\GamesRequestValidator;
use InvalidArgumentException;
use Laravel\Lumen\Testing\WithoutMiddleware;
use RuntimeException;
use Tests\TestCase;

class GamesControllerTest extends TestCase
{
    use WithoutMiddleware;

    private function makeController(): array
    {
        $service = $this->createMock(GamesUseCase::class);
        $controller = new GamesController($service, new GamesRequestValidator());
        return [$controller, $service];
    }

    /**
     * @test
     */
    public function returns200WhenLimitIsValid()
    {
        [$controller, $service] = $this->makeController();
        $service->method('getTopGamesWithStats')->willReturn([]);

        $request = Request::create('/analytics/games', 'GET', ['limit' => 3]);
        $response = $controller->getTopGames($request);

        $this->assertEquals(200, $response->status());
    }

    /**
     * @test
     */
    public function returns400WhenLimitIsTooLow()
    {
        [$controller, $service] = $this->makeController();
        $service->method('getTopGamesWithStats')
            ->willThrowException(new InvalidArgumentException("Limit must be between 1 and 3."));

        $request = Request::create('/analytics/games', 'GET', ['limit' => 0]);
        $response = $controller->getTopGames($request);

        $this->assertEquals(400, $response->status());
    }

    /**
     * @test
     */
    public function returns400WhenLimitIsTooHigh()
    {
        [$controller, $service] = $this->makeController();
        $service->method('getTopGamesWithStats')
            ->willThrowException(new InvalidArgumentException("Limit must be between 1 and 3."));

        $request = Request::create('/analytics/games', 'GET', ['limit' => 4]);
        $response = $controller->getTopGames($request);

        $this->assertEquals(400, $response->status());
    }

    /**
     * @test
     */
    public function returns400WhenLimitIsNegative()
    {
        [$controller, $service] = $this->makeController();
        $service->method('getTopGamesWithStats')
            ->willThrowException(new InvalidArgumentException("Limit must be between 1 and 3."));

        $request = Request::create('/analytics/games', 'GET', ['limit' => -1]);
        $response = $controller->getTopGames($request);

        $this->assertEquals(400, $response->status());
    }

    /**
     * @test
     */
    public function returns401WhenTwitchTokenIsExpired()
    {
        [$controller, $service] = $this->makeController();
        $service->method('getTopGamesWithStats')
            ->willThrowException(new RuntimeException("Twitch API error: HTTP 401"));

        $request = Request::create('/analytics/games', 'GET', ['limit' => 2]);
        $response = $controller->getTopGames($request);

        $this->assertEquals(401, $response->status());
    }

    /**
     * @test
     */
    public function returns500WhenUnexpectedErrorOccurs()
    {
        [$controller, $service] = $this->makeController();
        $service->method('getTopGamesWithStats')
            ->willThrowException(new RuntimeException("Connection timeout"));

        $request = Request::create('/analytics/games', 'GET', ['limit' => 1]);
        $response = $controller->getTopGames($request);

        $this->assertEquals(500, $response->status());
    }
}
