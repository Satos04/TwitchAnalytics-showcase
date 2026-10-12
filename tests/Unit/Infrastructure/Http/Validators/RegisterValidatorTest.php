<?php

namespace Tests\Unit\Infrastructure\Http\Validators;

use Infrastructure\Http\Validators\RegisterRequestValidator;
use PHPUnit\Framework\TestCase;

class RegisterValidatorTest extends TestCase
{
    /**
     * @test
     */
    public function returnsErrorWhenEmailIsMissing()
    {
        $validator = new RegisterRequestValidator();
        $result = $validator->checkForErrorInInputData([]);

        $this->assertEquals("Invalid or missing 'email' parameter.", $result);
    }

    /**
     * @test
     */
    public function returnsErrorWhenEmailIsEmpty()
    {
        $validator = new RegisterRequestValidator();
        $result = $validator->checkForErrorInInputData(['email' => '']);

        $this->assertEquals("Invalid or missing 'email' parameter.", $result);
    }

    /**
     * @test
     */
    public function returnsErrorWhenEmailFormatIsInvalid()
    {
        $validator = new RegisterRequestValidator();
        $result = $validator->checkForErrorInInputData(['email' => 'invalid-email']);

        $this->assertEquals("Invalid or missing 'email' parameter.", $result);
    }

    /**
     * @test
     */
    public function returnsEmptyWhenEmailIsValid()
    {
        $validator = new RegisterRequestValidator();
        $result = $validator->checkForErrorInInputData(['email' => 'test@example.com']);

        $this->assertEquals('', $result);
    }
}
