<?php

namespace Tests\Integration;

use Infrastructure\Authentication\JWTCipher;
use Tests\TestCase;

class JWTCipherTest extends TestCase
{
    /** @test */
    public function givenPayloadAndKeyReturnsToken(): void
    {
        $cipher = new JWTCipher();
        $payload = ['name' => "Example User"];
        $key = 'example-key-to-cipher-1234567890';

        $result = $cipher -> encode($payload, $key);

        $this->assertIsString($result);
        $this->assertNotEmpty($result);
    }

    /** @test */
    public function givenTokenAndKeyReturnsPayload(): void
    {
        $cipher = new JWTCipher();
        $payload = ['name' => "Example User"];
        $key = 'example-key-to-cipher-1234567890';

        $token = $cipher -> encode($payload, $key);
        $result = $cipher -> decode($token, $key);

        $this->assertEquals($payload, $result);
    }
}
