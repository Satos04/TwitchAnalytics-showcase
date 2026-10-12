<?php

namespace Tests\Unit\Application\UseCase;

use Application\UseCase\SearchUseCase;
use Domain\Repository\SearchRepositoryInterface;
use Mockery;
use PHPUnit\Framework\TestCase;
use Tests\Doubles\SearchRepositoryFake;
use RuntimeException;

class SearchUseCaseTest extends TestCase
{
    /**
     * @test
     */
    public function givingTestQueryReturnsTestCategory(): void
    {
        $stubSearchRepository = new SearchRepositoryFake();
        $searchService = new SearchUseCase($stubSearchRepository);

        $result = $searchService->searchCategories('test');

        $expected = [
            [
                'id' => 1,
                'name' => 'Test Category',
                'box_art_url' => 'http://example.com/boxart.jpg',
            ],
        ];

        $this->assertEquals($expected, $result);
    }
    /**
     * @test
     */
    public function givingEmptyQueryReturnsEmptyArray(): void
    {
        $stubSearchRepository = new SearchRepositoryFake();
        $searchService = new SearchUseCase($stubSearchRepository);

        $result = $searchService->searchCategories('');

        $this->assertEquals([], $result);
    }
    /**
     * @test
     */
    public function whenServiceUnavailableThrowsException(): void
    {
        $mockSearchRepository = Mockery::mock(SearchRepositoryInterface::class);
        $mockSearchRepository->shouldReceive('searchCategories')
            ->once()
            ->with('test')
            ->andThrow(new RuntimeException("Service unavailable"));
        $searchService = new SearchUseCase($mockSearchRepository);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Service unavailable');

        $result = $searchService->searchCategories('test');
    }
}
