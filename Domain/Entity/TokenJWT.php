<?php

namespace Domain\Entity;

use Domain\Authentication\JWTCipherInterface;
use Exception;

class TokenJWT
{
    private JWTCipherInterface $encoder;
    private string $issuer;

    public function __construct(JWTCipherInterface $encoder, string $issuer = 'twitch-analytics')
    {
        $this->encoder = $encoder;
        $this->issuer = $issuer;
    }

    /**
     * @param $clientIp
     * @param $requestContent
     * @param $secretKey
     * @return string|null
     * @throws Exception
     */
    public function generateToken($clientIp, $email, $apiKey, $secretKey): ?string
    {
        $creationTime = time();
        $expirationTime = $creationTime + 259200;
        $payload = [
            'iss' => $this->issuer,
            'aud' => $clientIp,
            'iat' => $creationTime,
            'nbf' => $creationTime,
            'exp' => $expirationTime,
            'datos' => [
                'email' => $email
            ]
        ];
        return $this->encoder->encode($payload, $secretKey);
    }
}
