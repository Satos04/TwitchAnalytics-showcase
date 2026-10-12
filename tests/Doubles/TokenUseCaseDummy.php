<?php

namespace Tests\Doubles;

use Application\UseCase\TokenUseCase;
use Exception;
use Random\RandomException;

class TokenUseCaseDummy extends TokenUseCase
{
    public function __construct()
    {
    }

    /**
     *  @throws Exception
     */
    public function createTokenJwt(string $email, string $apiKey, string $clientIp): array
    {
        throw new Exception();
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
        throw new Exception();
    }
}
