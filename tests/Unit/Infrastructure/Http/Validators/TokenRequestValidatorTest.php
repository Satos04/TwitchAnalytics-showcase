<?php

namespace Tests\Unit\Infrastructure\Http\Validators;

use Infrastructure\Http\Validators\TokenRequestValidator;
use Mockery;
use PHPUnit\Framework\TestCase;

class TokenRequestValidatorTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
    }
    /**
     * @test
     */
    public function rejectsRequestWithOnlyEmail()
    {
        $data = [
            'email' => 'example@gmail.com',
        ];
        $validator = new TokenRequestValidator();

        $result = $validator->checkForErrorInInputData($data);

        $this->assertEquals("Invalid API key.", $result);
    }
    /**
     * @test
     */
    public function rejectsEmptyRequest()
    {
        $data = [];
        $validator = new TokenRequestValidator();

        $result = $validator->checkForErrorInInputData($data);

        $this->assertNotEquals('', $result);
    }
    /**
     * @test
     */
    public function rejectsRequestWithOnlyApiKey()
    {
        $data = [
            'api_key' => 'api_key',
        ];
        $validator = new TokenRequestValidator();

        $result = $validator->checkForErrorInInputData($data);

        $this->assertEquals("Invalid email format.", $result);
    }
    /**
     * @test
     */
    public function admitsFilledRequest()
    {
        $data = [
            'email' => 'example@gmail.com',
            'api_key' => 'api_key',
        ];
        $validator = new TokenRequestValidator();

        $result = $validator->checkForErrorInInputData($data);

        $this->assertEquals('', $result);
    }
}
