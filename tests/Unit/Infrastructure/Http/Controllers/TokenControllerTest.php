<?php

namespace Tests\Unit\Infrastructure\Http\Controllers;

use Application\UseCase\TokenUseCase;
use Illuminate\Http\Request;
use Infrastructure\Http\Controllers\TokenController;
use Infrastructure\Http\Validators\TokenRequestValidator;
use Mockery;
use Tests\Doubles\TokenUseCaseDummy;
use Tests\TestCase;
use Exception;

class TokenControllerTest extends TestCase
{
    /**
     * @test
     */
    public function givenBadParametersRepliesBadRequest()
    {
        $tokenController = new TokenController(new TokenUseCaseDummy(), new TokenRequestValidator());
        $request = new Request([
            'email' => 'example@gmail.com',
        ]);
        $answer = $tokenController->handleTokenRequest(
            $request
        );

        $data = json_decode($answer->getContent(), true);
        $status = $answer->getStatusCode();

        $this->assertEquals(400, $status);
        $this->assertEquals('Invalid or missing parameters.', $data['error']);
    }
    /**
     * @test
     */
    public function rejectsUnauthorizedUser()
    {
        $tokenServiceMock = Mockery::mock(TokenUseCase::class);
        $tokenServiceMock->shouldReceive('createTokenJwt')
            ->once()
            ->andThrow(new Exception("Invalid credentials.", 401));
        $tokenController = new TokenController($tokenServiceMock, new TokenRequestValidator());
        $request = new Request([
            'email' => 'example@gmail.com',
            'api_key' => '----',
        ]);
        $answer = $tokenController->handleTokenRequest(
            $request
        );
        $data = json_decode($answer->getContent(), true);
        $status = $answer->getStatusCode();

        $this->assertEquals(401, $status);
        $this->assertEquals('Invalid credentials.', $data['error']);
    }
    /**
     * @test
     */
    public function acceptsRegisteredUser()
    {
        $tokenServiceMock = Mockery::mock(TokenUseCase::class);
        $tokenServiceMock->shouldReceive('createTokenJwt')
            ->once()
            ->andReturn([
                'token' => "validToken",
                'expires_in' => 259200
            ]);
        $request = new Request([
            'email' => 'example@gmail.com',
            'api_key' => 'api_key',
        ]);
        $tokenController = new TokenController($tokenServiceMock, new TokenRequestValidator());

        $answer = $tokenController->handleTokenRequest(
            $request
        );
        $data = json_decode($answer->getContent(), true);
        $status = $answer->getStatusCode();

        $this->assertEquals(200, $status);
        $this->assertEquals('validToken', $data['token']);
        $this->assertEquals(259200, $data['expires_in']);
    }
}
