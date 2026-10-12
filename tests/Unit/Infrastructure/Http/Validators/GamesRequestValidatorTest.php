<?php

namespace Tests\Unit\Infrastructure\Http\Validators;

use Infrastructure\Http\Validators\GamesRequestValidator;
use PHPUnit\Framework\TestCase;

class GamesRequestValidatorTest extends TestCase
{
    public const string NO_ERRORS = "";

    /**
     * @test
     */
    public function givenLimitBetween1And3ReturnsNoErrors(): void
    {
        $validator = new GamesRequestValidator();

        $result = $validator->checkForErrorInInputData(['limit' => '2']);

        $this->assertEquals(self::NO_ERRORS, $result);
    }
    /**
     * @test
     */
    public function givenLimitGreaterThanThreeOrLessThanOneReturnsError(): void
    {
        $validator = new GamesRequestValidator();

        $result = $validator->checkForErrorInInputData(['limit' => '6']);

        $this->assertEquals("Limit must be between 1 and 3.", $result);
    }
    /**
     * @test
     */
    public function givenEmptyLimitReturnsError(): void
    {
        $validator = new GamesRequestValidator();

        $result = $validator->checkForErrorInInputData(['limit' => '']);

        $this->assertEquals("Limit must be between 1 and 3.", $result);
    }
}
