<?php

namespace Tests\Unit\Infrastructure\Http\Controllers;

use Application\UseCase\RegisterUseCase;
use Infrastructure\Http\Controllers\RegisterController;
use Infrastructure\Http\Validators\RegisterRequestValidator;
use Tests\Doubles\ApiKeyGeneratorStub;
use Tests\Doubles\UsersRepositoryFake;
use Tests\TestCase;

class RegisterControllerTest extends TestCase
{
    /**
     * @test
     */
    public function givenUnregisteredEmailReturnsEmailWithApiKey()
    {
        $registerServiceFake = new RegisterUseCase(
            new UsersRepositoryFake(),
            new ApiKeyGeneratorStub()
        );
        $registerController = new RegisterController($registerServiceFake, new RegisterRequestValidator());

        $result = $registerController->handle(request()->merge(['email' => 'example@gmail.com']));

        $expect = response()->json([
            'email'   => 'example@gmail.com',
            'api_key' => '123456',
        ], 201);
        $this->assertEquals($expect, $result);
    }
    /**
     * @test
     */
    public function givenRegisteredEmailReturnsEmailRegisteredResponse()
    {
        $registerServiceFake = new RegisterUseCase(
            new UsersRepositoryFake(),
            new ApiKeyGeneratorStub()
        );
        $registerController = new RegisterController($registerServiceFake, new RegisterRequestValidator());

        $result = $registerController->handle(request()->merge(['email' => 'registered@gmail.com']));

        $expect = response()->json(['error' => 'Email already registered.'], 409);
        $this->assertEquals($expect, $result);
    }
}
