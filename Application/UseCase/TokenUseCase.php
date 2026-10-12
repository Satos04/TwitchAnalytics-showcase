<?php

namespace Application\UseCase;

use Domain\Authentication\JWTCipherInterface;
use Domain\Entity\TokenJWT;
use Domain\Repository\SecretKeyRepositoryInterface;
use Domain\Repository\UsersRepositoryInterface;
use Exception;
use Random\RandomException;

class TokenUseCase
{
    public const int TOKEN_DURATION_IN_SECONDS = 259200;

    private UsersRepositoryInterface $usersRepository;
    private JWTCipherInterface $jwtCipher;
    private SecretKeyRepositoryInterface $secretKeyRepository;

    public function __construct(
        UsersRepositoryInterface $usersRepository,
        JWTCipherInterface $jwtCipher,
        SecretKeyRepositoryInterface $secretKeyRepository,
    ) {
        $this->usersRepository = $usersRepository;
        $this->jwtCipher = $jwtCipher;
        $this->secretKeyRepository = $secretKeyRepository;
    }

    /**
     *  @throws Exception
     */
    public function createTokenJwt(string $email, string $apiKey, string $clientIp): array
    {
        if (!$this->usersRepository->emailExists($email)) {
            throw new Exception("Invalid credentials.", 401);
        }

        $user = $this->usersRepository->getUser($email, $apiKey);
        if (!$user) {
            throw new Exception("Invalid credentials.", 401);
        }

        try {
            $token = $this->generateTokenJwt($clientIp, $email, $apiKey);
        } catch (RandomException | Exception) {
            throw new Exception("Internal server error.", 500);
        }

        return [
            'token' => $token,
            'expires_in' => self::TOKEN_DURATION_IN_SECONDS
        ];
    }

    /**
     * @throws RandomException
     * @throws Exception
     */
    public function generateTokenJwt(
        string $clientIp,
        string $email,
        string $apiKey,
    ): string {
        $tokenGenerator = new TokenJWT($this->jwtCipher, $_ENV['APP_URL'] ?? 'twitch-analytics');
        $this->secretKeyRepository->createSecretKey();
        $secretKey = $this->secretKeyRepository->getSecretKey();
        return $tokenGenerator->generateToken($clientIp, $email, $apiKey, $secretKey);
    }
}
