<?php

namespace Tests\Unit\Http\Middleware;

use Infrastructure\Http\Middleware\HttpTokenRequestHandler;
use Tests\TestCase;
use Illuminate\Http\Request;

class HttpTokenRequestHandlerTest extends TestCase
{
    /** @test */
    public function givenRequestReturnsRequestBodyStructure(): void
    {
        $requestMock = \Mockery::mock(Request::class);
        $requestMock->shouldReceive('method')->andReturn('POST');
        $requestMock->shouldReceive('getRequestUri')->andReturn('/api/test');
        $requestMock->shouldReceive('ip')->andReturn('127.0.0.1');

        $requestHandler = new HttpTokenRequestHandler($requestMock);

        $result = $requestHandler->getRequestBodyStructure();

        $expected = ['POST', '/api/test', '127.0.0.1'];
        $this->assertEquals($expected, $result);
    }

    /** @test */
    public function givenRequestReturnsRequestBody(): void
    {
        $body = '{"name": "Example User",
        "email": example@test.com"}';
        $requestMock = \Mockery::mock(Request::class);
        $requestMock->shouldReceive('all')->andReturn($body);

        $requestHandler = new HttpTokenRequestHandler($requestMock);

        $result = $requestHandler->getRequestBodyData();

        $this->assertEquals($body, $result);
    }
}
