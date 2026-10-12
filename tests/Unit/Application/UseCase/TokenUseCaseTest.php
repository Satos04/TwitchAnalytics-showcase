<?php

namespace Tests\Unit\Application\UseCase;

use Application\UseCase\TokenUseCase;
use Domain\Repository\SecretKeyRepositoryInterface;
use Exception;
use Mockery;
use PHPUnit\Framework\TestCase;
use Random\RandomException;
use Tests\Doubles\JWTCipherStub;
use Tests\Doubles\JWTCipherDummy;
use Tests\Doubles\SecretKeyRepositoryDummy;
use Tests\Doubles\SecretKeyRepositoryStub;
use Tests\Doubles\UsersRepositoryFake;

class TokenUseCaseTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
    }

    /** @test */
    public function generatesTokenSuccessfully()
    {
        $tokenUseCase = new TokenUseCase(
            new UsersRepositoryFake(),
            new JWTCipherStub(),
            new SecretKeyRepositoryStub()
        );

        $clientToken = $tokenUseCase->generateTokenJwt(
            '127.0.0.1',
            'example@gmail.com',
            'api_key'
        );

        $this->assertEquals("12345", $clientToken);
    }

    /** @test
     * @throws RandomException
     */
    public function tokenGenerationFailsBecauseOfCipherError()
    {
        $mockJWTCipher = Mockery::mock(JWTCipherDummy::class);
        $mockJWTCipher->shouldReceive('encode')
            ->once()
            ->andThrow(new Exception("TokenJWT cipher fails"));

        $tokenUseCase = new TokenUseCase(
            new UsersRepositoryFake(),
            $mockJWTCipher,
            new SecretKeyRepositoryStub()
        );

        $this->expectException(Exception::class);
        $this->expectExceptionMessage("TokenJWT cipher fails");

        $tokenUseCase->generateTokenJwt('127.0.0.1', 'example@gmail.com', 'api_key');
    }

    /** @test */
    public function tokenGenerationFailsBecauseOfSecretKeyNotFound()
    {
        $mockSecretKeyRepo = Mockery::mock(SecretKeyRepositoryInterface::class);
        $mockSecretKeyRepo->shouldReceive('createSecretKey')
            ->once()
            ->andReturn();
        $mockSecretKeyRepo->shouldReceive('getSecretKey')
            ->once()
            ->andThrow(new Exception("Secret Key Not Found"));

        $tokenUseCase = new TokenUseCase(
            new UsersRepositoryFake(),
            new JWTCipherDummy(),
            $mockSecretKeyRepo
        );

        $this->expectException(Exception::class);
        $this->expectExceptionMessage("Secret Key Not Found");

        $tokenUseCase->generateTokenJwt('127.0.0.1', 'example@gmail.com', 'api_key');
    }
    /** @test */
    public function givenUnregisteredEmailReturnsError()
    {
        $tokenUseCase = new TokenUseCase(
            new UsersRepositoryFake(),
            new JWTCipherDummy(),
            new SecretKeyRepositoryDummy()
        );

        $this->expectException(Exception::class);
        $this->expectExceptionMessage("Invalid credentials.");

        $tokenUseCase->createTokenJwt('email@example.com', 'api_key', '127.0.0.1');
    }
    /** @test */
    public function givenWrongApiKeyReturnsError()
    {
        $tokenUseCase = new TokenUseCase(
            new UsersRepositoryFake(),
            new JWTCipherDummy(),
            new SecretKeyRepositoryDummy()
        );

        $this->expectException(Exception::class);
        $this->expectExceptionMessage("Invalid credentials.");

        $tokenUseCase->createTokenJwt('registered@gmail.com', 'invalid_key', '127.0.0.1');
    }
}
