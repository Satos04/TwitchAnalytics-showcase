<?php

namespace Tests\Unit\Infrastructure\Http\Controllers;

use Application\UseCase\SearchUseCase;
use Illuminate\Http\Request;
use Infrastructure\Http\Controllers\SearchController;
use Infrastructure\Http\Validators\SearchRequestValidator;
use Laravel\Lumen\Testing\WithoutMiddleware;
use RuntimeException;
use Tests\TestCase;

class SearchControllerTest extends TestCase
{
    use WithoutMiddleware;

    private function makeController(): array
    {
        $service = $this->createMock(SearchUseCase::class);
        $controller = new SearchController($service, new SearchRequestValidator());
        return [$controller, $service];
    }

    /**
     * @test
     */
    public function returns200WhenQueryIsValid()
    {
        [$controller, $service] = $this->makeController();
        $service->method('searchCategories')->willReturn([]);

        $request = Request::create('/analytics/search', 'GET', ['query' => 'league']);
        $response = $controller->searchCategories($request);

        $this->assertEquals(200, $response->status());
    }

    /**
     * @test
     */
    public function returns400WhenQueryIsMissing()
    {
        [$controller, $service] = $this->makeController();

        $request = Request::create('/analytics/search', 'GET', []);
        $response = $controller->searchCategories($request);

        $this->assertEquals(400, $response->status());
    }

    /**
     * @test
     */
    public function returns400WhenQueryIsEmpty()
    {
        [$controller, $service] = $this->makeController();

        $request = Request::create('/analytics/search', 'GET', ['query' => '']);
        $response = $controller->searchCategories($request);

        $this->assertEquals(400, $response->status());
    }

    /**
     * @test
     */
    public function returns401WhenTwitchTokenIsExpired()
    {
        [$controller, $service] = $this->makeController();
        $service->method('searchCategories')
            ->willThrowException(new RuntimeException("Twitch API error: HTTP 401"));

        $request = Request::create('/analytics/search', 'GET', ['query' => 'league']);
        $response = $controller->searchCategories($request);

        $this->assertEquals(401, $response->status());
    }

    /**
     * @test
     */
    public function returns500WhenUnexpectedErrorOccurs()
    {
        [$controller, $service] = $this->makeController();
        $service->method('searchCategories')
            ->willThrowException(new RuntimeException("Connection timeout"));

        $request = Request::create('/analytics/search', 'GET', ['query' => 'league']);
        $response = $controller->searchCategories($request);

        $this->assertEquals(500, $response->status());
    }
}
