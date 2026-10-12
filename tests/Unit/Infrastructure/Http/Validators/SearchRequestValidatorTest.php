<?php

namespace Tests\Unit\Infrastructure\Http\Validators;

use Infrastructure\Http\Validators\SearchRequestValidator;
use PHPUnit\Framework\TestCase;

class SearchRequestValidatorTest extends TestCase
{
    public const string NO_ERRORS = "";

    /**
     * @test
     */
    public function givenValidQueryReturnsNoErrors(): void
    {
        $validator = new SearchRequestValidator();

        $result = $validator->checkForErrorInInputData(['query' => 'league']);

        $this->assertEquals(self::NO_ERRORS, $result);
    }

    /**
     * @test
     */
    public function returnsErrorWhenQueryIsEmpty()
    {
        $validator = new SearchRequestValidator();

        $result = $validator->checkForErrorInInputData(['query' => '']);

        $this->assertEquals("Invalid or missing 'query' parameter.", $result);
    }

}
