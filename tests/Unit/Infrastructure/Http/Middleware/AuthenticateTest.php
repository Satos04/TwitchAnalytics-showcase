<?php

namespace Tests\Unit\Infrastructure\Http\Middleware;

use Infrastructure\Http\Middleware\Authenticate;
use Tests\Doubles\JWTCipherStub;
use Tests\Doubles\SecretKeyRepositoryStub;
use Tests\TestCase;

class AuthenticateTest extends TestCase
{
    /** @test */
    public function givenRequestWithoutAuthorizationHeaderReturnsUnauthorizedResponse(): void
    {
        $requestMock = \Mockery::mock('Illuminate\Http\Request');
        $requestMock->shouldReceive('header')->with('Authorization')->andReturn(null);

        $middleware = new Authenticate(
            \Mockery::mock('Domain\Authentication\JWTCipherInterface'),
            \Mockery::mock('Domain\Repository\SecretKeyRepositoryInterface')
        );

        $response = $middleware->handle($requestMock, function () {
        });

        $this->assertEquals(401, $response->getStatusCode());
        $this->assertEquals(['error' => 'Unauthorized. Token is invalid or has expired.'], $response->getData(true));
    }

    /** @test */
    public function givenRequestWithValidTokenCallsNextMiddleware(): void
    {
        $requestMock = \Mockery::mock('Illuminate\Http\Request');
        $requestMock->shouldReceive('header')->with('Authorization')->andReturn('Bearer valid.jwt.token');

        $middleware = new Authenticate(
            new JWTCipherStub(),
            new SecretKeyRepositoryStub()
        );

        $nextWasCalled = false;
        $response = $middleware->handle($requestMock, function () use (&$nextWasCalled) {
            $nextWasCalled = true;
            return response()->json(['status' => 'ok'], 200);
        });

        $this->assertTrue($nextWasCalled);
        $this->assertEquals(200, $response->getStatusCode());
    }
}
