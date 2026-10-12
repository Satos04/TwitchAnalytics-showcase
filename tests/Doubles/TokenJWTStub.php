<?php

namespace Tests\Doubles;

use Domain\Authentication\JWTCipherInterface;
use Domain\Entity\TokenJWT;
use Exception;

class TokenJWTStub extends TokenJWT
{
    private JWTCipherInterface $encoder;

    public function __construct(JWTCipherInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    /**
     * @param $clientIp
     * @param $requestContent
     * @param $secretKey
     * @return string|null
     * @throws Exception
     */
    public function generateToken($clientIp, $requestContent, $secretKey): ?string
    {
        try {
            return "abcde";
        } catch (Exception $e) {
            throw $e;
        }
    }
}
