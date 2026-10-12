<?php

namespace Tests\Unit\Application\UseCase;

use Application\UseCase\RegisterUseCase;
use Domain\Repository\UsersRepositoryInterface;
use Mockery;
use PHPUnit\Framework\TestCase;
use Tests\Doubles\ApiKeyGeneratorStub;
use PDOException;

class RegisterUseCaseTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
    }

    /**
     * @test
     */
    public function generatesApiKeySuccessfully()
    {
        $mockRepository = Mockery::mock(UsersRepositoryInterface::class);
        $mockRepository->shouldReceive('createUser')
            ->once()
            ->andReturn(true);

        $registerUseCase = new RegisterUseCase($mockRepository, new ApiKeyGeneratorStub());
        $result = $registerUseCase->register('test@example.com');

        $expected = [
            'email' => 'test@example.com',
            'apiKey' => '123456'];

        $this->assertEquals($expected, $result);
    }

    /**
     * @test
     */
    public function returnsFalseWhenEmailDoesNotExist()
    {
        $mockRepository = Mockery::mock(UsersRepositoryInterface::class);
        $mockRepository->shouldReceive('emailExists')
            ->once()
            ->with('nonexistent@example.com')
            ->andReturn(false);

        $registerUseCase = new RegisterUseCase($mockRepository, new ApiKeyGeneratorStub());
        $result = $registerUseCase->emailExists('nonexistent@example.com');

        $this->assertFalse($result);
    }

    /**
     * @test
     */
    public function returnsTrueWhenEmailAlreadyExists()
    {
        $mockRepository = Mockery::mock(UsersRepositoryInterface::class);
        $mockRepository->shouldReceive('emailExists')
            ->once()
            ->with('existing@example.com')
            ->andReturn(true);

        $registerUseCase = new RegisterUseCase($mockRepository, new ApiKeyGeneratorStub());
        $result = $registerUseCase->emailExists('existing@example.com');

        $this->assertTrue($result);
    }

    /**
     * @test
     */
    public function registerFailsDueToDatabaseError()
    {
        $mockRepository = Mockery::mock(UsersRepositoryInterface::class);
        $mockRepository->shouldReceive('createUser')
            ->once()
            ->andThrow(new PDOException('Database connection failed'));

        $registerUseCase = new RegisterUseCase($mockRepository, new ApiKeyGeneratorStub());

        $this->expectException(PDOException::class);
        $this->expectExceptionMessage('Database connection failed');

        $registerUseCase->register('test@example.com');
    }
}
